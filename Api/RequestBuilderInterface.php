<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Api;

use Magento\Sales\Api\Data\InvoiceInterface;

/**
 * Interface RequestBuilderInterface
 * @package TradeCentric\Invoice\Api
 */
interface RequestBuilderInterface
{
    /**
     * @param string $key
     * @param string $value
     * @return RequestBuilderInterface
     */
    public function addHeader(string $key, string $value): RequestBuilderInterface;

    /**
     * @param array $params
     * @return RequestBuilderInterface
     */
    public function addParams(array $params): RequestBuilderInterface;

    /**
     * @param string $key
     * @param string $value
     * @return RequestBuilderInterface
     */
    public function addConfig(string $key, string $value): RequestBuilderInterface;

    /**
     * @param string $uri
     * @return RequestBuilderInterface
     */
    public function setUri(string $uri): RequestBuilderInterface;

    /**
     * @param InvoiceInterface $invoice
     * @return RequestInterface
     */
    public function build(InvoiceInterface $invoice): RequestInterface;
}
