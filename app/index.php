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
require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/PendienteController.php';
require_once './controllers/AuthController.php';
require_once './controllers/CsvController.php';
require_once './controllers/ClienteController.php';
require_once './controllers/SocioController.php';

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
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new ValidadorPostMiddleware("usuario"))->add(new Logger());
    $group->put('/{usuario}', \UsuarioController::class . ':ModificarUno')->add(new ValidadorPostMiddleware("modificarUsuario"))->add(new Logger());
    $group->delete('[/]', \UsuarioController::class . ':BorrarUno')->add(new ValidadorPostMiddleware("inputUsuarioDel"))->add(new Logger());
  })->add(new AuthMiddleware("Socio"));

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{producto}', \ProductoController::class . ':TraerUno');
    $group->post('[/]', \ProductoController::class . ':CargarUno')->add(new ValidadorPostMiddleware("producto"))->add(new Logger());
    $group->put('/{producto}', \ProductoController::class . ':ModificarUno')->add(new ValidadorPostMiddleware("modificarProducto"))->add(new Logger());
    $group->delete('[/]', \ProductoController::class . ':BorrarUno')->add(new ValidadorPostMiddleware("inputProductoDel"))->add(new Logger());
  })->add(new AuthMiddleware("Socio"));

$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{mesa}', \MesaController::class . ':TraerUno');
    $group->post('[/]', \MesaController::class . ':CargarUno')->add(new ValidadorPostMiddleware("mesa"))->add(new Logger());
    $group->post('/{mesa}', \MesaController::class . ':CambiarEstado')->add(new Logger());
    $group->delete('[/]', \MesaController::class . ':BorrarUno')->add(new ValidadorPostMiddleware("inputMesaDel"))->add(new Logger());
  })->add(new AuthMiddleware());

$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->get('/{pedido}', \PedidoController::class . ':TraerUno');
    $group->post('[/]', \PedidoController::class . ':CargarUno')->add(new ValidadorPostMiddleware("pedido"))->add(new Logger());
    $group->post('/{pedido}', \PedidoController::class . ':CargarProductos')->add(new ValidadorPostMiddleware("cargarProducto"))->add(new Logger());
  })->add(new AuthMiddleware());

$app->group('/pendientes', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodosPedidosEstado');
  $group->post('[/]', \PendienteController::class . ':CambiarEstado')->add(new Logger());
})->add(new AuthMiddleware());

$app->group('/csv', function (RouteCollectorProxy $group) {
  $group->get('/download/{tabla}', \CsvController::class . ':Descargarcsv');
  $group->post('/{tabla}', \CsvController::class . ':CargarCsvUsuarios');
})->add(new AuthMiddleware("Socio"));

$app->group('/clientes', function (RouteCollectorProxy $group) {
  $group->post('/encuesta', \ClienteController::class . ':CargarEncuesta')->add(new ValidadorPostMiddleware("encuesta"));
  $group->post('/demora', \ClienteController::class . ':Demora')->add(new ValidadorPostMiddleware("demora"));
});

$app->group('/socios', function (RouteCollectorProxy $group) {
  $group->get('/mejoresComentarios', \SocioController::class . ':MejoresComentarios');
  $group->get('/mesaMasUsada', \SocioController::class . ':MesaMasUsada');
  $group->post('/suspenderUsuario', \SocioController::class . ':SuspenderUsuario')->add(new ValidadorPostMiddleware("inputUsuario"));
  $group->post('/historialDeLogueo', \SocioController::class . ':LogueoDeUsuariosEnPDF')->add(new ValidadorPostMiddleware("inputUsuario")); // PDF
})->add(new AuthMiddleware("Socio"));


// JWT en login
$app->group('/auth', function (RouteCollectorProxy $group) {
  $group->post('/login', \AuthController::class . ':Login')->add(new ValidadorPostMiddleware("auth"))->add(new Logger("Login"));
});
$app->get('[/]', function ($request, $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
