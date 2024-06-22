<?php
require_once './models/Pendiente.php';
require_once './interfaces/IPendiente.php';

class PendienteController extends Pendiente implements IPendiente
{
    public function CambiarEstado($request, $response, $args)
    {
         // Buscamos producto por nombre
         $pendiente = $args['pendiente'];
         $pendiente = Pendiente::cambiarEstadoPedido($pendiente);
         $payload = json_encode($pendiente);
 
         $response->getBody()->write($payload);
         return $response
           ->withHeader('Content-Type', 'application/json');
    }
}