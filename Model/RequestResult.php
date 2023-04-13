<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model;

use TradeCentric\Invoice\Api\RequestResultInterface;
use Magento\Framework\Serialize\Serializer\Json;
use GuzzleHttp\Psr7\Response;

class RequestResult implements RequestResultInterface
{
    /** @var \Zend_Http_Response  */
    private $response;

    /** @var Json  */
    private $json;

    private ?array $content = null;

    /**
     * @param $response
     * @param Json $json
     */
    public function __construct(Response $response, Json $json)
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
            return end($result);
        }
        return '';
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->response->getStatusCode() === 200;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        if (!$this->content) {
            try {
                $this->content = $this->json->unserialize($this->response->getBody()->getContents());
            } catch (\InvalidArgumentException $e) {
                $this->content = [];
            }
        }

        return $this->content;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->response->getStatusCode();
    }
}
