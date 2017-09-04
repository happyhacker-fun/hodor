<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 30/08/2017
 * Time: 22:00
 */

use App\Controller\HelloController;

$app->group('', function () {
    $this->get('/hello', HelloController::class . ':world')->setName('test');
    $this->get('/foo', HelloController::class . ':world')->setName('foo');
});