<?php
require_once './utils/AutentificadorJWT.php';


class AuthController  
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $perfil = $parametros['perfil'];
        $alias = $parametros['alias'];
    
        $datos = array('usuario' => $usuario, 'perfil' => $perfil, 'alias' => $alias);
    
        $token = AutentificadorJWT::CrearToken($datos);
        $payload = json_encode(array('jwt' => $token));
    
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
      });
      
        $parametros = $request->getParsedBody();

        $estado = $parametros['estado'];
        $codigoPedido = $parametros['codigoPedido'];
        $idEmpleadoMozo = $parametros['idEmpleadoMozo'];
        $fechaHoraIngresoMesa = $parametros['fechaHoraIngresoMesa'];

        // Creamos la mesa
        $mesa = new Mesa();
        $mesa->estado = $estado;
        $mesa->codigoPedido = $codigoPedido;
        $mesa->idEmpleadoMozo = $idEmpleadoMozo;
        $mesa->fechaHoraIngresoMesa = $fechaHoraIngresoMesa;
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}