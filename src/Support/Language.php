<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 30/06/2017
 * Time: 21:56
 */

namespace Hodor\Support;


use Slim\Http\Request;

class Language
{
    const ZH = 'zh';
    const EN = 'en';

    private static $supported = [
        self::ZH,
        self::EN,
    ];

    public static function getAccepted(Request $request)
    {
        $al = $request->getHeader('Accept-Language')[0];
        if (!in_array($al, self::$supported)) {
            return self::ZH;
        }

        return $al;
    }
}