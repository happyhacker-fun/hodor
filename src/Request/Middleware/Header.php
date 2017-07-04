<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 28/05/2017
 * Time: 22:22
 */

namespace Hodor\HttpRequest\Middleware;


use Psr\Http\Message\RequestInterface;

/**
 * Add specific header to request
 *
 * @package SubtleFramework\HttpRequest\Middleware
 */
class Header
{
    public function __invoke()
    {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                $request = $request->withHeader('X-Request-Id', REQUEST_ID);
                return $handler($request, $options);
            };
        };
    }
}