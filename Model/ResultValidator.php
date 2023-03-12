<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model;

use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use TradeCentric\Invoice\Api\ResultValidatorInterface;
use TradeCentric\Invoice\Api\RequestResultInterface;

/**
 * Class ResultValidator
 * @package TradeCentric\Invoice\Model
 */
class ResultValidator implements ResultValidatorInterface
{
    /**
     * @var ValidationResultFactory
     */
    protected $resultFactory;

    /**
     * ResultValidator constructor.
     * @param ValidationResultFactory $resultFactory
     */
    public function __construct(ValidationResultFactory $resultFactory)
    {
        $this->resultFactory = $resultFactory;
    }

    /**
     * @param RequestResultInterface $response
     * @return ValidationResult
     */
    public function validate(RequestResultInterface $response): ValidationResult
    {
        return $this->resultFactory->create(['errors' => $this->getError($response)]);
    }

    /**
     * @param RequestResultInterface $response
     * @return array|string[]
     */
    protected function getError(RequestResultInterface $response): array
    {
        if (!$response->isSuccessful()) {
            return ["Invoice was not transferred through TradeCentric. 103 (see logs for details)"];
        }
        if ($response->getHeader('content-type') !== "application/json") {
            return ['Invoice transfer failed, Content Type was not Application/JSON. See var/log/punch2go-invoices/debug.log for details.'];
        }
        $body = $response->getBody();
        if (isset($body['errors'])) {
            $errors = $body['errors']['message'];
            return ['Invoice transfer failed with error - ' . $errors];
        }
        if (!$body) {
            return ['Invoice transfer failed, response is invalid or empty'];
        }
        return [];
    }
}
