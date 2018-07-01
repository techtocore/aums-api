<?php
require '../vendor/autoload.php';
require '../app/Exceptions.php';
require '../app/Encryption.php';
require '../app/Response.php';
require '../app/Client.php';
require '../app/API.php';

require '../oauth/db.class.php';
require '../oauth/server.php';

use Aums\CredentialsInvalidException;

$app = new \Slim\Slim();

$app->get('/', function () {
    $api = new \Aums\API("username", "password");
    print_r($api->login());
});

$app->get('/image/:filename', function ($filename) use ($app) {
    $app->response->headers->set('Content-Type', 'image/jpg');
    echo file_get_contents(__DIR__."/../storage/images/".$filename);
});

$app->post('/oauth/token', function() use ($server) {
    $response = $server->handleTokenRequest(OAuth2\Request::createFromGlobals());
    if($response->getStatusCode() == 200) {
        $code = $_POST['code'];

        $responseJson = json_decode($response->getResponseBody());

        $hash = DB::queryFirstField("SELECT hash FROM authorization_codes_map WHERE authorization_code = '$code'");
        $hash = \Aums\Encryption::encode(\Aums\Encryption::decode($hash,$code),$responseJson->access_token);

        DB::insert('access_tokens_map', array(
            'access_token' => $responseJson->access_token,
            'hash' => $hash
        ));

        $response->send();
    }
});

$app->get('/oauth/authorize', function() use ($app, $server) {
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();
    if (!$server->validateAuthorizeRequest($request, $response)) {
        $app->view()->setData(array('response' => json_decode($response->getResponseBody())));
        $app->render('error.php');
    } else {
        $appName = DB::queryFirstField("SELECT client_app_name FROM oauth_clients WHERE client_id = %s",$_GET['client_id']);
        $app->view()->setData(array('app_name' => $appName, "error" => (isset($_GET['auth_error'])&&$_GET['auth_error']=="incorrect")?true:false));
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
            $rollNo = $api->login(false)['roll_no'];
            $server->handleAuthorizeRequest($request, $response, true, $rollNo);
            $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);

            DB::insert('authorization_codes_map',array(
                'authorization_code' => $code,
                'hash' => \Aums\Encryption::encode($_POST['password'],$code)
            ));

            $app->response->redirect($response->getHttpHeader('Location'));
        } catch (CredentialsInvalidException $e) {
            $app->response->redirect($_SERVER['REQUEST_URI']."&auth_error=incorrect");
        }
    }
});


$app->post('/oauth/resource/basic', function() use ($server) {
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();
    $scopeRequired = 'basic';
    if (!$server->verifyResourceRequest($request, $response, $scopeRequired)) {
        $server->getResponse()->send();
        die;
    }

    $api = new \Aums\API("username", "password");
    $info = $api->login();

    echo json_encode(array('success' => true, 'data' => array(
        'roll_no'       => $info['roll_no'],
        'first_name'    => $info['first_name'],
        'last_name'     => $info['last_name'],
        'email'         => $info['email'],
        'image_filename'=> $info['image_filename']
    )));

});

$app->post('/oauth/resource/extra', function() use ($server) {
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();
    $scopeRequired = 'extras';
    if (!$server->verifyResourceRequest($request, $response, $scopeRequired)) {
        $server->getResponse()->send();
        die;
    }

    $api = new \Aums\API("username", "password");
    $info = $api->login();

    echo json_encode(array('success' => true, 'data' => array(
        'roll_no'       => $info['roll_no'],
        'first_name'    => $info['first_name'],
        'last_name'     => $info['last_name'],
        'email'         => $info['email'],
        'degree_program'=> $info['degree_program'],
        'branch'        => $info['branch'],
        'semester'      => $info['semester'],
        'image_filename'=> $info['image_filename']
    )));
});

$app->post('/oauth/resource/picture/:filename', function($filename) use ($app, $server) {
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();
    $scopeRequired = 'profile_pic';
    if (!$server->verifyResourceRequest($request, $response, $scopeRequired)) {
        $server->getResponse()->send();
        die;
    }
    $app->response->headers->set('Content-Type', 'image/jpg');
    echo file_get_contents(__DIR__."/../storage/images/".$filename);

});


$app->run();