<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model\InvoiceService;

use TradeCentric\Invoice\Api\RequestInterface;
use Magento\Framework\HTTP\LaminasClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use TradeCentric\Invoice\Api\RequestResultInterfaceFactory;
use Magento\Framework\HTTP\LaminasClient;
use TradeCentric\Invoice\Api\InvoiceSenderInterface;
use TradeCentric\Invoice\Api\RequestResultInterface;

class InvoiceSender implements InvoiceSenderInterface
{
    /**
     * @var LaminasClientFactory
     */
    private $clientFactory;

    /**
     * @var RequestResultInterfaceFactory
     */
    private $resultFactory;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param LaminasClientFactory $clientFactory
     * @param RequestResultInterfaceFactory $resultFactory
     * @param Json $json
     */
    public function __construct(
        LaminasClientFactory $clientFactory,
        RequestResultInterfaceFactory $resultFactory,
        Json $json)
    {
        $this->clientFactory = $clientFactory;
        $this->resultFactory = $resultFactory;
        $this->json = $json;
    }

    /**
     * @param RequestInterface $request
     * @return RequestResultInterface
     */
    public function send(RequestInterface $request): RequestResultInterface
    {
        /** @var LaminasClient $client */
        $client = $this->getClient($request);
        /** @var \Zend_Http_Response $result */
        $client->setMethod(\Laminas\Http\Request::METHOD_POST);
        $result = $client->send();
        return $this->resultFactory->create(['response' => $result]);
    }

    /**
     * @param RequestInterface $request
     * @return LaminasClient
     */
    protected function getClient(RequestInterface $request): LaminasClient
    {
        /** @var LaminasClient $client */
        $client = $this->clientFactory->create();
        $client->setOptions($request->getConfig());
        $client->setHeaders($request->getHeaders());
        $client->setUri($request->getUri());
        $requestBody = $this->json->serialize($request->getParams());
        $client->setRawBody($requestBody);
        return $client;
    }
}
