<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 28/05/2017
 * Time: 22:21
 */

namespace Hodor\HttpRequest\Middleware;


use GuzzleHttp\Promise\RejectedPromise;
use Hodor\Log;
use Hodor\Support\ContentType;
use Hodor\Support\Timer;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Log a http request to remote service
 *
 * @package Hodor\HttpRequest\Middleware
 */
class Logger
{
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $promise = $handler($request, $options);
            Timer::start('guzzle');

            if (get_class($promise) === RejectedPromise::class) {
                $cost = Timer::stop('guzzle');
                $req = self::logRequest($request);
                $log = array_merge(['cost#' . $cost], $req, ['httpCode#0', 'reasonPhrase#connectFail', 'response#']);
                $line = implode('#|', $log);
                Log::info($line);
            }

            return $promise->then(
                function (ResponseInterface $response) use ($request) {
                    $cost = Timer::stop('guzzle');
                    $req = self::logRequest($request);
                    $res = self::logResponse($response);
                    $log = array_merge(['cost#' . $cost], $req, $res);
                    $line = implode('#|', $log);
                    if ((int)$response->getStatusCode() >= 500) {
                        Log::error($line);
                    } else if ((int)$response->getStatusCode() >= 300) {
                        Log::warning($line);
                    } else {
                        Log::info($line);
                    }

                    return $response;
                }
            );
        };
    }

    private function logRequest(RequestInterface $r)
    {
        $arr = ['curl', '-X'];
        $arr[] = $r->getMethod();
        foreach ($r->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $arr[] = '-H';
                $arr[] = "'$name:$value'";
            }
        }

        $contentType = $r->getHeaderLine('Content-Type');
        if (ContentType::isReadable($contentType)) {
            $body = $this->prettyString($r->getBody());
            if ($body) {
                $arr[] = '-d';
                $arr[] = "'$body'";
            }
        }

        $uri = (string)$r->getUri();
        $arr[] = "'$uri'";

        $log = [
            'curl#' . implode(' ', $arr)
        ];
        return $log;
    }

    private function logResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $data = [
            'httpCode#' . $response->getStatusCode(),
            'reasonPhrase' . $response->getReasonPhrase(),
        ];

        if (ContentType::isReadable($contentType)) {
            $data[] = 'response#' . $this->prettyString($response->getBody());
        } else {
            $data[] = 'response#';
        }

        return $data;
    }

    private function prettyString(StreamInterface $body)
    {
        return json_encode(json_decode($body), JSON_UNESCAPED_UNICODE);
    }

}