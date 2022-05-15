<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Api;

use Magento\Sales\Api\Data\InvoiceInterface;

/**
 * Interface InvoiceServiceInterface
 * @package TradeCentric\Invoice\Api
 */
interface InvoiceServiceInterface
{
    /**
     * @param InvoiceInterface $invoice
     * @return mixed
     */
    public function sendInvoice(InvoiceInterface $invoice): bool;
}
