<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 28/05/2017
 * Time: 22:20
 */

namespace SubtleFramework;


use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use SubtleFramework\Exception\ApiNotSetException;
use SubtleFramework\Exception\BaseUriNotSetException;
use SubtleFramework\Exception\UriNotSetException;
use SubtleFramework\HttpRequest\Middleware\Header;
use SubtleFramework\HttpRequest\Middleware\Logger;
use SubtleFramework\HttpRequest\Middleware\Replacer;
use SubtleFramework\HttpRequest\Middleware\Retry;

abstract class HttpRequest
{
    use ErrorHandlerTrait;
    protected $retryOption = [
        'max' => 5,
        'delay' => 100,
    ];

    /**
     * Service config, e.g base_uri
     *
     * @var array
     */
    protected $serviceConfig = [];
    protected $apiListConfig = [];

    /**
     * Set service level config
     *
     * @return null
     */
    abstract protected function setServiceConfig();

    /**
     * Set api config list
     *
     * @return null
     */
    abstract protected function setApiConfig();

    /**
     * Call remote service
     *
     * @param $apiName
     * @param array $options
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     * @throws ApiNotSetException
     * @throws BaseUriNotSetException
     * @throws UriNotSetException
     */
    public function call($apiName, array $options = [])
    {
        $this->setServiceConfig();
        $this->setApiConfig();

        if (!isset($this->apiListConfig[$apiName])) {
            throw new ApiNotSetException('api ' . $apiName . ' is not set yet');
        }
        $apiConfig = $this->apiListConfig[$apiName];

        if (!isset($this->serviceConfig['base_uri'])) {
            throw new BaseUriNotSetException('base_uri must be set');
        }

        $handlerStack = $this->getClientHandlerStack($apiName);
        $client = new Client([
            'base_uri' => $this->serviceConfig['base_uri'],
            'handler' => $handlerStack,
        ]);

        $serviceHeaders = isset($this->serviceConfig['headers']) ? $this->serviceConfig['headers'] : [];
        $apiHeaders = isset($apiConfig['headers']) ? $apiConfig['headers'] : [];
        $headers = $apiHeaders + $serviceHeaders;

        if (!isset($apiConfig['uri'])) {
            throw new UriNotSetException('uri for ' . $apiName . ' of service is not set yet');
        }
        $uri = $apiConfig['uri'];

        $request = new Request($this->serviceConfig['method'], $uri, $headers);

        try {
            $response = $client->send($request, $options);
        } catch (ClientException $e) {
            $this->writeToErrorLog($e);
            $response = $e->getResponse();
        } catch (ServerException $e) {
            $this->writeToErrorLog($e);
            $response = $e->getResponse();
        } catch (ConnectException $e) {
            $this->writeToErrorLog($e);
            $response = null;
        } catch (RequestException $e) {
            $this->writeToErrorLog($e);
            $response = $e->getResponse();
        } catch (Exception $e) {
            $this->writeToErrorLog($e);
            $response = null;
        }

        return $response;
    }

    /**
     * Composer handler stack for request
     *
     * @param $apiName
     * @return HandlerStack
     */
    private function getClientHandlerStack($apiName)
    {
        $apiRetryOption = isset($this->apiListConfig[$apiName]['retry']) ? $this->apiListConfig[$apiName]['retry'] : [];
        $serviceRetryOption = isset($this->serviceConfig['retry']) ? $this->serviceConfig['retry'] : [];

        $retryOption = $apiRetryOption + $serviceRetryOption + $this->retryOption;

        $middlewares = [
            Middleware::retry(Retry::decider($retryOption['max']), Retry::delay($retryOption['delay'])),
            Replacer::replaceHandler(),
            Header::commonHeaderHandler(),
            new Logger(),
        ];

        $handlerStack = HandlerStack::create();
        foreach ($middlewares as $middleware) {
            $handlerStack->push($middleware);
        }

        return $handlerStack;
    }
}
