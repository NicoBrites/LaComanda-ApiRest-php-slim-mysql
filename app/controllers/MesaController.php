<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController 
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
        $mesaCodigo = $args['mesa'];
        $mesa = Mesa::obtenerMesa($mesaCodigo);
        if ($mesa) {
          $payload = json_encode($mesa);
        } else {
          $payload = json_encode(array("mensaje" => "No se encontro la mesa"));
        }

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
    
    public function ModificarUno($request, $response, $args) # no se puede modificar la mesa
    {

    }

    public function BorrarUno($request, $response, $args)
    {
      $putdata = file_get_contents('php://input');
      $params = json_decode($putdata, true);
  
      $mesa = $params['mesa'];
      $validacion = Mesa::obtenerMesa($mesa);
      if ($validacion){
  
        Mesa::borrarMesa($mesa);
        $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));
  
      } else {
  
        $payload = json_encode(array("mensaje" => "No existe esa mesa"));
  
      }
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
      
            $payload = json_encode(array('mensaje' => 'ERROR: No sos el Mozo que abrio maneja esa mesa'));
      
          } else if ($validador == -3) {
      
            $payload = json_encode(array('mensaje' => 'ERROR: Solo el socio puede cerrar la mesa'));
      
          } else {
      
            $payload = json_encode(array('mensaje' => 'Estado de la mesa cambiado'));
      
          }
  
        } catch (Exception $e) {
          $payload = json_encode(array('error' => $e->getMessage()));
        }
        
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}