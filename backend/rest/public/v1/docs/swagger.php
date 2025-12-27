<?php
ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: application/json');

require __DIR__ . '/../../../vendor/autoload.php';

/* 🔧 Base URLs */
define('LOCALSERVER', 'http://localhost/projects/votingsystem/backend/rest');
define('PRODSERVER', 'https://votingsystem-xxmci.ondigitalocean.app');

/* 🌍 Auto-detect server */
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    define('BASE_URL', LOCALSERVER);
} else {
    define('BASE_URL', PRODSERVER);
}

/* 📌 Swagger scan */
$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/doc_setup.php',
    __DIR__ . '/../../../routes'
]);

/* 🎯 Add server to generated spec */
$data = json_decode($openapi->toJson(), true);
$data['servers'] = [
    [
        "url" => BASE_URL,
        "description" => ($_SERVER['SERVER_NAME'] === 'localhost') ? "Local" : "Production"
    ]
];

echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>