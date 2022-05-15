<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model\RequestBuilder;

use Magento\Sales\Api\Data\InvoiceInterface;
use TradeCentric\Invoice\Api\ModuleMetadataInterface;
use TradeCentric\Invoice\Helper\Data;
use TradeCentric\Invoice\Model\ModuleVersion;
use TradeCentric\Invoice\Api\RequestBuildElementInterface;
use TradeCentric\Invoice\Api\RequestBuilderInterface;

/**
 * Class ParamsRequestElement
 * @package TradeCentric\Invoice\Model\RequestBuilder
 */
class ParamsRequestElement implements RequestBuildElementInterface
{
    /**
     * @var ModuleVersion
     */
    protected $moduleMetadata;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param ModuleMetadataInterface $moduleMetadata
     * @param Data $helper
     */
    public function __construct(
        ModuleMetadataInterface $moduleMetadata,
        Data $helper
    ) {
        $this->moduleMetadata = $moduleMetadata;
        $this->helper = $helper;
    }

    /**
     * @param RequestBuilderInterface $requestBuilder
     * @param InvoiceInterface $invoice
     */
    public function build(RequestBuilderInterface $requestBuilder, InvoiceInterface $invoice): void
    {
        $requestBuilder->addParams([
            'apikey' => $this->helper->getInvoiceApiKey($invoice->getStoreId()),
            'version' => $this->moduleMetadata->getModuleVersion(),
            'params' => $this->getParams($invoice)
        ]);
    }

    /**
     * @param InvoiceInterface $invoice
     */
    protected function getParams(InvoiceInterface $invoice)
    {
        $payment = $invoice->getOrder()->getPayment();
        return [
            'order_request_id' => $payment->getAdditionalInformation('request_id'),
            'magento_version' => $this->moduleMetadata->getMagentoVersion(),
            'module_version' => $this->moduleMetadata->getModuleVersion()
        ];
    }
}
