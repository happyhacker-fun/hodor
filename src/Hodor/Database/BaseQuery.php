<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 29/06/2017
 * Time: 23:54
 */

namespace Hodor\Database;


use Hodor\IoC;
use Illuminate\Database\Connection;

abstract class BaseQuery extends IoC
{
    /**
     * @return Connection
     */
    abstract protected function getConnection();

    use QueryTrait;
}