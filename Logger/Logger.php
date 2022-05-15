<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Logger;

use Psr\Log\LoggerInterface;
use TradeCentric\Invoice\Api\StoreLoggerInterface;
use TradeCentric\Invoice\Helper\Data;

class Logger implements StoreLoggerInterface
{
    /**
     * @var string
     */
    protected $storeId = null;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $helper,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * @param string $storeId
     * @return mixed|void
     */
    public function setStoreId(string $storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param $message
     * @param array $context
     * @return mixed|void
     */
    public function info($message, array $context = array())
    {
        if ($this->helper->isLogging($this->storeId)) {
            $this->logger->info($message, $context);
        }
    }

    /**
     * @param $message
     * @param array $context
     * @return mixed|void
     */
    public function error($message, array $context = array())
    {
        if ($this->helper->isLogging($this->storeId)) {
            $this->logger->error($message, $context);
        }
    }

}
