<?php

require_once './models/Encuesta.php';

class ClienteController  
{
    public function CargarEncuesta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
            
        $pedido = $args['pedido'];

        $validacion = Pedido::obtenerPedido($pedido);   

        if ($validacion != null) {
            
            if ($validacion->estado == "cerrado")
            {
                $puntajeMesa = $parametros['puntajeMesa'];
                $puntajeRestaurante = $parametros['puntajeRestaurante'];
                $puntajeMozo = $parametros['puntajeMozo'];
                $puntajeCocinero = $parametros['puntajeCocinero'];
                $textoExperiencia = $parametros['textoExperiencia'];
            
                // Creamos el usuario
                $enc = new Encuesta();
                $enc->puntajeMesa = $puntajeMesa;
                $enc->puntajeRestaurante = $puntajeRestaurante;
                $enc->puntajeMozo = $puntajeMozo;
                $enc->puntajeCocinero = $puntajeCocinero;
                $enc->textoExperiencia = $textoExperiencia;

                try {
            
                    $enc->crearEncuesta();
                    $payload = json_encode(array("mensaje" => "Encuesta cargada con exito"));
            
                } catch (UsuarioYaEnUsoException $e) {
            
                    $payload = json_encode(array("mensaje" => $e->getMessage()));
            
                }
            } else {

                $payload = json_encode(array("Error" => "Todavia no se habilito la encuesta"));

            }  

        } else {

            $payload = json_encode(array("Error" => "No existe el pedido al cual hacerle la encuesta"));

        }
        
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    

}
