<?php

//ini_set('display_errors',1);
ini_set('display_starup_error',1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();


use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;


$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER')
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USER'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
    'port'      => getenv('DB_PORT')
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();
// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

//Peticiones a nuestra pagina
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

//var_dump($request->getUri()->getPath());


$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();
$map->get('index', '/',[
        'controller'=> 'App\Controllers\IndexController',
        'action' =>'indexAction'
      ]);

$map->get('addJob', '/job/add',[
        'controller'=> 'App\Controllers\JobsController',
        'action' =>'getAddJobAction'
      ]);
$map->post('saveJob', '/job/add',[
          'controller'=> 'App\Controllers\JobsController',
          'action' =>'getAddJobAction'
        ]);

$map->get('addUser', '/users/add', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'getAddUser',
    'auth' => true

]);
$map->post('saveUser', '/PlatziPH/users/save', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'postSaveUser'
]);


$map->get('loginForm', '/login', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogin'
]);
$map->get('logout', '/logout', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogout'
]);


$map->post('auth', '/auth', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'postLogin'
]);

$map->get('admin', '/admin', [
    'controller' => 'App\Controllers\AdminController',
    'action' => 'getIndex',
    'auth' => true
]);


$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);




if (!$route) {
    echo 'No route';
} else {
    $handlerData = $route->handler;
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];


    $needsAuth = $handlerData['auth'] ?? false;

        $sessionUserId = $_SESSION['userId'] ?? null;
        if ($needsAuth && !$sessionUserId) {
            echo 'Protected route';
            die;
        }



    $controller = new $controllerName;
    $response = $controller->$actionName($request);

    foreach ($response->getHeaders() as $name => $values) {
      foreach($values as $value){
        header(sprintf('%s: %s',$name, $value),false);
      }
    }
    http_response_code($response->getStatusCode());
    echo $response->getBody();

}

 ?>
