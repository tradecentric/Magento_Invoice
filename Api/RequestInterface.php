<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Api;

/**
 * Interface RequestInterface
 * @package TradeCentric\Invoice\Api
 */
interface RequestInterface
{
    /**
     * @return array
     */
    public function getHeaders(): array;

    /**
     * @return array
     */
    public function getParams(): array;

    /**
     * @return array
     */
    public function getConfig(): array;

    /**
     * @return string
     */
    public function getUri(): string;

}
