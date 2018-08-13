<?php

namespace Newbury\AlexaFramework\Http;

class Request
{
    const REQUEST_TYPE_LAUNCH   = 'LaunchRequest';

    const REQUEST_TYPE_INTENT   = 'IntentRequest';

    const REQUEST_TYPE_END      = 'SessionEndedRequest';

    protected $headers;

    protected $rawRequestBody;

    protected $requestBody;

    protected $now;

    protected $certificate;

    public function __construct()
    {
        if (!function_exists('getallheaders')) {
            function getallheaders()
            {
                $headers = [];
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
                return $headers;
            }
        }
        $this->headers = getallheaders();
        $this->rawRequestBody = file_get_contents('php://input');
        $this->requestBody = json_decode($this->rawRequestBody, true);
        $this->now = time();
    }
    public function getAppId()
    {
        return $this->requestBody['session']['application']['applicationId'];
    }

    public function getRequestBody()
    {
        return $this->requestBody;
    }

    public function getRequestType()
    {
        return $this->requestBody['request']['type'];
    }

    public function getIntent()
    {
        return $this->requestBody['request']['intent']['name'];
    }

    public function getSlotValue($slot)
    {
        return $this->requestBody['request']['intent']['slots'][$slot]['value'];
    }

    public function getDirectiveParams()
    {
        return [
            $this->getApiEndpoint(),
            $this->getApiAccessToken(),
            $this->getRequestId()
        ];
    }

    public function getApiEndpoint()
    {
        return $this->requestBody['context']['System']['apiEndpoint'];
    }

    public function getApiAccessToken()
    {
        return $this->requestBody['context']['System']['apiAccessToken'];
    }

    public function getRequestId()
    {
        return $this->requestBody['request']['requestId'];
    }

    public function getSessionId()
    {
        return $this->requestBody['session']['sessionId'];
    }

    public function getLocale()
    {
        return $this->requestBody['request']['locale'];
    }

    /**
     * @return bool
     */
    public function isRequestAuthentic($appId)
    {
        if (!$this->isIntendedRecepient($appId)) {
            error_log('isRequestAuthentic() failed @ _isIntendedRecepient()');
            return false;
        }
        // Verify the request came from Amazon
        if (!$this->verifyCertificateChainURL()) {
            error_log('isRequestAuthentic() failed @ verifyCertificateChainURL()');
            return false;
        }
        if (!$this->verifyCertificate()) {
            error_log('isRequestAuthentic() failed @ verifyCertificate()');
            return false;
        }
        if (!$this->verifySignature()) {
            error_log('isRequestAuthentic() failed @ verifySignature()');
            return false;
        }
        if (!$this->verifyTimeDifference()) {
            error_log('isRequestAuthentic() failed @ verifyTimeDifference()');
            return false;
        }
        return true;
    }

    private function isIntendedRecepient($appId)
    {
        $requestedAppId = $this->requestBody['session']['application']['applicationId'];
        if ($appId == $requestedAppId) {
            return true;
        }
        return false;
    }

    private function verifyCertificateChainURL()
    {
        $sigCertChainUrl = $this->headers['Signaturecertchainurl'] ?? false;

        if ($sigCertChainUrl === false) {
            return false;
        }

        $URL = parse_url($sigCertChainUrl);

        if ($URL['scheme'] != 'https') {
            return false;
        }
        if ($URL['host'] != 's3.amazonaws.com') {
            return false;
        }
        if (isset($URL['path']) == true) {
            $splitPath = explode('/', $URL['path']);
            if ($splitPath[1] != 'echo.api') {
                return false;
            }
        }
        if (isset($URL['port']) == true) {
            if ($URL['port'] != 443) {
                return false;
            }
        }
        // Method had not returned yet so all tests have passed
        return true;
    }

    private function verifyCertificate()
    {
        $this->getCertificate();

        $certData = openssl_x509_parse($this->certificate);

        $certFromStamp = $certData['validFrom_time_t'];
        $certToStamp = $certData['validTo_time_t'];
        $certDomain = $certData['extensions']['subjectAltName'];

        if (strpos($certDomain, 'echo-api.amazon.com') === false) {
            return false;
        }
        if ($this->now < $certFromStamp) {
            return false;
        }
        if ($this->now > $certToStamp) {
            return false;
        }
        return true;
    }

    private function getCertificate()
    {
        $certPathHash = BASE_PATH.'/var/CertificateCache/'.md5($this->headers['Signaturecertchainurl']);

        if (file_exists($certPathHash)) {
            $this->certificate = file_get_contents($certPathHash);
        } else {
            $cert = file_get_contents($this->headers['Signaturecertchainurl']);
            file_put_contents($certPathHash, $cert);
            $this->certificate = $cert;
        }
    }

    private function verifySignature()
    {
        $signature = base64_decode($this->headers['Signature']);

        if (openssl_verify($this->rawRequestBody, $signature, $this->certificate) !== 1) {
            return false;
        }

        return true;
    }

    private function verifyTimeDifference()
    {

        $tReq = strtotime($this->requestBody['request']['timestamp']);
        $tMin = $tReq - 150;
        $tMax = $tReq + 150;

        if ($this->now > $tMin &&
            $this->now < $tMax) {

            return true;
        }
        return false;
    }

    public function getSessionAttributes()
    {
        return $this->requestBody['session']['attributes'];
    }

    public function getSessionAttribute($key)
    {
        return $this->requestBody['session']['attributes'][$key];
    }
}