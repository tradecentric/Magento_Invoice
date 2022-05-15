<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model\RequestBuilder;

use Magento\Sales\Api\Data\InvoiceInterface;
use TradeCentric\Invoice\Api\ModuleMetadataInterface;
use TradeCentric\Invoice\Api\RequestBuildElementInterface;
use TradeCentric\Invoice\Api\RequestBuilderInterface;

    /**
 * Class HeadersRequestElement
 * @package TradeCentric\Invoice\Model\RequestBuilder
 */
class HeadersRequestElement implements RequestBuildElementInterface
{
    /**
     * @var string[]
     */
    protected $headers = ['Content-Type' => 'application/json'];

    /**
     * @param ModuleMetadataInterface $moduleMetadata
     * @param array $headers
     */
    public function __construct(
        ModuleMetadataInterface $moduleMetadata,
        array $headers = []
    ) {
        $this->headers += $headers;
        $this->headers += [
            'Magento-Version' => $moduleMetadata->getMagentoVersion(),
            'Module-Version' => $moduleMetadata->getModuleVersion()
        ];
    }

    /**
     * @param RequestBuilderInterface $requestBuilder
     * @param InvoiceInterface $invoice
     */
    public function build(RequestBuilderInterface $requestBuilder, InvoiceInterface $invoice): void
    {
        foreach ($this->headers as $name => $value) {
            $requestBuilder->addHeader($name, $value);
        }
    }
}
