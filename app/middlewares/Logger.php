<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;


class Logger implements MiddlewareInterface
{
    public $tipoLog;
    
    public function __construct( $tipoLog =""){
        $this->tipoLog = $tipoLog;
    }


    public function process(Request $request, RequestHandler $handler): Response
    {
        // Ejecutar la operación principal (controlador o siguiente middleware)
        $response = $handler->handle($request);
      
        if ($this->tipoLog == "Login")
        {
            Logger::LogLogin($request, $response, function($request, $response) {
            });
        } else {

            Logger::LogOperacion($request, $response, function($request, $response) {
            });
        }

        // Devolver la respuesta
        return $response;
    }

    public static function LogOperacion($request, $response, $next)
    {
        // Ejecutar la operación principal
        $retorno = $next($request, $response);
        
        // Registrar la operación en el log
        self::registrarEnLog($request, $response);

        // Devolver el resultado de la operación principal
        return $retorno;
    }

    public static function LogLogin($request, $response, $next)
    {
        // Ejecutar la operación principal
        $retorno = $next($request, $response);
        
        // Registrar la operación en el log
        self::registrarInicioSecion($request, $response);

        // Devolver el resultado de la operación principal
        return $retorno;
    }

    private static function registrarEnLog($request, $response)
    {

        
        // Datos para registrar en el log
        $fecha = date('Y-m-d H:i:s');
        $ipCliente = $_SERVER['REMOTE_ADDR']; // Obtener la dirección IP del cliente
        $metodo = $request->getMethod(); // Método HTTP (GET, POST, etc.)
        $ruta = $request->getUri()->getPath(); // Ruta de la solicitud
        $codigoRespuesta = $response->getStatusCode(); // Código de estado HTTP de la respuesta

        $responseBody = (string) $response->getBody();

        // Mensaje a registrar en el log
        $mensaje = "$fecha - IP: $ipCliente - Método: $metodo - Ruta: $ruta - Código: $codigoRespuesta" . PHP_EOL;
        $mensaje .= "Respuesta: $responseBody" . PHP_EOL;

        // Ruta del archivo de log (ajusta la ruta y nombre de archivo según tu estructura)
        $rutaArchivoLog = './logs/log_operaciones.log';

        // Escribir en el archivo de log (agregar al final del archivo)
        file_put_contents($rutaArchivoLog, $mensaje, FILE_APPEND);
    }

    private static function registrarInicioSecion($request, $response)
    {

       // Datos para registrar en el log
       $fecha = date('Y-m-d H:i:s');
       $ipCliente = $_SERVER['REMOTE_ADDR']; // Obtener la dirección IP del cliente
       $responseBody = $response->getBody();

       $arrayResponse = json_decode($responseBody, true);
       if (isset($arrayResponse['jwt'])){

           $credencial = AutentificadorJWT::ObtenerData($arrayResponse['jwt']);

           $logData = [
            'fecha' => $fecha,
            'ip'=> $ipCliente,
            'usuario' => $credencial->usuario, // Datos del token de autenticación
            ];

        } else {

            $logData = [
                'fecha' => $fecha,
                'ip'=> $ipCliente,
                'respuesta' => (string)$responseBody
            ];
        }
        
        // Convertir a JSON
        $logJson = json_encode($logData) . PHP_EOL;

        // Ruta del archivo de log (ajusta la ruta y nombre de archivo según tu estructura)
        $rutaArchivoLog = './logs/log_operaciones.json';

        // Verificar si el archivo existe, si no existe, crearlo
        if (!file_exists($rutaArchivoLog)) {
            // Crea el archivo vacío
            file_put_contents($rutaArchivoLog, '');
        }

        // Escribir en el archivo de log (agregar al final del archivo)
        file_put_contents($rutaArchivoLog, $logJson, FILE_APPEND);

    }
    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}