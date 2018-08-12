<?php

namespace Newbury\AlexaFramework\Intent;

class DefaultIntent extends BaseIntent
{
    public function execute()
    {
        $this->response->setCard(
            'No Route Configured', 'This skill has no route configured for this intent')
            ->setOutputSpeech('This skill has no route configured for this intent')
            ->setShouldEndSession(true)
            ->sendResponse();
    }
}