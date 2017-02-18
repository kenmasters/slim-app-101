<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

// date_default_timezone_set('America/Chicago'); // Set timezone
date_default_timezone_set('Asia/Manila'); // Set timezone

spl_autoload_register(function ($classname) {
    require ("classes/" . $classname . ".php");
});

$config = require 'config.php';
$app = new Slim\App($config);
$container = $app->getContainer();


 /* DEPENDENCY INJECTION */
$container['logger'] = function($container) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

// This connection is using default PHP PDO
$container['DB'] = function ($container) {
    $db = $container['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, FALSE);
    return $pdo;
};

// This connection is using Slim/PDO package
$container['db'] = function ($container) {
    $db = $container['settings']['db'];
    $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
    $usr = $db['user'];
    $pwd = $db['pass'];
    $pdo = new \Slim\PDO\Database($dsn, $usr, $pwd);
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
    return $pdo;
};



/** 
==========================================
APPLICATION ROUTE/ENDPOINTS
==========================================
*/

// API Main ROUTES/ENDPOINTS
$app->group('/api', function() {

    $this->group('/v1', function() {

        $this->group('/users', function() {
            $this->get('/', function(Request $request, Response $response) {
                exit('User routes');
            });
            $this->get('/{id}/', function(Request $request, Response $response) {
                exit('User of id: ' . $request->getAttribute('id'));
            });
        });

        $this->group('/events', function() {
            $this->get('/', function(Request $request, Response $response) {
                exit('Events routes');
            });
            $this->get('/{id}/', function(Request $request, Response $response) {
                exit('Event of id: ' . $request->getAttribute('id'));
            });
        });

    });

});


$app->get('/hello/{name}/', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->get('/', function ($request, $response, $args) {
	$this->logger->addInfo("Welcome! May Slim be with you.");
    return $response->write("<h1><pre>Silence is a woman's best garment. (anonymous)</pre></h1>");
});


$app->get('/docs/', function(Request $request, Response $response) {
    echo '<h3>This docs is for testing Slim Framework API</h3>';
    echo "<pre>";   

    print_r ($request->getUri());
    echo '<br/>';

    print_r ($request->getMethod());
    echo '<br/>';

    print_r ($request->getUri()->getQuery());
    echo '<br/>';

    print_r ($request->getQueryParams());
    echo '<br/>';

    print_r ($request->getQueryParam('age', null));
    echo '<br/>';

    return '';
});

$app->get('/slim-pdo/', function (Request $request, Response $response) {
   
    $selectStatement = $this->DB->select()->from('tickets');

    $id = $request->getQueryParam('id');
    $name = $request->getQueryParam('name');
    

    if( $id ) {
        $selectStatement->where('id', '=', $id);
    }

    if( $name ) {
        $selectStatement->whereLike('name', "$name%");
    }

    $stmt = $selectStatement->execute();
    $data = $stmt->fetchAll();
    return $response->withJson($data);
});



$app->group('/tickets', function(){

    $this->get('/fdaczczc', function(Request $request, Response $response){
        $this->logger->addInfo("Ticket list");
        $mapper = new TicketMapper($this->db);
        $tickets = $mapper->index();
        return $response->withJson($tickets);
    });

    $this->get('/', function(Request $request, Response $response){
        $this->logger->addInfo("Ticket list");
        $mapper = new TicketMapper($this->db);
        $tickets = $mapper->index($request);
        return $response->withJson($tickets);
    });

    // Add a new ticket
    $this->post('/', function ($request, $response) {
       $input = $request->getParsedBody();
       echo $input['name'];
    });
    // - See more at: https://arjunphp.com/creating-restful-api-slim-framework/#sthash.Dyxd4GPx.dpuf

    $this->get('/{id}/', function(Request $request, Response $response){
        $this->logger->addInfo("Ticket Info");
        $mapper = new TicketMapper($this->db);
        $ticket = $mapper->show($request->getAttribute('id'));
        return $response->withJson($ticket);
    });

    // DELETE a todo with given id
    $this->delete('/{id}/', function ($request, $response, $args) {
        $sth = $this->db->prepare("DELETE FROM tickets WHERE id=:id");
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        $todos = $sth->rowCount();
        return $response->withJson($todos);
    });
    // - See more at: https://arjunphp.com/creating-restful-api-slim-framework/#sthash.Dyxd4GPx.dpuf
    // Update todo with given id
    $this->put('/{id}/', function ($request, $response, $args) {
        $input = $request->getParsedBody();
        $sql = "UPDATE tickets SET name=:name WHERE id=:id";
        $sth = $this->db->prepare($sql);
        $sth->bindParam("id", $args['id']);
        $sth->bindParam("name", $input['name']);
        $sth->execute();
        $input['id'] = $args['id'];
        return $response->withJson($input);
    });
    // - See more at: https://arjunphp.com/creating-restful-api-slim-framework/#sthash.Dyxd4GPx.dpuf
});




/* RUN THE APPLICATION */ 
$app->run();


