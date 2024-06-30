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
        } elseif ($this->tipoValidador == "modificarUsuario"){
            return $this->validarPutUsuario($request,  $handler);
        } elseif ($this->tipoValidador == "producto"){
            return $this->validarPostProducto($request,  $handler);
        } elseif ($this->tipoValidador == "mesa"){
            return $this->validarPostMesa($request,  $handler);
        } elseif ($this->tipoValidador == "pedido"){
            return $this->validarPostPedido($request,  $handler);
        } elseif ($this->tipoValidador == "cargarProducto"){
            return $this->validarPostCargarPedido($request,  $handler);
        } elseif ($this->tipoValidador == "cargarCsv"){
            return $this->validarPostCargarCsv($request,  $handler);
        } elseif ($this->tipoValidador == "encuesta"){
            return $this->validarPostEncuesta($request,  $handler);
        } elseif ($this->tipoValidador == "inputUsuario"){
            return $this->validarInputUsuario($request,  $handler);
        } elseif ($this->tipoValidador == "inputUsuarioDel"){
            return $this->validarInputUsuarioDel($request,  $handler);
        }
    }

    private function validarHorario($horario, $formato = 'H:i:s') {
        $dateTime = DateTime::createFromFormat($formato, $horario);
        return $dateTime && $dateTime->format($formato) === $horario;
    }

    private function validarEntero($cadena) {
        $numero = filter_var($cadena, FILTER_VALIDATE_INT);
        return $numero !== false && $numero > 0;
    }


    private function validarPostUsuario(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["usuario"], $params["clave"],$params["tipoUsuario"], $params["nombreSector"])){
                          
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
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function validarPostProducto(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["nombre"], $params["precio"],$params["tiempoPreparacion"],$params["sector"])){

          
            if (is_string($params["nombre"]) && $this->validarEntero($params["precio"]) && 
            $this->validarHorario($params["tiempoPreparacion"]) && is_string($params["sector"])) {
                
                $response = $handler->handle($request); 
            } else {
                
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));        
            }
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function validarPostMesa(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["estado"], $params["codigoPedido"],$params["usuarioEmpleadoMozo"], $params["fechaHoraIngresoMesa"])){
               
        
            if (is_string($params["estado"])) {

                $response = $handler->handle($request); 
            } else {

                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));

            }
        

        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function validarPostPedido(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["codigoMesa"],$params["nombreCliente"])){
               
        
            if (is_string($params["codigoMesa"]) &&
            is_string($params["nombreCliente"])) {

                $response = $handler->handle($request); 
            } else {

                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));

            }
        

        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function validarPostCargarPedido(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["idProducto"])){
               
           
            if ($this->validarEntero($params["idProducto"])) {

                $response = $handler->handle($request); 
            } else {

                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));

            }
        

        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }
   
    private function validarPostCargarCsv(Request $request, RequestHandler $handler) {
        $uploadedFiles = $request->getUploadedFiles();

        if (!isset($uploadedFiles['archivo_csv'])) {
            $response = new Response();
            $response->getBody()->write(json_encode(["error" => "Por favor sube un archivo CSV."]));
        } else {
            
            $archivoCsv = $uploadedFiles['archivo_csv'];

            if ($archivoCsv->getError() !== UPLOAD_ERR_OK) {
                $response = new Response();
                $response->getBody()->write(json_encode(["error" => "Error al subir el archivo."]));
            } else {

                $response = $handler->handle($request); 

            }
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function validarPutUsuario(Request $request, RequestHandler $handler) {
        $putdata = file_get_contents('php://input');
        $params = json_decode($putdata, true);      

        if(isset($params["usuario"],$params["clave"],$params["tipoUsuario"],$params["sector"])){
            
            $tipoUsuarios = ["Socio", "Mozo","Bartender","Cocinero","Cervecero"];
            $sectores = ["Mesas", "Barra", "Cocina", "Choepras"];
           
            if (is_string($params["idProducto"]) && is_string($params["clave"]) &&
                in_array($params["sector"],$sectores) && in_array($params["tipoUsuario"],$tipoUsuarios)) {

                $response = $handler->handle($request); 
            } else {

                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));

            }
        

        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function validarPostEncuesta(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["puntajeMesa"],$params["puntajeRestaurante"],$params["puntajeMozo"],$params["puntajeCocinero"],$params["textoExperiencia"],
        $params["mesa"],$params["pedido"])){
               
           
            if ($this->validarEntero($params["puntajeMesa"]) && $this->validarEntero($params["puntajeRestaurante"]) 
            && $this->validarEntero($params["puntajeMozo"]) && $this->validarEntero($params["puntajeCocinero"]) 
            && is_string($params["textoExperiencia"]) && is_string($params["mesa"]) && is_string($params["pedido"])) {

                if ($params["puntajeMesa"]< 11 && $params["puntajeRestaurante"]< 11 && $params["puntajeMozo"]< 11 &&
                $params["puntajeCocinero"]< 11 && strlen($params["textoExperiencia"])< 67) {

                    $response = $handler->handle($request); 

                } else {

                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "Te excediste del valor maximo")));
    
                }

            } else {

                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));

            }
        

        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function validarInputUsuario(Request $request, RequestHandler $handler) {
        $params = $request->getParsedBody();

        if(isset($params["usuario"])){
                          
            if (is_string($params["usuario"]) ) {

                $response = $handler->handle($request); 

            } else {

                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));
            }
        
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }
   
    private function validarInputUsuarioDel(Request $request, RequestHandler $handler) {

        $putdata = file_get_contents('php://input');
        $params = json_decode($putdata, true);

        if(isset($params["usuario"])){
                          
            if (is_string($params["usuario"]) ) {

                $response = $handler->handle($request); 

            } else {

                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Error en el tipo de dato ingresado")));
            }
        
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }
   
}