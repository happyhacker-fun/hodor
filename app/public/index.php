<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 30/08/2017
 * Time: 22:01
 */

use Hodor\Middleware\AccessLog;
use Ramsey\Uuid\Uuid;
use Slim\App;
use Slim\Container;


define('APP_NAME', 'hodor');
define('LOG_DIR', '/data/log/apps/' . APP_NAME);
define('APP_ROOT', __DIR__ . '/..');
require __DIR__ . '/../../vendor/autoload.php';
define('REQUEST_ID', Uuid::uuid4());


$settings = [
    'determineRouteBeforeAppMiddleware' => true,
];
$container = new Container($settings);
$app = new App($container);

require __DIR__ . '/../src/routes.php';

$app->add(AccessLog::class)
    ->run();