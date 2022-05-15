<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Api;

use Magento\Sales\Api\Data\InvoiceInterface;

/**
 * Interface RequestBuildElementInterface
 * @package TradeCentric\Invoice\Api
 */
interface RequestBuildElementInterface
{
    /**
     * @param RequestBuilderInterface $requestBuilder
     */
    public function build(RequestBuilderInterface $requestBuilder, InvoiceInterface $invoice): void;
}
