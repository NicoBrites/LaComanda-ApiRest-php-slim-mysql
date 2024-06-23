<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

//use Psr\Http\Message\ResponseInterface as Response;
//use Psr\Http\Message\ServerRequestInterface as Request;

use Illuminate\Support\Facades\Auth;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/PendienteController.php';
require_once './controllers/AuthController.php';

require_once './middlewares/ValidadorPostMiddleware.php';
require_once './middlewares/AuthMiddleware.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new ValidadorPostMiddleware("usuario"));
  })->add(new AuthMiddleware("Socio"));
$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos')->add(new AuthMiddleware());
    $group->get('/{producto}', \ProductoController::class . ':TraerUno')->add(new AuthMiddleware());
    $group->post('[/]', \ProductoController::class . ':CargarUno')->add(new ValidadorPostMiddleware("producto"));
  });
$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos')->add(new AuthMiddleware());
    $group->get('/{mesa}', \MesaController::class . ':TraerUno')->add(new AuthMiddleware());
    $group->post('[/]', \MesaController::class . ':CargarUno')->add(new ValidadorPostMiddleware("mesa"));
  });
$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(new AuthMiddleware());
    $group->get('/{pedido}', \PedidoController::class . ':TraerUno')->add(new AuthMiddleware());
    $group->post('[/]', \PedidoController::class . ':CargarUno')->add(new ValidadorPostMiddleware("pedido"));
    $group->post('/{pedido}', \PedidoController::class . ':CargarProductos')->add(new ValidadorPostMiddleware("cargarProducto"));
  });
$app->group('/pendientes', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodosPedidosEstado')->add(new AuthMiddleware());
  $group->get('/{pendiente}', \PendienteController::class . ':CambiarEstado')->add(new AuthMiddleware());
});

// JWT en login
$app->group('/auth', function (RouteCollectorProxy $group) {
  $group->post('/login', \AuthController::class . ':Login');
});
$app->get('[/]', function ($request, $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
