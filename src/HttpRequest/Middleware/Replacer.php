<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 28/05/2017
 * Time: 22:21
 */

namespace Hodor\HttpRequest\Middleware;


use Closure;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

/**
 * Class Replacer
 *
 * Replace attributes in uri before sending request.
 *
 * @package SubtleFramework\HttpRequest\Middleware
 */
class Replacer
{
    /**
     * Return handler for guzzle.
     *
     * @return Closure
     */
    public static function replaceHandler()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (isset($options['attributes'])) {
                    $attributes = $options['attributes'];
                    $uri = (string)$request->getUri();

                    $parsedUri = self::interpolate($uri, $attributes);

                    $request = $request->withUri(new Uri($parsedUri), true);
                }
                return $handler($request, $options);
            };
        };
    }

    /**
     * Translate fields with context.
     *
     * @param $message
     * @param array $context
     * @return string
     */
    private static function interpolate($message, array $context = [])
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr(urldecode($message), $replace);
    }
}