<?php

// Authentication Endpoint
$app->get('/test/', function(Request $request, Response $response) use ($app) {
    // This will show our login form to let user login
    return $app->view->make($response, 'sample', ['name' => 'Ken']);
});

// Authentication Endpoint
$app->get('/login/', function(Request $request, Response $response) {
    // This will show our login form to let user login
    return $response->write("<pre><h1>Login Form</h1></pre>");
});