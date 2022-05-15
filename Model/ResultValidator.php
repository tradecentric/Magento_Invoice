<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model;

use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use TradeCentric\Invoice\Api\ResultValidatorInterface;

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
     * @param \Zend_Http_Response $response
     * @param InvoiceInterface $invoice
     */
    public function validate(\Zend_Http_Response $response, array $body): ValidationResult
    {
        return $this->resultFactory->create(['errors' => $this->getError($response, $body)]);
    }

    /**
     * @param \Zend_Http_Response $response
     * @param array $body
     * @return string
     */
    protected function getError(\Zend_Http_Response $response, array $body): array
    {
        if (!$response->isSuccessful()) {
            return ["Invoice was not transferred through TradeCentric. 103 (see logs for details)"];
        }
        if ($response->getHeader('content-type') !== "application/json") {
            return ['Invoice transfer failed, Content Type was not Application/JSON. See var/log/punch2go-invoices/debug.log for details.'];
        }
        if (!empty($body['errors'])) {
            $errors = $body['errors']['message'];
            return ['Invoice transfer failed with error - ' . $errors];
        }
        if (empty($body)) {
            return ['Invoice transfer failed, response is invalid or empty'];
        }
        return [];
    }
}
