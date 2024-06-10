<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware {

    public $tipoDeUsuario;
    public $tipoUsuarioPermitido;

    
    public function __construct($tipoDeUsuario="", $tipoUsuarioPermitido =""){
        $this->tipoDeUsuario = $tipoDeUsuario;
        $this->tipoUsuarioPermitido = $tipoUsuarioPermitido;
    }

    public function __invoke(Request $request, RequestHandler $handler) {
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
    }
}