<?php

namespace Newbury\AlexaFramework\Intent;

class SessionEnd extends BaseIntent
{
    public function execute()
    {
        $this->response->setErrorCode(self::ALEXA_HTTP_OK);
    }
}