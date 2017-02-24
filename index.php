<?php
require 'vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Middleware\JwtAuthentication as JwtAuth;
use \Slim\Middleware\HttpBasicAuthentication as HttpBasicAuth;
use \Firebase\JWT\JWT;

// date_default_timezone_set('America/Chicago'); // Set timezone
date_default_timezone_set('Asia/Manila'); // Set timezone

// spl_autoload_register(function ($classname) {
//     require ("classes/" . $classname . ".php");
// });

$config = require 'config.php';
$app = new Slim\App($config);
$container = $app->getContainer();

 /* DEPENDENCY INJECTION */

$container["jwt"] = function ($container) {
    return new StdClass;
};


$container['logger'] = function($container) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['view'] = function($container) {
    $view = new Jenssegers\Blade\Blade(
        __DIR__.'/resources/views',
        __DIR__.'/resources/cache'
    );

    return $view;
};

$container['params'] = function($container) {
    return [];
};

// REGISTER/BIND APPLICATION CONTROLLERS

// Home Controller
$container['HomeController'] = function($container) {
    return new App\Controllers\HomeController($container);
};

$container['RestController'] = function($container) use ($app) {
    return new App\Controllers\RestController($app);
};

// This connection is using default PHP PDO
// $container['DB'] = function ($container) {
//     $db = $container['settings']['db'];
//     $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//     $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, FALSE);
//     return $pdo;
// };

// This connection is using Slim/PDO package
// $container['db'] = function ($container) {
//     $db = $container['settings']['db'];
//     $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
//     $usr = $db['user'];
//     $pwd = $db['pass'];
//     $pdo = new \Slim\PDO\Database($dsn, $usr, $pwd);
//     $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
//     return $pdo;
// };



$app->add(new JwtAuth([
    // "algorithm" => ["HS256", "HS384"],
    "path" => "/api/v1/", // or ["/api", "/admin"] 
    // "passthrough" => ["/api/token/", "/admin/ping"],
    "secret" => "mysecretkey",
    "secure" => false,
    "callback" => function ($request, $response, $arguments) {
        // this callback is called when authentication succeeds
        $data = [];
        $data["test"] = 'Yahahaha';
        return $response->withJson($data);
    },
    "error" => function ($request, $response, $arguments) {
        // this callback is called when authentication fails
        $data = [];
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        $data["test"] = 'Nope';
        return $response->write(json_encode($data, JSON_UNESCAPED_SLASHES));
    }
]));

$app->add(new HttpBasicAuth([
    "path" => "/api/token/",
    "secure" => false,
    "users" => [
        "root" => "t00r",
    ],
    "callback" => function ($request, $response, $arguments) {
        $data = [];
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response->write(json_encode($data, JSON_UNESCAPED_SLASHES));

    },
    "error" => function ($request, $response, $arguments) {
        // this callback is called when authentication fails
        $data = [];
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response->write(json_encode($data, JSON_UNESCAPED_SLASHES));
    }
]));

// This will show our login form.
$app->get('/login/', function(Request $request, Response $response) {
    return $this->view->render('login');
});

// User endpoint bind to controller
$app->get('/home/', 'HomeController:index');
$app->any('/rest/', 'RestController');


// $app->add(function ($request, $response, $next) {
    
//     $response->getBody()->write('APPLICATION Middleware BEFORE - 1 <hr/>');
//         $response = $next($request, $response);
//         $response->getBody()->write('<hr/> APPLICATION Middleware AFTER - 1');

//         return $response;

    
// });

// $app->add(function ($request, $response, $next) {
//     $response->getBody()->write('APPLICATION Middleware BEFORE - 2 <hr/>');
//     $response = $next($request, $response);
//     $response->getBody()->write('<hr/> APPLICATION Middleware AFTER - 2');

//     return $response;
// });


// $now = new DateTime();
// $future = new DateTime("now +2 hours");
// $jti = Base62::encode(random_bytes(16));

// $secret = "your_secret_key";

// $payload = [
//     "jti" => $jti,
//     "iat" => $now->getTimeStamp(),
//     "nbf" => $future->getTimeStamp()
// ];

// $token = JWT::encode($payload, $secret, "HS256");

/** 
==========================================
APPLICATION ROUTE/ENDPOINTS
==========================================
*/

// Authentication Endpoint
// $app->get('/login/', function(Request $request, Response $response) {
//     // This will show our login form to let user login
//     return $response->write("<pre><h1>Login Form</h1></pre>");
// });

$app->post('/authenticate/', function(Request $request, Response $response) {
    // process passed information
    // generate token for current user to have access over protected endpoints
    // returns json with token and current user data

    // for testing lets write to the browser.
    return $response->write("<pre><h1>Successfully logged in.</h1></pre>");
});


// Logout current user
$app->post('/logout/', function(Request $request, Response $response) {
    return $response->write("<pre><h1>Successfully logged out.</h1></pre>");
});


// API Main ROUTES/ENDPOINTS
$app->group('/api', function() use ($app) {


    // AUTHENTICATION / ENTRY POINT
    $app->post("/token/", function (Request $request, Response $response) {
      /* Here generate and return JWT to the client. */

        // $now = new DateTime();
        // $future = new DateTime("now +2 hours");
        // $server = $request->getServerParams();

        // $payload = [
        //     "iat" => $now->getTimeStamp(),
        //     "exp" => $future->getTimeStamp(),
        //     "sub" => $server["PHP_AUTH_USER"],
        // ];

        // $secret = "mysecretkey";
        // $token = JWT::encode($payload, $secret, "HS256");
        // $data["status"] = "ok";
        // $data["token"] = $token;


        $key = "mysecretkey";
        $token = array(
            "iss" => "http://slimapp101.dev/",
            "aud" => "http://slimapp101.dev/",
            "iat" => 1356999524,
            "nbf" => 1357000000
        );

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($token, $key);
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $data = [];
        $data['jwt'] = $jwt;
        $data['decoded'] = $decoded;

          return $response->withStatus(201)
              ->withHeader("Content-Type", "application/json")
              ->withJson($data);
    });


    // PROTECED RESOURES
    $app->group('/v1', function() use ($app) {

        $app->group('/users', function() use ($app) {
            $app->get('/', function(Request $request, Response $response) {
                exit('User routes');
            });
            $app->get('/{id}/', function(Request $request, Response $response) {
                exit('User of id: ' . $request->getAttribute('id'));
            });
            $app->delete("/{id}", function ($request, $response, $arguments) {
                if (in_array("delete", $this->jwt->scope)) {
                    /* Code for deleting item */
                    return $response->withStatus(200);
                } else {
                    /* No scope so respond with 401 Unauthorized */
                    return $response->withStatus(401);
                }
            });
        });

        $app->group('/events', function() use ($app) {
            $app->get('/', function(Request $request, Response $response) {
                exit('Events routes');
            });
            $app->get('/{id}/', function(Request $request, Response $response) {
                exit('Event of id: ' . $request->getAttribute('id'));
            });
        });

    });

});




// $app->group('/auth', function() { 

//             $this->post('/', function(Request $request, Response $response) {
//                 // Check and validate token

//                 print_r( $decoded = $request->getAttribute("token"));

//                 // if not validated return unauthorized

//                 // token is validated

//                 // process request

//                 // return response
//                 exit('Protected routes');
//             });
            

    

// });


// $app->get('/hello/{name}/', function (Request $request, Response $response) {
//     $name = $request->getAttribute('name');
//     $response->getBody()->write("Hello, $name");
//     return $response;
// });

$app->get('/', function ($request, $response, $args) {
	$this->logger->addInfo("Welcome! May Slim be with you.");
    return $response->write("<h1><pre>Silence is a woman's best garment. (anonymous)</pre></h1>");
});


// $app->get('/docs/', function(Request $request, Response $response) {
//     echo '<h3>This docs is for testing Slim Framework API</h3>';
//     echo $request->getAttribute('foo');
//     echo "<pre>";   

//     print_r ($request->getHeaders()); // ALL HEADERS
//     echo '<br/>';

//     print ('Host: ' . $request->getHeader('Host')[0]); // GET 1 HEADER
//     echo '<br/><br/>';

//     print_r ($request->getUri());
//     echo '<br/>';

//     print_r ($request->getMethod());
//     echo '<br/>';

//     print_r ($request->getUri()->getQuery());
//     echo '<br/>';

//     print_r ($request->getQueryParams());
//     echo '<br/>';

//     print_r ($request->getQueryParam('age', null));
//     echo '<br/>';

//     echo "</pre>";

//     return '';
// })->add(function ($request, $response, $next) {
//     $response->getBody()->write('ROUTE SPECIFIC Middleware BEFORE <hr/>');
//     $request = $request->withAttribute('foo', 'bar');
//     $response = $next($request, $response);
//     $response->getBody()->write('<hr/> ROUTE SPECIFIC Middleware AFTER');

//     return $response;
// });

// $app->get('/slim-pdo/', function (Request $request, Response $response) {
   
//     $selectStatement = $this->DB->select()->from('tickets');

//     $id = $request->getQueryParam('id');
//     $name = $request->getQueryParam('name');
    

//     if( $id ) {
//         $selectStatement->where('id', '=', $id);
//     }

//     if( $name ) {
//         $selectStatement->whereLike('name', "$name%");
//     }

//     $stmt = $selectStatement->execute();
//     $data = $stmt->fetchAll();
//     return $response->withJson($data);
// });


// $app->group('/utils', function () use ($app) {

//     $app->get('/', function ($request, $response) {
//         return $response->getBody()->write(date('Y-m-d H:i:s'));
//     });

//     $app->get('/date', function ($request, $response) {
//         return $response->getBody()->write(date('Y-m-d H:i:s'));
//     });
//     $app->get('/time', function ($request, $response) {
//         return $response->getBody()->write(time());
//     });
// })->add(function ($request, $response, $next) {
//     $response->getBody()->write('It is now ');
//     $response = $next($request, $response);
//     $response->getBody()->write('. Enjoy!');

//     return $response;
// });


// $app->group('/tickets', function() use ($app) {

//     $app->get('/', function(Request $request, Response $response){
//         $this->logger->addInfo("Ticket list");
//         $mapper = new TicketMapper($this->db);
//         $tickets = $mapper->index($request);
//         return $response->withJson($tickets);
//     });

//     $app->get('/testroute/', function(Request $request, Response $response){
//         return $response->getBody()->write('Enjoy!');
//     });

//     // Add a new ticket
//     $app->post('/', function ($request, $response) {
//        $input = $request->getParsedBody();
//        echo $input['name'];
//     });
//     // - See more at: https://arjunphp.com/creating-restful-api-slim-framework/#sthash.Dyxd4GPx.dpuf

//     $app->get('/{id}/', function(Request $request, Response $response){
//         $this->logger->addInfo("Ticket Info");
//         $mapper = new TicketMapper($this->db);
//         $ticket = $mapper->show($request->getAttribute('id'));
//         return $response->withJson($ticket);
//     });

//     // DELETE a todo with given id
//     $app->delete('/{id}/', function ($request, $response, $args) {
//         $sth = $this->db->prepare("DELETE FROM tickets WHERE id=:id");
//         $sth->bindParam("id", $args['id']);
//         $sth->execute();
//         $todos = $sth->rowCount();
//         return $response->withJson($todos);
//     });
//     // - See more at: https://arjunphp.com/creating-restful-api-slim-framework/#sthash.Dyxd4GPx.dpuf
//     // Update todo with given id
//     $app->put('/{id}/', function ($request, $response, $args) {
//         $input = $request->getParsedBody();
//         $sql = "UPDATE tickets SET name=:name WHERE id=:id";
//         $sth = $this->db->prepare($sql);
//         $sth->bindParam("id", $args['id']);
//         $sth->bindParam("name", $input['name']);
//         $sth->execute();
//         $input['id'] = $args['id'];
//         return $response->withJson($input);
//     });
//     // - See more at: https://arjunphp.com/creating-restful-api-slim-framework/#sthash.Dyxd4GPx.dpuf
// });




/* RUN THE APPLICATION */ 
$app->run();


