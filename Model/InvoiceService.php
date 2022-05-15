<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\Data\InvoiceInterface;
use TradeCentric\Invoice\Api\InvoiceServiceInterface;
use TradeCentric\Invoice\Api\RequestBuilderInterface;
use TradeCentric\Invoice\Api\RequestInterface;
use TradeCentric\Invoice\Api\ResultValidatorInterface;
use TradeCentric\Invoice\Api\StoreLoggerInterface;

/**
 * Class InvoiceService
 * @package TradeCentric\Invoice\Model
 */
class InvoiceService implements InvoiceServiceInterface
{
    /**
     * @var ZendClientFactory
     */
    protected $clientFactory;

    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var ResultValidatorInterface
     */
    protected $resultValidator;

    /**
     * @var StoreLoggerInterface
     */
    protected $logger;

    /**
     * @var Json
     */
    protected $json;

    /**
     * InvoiceService constructor.
     * @param ZendClientFactory $clientFactory
     * @param RequestBuilderInterface $requestBuilder
     * @param ResultValidatorInterface $resultValidator
     * @param StoreLoggerInterface $logger
     * @param Json $json
     */
    public function __construct(
        ZendClientFactory $clientFactory,
        RequestBuilderInterface $requestBuilder,
        ResultValidatorInterface $resultValidator,
        StoreLoggerInterface $logger,
        Json $json
    ) {
        $this->clientFactory = $clientFactory;
        $this->requestBuilder = $requestBuilder;
        $this->resultValidator = $resultValidator;
        $this->logger = $logger;
        $this->json = $json;
    }

    /**
     * @param InvoiceInterface $invoice
     * @return bool
     */
    public function sendInvoice(InvoiceInterface $invoice): bool
    {
        $this->logger->info('Send invoice to punch2go before, invoice id - ' . $invoice->getIncrementId());
        try {
            /** @var RequestInterface $request */
            $request = $this->requestBuilder->build(clone $invoice);
            /** @var \Magento\Framework\HTTP\ZendClient $client */
            $client = $this->getClient($request);
            /** @var \Zend_Http_Response $result */
            $result = $client->request(\Zend_Http_Client::POST);
            return $this->processResponse($result, $invoice);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->info('Send invoice to punch2go error, invoice id - ' . $invoice->getIncrementId());
            throw new LocalizedException(__('Order transfer error, please, check logs'));
        }
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\HTTP\ZendClient
     * @throws \Zend_Http_Client_Exception
     */
    protected function getClient(RequestInterface $request)
    {
        $client = $this->clientFactory->create();
        $client->setConfig($request->getConfig());
        $client->setHeaders($request->getHeaders());
        $client->setUri($request->getUri());
        $requestBody = $this->json->serialize($request->getParams());
        $this->logger->info('Request body - ' . $requestBody);
        $client->setRawData($requestBody);
        return $client;
    }

    /**
     * @param \Zend_Http_Response $result
     * @param InvoiceInterface $invoice
     * @return bool
     * @throws \Exception
     */
    protected function processResponse(\Zend_Http_Response $result, InvoiceInterface $invoice): bool
    {
        $this->logger->info('Response body - ' . $result->getBody());
        try {
            $responseBody = $this->json->unserialize($result->getBody());
        } catch (\InvalidArgumentException $e) {
            $responseBody = [];
        }
        $validationResult = $this->resultValidator->validate($result, $responseBody);
        if ($validationResult->isValid()) {
            $invoice->addComment("Invoice transferred successfully (Reference #{$responseBody['result']})");
            $this->logger->info('Send invoice to punch2go after, invoice id - ' . $invoice->getIncrementId());
            return true;
        }
        foreach ($validationResult->getErrors() as $error) {
            $invoice->addComment($error);
        }
        return false;
    }
}
