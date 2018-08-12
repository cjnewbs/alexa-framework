<?php
use Newbury\AlexaFramework\App;
try {
    define('BASE_PATH', __DIR__ . '/../');
    require_once '../vendor/autoload.php';
    App::run(App::APP_MODE_PRODUCTION);
} catch (Exception $e) {
    die($e->getMessage());
}
