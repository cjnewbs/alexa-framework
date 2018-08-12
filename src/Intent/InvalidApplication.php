<?php

namespace Newbury\AlexaFramework\Intent;

class InvalidApplication extends BaseIntent
{
    public function execute()
    {
        $this->response->setErrorCode(self::ALEXA_HTTP_ERROR_NOT_AUTHORISED);
    }
}