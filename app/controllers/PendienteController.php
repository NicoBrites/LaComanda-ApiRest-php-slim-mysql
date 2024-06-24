<?php
require_once './models/Pendiente.php';
require_once './interfaces/IPendiente.php';

class PendienteController extends Pendiente implements IPendiente
{
  public function CambiarEstado($request, $response, $args)
  {

    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $credencial = AutentificadorJWT::ObtenerData($token);

    // Buscamos producto por nombre
    $parametros = $request->getParsedBody();
    $pendiente = $parametros['pendienteId'];
    $pendiente = Pendiente::cambiarEstadoPedido($pendiente, $credencial);
    if ($pendiente == null){

      $payload = json_encode(array('mensaje' => 'ERROR: No existe ese producto pendiente'));

    } elseif ($pendiente == -1 ) {

      $payload = json_encode(array('mensaje' => 'ERROR: Este usuario no estaba preparando el pedido'));

    } else if ($pendiente == -2 ) {

      $payload = json_encode(array('mensaje' => 'ERROR: Este producto pendiente no pertenece a tu sector'));

    } else {

      $payload = json_encode(array('mensaje' => 'Exito! Pendiente cambiado de estado'));

    }
    

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
