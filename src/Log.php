<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/26
 * Time: 23:30
 */

namespace SubtleFramework;


use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * Class Log
 * @method static emergency($message, array $context = [])
 * @method static alert($message, array $context = [])
 * @method static critical($message, array $context = [])
 * @method static error($message, array $context = [])
 * @method static warning($message, array $context = [])
 * @method static notice($message, array $context = [])
 * @method static info($message, array $context = [])
 * @method static debug($message, array $context = [])
 * @method static log($level, $message, array $context = [])
 * @package SubtleFramework
 */
class Log
{
    private static $lineFormatter;
    private static $logger;
    private static $level = Logger::INFO;

    private static $defaultLineFormatter = '[%datetime%] [' . REQUEST_ID . "] %channel%.%level_name%: %message% %context% %extra%\n";

    public static function setLevel($level)
    {
        self::$level = $level;
    }

    public static function getLevel()
    {
        return self::$level;
    }

    public static function setLineFormatter($lineFormatter)
    {
        self::$lineFormatter = $lineFormatter;
    }

    public static function getLineFormatter()
    {
        if (null === self::$lineFormatter) {
            self::$lineFormatter = self::$defaultLineFormatter;
        }

        return self::$lineFormatter;
    }

    private static function setUpLogger()
    {
        if (null === self::$logger) {
            $logger = new Logger(APP_NAME);
            $handler = new RotatingFileHandler(LOG_DIR . '/app.log');
            $lineFormatter = new LineFormatter(
                self::getLineFormatter(),
                LineFormatter::SIMPLE_DATE,
                false,
                true
            );
            $handler->setFormatter($lineFormatter);
            $handler->setLevel(self::getLevel());
            $logger->pushHandler($handler);
            self::$logger = $logger;
        }
    }

    public static function __callStatic($name, $arguments)
    {
        self::setUpLogger();
        call_user_func_array([self::$logger, $name], $arguments);
    }
}