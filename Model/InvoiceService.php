<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use TradeCentric\Invoice\Api\InvoiceServiceInterface;
use TradeCentric\Invoice\Api\RequestBuilderInterface;
use TradeCentric\Invoice\Api\RequestInterface;
use TradeCentric\Invoice\Api\ResultValidatorInterface;
use TradeCentric\Invoice\Api\StoreLoggerInterface;
use TradeCentric\Invoice\Api\InvoiceSenderInterface;
use TradeCentric\Invoice\Helper\Data;
use TradeCentric\Invoice\Model\InvoiceService\InvoiceRegistrationContext;
use TradeCentric\Invoice\Model\System\Config\Source\InvoiceEvents;
use Magento\Sales\Model\Order;

/**
 * Class InvoiceService
 * @package TradeCentric\Invoice\Model
 */
class InvoiceService implements InvoiceServiceInterface
{
    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var ResultValidatorInterface
     */
    protected $resultValidator;

    /**
     * @var StoreLoggerInterface
     */
    protected $logger;

    /**
     * @var InvoiceSenderInterface
     */
    protected $invoiceSender;

    /**
     * @var InvoiceRegistrationContext
     */
    protected $invoiceRegistrationContext;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param RequestBuilderInterface $requestBuilder
     * @param ResultValidatorInterface $resultValidator
     * @param StoreLoggerInterface $logger
     * @param InvoiceSenderInterface $invoiceSender
     * @param InvoiceRegistrationContext $invoiceRegistrationContext
     * @param Data $helper
     */
    public function __construct(
        RequestBuilderInterface $requestBuilder,
        ResultValidatorInterface $resultValidator,
        StoreLoggerInterface $logger,
        InvoiceSenderInterface $invoiceSender,
        InvoiceRegistrationContext $invoiceRegistrationContext,
        Data $helper
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->resultValidator = $resultValidator;
        $this->logger = $logger;
        $this->invoiceSender = $invoiceSender;
        $this->helper = $helper;
        $this->invoiceRegistrationContext = $invoiceRegistrationContext;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isOrderReadyToAutoInvoiceSend(Order $order): bool
    {
        if (!$this->helper->isEnabled($order->getStoreId())) {
            return false;
        }
        $onEvent = $this->helper->getOnEvent($order->getStoreId());
        if ($onEvent === InvoiceEvents::ON_INVOICE) {
            return $order->hasInvoices()
                && $this->invoiceRegistrationContext->isInvoiceRegistered();
        }
        if ($onEvent === InvoiceEvents::ON_COMPLETE) {
            return $order->hasInvoices()
                && $order->dataHasChangedFor(Order::STATE)
                && $order->getState() === Order::STATE_COMPLETE;
        }
        return false;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isOrderReadyToManualInvoiceSend(Order $order): bool
    {
        if (!$this->helper->isEnabled($order->getStoreId())) {
            return false;
        }
        $onEvent = $this->helper->getOnEvent($order->getStoreId());
        if ($onEvent === InvoiceEvents::MANUAL) {
            return $order->hasInvoices();
        }
        return false;
    }

    /**
     * @param Order $order
     * @return bool
     */
    private function isOrderReadyToSendInvoice(Order $order)
    {
        return $this->isOrderReadyToAutoInvoiceSend($order) || $this->isOrderReadyToManualInvoiceSend($order);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return bool
     */
    public function sendInvoice(InvoiceInterface $invoice): bool
    {
        $order = $invoice->getOrder();
        if (!$this->isOrderReadyToSendInvoice($order)) {
            throw new LocalizedException(__('Order transfer is not allowed'));
        }
        $this->logger->info('Send invoice to punch2go before, invoice id - ' . $invoice->getIncrementId());
        try {
            /** @var RequestInterface $request */
            $request = $this->requestBuilder->build(clone $invoice);
            $this->logger->info('Request body - ' . print_r($request->getParams(), true));
            $result = $this->invoiceSender->send($request);
            $this->logger->info('Response body - ' . print_r($result->getBody(), true));
            $validationResult = $this->resultValidator->validate($result);
            if ($validationResult->isValid()) {
                $invoice->addComment("Invoice transferred successfully (Reference #{$result->getBody()['result']})");
                $this->logger->info('Send invoice to punch2go after, invoice id - ' . $invoice->getIncrementId());
                return true;
            }
            $invoice->getCommentsCollection(true);
            foreach ($validationResult->getErrors() as $error) {
                $invoice->addComment($error);
            }
            return false;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->info('Send invoice to punch2go error, invoice id - ' . $invoice->getIncrementId());
            throw new LocalizedException(__('Order transfer error, please, check logs'));
        }
    }
}
