<?php

namespace Newbury\AlexaFramework;

class App
{
    const APP_MODE_DEVELOPER = 'DEVELOP';
    const APP_MODE_PRODUCTION = 'PRODUCTION';

    public static function run($mode)
    {
        /**
         * Load Config files
         */
        $skills = require_once BASE_PATH . 'config/skills.php';

        /**
         * Prepare for routing
         */
        $http = new \Newbury\AlexaFramework\Http\Http($mode);
        $appId = $http->request->getAppId();
        $type = $http->request->getRequestType();

        self::createCachePath();

        if ($mode === self::APP_MODE_PRODUCTION) {
            $proceed = $http->request->isRequestAuthentic($appId);
        } else {
            $proceed = true;
        }

        /**
         * Determine controller
         */
        if (isset($skills[$appId]) && $proceed) {
            switch ($type) {
                case 'LaunchRequest':
                    $controller = $skills[$appId]['routes']['LaunchRequest'] ??
                        \Newbury\AlexaFramework\Intent\DefaultIntent::class;
                    break;

                case 'IntentRequest':
                    $intent = $http->request->getIntent();
                    $controller = $skills[$appId]['routes'][$intent] ??
                        \Newbury\AlexaFramework\Intent\DefaultIntent::class;
                    break;

                case 'SessionEndedRequest':
                    $controller = $skills[$appId]['routes']['SessionEndedRequest'] ??
                        \Newbury\AlexaFramework\Intent\SessionEnd::class;
                    break;

                default:
                    $controller = \Newbury\AlexaFramework\Intent\DefaultIntent::class;
            }
        } else {
            $controller = \Newbury\AlexaFramework\Intent\InvalidApplication::class;
        }

        /**
         * Load and execute controller
         */
        /** @var $controller \Newbury\AlexaFramework\Intent\BaseIntent */
        $controller = new $controller(
            $http
        );
        $controller->execute();
    }

    private static function createCachePath()
    {
        $path = BASE_PATH . '/var/CertificateCache/';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}