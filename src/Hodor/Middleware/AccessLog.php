<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 18/06/2017
 * Time: 11:45
 */

namespace Hodor\Middleware;


use Hodor\Log;
use Hodor\Support\ContentType;
use Hodor\Support\IP;
use Hodor\Support\Timer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Record access log on receiving a request, log body only when it is readable and only log part of it
 *
 * @package Hodor\Middleware
 */
class AccessLog extends BaseMiddleware
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        Timer::start('request');
        $newResponse = $next($request, $response);

        $requestContext = $this->request($request);
        $responseContext = $this->response($newResponse);

        if ($newResponse->getStatusCode() < 500) {
            Log::info('Request Response', [
                'request' => $requestContext,
                'response' => $responseContext,
            ]);
        } else {
            Log::info('Request Response', [
                'request' => $requestContext,
                'response' => $responseContext,
            ]);
        }

        return $newResponse;
    }

    private function request(ServerRequestInterface $request)
    {
        $clientIP = IP::getClientIP();

        $requestContext = [
            'method' => $request->getMethod(),
            'target' => $request->getRequestTarget(),
            'ip' => $clientIP,
            'size' => $request->getBody()->getSize(),
            'headers' => $request->getHeaders(),
        ];
        $requestContentType = $request->getHeaderLine('Content-Type');

        if (ContentType::isReadable($requestContentType)) {
            $requestContext['body'] = json_decode($request->getBody(), true);
        }

        return $requestContext;
    }

    private function response(ResponseInterface $response)
    {
        $cost = Timer::stop('request');

        $responseContext = [
            'cost' => $cost,
            'code' => $response->getStatusCode(),
            'size' => $response->getBody()->getSize(),
            'headers' => $response->getHeaders(),
        ];

        $responseContentType = $response->getHeaderLine('Content-Type');
        if (ContentType::isReadable($responseContentType)) {
            $responseContext['body'] = json_decode($response->getBody(), true);
        }

        return $responseContext;
    }
}