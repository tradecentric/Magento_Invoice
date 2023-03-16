<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class RegisterInvoiceObserver
 * @package TradeCentric\Invoice\Observer
 */
class RegisterInvoiceObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $invoice = $observer->getInvoice();
        $invoice->setIsCreated(1);
    }
}
