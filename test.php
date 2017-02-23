<?php


require 'vendor/autoload.php';

// $server = new \League\OAuth2\Server\AuthorizationServer;
$client = new new \Slim\Middleware\HttpBasicAuthentication();