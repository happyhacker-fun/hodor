<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31
 * Time: 22:19
 */

namespace SubtleFramework;


class Gank extends HttpRequest
{

    protected function serviceConfig()
    {
        return [
            'base_uri' => 'http://gankk.io',
            'retries' => 2,
            'delay' => 100,
        ];
    }

    protected function apiConfig()
    {
        return [
            'show' => [
                'path' => '/api/data/Android/10/1',
                'method' => 'get',
                'retries' => 2,
                'delay' => 100,
            ],
        ];

    }
}