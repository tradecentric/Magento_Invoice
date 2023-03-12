<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use TradeCentric\Invoice\Api\InvoiceServiceInterface;
use TradeCentric\Invoice\Api\StoreLoggerInterface;

/**
 * Class SaveOrderAfterObserver
 * @package TradeCentric\Invoice\Observer
 */
class SaveOrderAfterObserver implements ObserverInterface
{
    /**
     * @var InvoiceServiceInterface
     */
    protected $invoiceService;

    /**
     * @var InvoiceRepositoryInterface 
     */
    protected $invoiceRepository;
    
    /**
     * @var StoreLoggerInterface
     */
    protected $storeLogger;

    /**
     * @param InvoiceServiceInterface $invoiceService
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param StoreLoggerInterface $storeLogger
     */
    public function __construct(
        InvoiceServiceInterface $invoiceService,
        InvoiceRepositoryInterface $invoiceRepository,
        StoreLoggerInterface $storeLogger
    ) {
        $this->invoiceService = $invoiceService;
        $this->invoiceRepository = $invoiceRepository;
        $this->storeLogger = $storeLogger;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getOrder();
        if (!$this->invoiceService->isOrderReadyToAutoInvoiceSend($order)) {
            return;
        }
        try {
            $invoice = current($order->getInvoiceCollection()->getItems());
            $this->invoiceService->sendInvoice($invoice);
            $this->invoiceRepository->save($invoice);
        } catch (\Exception $e) {
            $this->storeLogger->setStoreId($order->getStoreId());
            $this->storeLogger->error($e->getMessage());
        }
    }
}
