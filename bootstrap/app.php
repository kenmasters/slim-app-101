<?php

require '../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Middleware\JwtAuthentication as JwtAuth;
use Slim\Middleware\HttpBasicAuthentication as HttpBasicAuth;
use \Firebase\JWT\JWT;
use Jenssegers\Blade\Blade;

// date_default_timezone_set('America/Chicago'); // Set timezone
date_default_timezone_set('Asia/Manila'); // Set timezone


$config = require 'config.php';
$app = new Slim\App($config);
$container = $app->getContainer();

// DEPENDENCY INJECTION

$container['view'] = new Blade(
	__DIR__.'/resources/views',
	__DIR__.'/resources/cache'
);