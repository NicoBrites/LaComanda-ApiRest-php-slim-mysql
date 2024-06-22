<?php
require_once './utils/AutentificadorJWT.php';
require_once './models/Usuario.php';


class AuthController  
{
    public function Login($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];

        $lista = Usuario::obtenerTodos();

        $flag = 0;

        foreach ($lista as $value) {
            var_dump($usuario);
            var_dump($clave);
            var_dump($value->usuario);
            var_dump($value->clave);

            var_dump(password_verify($clave, $value->clave));

            if ($usuario == $value->usuario && password_verify($clave, $value->clave)){

                $flag++;
                var_dump($flag);

            }
        }
        
        if($flag == 1){ 

            $datos = array('usuario' => $usuario);
            $token = AutentificadorJWT::CrearToken($datos);
            $payload = json_encode(array('jwt' => $token));

        } else {

            $payload = json_encode(array('error' => 'Usuario o contraseña incorrectos'));
            
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

}