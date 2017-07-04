<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 30/06/2017
 * Time: 21:50
 */

namespace Hodor\View;


use Hodor\Exception\InvalidErrorMessageException;
use Hodor\Support\Language;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Request request
 * @property Response response
 */
trait Responder
{
    protected $code = 'code';
    protected $message = 'message';
    protected $data = 'data';
    protected $requestId = 'request_id';


    /**
     * Append specific header to response
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function withHeader($name, $value)
    {
        $this->response = $this->response->withHeader($name, $value);

        return $this;
    }

    /**
     * Set specific HTTP status code for a response
     *
     * @param $status
     * @return $this
     */
    public function withStatus($status = 200)
    {
        $this->response = $this->response->withStatus($status);

        return $this;
    }


    /**
     *
     * @param $data
     * @param $code
     * @param array ...$params
     * @return Response
     */
    public function respond($data, $code = 10000, ...$params)
    {
        $message = $this->getMessage($code);

        $array = [];
        $array[$this->code] = $code;
        array_unshift($params, $message);
        $array[$this->message] = call_user_func_array('sprintf', $params);
        $array[$this->data] = $data;

//        $acceptedContentType = $this->request->getHeader('Accept')[0];

        return $this->renderJson($array);
    }

    public function abort($code, ...$params)
    {
        return $this->respond(null, $code, ...$params);
    }

    /**
     * @param $data
     * @return Response
     */
    protected function renderJson($data)
    {
        $data[$this->requestId] = REQUEST_ID;

        return $this->response->withJson($data);
    }


    //todo
    protected function renderXml($data)
    {

    }

    final public function getMessage($code)
    {
        $messages = $this->codes[$code];

        if (is_string($messages)) {
            return $messages;
        }

        if (is_array($messages)) {
            $al = Language::getAccepted($this->request);
            return $messages[$al];
        }

        throw new InvalidErrorMessageException('Message ' . json_encode($messages, JSON_UNESCAPED_UNICODE) . ' is invalid');
    }
}