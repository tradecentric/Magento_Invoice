<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use TradeCentric\Invoice\Model\InvoiceService\InvoiceRegistrationContext;

/**
 * Class RegisterInvoiceObserver
 * @package TradeCentric\Invoice\Observer
 */
class RegisterInvoiceObserver implements ObserverInterface
{
    /**
     * @var InvoiceRegistrationContext 
     */
    protected $invoiceRegistrationContext;

    /**
     * @param InvoiceRegistrationContext $invoiceRegistrationContext
     */
    public function __construct(InvoiceRegistrationContext $invoiceRegistrationContext) 
    {
        $this->invoiceRegistrationContext = $invoiceRegistrationContext;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->invoiceRegistrationContext->isInvoiceRegistered(true);
    }
}
