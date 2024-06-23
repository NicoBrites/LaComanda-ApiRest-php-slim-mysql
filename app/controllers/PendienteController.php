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
    $pendiente = Pendiente::cambiarEstadoPedido($pendiente);
    if ($pendiente == null){

      $payload = json_encode(array('mensaje' => 'ERROR: No existe ese producto pendiente'));

    } else {

      $payload = json_encode(array('mensaje' => 'Exito! Pendiente cambiado de estado'));

    }
    

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
