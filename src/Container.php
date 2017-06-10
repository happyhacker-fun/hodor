<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 10/06/2017
 * Time: 12:12
 */

namespace SubtleFramework;


use SubtleFramework\Exception\KeyNotExistException;

/**
 * Global container for objects
 *
 * @package SubtleFramework
 */
class Container
{
    protected static $container;

    /**
     * Make instance of a class
     *
     * @param $className
     * @param array ...$args
     * @return object
     */
    public static function make($className, ...$args)
    {
        $index = md5($className . json_encode($args));
        if (!array_key_exists($index, self::$container) || self::$container[$index] === null) {
            self::$container[$index] = new $className($args);
        }

        return self::$container[$index];
    }


    /**
     * Put a callable to container with a key
     *
     * @param $key
     * @param callable $callable
     */
    public static function set($key, Callable $callable)
    {
        if (!array_key_exists($key, self::$container)) {
            self::$container[$key] = $callable;
        }
    }


    /**
     * Get callable from container by key
     *
     * @param $key
     * @return mixed
     * @throws KeyNotExistException
     */
    public static function get($key)
    {
        if (!array_key_exists($key, self::$container)) {
            throw new KeyNotExistException('Key ' . $key . ' is not set');
        }

        return self::$container[$key];
    }
}