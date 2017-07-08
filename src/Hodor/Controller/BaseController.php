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
 * This class is for user-defined controllers to extend to obtain responder to easily.
 *
 * @property Responder responder
 * @package Hodor
 */
class BaseController extends IoC
{
    use Responder;
}