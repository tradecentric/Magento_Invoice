<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Logger\Handlers;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger as MonologLogger;

/**
 * Class Debug
 * @package TradeCentric\Invoice\Logger\Handlers
 */
class Exception extends Base
{
    protected $fileName = 'var/log/punch2go-invoices/exception.log';
    protected $loggerType = MonologLogger::ERROR;

}
