<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 28/05/2017
 * Time: 22:21
 */

namespace SubtleFramework\HttpRequest\Middleware;


use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SubtleFramework\Log;
use function GuzzleHttp\Promise\rejection_for;

class Logger
{
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            return $handler($request, $options)->then(
                $this->onSuccess($request),
                $this->onFailure($request)
            );
        };
    }

    protected function log(RequestInterface $request, ResponseInterface $response = null, $reason = null)
    {
        if ($reason instanceof RequestException) {
            $response = $reason->getResponse();
        }

        $level = $this->getLogLevel($response);
        $message = $this->getLogMessage($request, $response, $reason);
        $context = compact('request', 'response', 'reason');

        Log::log($level, $message, $context);
    }


    protected function getLogMessage(RequestInterface $request, ResponseInterface $response = null, $reason = null)
    {
        $formatter = new MessageFormatter(MessageFormatter::CLF);
        return $formatter->format($request, $response, $reason);
    }


    protected function getLogLevel(ResponseInterface $response = null)
    {
        if ($response === null) {
             return \Monolog\Logger::WARNING;
        }

        $statusCode = (int)$response->getStatusCode();
        if ($statusCode >= 500) {
            return \Monolog\Logger::ERROR;
        }

        if ($statusCode >= 400) {
            return \Monolog\Logger::WARNING;
        }

        if ($statusCode >= 300) {
            return \Monolog\Logger::NOTICE;
        }

        return \Monolog\Logger::INFO;
    }


    protected function onSuccess(RequestInterface $request)
    {
        return function($response) use ($request) {
            $this->log($request, $response);
            return $response;
        };
    }


    protected function onFailure(RequestInterface $request)
    {
        return function ($reason) use ($request) {
            $this->log($request, null, $reason);
            return rejection_for($reason);
        };
    }
}