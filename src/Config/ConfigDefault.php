<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 10/06/2017
 * Time: 12:08
 */

namespace SubtleFramework\Config;

/**
 * Default implementation of ConfigInterface
 *
 * @package SubtleFramework\Config
 */
class ConfigDefault implements ConfigInterface
{
    protected $config;

    public function __construct(array $data = [])
    {
        $this->config = $data;
    }

    public function get()
    {
        return $this->config;
    }
}