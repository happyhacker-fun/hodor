<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 10/06/2017
 * Time: 12:08
 */

namespace Hodor\Config;

/**
 * Default implementation of ConfigInterface
 *
 * @package SubtleFramework\Config
 */
class ConfigDefault implements ConfigInterface
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function get()
    {
        return $this->config;
    }
}