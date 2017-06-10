<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 10/06/2017
 * Time: 11:49
 */

namespace SubtleFramework\Config;


interface ConfigInterface
{
    public function __construct(array $data = []);

    public function get();
}