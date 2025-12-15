<?php
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/../../../vendor/autoload.php';

$openapi = \OpenApi\Generator::scan([
    realpath(__DIR__ . '/doc_setup.php'),
    realpath(__DIR__ . '/../../../routes')
]);

header('Content-Type: application/json');
echo $openapi->toJson();
?>