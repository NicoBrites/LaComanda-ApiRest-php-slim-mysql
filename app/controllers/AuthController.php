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
        $tipoUsuario = "";
        
        foreach ($lista as $value) {
            if ($usuario == $value->usuario && password_verify($clave, $value->clave)){
                $flag++;
                $tipoUsuario = $value->tipoUsuario;
                $sector = $value->sector;
                $suspendido = $value->estaSuspendido;
                $fechaBaja = $value->fechaBaja;
            }
        }
        
        if($flag == 1){ 

            if($suspendido || $fechaBaja != null)
            {

                $payload = json_encode(array('No Autorizado' => 'Estas suspendido o dado de baja, no podes iniciar sesion'));
            
            } else {

                $datos = array('usuario' => $usuario, 'tipoUsuario' => $tipoUsuario, 'sector' => $sector);
                $token = AutentificadorJWT::CrearToken($datos);
                $payload = json_encode(array('jwt' => $token));

            }

        } else {

            $payload = json_encode(array('error' => 'Usuario o contraseña incorrectos'));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');

    }

}