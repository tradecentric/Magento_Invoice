<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Controller\Adminhtml\Sendinvoice;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Psr\Log\LoggerInterface;
use TradeCentric\Invoice\Api\InvoiceServiceInterface;
use TradeCentric\Invoice\Api\StoreLoggerInterface;
use TradeCentric\Invoice\Helper\Data;

/**
 * Class Index
 * @package TradeCentric\Invoice\Controller\Adminhtml\Sendinvoice
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'TradeCentric_Invoice::transfer';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var InvoiceServiceInterface
     */
    protected $invoiceService;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Index constructor.
     * @param Context $context
     * @param Data $helper
     * @param InvoiceServiceInterface $invoiceService
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param StoreLoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Data $helper,
        InvoiceServiceInterface $invoiceService,
        InvoiceRepositoryInterface $invoiceRepository,
        StoreLoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->invoiceService = $invoiceService;
        $this->invoiceRepository = $invoiceRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        try {
            /** @var \Magento\Sales\Model\Order\Invoice $invoiceModel */
            $invoice = $this->invoiceRepository->get($invoiceId);
            if (!$this->helper->isEnabled($invoice->getStoreId())) {
                throw new LocalizedException(__('Invoice export is disabled'));
            }
            $result = $this->invoiceService->sendInvoice($invoice);
            $this->invoiceRepository->save($invoice);
            if ($result) {
                $this->messageManager->addSuccessMessage('Invoice was successfully sent');
            } else {
                $this->messageManager->addErrorMessage('Invoice was sent with error');
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->messageManager->addErrorMessage('Invoice did not transfer');
        }
        return $this->_redirect('sales/invoice/view/invoice_id/'. $invoiceId);
    }
}
