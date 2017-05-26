<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/26
 * Time: 23:23
 */

namespace SubtleFramework\Config;


abstract class AbstractConfig
{
    protected $configBag = [];

    abstract protected function fulfilConfig();

    public function get()
    {
        return $this->configBag[ENV];
    }
}