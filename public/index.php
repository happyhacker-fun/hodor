<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 30/06/2017
 * Time: 21:35
 */

use Hodor\Controller\Hello;
use Hodor\Log;
use Hodor\Middleware\AccessLog;
use Monolog\Logger;
use Ramsey\Uuid\Uuid;
use Slim\App;
use Slim\Container;

require __DIR__ . '/../vendor/autoload.php';

if (!defined('ENV')) {
    define('ENV', 'dev');
}

if (!defined('APP_NAME')) {
    define('APP_NAME', 'subtleapp');
}

if (!defined('REQUEST_ID')) {
    define('REQUEST_ID', Uuid::uuid4());
}

if (!defined('APP_DIR')) {
    define('APP_DIR', __DIR__ . '/src');
}

if (!defined('LOG_DIR')) {
    define('LOG_DIR', '/data/log/apps/' . APP_NAME);
}

Log::setLevel(Logger::DEBUG);
Log::debug('hello, I am subtle php framework', ['foo' => 'bar']);

$container = new Container([]);

$app = new App($container);

$app->post('/hello', Hello::class . ':world');
$app->add(AccessLog::class);


$app->run();