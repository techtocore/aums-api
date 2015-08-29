<?php
require '../vendor/autoload.php';
require '../app/Exceptions.php';
require '../app/Encryption.php';
require '../app/Response.php';
require '../app/Client.php';
require '../app/API.php';

$app = new \Slim\Slim();

$app->get('/', function () {
    $api = new \Aums\API("username", "password");
    print_r($api->login());
});

$app->get('/image/:filename', function ($filename) use ($app) {
    $app->response()->header('Content-Type', 'content-type: image/jpg');
    echo file_get_contents(__DIR__."/../storage/images/".$filename);
});

$app->run();