<?php

namespace Newbury\AlexaFramework\Intent;

use Newbury\AlexaFramework\Http\Http;

abstract class BaseIntent
{
    protected $request;

    protected $response;

    const ALEXA_HTTP_OK = 200;

    const ALEXA_HTTP_ERROR_NOT_AUTHORISED = 401;

    public function __construct(
        Http $http
    ) {
        $this->request = $http->request;
        $this->response = $http->response;
    }

    abstract public function execute();
}