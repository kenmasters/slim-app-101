<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Psr7Middlewares\Middleware\TrailingSlash;

require 'vendor/autoload.php';


date_default_timezone_set('Australia/Perth'); // Set timezone

spl_autoload_register(function ($classname) {
    require ("classes/" . $classname . ".php");
});

// $config['displayErrorDetails'] = true;
// $config['addContentLengthHeader'] = false;

// $config['db']['host']   = "192.185.81.207"; // Remote database conn
// $config['db']['dbname'] = "wowprobl_slim";
// $config['db']['user']   = "wowprobl_slim";
// $config['db']['pass']   = "du(&Ab2&Zmo1";

// wowprobl_slim du(&Ab2&Zmo1
$config = require 'config.php';
$app = new Slim\App($config);

$container = $app->getContainer();

$container['logger'] = function($container) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};


$container['db'] = function ($container) {
    $db = $container['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
// $app->add(new TrailingSlash(true)); 

// $app->add(function (Request $request, Response $response, callable $next) {
//     $uri = $request->getUri();
//     $path = $uri->getPath();
//     if ($path != '/' && substr($path, -1) == '/') {
//         // permanently redirect paths with a trailing slash
//         // to their non-trailing counterpart
//         $uri = $uri->withPath(substr($path, 0, -1));
        
//         if($request->getMethod() == 'GET') {
//             return $response->withRedirect((string)$uri, 301);
//         }
//         else {
//             return $next($request->withUri($uri), $response);
//         }
//     }

//     return $next($request, $response);
// });

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;

});

$app->get('/', function ($request, $response, $args) {
	$this->logger->addInfo("Something interesting happened");
    return $response->write("Silence is a woman's best garment. (anonymous)");
});

// $app->get('/tickets', function (Request $request, Response $response) {
//     $this->logger->addInfo("Ticket list");
//     $mapper = new TicketMapper($this->db);
//     $tickets = $mapper->getTickets();
//     $response->getBody()->write($tickets);
//     return $response;
// });


// Tickets
$app->group('/tickets', function(){

    $this->get('', function(Request $request, Response $response){
        $this->logger->addInfo("Ticket list");
        $mapper = new TicketMapper($this->db);
        $tickets = $mapper->index();
        $response->getBody()->write($tickets);
        return $response;
    });

    $this->get('/{id}', function(Request $request, Response $response){
        $this->logger->addInfo("Ticket Info");
        $mapper = new TicketMapper($this->db);
        $ticket = $mapper->show($request->getAttribute('id'));
        $response->getBody()->write($ticket);
        return $response;
    });

});



$app->run();


