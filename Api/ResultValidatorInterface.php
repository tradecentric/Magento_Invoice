<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Api;

use Magento\Framework\Validation\ValidationResult;

/**
 * Interface ResultHandlerInterface
 * @package TradeCentric\Invoice\Api
 */
interface ResultValidatorInterface
{
    /**
     * @param \Zend_Http_Response $response
     * @param array $body
     * @return ValidationResult
     */
    public function validate(\Zend_Http_Response $response, array $body): ValidationResult;
}
