<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\InvoiceInterface;
use TradeCentric\Invoice\Api\RequestBuildElementInterface;
use TradeCentric\Invoice\Api\RequestBuilderInterface;
use TradeCentric\Invoice\Api\RequestInterfaceFactory;
use TradeCentric\Invoice\Api\RequestInterface;
use Magento\Framework\ObjectManager\TMapFactory;
use Magento\Framework\ObjectManager\Helper\Composite as CompositeHelper;

/**
 * Class RequestBuilder
 * @package TradeCentric\Invoice\Model
 */
class RequestBuilder implements RequestBuilderInterface
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var string
     */
    protected $uri = '';

    /**
     * @var \Magento\Framework\ObjectManager\TMap
     */
    protected $requestBuilderPool;

    /**
     * @var RequestInterfaceFactory
     */
    protected $factory;

    /**
     * @param RequestInterfaceFactory $factory
     * @param TMapFactory $mapFactory
     * @param CompositeHelper $compositeHelper
     * @param array $requestBuilderPool
     */
    public function __construct(
        RequestInterfaceFactory $factory,
        TMapFactory $mapFactory,
        CompositeHelper $compositeHelper,
        array $requestBuilderPool
    ) {
        $this->factory = $factory;
        $this->requestBuilderPool = $mapFactory->create([
            'array' => array_column($compositeHelper->filterAndSortDeclaredComponents($requestBuilderPool), 'type'),
            'type' => RequestBuildElementInterface::class
        ]);
    }

    /**
     * @param string $key
     * @param string $value
     * @return RequestBuilderInterface
     */
    public function addConfig(string $key, string $value): RequestBuilderInterface
    {
        $this->config[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return RequestBuilderInterface
     */
    public function addHeader(string $key, string $value): RequestBuilderInterface
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * @param array $params
     * @return RequestBuilderInterface
     */
    public function addParams(array $params): RequestBuilderInterface
    {
        $this->params = array_merge_recursive($this->params, $params);
        return $this;
    }

    /**
     * @param string $uri
     * @return RequestBuilderInterface
     */
    public function setUri(string $uri): RequestBuilderInterface
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RequestInterface
     */
    public function build(InvoiceInterface $invoice): RequestInterface
    {
        foreach ($this->requestBuilderPool as $requestBuilder) {
            $requestBuilder->build($this, $invoice);
        }

        if (!$this->uri) {
            throw new LocalizedException(__("Invoice export uri is not specified"));
        }
        return $this->factory->create([
            'config' => $this->config,
            'headers' => $this->headers,
            'params' => $this->params,
            'uri' => $this->uri
        ]);
    }
}
