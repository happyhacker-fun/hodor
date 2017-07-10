<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 10/07/2017
 * Time: 23:30
 */

namespace Hodor\Support;


class Time
{
    public static function now()
    {
        return strtotime(date('Y-m-d H:i:s'));
    }

    public static function nowInSeconds()
    {
        return time();
    }

    public static function nowInMilliSeconds()
    {
        return microtime(true) * 1000;
    }
}