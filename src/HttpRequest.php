<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 28/05/2017
 * Time: 22:20
 */

namespace SubtleFramework;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use SubtleFramework\HttpRequest\Middleware\Logger;
use SubtleFramework\HttpRequest\Middleware\Replacer;
use SubtleFramework\HttpRequest\Middleware\Retry;

abstract class HttpRequest
{
    use ErrorHandlerTrait;

    abstract protected function serviceConfig();

    abstract protected function apiConfig();

    public function call($apiName, array $config = [])
    {
        $serviceConfig = $this->serviceConfig();

        $serviceConfig['handler'] = $this->getClientHandlerStack($serviceConfig);
        $client = new Client($serviceConfig);

        $apiConfig = $this->apiConfig()[$apiName];
        Log::debug('api config for ' . $apiName, $apiConfig);
        Log::debug('client config', [$client->getConfig()]);
        $request = new Request($apiConfig['method'], $apiConfig['path']);
        Log::debug('request', [$request->getRequestTarget(), (string)$request->getUri()]);

        try {
            $response = $client->send($request);
            $httpCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $this->writeToErrorLog($e);
            $response = $e->getResponse();
            $httpCode = $response->getStatusCode();
        } catch (ServerException $e) {
            $this->writeToErrorLog($e);
            $response = $e->getResponse();
            $httpCode = $response->getStatusCode();
        } catch (ConnectException $e) {
            $this->writeToErrorLog($e);
            $response = null;
            $httpCode = 500;
        } catch (RequestException $e) {
            $this->writeToErrorLog($e);
            $response = $e->getResponse();
            $httpCode = 500;
        } catch (\Exception $e) {
            $this->writeToErrorLog($e);
            $response = null;
            $httpCode = 500;
        }

//        return \GuzzleHttp\json_decode($response->getBody(), true);
    }

    protected function getClientHandlerStack($options)
    {
        $retryMiddleware = Middleware::retry(Retry::decider($options['retries']), Retry::delay($options['delay']));
        $middlewares = [
            $retryMiddleware,
            Replacer::replaceHandler(),
            new Logger(),
        ];

        $handlerStack = HandlerStack::create();
        foreach ($middlewares as $middleware) {
            $handlerStack->push($middleware);
        }

        return $handlerStack;
    }
}
