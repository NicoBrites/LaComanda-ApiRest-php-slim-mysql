<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $estado = $parametros['estado'];
        $codigoPedido = $parametros['codigoPedido'];
        $usuarioEmpleadoMozo = $parametros['usuarioEmpleadoMozo'];
        $fechaHoraIngresoMesa = $parametros['fechaHoraIngresoMesa'];

        // Creamos la mesa
        $mesa = new Mesa();
        $mesa->estado = $estado;
        $mesa->codigoPedido = $codigoPedido;
        $mesa->usuarioEmpleadoMozo = $usuarioEmpleadoMozo;
        $mesa->fechaHoraIngresoMesa = $fechaHoraIngresoMesa;
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos producto por nombre
        $mesa = $args['mesa'];
        $producto = Mesa::obtenerMesa($mesa);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args) # FALTA
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CambiarEstado($request, $response, $args)
    {

        $mesa = $args['mesa'];

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $credencial = AutentificadorJWT::ObtenerData($token);

        try{
          $validador = Mesa::CambiarEstadoMesa($mesa, $credencial);

          if ($validador == -1){

            $payload = json_encode(array('mensaje' => 'ERROR: No existe esa mesa'));

          } else if ($validador == -2) {
      
            $payload = json_encode(array('mensaje' => 'ERROR! No sos el Mozo que abrio maneja esa mesa'));
      
          } else if ($validador == -3) {
      
            $payload = json_encode(array('mensaje' => 'ERROR! Solo el socio puede cerrar la mesa'));
      
          } else {
      
            $payload = json_encode(array('mensaje' => 'Exito! Mesa cerrada'));
      
          }
  
        } catch (Exception $e) {
          $payload = json_encode(array('Error' => $e->getMessage()));
        }
        
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}