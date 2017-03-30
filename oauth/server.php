<?php

$database = 'aums_api';

$dsn      = 'mysql:dbname='.$database.';host=localhost';
$username = 'root';
$password = 'root';

DB::$user = $username;
DB::$password = $password;
DB::$dbName = $database;

// error reporting (this is a demo, after all!)
ini_set('display_errors',1);error_reporting(E_ALL);

// $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

// Pass a storage object or array of storage objects to the OAuth2 server class
$server = new OAuth2\Server($storage);

// Add the "Authorization Code" grant type (this is where the oauth magic happens)
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));

$grantType = new OAuth2\GrantType\RefreshToken($storage);

$server->addGrantType($grantType);

// configure available scopes
$defaultScope = 'basic';
$supportedScopes = array(
    'basic',
    'extras',
    'profile_pic'
);

$memory = new OAuth2\Storage\Memory(array(
    'default_scope' => $defaultScope,
    'supported_scopes' => $supportedScopes
));

$scopeUtil = new OAuth2\Scope($memory);

$server->setScopeUtil($scopeUtil);