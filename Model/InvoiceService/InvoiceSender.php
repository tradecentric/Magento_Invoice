<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model\InvoiceService;

use TradeCentric\Invoice\Api\RequestInterface;
use Magento\Framework\HTTP\LaminasClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use TradeCentric\Invoice\Api\RequestResultInterfaceFactory;
use TradeCentric\Invoice\Api\InvoiceSenderInterface;
use TradeCentric\Invoice\Api\RequestResultInterface;
use GuzzleHttp\Client;

class InvoiceSender implements InvoiceSenderInterface
{
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
        Json $json,
    )
    {
        $this->clientFactory = $clientFactory;
        $this->resultFactory = $resultFactory;
        $this->json = $json;
    }

    /**
     * @param RequestInterface $request
     * @return RequestResultInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(RequestInterface $request): RequestResultInterface {
        $client = new Client();
        $response = $client->request('POST', $request->getUri(), [
            'headers' => $request->getHeaders(),
            'json' => $request->getParams(),
        ]);

        return $this->resultFactory->create(['response' => $response]);
    }
}
