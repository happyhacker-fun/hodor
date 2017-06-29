<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 29/06/2017
 * Time: 23:22
 */

namespace Hodor\Support;


class Timer
{
    private static $now = [];

    public static function start($name)
    {
        if (!isset(self::$now[$name])) {
            self::$now[$name] = microtime(true);
        }
    }


    public static function stop($name)
    {
        $t = round((microtime(true) - self::$now[$name]) * 1000, 2);

        unset(self::$now[$name]);

        return $t;
    }
}