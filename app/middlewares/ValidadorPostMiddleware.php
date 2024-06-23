<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ValidadorPostMiddleware {

    public $tipoValidador;

    public function __construct($tipoValidador){
        $this->tipoValidador = $tipoValidador;
    }

    public function __invoke(Request $request, RequestHandler $handler) {
        if ($this->tipoValidador == "usuario"){
            return $this->validarPostUsuario($request,  $handler);
        } elseif ($this->tipoValidador == "producto"){
            return $this->validarPostProducto($request,  $handler);
        } elseif ($this->tipoValidador == "mesa"){
            return $this->validarPostMesa($request,  $handler);
        } elseif ($this->tipoValidador == "pedido"){
            return $this->validarPostPedido($request,  $handler);
        } elseif ($this->tipoValidador == "cargarProducto"){
            return $this->validarPostCargarPedido($request,  $handler);
        }
    }

    private function validarHorario($horario, $formato = 'H:i:s') {
        $dateTime = DateTime::createFromFormat($formato, $horario);
        return $dateTime && $dateTime->format($formato) === $horario;
    }

    private function validarEntero($cadena) {
        return filter_var($cadena, FILTER_VALIDATE_INT) !== false;
    }


    private function validarPostUsuario(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["usuario"], $params["clave"],$params["tipoUsuario"], $params["nombreSector"],$params["credenciales"])){
                          
            if (is_string($params["usuario"]) && is_string($params["clave"]) && 
                is_string($params["tipoUsuario"]) && is_string($params["nombreSector"])) {

                $response = $handler->handle($request); 
            } else {

                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));
            }
        
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response;
    }

    private function validarPostProducto(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["nombre"], $params["precio"],$params["tiempoPreparacion"],$params["credenciales"])){

            if ($params["credenciales"] != "supervisor"){ // ESTO SE CAMBIA POR JWT
                
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "No estas autorizado")));

            } else {

                if (is_string($params["nombre"]) && $this->validarEntero($params["precio"]) && 
                $this->validarHorario($params["tiempoPreparacion"])) {
                    
                    $response = $handler->handle($request); 
                } else {
                    
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));
                }
            }
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response;
    }

    private function validarPostMesa(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["estado"], $params["codigoPedido"],$params["idEmpleadoMozo"], $params["fechaHoraIngresoMesa"],$params["credenciales"])){
               
            if ($params["credenciales"] != "supervisor"){ // ESTO SE CAMBIA POR JWT

                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "No estas autorizado")));

            } else {
                if (is_string($params["estado"])) {

                    $response = $handler->handle($request); 
                } else {

                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));

                }
            }

        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response;
    }

    private function validarPostPedido(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["codigoMesa"], $params["idEmpleado"],$params["nombreCliente"],$params["credenciales"])){
               
            if ($params["credenciales"] != "supervisor"){ // ESTO SE CAMBIA POR JWT

                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "No estas autorizado")));

            } else {
                if (is_string($params["codigoMesa"]) && $this->validarEntero($params["idEmpleado"]) &&
                is_string($params["nombreCliente"])) {

                    $response = $handler->handle($request); 
                } else {

                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));

                }
            }

        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response;
    }

    private function validarPostCargarPedido(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["idProducto"], $params["credenciales"])){
               
            if ($params["credenciales"] != "supervisor"){ // ESTO SE CAMBIA POR JWT

                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "No estas autorizado")));

            } else {
                if ($this->validarEntero($params["idProducto"])) {

                    $response = $handler->handle($request); 
                } else {

                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));

                }
            }

        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response;
    }
   

   
}