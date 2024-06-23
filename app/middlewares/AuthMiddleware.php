<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './exceptions/Exceptions.php';

class AuthMiddleware {
    public $tipoUsuarioPermitido;
    
    public function __construct( $tipoUsuarioPermitido =""){
        $this->tipoUsuarioPermitido = $tipoUsuarioPermitido;
    }

    /*public function __invoke(Request $request, RequestHandler $handler) {
        $params = $request->getQueryParams();
        
        if(isset($params["credenciales"])){
            $credenciales = $params["credenciales"];

            if($credenciales == "supervisor" || $this->tipoDeUsuario == $this->tipoUsuarioPermitido){
                $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "No sos " . $this->tipoUsuarioPermitido)));
            }
        } else {
            //Si no hay credenciales 
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "No hay credenciales")));
        }    
        return $response;
    }*/

    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        $header = $request->getHeaderLine('Authorization');

        if (empty($header)) {

            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: No esta autorizado'));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } 
        $token = trim(explode("Bearer", $header)[1]);

        try {
            AutentificadorJWT::VerificarToken($token);

            if ($this->tipoUsuarioPermitido != "")
            {
                $credencial = AutentificadorJWT::ObtenerData($token);
                if ($credencial->tipoUsuario != $this->tipoUsuarioPermitido)
                {
                    $response = new Response();
                    $payload = json_encode(array('mensaje' => 'ERROR: No estas autorizado'));
                    $response->getBody()->write($payload);
                }
            }

            $response = $handler->handle($request);
        } catch (UsuarioYaEnUsoException $e){

            $response = new Response();
            $payload = json_encode(array('mensaje' => $e->getMessage()));
            $response->getBody()->write($payload);

        } catch (Exception $e) {
        
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}