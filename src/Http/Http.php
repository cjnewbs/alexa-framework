<?php

namespace Newbury\AlexaFramework\Http;

class Http
{
    protected $debug;
    public $request;
    public $response;

    public function __construct($debug)
    {
        $this->debug = $debug;
        $this->request = new \Newbury\AlexaFramework\Http\Request();
        $this->response = new \Newbury\AlexaFramework\Http\Response();
    }

    public function __destruct()
    {
        if ($this->debug) {
            $requestBody = $this->request->getRequestBody();
            $response = $this->response->getResponse();

            $sessionId = $this->request->getSessionId();

            $requestId = $this->request->getRequestId();

            $path = BASE_PATH . '/var/debug/' . $sessionId . '/' . $requestId;
            if (!file_exists($path)) {
                $success = mkdir($path, 0777, true);
            }
            is_null($requestBody) ?: file_put_contents($path . '/request', json_encode($requestBody));
            is_null($response) ?: file_put_contents($path . '/response', json_encode($response));
            is_null($response) ?: file_put_contents($path . '/rawResponse', ob_get_contents());
        }
    }
}