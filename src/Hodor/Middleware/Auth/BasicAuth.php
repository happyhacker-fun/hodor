<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 31/08/2017
 * Time: 14:07
 */

namespace Hodor\Middleware\Auth;


use Hodor\Log;
use Hodor\Middleware\BaseMiddleware;
use Hodor\Support\IP;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr7Middlewares\Utils\AttributeTrait;
use Slim\Route;

class BasicAuth extends BaseMiddleware
{
    /**
     * @var Route
     */
    private $route;
    use AttributeTrait;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $auth = $request->getHeaderLine('Authorization');

        if (!preg_match("/Basic\s+(.*)$/i", $auth, $matches)) {
            Log::info('auth info must be provided.');
            return $response->withStatus(401)
                ->withHeader('WWW-Authenticate', sprintf('Basic realm="%s"', 'Protected'));
        }

        list($ak, $sk) = explode(':', base64_decode($matches[1]), 2);

        $this->route = $request->getAttribute('route');

        if (!$this->check($ak, $sk)) {
            Log::info('auth info provided does not match', ['ak' => $ak, 'sk' => $sk]);
            return $response->withStatus(401)
                ->withHeader('WWW-Authenticate', sprintf('Basic realm="%s"', 'Protected'));
        }

        $request = self::setAttribute($request, 'username', $ak);
        $request = self::setAttribute($request, 'clientIp', IP::getClientIP());

        return $next($request, $response);
    }

    private function check($ak, $sk)
    {
        $authConfig = require APP_ROOT . '/src/Config/basic_auth.php';

        if (!array_key_exists($ak, $authConfig)) {
            return false;
        }

        if (!array_key_exists('pass', $authConfig[$ak])) {
            return false;
        }

        if (trim($authConfig[$ak]['pass']) !== trim($sk)) {
            return false;
        }

        if (!array_key_exists('routes', $authConfig[$ak])) {
            return true;
        }

        $name = $this->route->getName();

        return in_array($name, $authConfig[$ak]['routes'], true);
    }
}