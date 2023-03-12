<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Api;

/**
 * Interface StoreLoggerInterface
 * @package TradeCentric\PurchaseOrder\Api
 */
interface StoreLoggerInterface
{
    /**
     * @param string $storeId
     * @return mixed
     */
    public function setStoreId(string $storeId);

    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function info($message, array $context = array());

    /**
     * @param $message
     * @param array $context
     * @return mixed
     */
    public function error($message, array $context = array());
}

