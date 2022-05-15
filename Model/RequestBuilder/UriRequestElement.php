<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model\RequestBuilder;

use Magento\Sales\Api\Data\InvoiceInterface;
use TradeCentric\Invoice\Helper\Data;
use TradeCentric\Invoice\Api\RequestBuildElementInterface;
use TradeCentric\Invoice\Api\RequestBuilderInterface;

/**
 * Class UriRequestElement
 * @package TradeCentric\Invoice\Model\RequestBuilder
 */
class UriRequestElement implements RequestBuildElementInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param RequestBuilderInterface $requestBuilder
     * @param InvoiceInterface $invoice
     */
    public function build(RequestBuilderInterface $requestBuilder, InvoiceInterface $invoice): void
    {
        $requestBuilder->setUri($this->helper->getInvoiceUrl($invoice->getStoreId()));
    }
}
