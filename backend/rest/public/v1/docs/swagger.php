<?php
ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: application/json');

require __DIR__ . '/../../../vendor/autoload.php';

define('LOCALSERVER', 'http://localhost/projects/votingsystem/backend/rest');
define('PRODSERVER', 'https://your-production-domain.com/backend/rest');

if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    define('BASE_URL', LOCALSERVER);
} else {
    define('BASE_URL', PRODSERVER);
}

$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/doc_setup.php',
    __DIR__ . '/../../../routes'
]);

echo $openapi->toJson();
?>