<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model;

use TradeCentric\Invoice\Api\RequestInterface;

class Request implements RequestInterface
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
     * @param array $config
     * @param array $headers
     * @param array $params
     * @param string $uri
     */
    public function __construct(array $config, array $headers, array $params, string $uri)
    {
        $this->config = $config;
        $this->headers = $headers;
        $this->params = $params;
        $this->uri = $uri;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }
}
