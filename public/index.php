<?php
require '../vendor/autoload.php';
require '../app/Exceptions.php';
require '../app/Encryption.php';
require '../app/Response.php';
require '../app/Client.php';
require '../app/API.php';

require '../oauth/server.php';

use Aums\CredentialsInvalidException;

$app = new \Slim\Slim();

$app->get('/', function () {
    $api = new \Aums\API("username", "password");
    print_r($api->login());
});

$app->get('/image/{filename}', function ($filename) use ($app) {
    $app->response->headers->set('Content-Type', 'image/jpg');
    echo file_get_contents(__DIR__."/../storage/images/".$filename);
});

$app->post('/oauth/token', function() use ($app, $server) {
    $app->response->headers->set('Content-Type', 'application/json');
    $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
});

$app->post('/oauth/resource', function() use ($app, $server) {
    if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
        $server->getResponse()->send();
        die;
    }
    echo json_encode(array('success' => true, 'message' => 'You accessed my APIs!'));
});

$app->get('/oauth/authorize', function() use ($app, $server) {
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();
    if (!$server->validateAuthorizeRequest($request, $response)) {
        $app->view()->setData(array('response' => json_decode($response->getResponseBody())));
        $app->render('error.php');
    } else {
        $app->view()->setData(array('app_name' => 'Amrita Facemash', "error" => (isset($_GET['auth_error'])&&$_GET['auth_error']=="incorrect")?true:false));
        $app->render('authorization_page.php');
    }
});

$app->post('/oauth/authorize', function() use ($app, $server) {
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();
    if (!$server->validateAuthorizeRequest($request, $response)) {
        $app->view()->setData(array('response' => json_decode($response->getResponseBody())));
        $app->render('error.php');
    } else {
        $api = new \Aums\API(trim($_POST['username']), trim($_POST['password']));

        try {
            $api->login(false);

            $server->handleAuthorizeRequest($request, $response, true);
            if (true) {
                //$code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
                $app->response->redirect($response->getHttpHeader('Location'));
            }
        } catch (CredentialsInvalidException $e) {
            $app->response->redirect($_SERVER['REQUEST_URI']."&auth_error=incorrect");
        }
    }
});


$app->run();