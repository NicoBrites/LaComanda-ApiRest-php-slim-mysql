<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require_once './utils/ArchivosJson.php';

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
        self::RegistrarOperaciones($request, $response);

        // Devolver el resultado de la operación principal
        return $retorno;
    }

    public static function LogLogin($request, $response, $next)
    {
        // Ejecutar la operación principal
        $retorno = $next($request, $response);
        
        // Registrar la operación en el log
        self::RegistrarInicioSecion($request, $response);

        // Devolver el resultado de la operación principal
        return $retorno;
    }

    private static function RegistrarOperaciones($request, $response)
    {
        $logData = ArchivosJson::LeerJson("./logs/log_operaciones.json");  

        $responseBody = $response->getBody();

        $arrayResponse = json_decode($responseBody, true);

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $credencial = AutentificadorJWT::ObtenerData($token);

        // Datos para registrar en el log
        $fecha = date('Y-m-d H:i:s');
        $ipCliente = $_SERVER['REMOTE_ADDR']; // Obtener la dirección IP del cliente
        $metodo = $request->getMethod(); // Método HTTP (GET, POST, etc.)
        $ruta = $request->getUri()->getPath(); // Ruta de la solicitud
        $codigoRespuesta = $response->getStatusCode(); // Código de estado HTTP de la respuesta

        $request =  (string )$request->getBody();
        $arrayRequest = json_encode($request, true);

        if (isset($arrayResponse['error'])){ 

            $logData = ArchivosJson::LeerJson("./logs/log_errores.json");  

            $logData[] = [
                'fecha' => $fecha,
                'ip'=> $ipCliente,
                'metodo' => $metodo, 
                'ruta' => $ruta,
                'codigo' => $codigoRespuesta,
                'usuario' => $credencial->usuario,
                'respuesta' => $arrayResponse['error'],
                'parametros' => $request
            ];

            ArchivosJson::GuardarJson("./logs/log_errores.json",$logData);
            

        } else {

            $logData[] = [
                'fecha' => $fecha,
                'ip'=> $ipCliente,
                'metodo' => $metodo, 
                'ruta' => $ruta,
                'codigo' => $codigoRespuesta,
                'usuario' => $credencial->usuario,
                'respuesta' => $arrayResponse['mensaje'],
                'parametros' => $arrayRequest
            ];

            ArchivosJson::GuardarJson("./logs/log_operaciones.json",$logData);

        }

        
    }

    private static function RegistrarInicioSecion($request, $response)
    {
        
        $logData = ArchivosJson::LeerJson("./logs/log_sesion.json");

        // Datos para registrar en el log
        $fecha = date('Y-m-d H:i:s');
        $ipCliente = $_SERVER['REMOTE_ADDR']; // Obtener la dirección IP del cliente
        $responseBody = $response->getBody();

        $arrayResponse = json_decode($responseBody, true);
        if (isset($arrayResponse['jwt'])){

            $credencial = AutentificadorJWT::ObtenerData($arrayResponse['jwt']);

           $logData[] = [
            'fecha' => $fecha,
            'ip'=> $ipCliente,
            'usuario' => $credencial->usuario, // Datos del token de autenticación
            ];

        } else {

            $logData[] = [
                'fecha' => $fecha,
                'ip'=> $ipCliente,
                'usuario' => (string)$responseBody
            ];
        }
        
        ArchivosJson::GuardarJson("./logs/log_sesion.json",$logData);

    }
}