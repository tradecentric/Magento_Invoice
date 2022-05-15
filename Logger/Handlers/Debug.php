<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Logger\Handlers;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger as MonologLogger;

/**
 * Class Debug
 * @package TradeCentric\Invoice\Logger\Handlers
 */
class Debug extends Base
{
    protected $fileName = 'var/log/punch2go-invoices/debug.log';
    protected $loggerType = MonologLogger::DEBUG;

}
