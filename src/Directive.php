<?php

namespace Newbury\AlexaFramework;

use Newbury\AlexaFramework\Http\Request;

class Directive
{
    public static function sendDirective(Request $request, $message)
    {
        list($endPoint, $token, $requestId) = $request->getDirectiveParams();

        $ch = curl_init($endPoint.'/v1/directives');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.$token,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);

        $out = [
            "header" => [
                "requestId" => $requestId
            ],
            "directive" => [
                "type" => "VoicePlayer.Speak",
                "speech" => $message
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($out));

        curl_exec($ch);
    }
}
