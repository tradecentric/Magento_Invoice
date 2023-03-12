<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Api;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order;

/**
 * Interface InvoiceServiceInterface
 * @package TradeCentric\Invoice\Api
 */
interface InvoiceServiceInterface
{
    /**
     * @param Order $order
     * @return bool
     */
    public function isOrderReadyToAutoInvoiceSend(Order $order): bool;

    /**
     * @param Order $order
     * @return bool
     */
    public function isOrderReadyToManualInvoiceSend(Order $order): bool;
    
    /**
     * @param InvoiceInterface $invoice
     * @return mixed
     */
    public function sendInvoice(InvoiceInterface $invoice): bool;
}
