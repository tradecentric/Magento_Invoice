<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Api;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface InvoiceSenderInterface
 * @package TradeCentric\Invoice\Api
 */
interface InvoiceSenderInterface
{
    /**
     * @param RequestInterface $request
     * @return RequestResultInterface
     */
    public function send(RequestInterface $request): RequestResultInterface;
}
