<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 30/06/2017
 * Time: 21:40
 */

namespace Hodor\Controller;

use Hodor\IoC;
use Hodor\View\Responder;

/**
 * Class BaseController
 * @package Hodor
 */
class BaseController extends IoC
{
    use Responder;
}