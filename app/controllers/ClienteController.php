<?php

require_once './models/Encuesta.php';

class ClienteController  
{
    public function CargarEncuesta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
            
        $pedido = $parametros['pedido'];
        $mesa = $parametros['mesa'];

        $validacionPedido = Pedido::obtenerPedido($pedido); 
        $validacionMesa = Mesa::obtenerMesa($mesa);

        if ($validacionPedido != null && $validacionMesa != null) {
            
            if ($validacionMesa->codigoPedido == $pedido){

                if ($validacionPedido->estado == "cerrado")
                {
                    $puntajeMesa = $parametros['puntajeMesa'];
                    $puntajeRestaurante = $parametros['puntajeRestaurante'];
                    $puntajeMozo = $parametros['puntajeMozo'];
                    $puntajeCocinero = $parametros['puntajeCocinero'];
                    $textoExperiencia = $parametros['textoExperiencia'];
                
                    // Creamos el usuario
                    $enc = new Encuesta();
                    $enc->codigoPedido = $pedido;
                    $enc->puntajeMesa = $puntajeMesa;
                    $enc->puntajeRestaurante = $puntajeRestaurante;
                    $enc->puntajeMozo = $puntajeMozo;
                    $enc->puntajeCocinero = $puntajeCocinero;
                    $enc->textoExperiencia = $textoExperiencia;

                    try {
                
                        $enc->crearEncuesta();
                        $payload = json_encode(array("mensaje" => "Encuesta cargada con exito"));
                
                    } catch (EncuestaYaRealizadaException $e) {
                
                        $payload = json_encode(array("Error" => $e->getMessage()));
                
                    } catch (Exception $e) {
                
                        $payload = json_encode(array("Error" => $e->getMessage()));
                
                    }
                } else {

                    $payload = json_encode(array("Error" => "Todavia no se habilito la encuesta"));

                }  

            } else {

                $payload = json_encode(array("Error" => "Ese pedido no corresponde a esa mesa"));

            }

        } else {

            $payload = json_encode(array("Error" => "Error al ingresar el pedido o la mesa"));

        }
        
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    

}
