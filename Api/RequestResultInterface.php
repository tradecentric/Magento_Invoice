<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Api;

interface RequestResultInterface
{
    /**
     * @return array
     */
    public function getBody(): array;

    /**
     * @param string $header
     * @return string
     */
    public function getHeader(string $header): string;

    /**
     * @return bool
     */
    public function isSuccessful(): bool; 
    
    /**
     * @return int
     */
    public function getStatus(): int;
}