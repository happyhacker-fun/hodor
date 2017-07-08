<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 10/06/2017
 * Time: 12:12
 */

namespace Hodor;


use Hodor\Exception\KeyNotExistException;
use Psr\Container\ContainerInterface;

/**
 * Global container for objects
 *
 * @package SubtleFramework
 */
class IoC
{
    protected $ci;

    public function __construct(ContainerInterface $container)
    {
        $this->ci = $container;
    }

    /**
     * Make instance of a class in a lazy way
     *
     * @param $className
     * @param array ...$args
     * @return object
     */
    public function make($className, ...$args)
    {
        $index = md5($className . json_encode($args));
        if (!$this->ci->has($index) || $this->ci->get($index) === null) {
            $this->ci[$index] = new $className($args);
        }

        return $this->ci->get($index);
    }

    /**
     * Make instance of a class in an aggressive way
     *
     * @param $className
     * @param array $args
     * @return mixed
     */
    public function makeNew($className, ...$args)
    {
        return new $className($args);
    }

    /**
     * Put a callable to container with a key
     *
     * @param $key
     * @param callable $callable
     */
    public function set($key, Callable $callable)
    {
        if (!$this->ci->has($key)) {
            $this->ci[$key] = $callable();
        }
    }


    public function __get($key)
    {
        return $this->ci->get($key);
    }


    /**
     * Get callable from container by key
     *
     * @param $key
     * @return mixed
     * @throws KeyNotExistException
     */
    public function get($key)
    {
        if (!$this->ci->has($key)) {
            throw new KeyNotExistException('Key ' . $key . ' is not set');
        }

        return $this->ci->get($key);
    }
}
