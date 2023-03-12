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
     * @param RequestResultInterface $result
     * @return ValidationResult
     */
    public function validate(RequestResultInterface $result): ValidationResult;
}
