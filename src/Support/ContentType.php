<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 18/06/2017
 * Time: 12:05
 */

namespace Hodor\Support;


class ContentType
{
    private static $readable = [
        'application/json',
        'application/xml',
    ];

    public static function isReadable($contentType)
    {
        $c = explode(';', $contentType)[0];
        return in_array($c, self::$readable);
    }

}