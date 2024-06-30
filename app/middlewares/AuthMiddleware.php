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
            
        } catch (Exception $e) {
        
            $response = new Response();
            $payload = json_encode(array('mensaje' => $e->getMessage()));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}