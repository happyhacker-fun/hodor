<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 30/08/2017
 * Time: 21:55
 */

namespace App\Controller;


use Hodor\Controller\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;

class HelloController extends BaseController
{
    public function world(Request $request, Response $response)
    {
        return $response->withJson([
            'ab' => 'cd',
        ]);
    }
}