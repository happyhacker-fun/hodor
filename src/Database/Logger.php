<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 10/06/2017
 * Time: 11:56
 */

namespace SubtleFramework\Database;


use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Psr\Log\LoggerInterface;

/**
 * Logger for database connection
 *
 * @package SubtleFramework\Database
 */
class Logger
{    /**
     * @var LoggerInterface
     */
    private static $sqlLogger;

    public static function makeLogger()
    {
        if (null === self::$sqlLogger) {
            $handler = new RotatingFileHandler(LOG_DIR . '/sql.log');
            $logger = new \Monolog\Logger('sql');
            $formatterWithRequestId = new LineFormatter(
                "[%datetime%] [" . REQUEST_ID . "] %channel%.%level_name%: %message% %context% %extra%\n",
                LineFormatter::SIMPLE_DATE,
                false,
                true
            );
            $handler->setFormatter($formatterWithRequestId);
            $logger->pushHandler($handler);
            self::$sqlLogger = $logger;
        }

        return self::$sqlLogger;
    }

}