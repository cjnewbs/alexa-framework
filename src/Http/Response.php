<?php

namespace Newbury\AlexaFramework\Http;

class Response
{
    protected $response = [];

    const IMAGE_TYPE_SMALL = 'smallImageUrl';

    const IMAGE_TYPE_LARGE = 'largeImageUrl';

    public function __construct()
    {
        $this->response['version'] = '1.0';
        return $this;
    }

    public function setOutputSpeech($text)
    {
        $this->response['response']['outputSpeech'] = [
            "type" => 'PlainText',
            "text" => $text
        ];
        return $this;
    }

    public function setCard($title, $content)
    {
        $this->response['response']['card'] = [
            "type" => "Simple",
            "title" => $title,
            "content" => $content
        ];
        return $this;
    }

    public function setImage($type = self::IMAGE_TYPE_SMALL, $url)
    {
        $this->response['response']['card']['type'] = 'Standard';
        $this->response['response']['card']['image'][$type] = $url;
        return $this;
    }

    public function setShouldEndSession($shouldEndSession)
    {
        $this->response['response']['shouldEndSession'] = $shouldEndSession;
        return $this;
    }

    public function sendResponse()
    {
        $response = json_encode(
            $this->response
        );
        header('Content-Type: application/json;charset=UTF-8');
        header('Content-Length: '.strlen($response));
        echo $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setErrorCode($code)
    {
        http_response_code($code);
    }

    public function setSessionAttribute($key, $value)
    {
        $this->response['sessionAttributes'][$key] = $value;
        return $this;
    }
}