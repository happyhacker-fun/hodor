<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 28/05/2017
 * Time: 22:22
 */

namespace Hodor\HttpRequest\Middleware;


use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Hodor\Log;

/**
 * Class Retry
 * Retry middleware for guzzlehttp.
 *
 * @package SubtleFramework\HttpRequest\Middleware
 */
class Retry
{
    /**
     * Decide whether to retry or not.
     *
     * @param int $maxTimes
     * @return \Closure
     */
    public static function decider($maxTimes = 5)
    {
        return function ($retries, Request $request, Response $response = null, RequestException $exception = null) use ($maxTimes) {
            if ($retries >= $maxTimes) {
                return false;
            }

            if ($exception instanceof ConnectException) {
                Log::warning('Cannot connect to target ' . $request->getRequestTarget() . ' retrying for the ' . $retries . ' time');
                return true;
            }

            if ($response) {
                if ($response->getStatusCode() >= 500) {
                    Log::error('Server responses with status code ' . $response->getStatusCode() . ' retrying for the ' . $retries . ' time');
                    return true;
                }

                if ($response->getStatusCode() >= 400) {
                    Log::warning('Server responses with status code ' . $response->getStatusCode() . ' and this is a client error, won\'t retry');
                    return false;
                }

                if ($response->getStatusCode() >= 300) {
                    Log::notice('Server responses with status code ' . $response->getStatusCode() . ' and this is not error, won\'t retry');
                    return false;
                }

                if ($response->getStatusCode() >= 200) {
                    Log::info('Servers responses with status code ' . $response->getStatusCode() . ' and this is OK, won\'t retry');
                    return false;
                }
            }

            Log::info('Retrying for the ' . $retries . ' time');

            return true;
        };
    }

    /**
     * Time(ms) to delay before next retry.
     *
     * @param int $delay
     * @return \Closure
     */
    public static function delay($delay = 100)
    {
        return function ($numberOfRetries) use ($delay) {
            return $delay * $numberOfRetries;
        };
    }
}