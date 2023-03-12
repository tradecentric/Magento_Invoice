<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model;

use TradeCentric\Invoice\Api\RequestResultInterface;
use Magento\Framework\Serialize\Serializer\Json;

class RequestResult implements RequestResultInterface
{
    /** @var \Zend_Http_Response  */
    private $response;
    
    /** @var Json  */
    private $json;

    /**
     * @param \Zend_Http_Response $response
     * @param Json $json
     */
    public function __construct(\Zend_Http_Response $response, Json $json) 
    {
        $this->response = $response;
        $this->json = $json;
    }

    /**
     * @param string $header
     * @return string
     */
    public function getHeader(string $header): string
    {
        if ($result = $this->response->getHeader($header)) {
            return $result;
        }
        return '';
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->response->isSuccessful();
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        try {
            $responseBody = $this->json->unserialize($this->response->getBody());
        } catch (\InvalidArgumentException $e) {
            $responseBody = [];
        }
        return $responseBody;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->response->getStatus();
    }
}