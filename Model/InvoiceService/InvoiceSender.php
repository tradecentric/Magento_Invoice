<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model\InvoiceService;

use TradeCentric\Invoice\Api\RequestInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use TradeCentric\Invoice\Api\RequestResultInterfaceFactory;
use Magento\Framework\HTTP\ZendClient;
use TradeCentric\Invoice\Api\InvoiceSenderInterface;
use TradeCentric\Invoice\Api\RequestResultInterface;

class InvoiceSender implements InvoiceSenderInterface
{
    /**
     * @var ZendClientFactory 
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
     * @param ZendClientFactory $clientFactory
     * @param RequestResultInterfaceFactory $resultFactory
     * @param Json $json
     */
    public function __construct(
        ZendClientFactory $clientFactory, 
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
        /** @var \Magento\Framework\HTTP\ZendClient $client */
        $client = $this->getClient($request);
        /** @var \Zend_Http_Response $result */
        $result = $client->request(\Zend_Http_Client::POST);
        return $this->resultFactory->create(['response' => $result]);
    }

    /**
     * @param RequestInterface $request
     * @return ZendClient
     */
    protected function getClient(RequestInterface $request): ZendClient
    {
        $client = $this->clientFactory->create();
        $client->setConfig($request->getConfig());
        $client->setHeaders($request->getHeaders());
        $client->setUri($request->getUri());
        $requestBody = $this->json->serialize($request->getParams());
        $client->setRawData($requestBody);
        return $client;
    }
}