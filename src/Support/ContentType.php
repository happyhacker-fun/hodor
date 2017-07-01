<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 18/06/2017
 * Time: 12:05
 */

namespace Hodor\Support;


use Slim\Http\Request;

class ContentType
{
    private static $readable = [
        'application/json',
        'application/xml',
    ];

    public static function isReadable($contentType)
    {
        return in_array($contentType, self::$readable);
    }

}