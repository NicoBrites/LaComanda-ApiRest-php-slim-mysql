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
                
                        $payload = json_encode(array("error" => $e->getMessage()));
                
                    } catch (Exception $e) {
                
                        $payload = json_encode(array("error" => $e->getMessage()));
                
                    }
                } else {

                    $payload = json_encode(array("error" => "Todavia no se habilito la encuesta"));

                }  

            } else {

                $payload = json_encode(array("error" => "Ese pedido no corresponde a esa mesa"));

            }

        } else {

            $payload = json_encode(array("Error" => "Error al ingresar el pedido o la mesa"));

        }
        
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    
    public function Demora($request, $response, $args) {

        $parametros = $request->getParsedBody();
            
        $pedido = $parametros['pedido'];
        $mesa = $parametros['mesa'];

        $validacionPedido = Pedido::obtenerPedido($pedido); 
        $validacionMesa = Mesa::obtenerMesa($mesa);

        if ($validacionPedido != null && $validacionMesa != null) {
            
            if ($validacionMesa->codigoPedido == $pedido){

                $listaPendientes = Pendiente::obtenerPendientePorPedido($pedido);

                $demora = 0;

                foreach ($listaPendientes as $value) {
                    if ($value->tiempoDemora > $demora){
                       
                        $demora = $value->tiempoDemora;
                        $ingreso = $value->fechaHoraIngreso;

                    }
                }    
                
                $currentTime = date('H:i:s');

                $timeRemaining = $this->getTimeRemaining($ingreso, $demora, $currentTime);

                $payload = json_encode(array("tiempoDeDemora" => $timeRemaining));

            } else {

                $payload = json_encode(array("error" => "Ese pedido no corresponde a esa mesa"));

            }

        } else {

            $payload = json_encode(array("error" => "Error al ingresar el pedido o la mesa"));

        }
        
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');     



    }

    private function getTimeRemaining($startTime, $delay, $currentTime) {
        // Convierte las horas a timestamps
        $start = strtotime($startTime);
        $current = strtotime($currentTime);
        
        // Convierte el delay (00:10:00) a segundos
        list($hours, $minutes, $seconds) = explode(':', $delay);
        $delayInSeconds = $hours * 3600 + $minutes * 60 + $seconds;
        
        // Calcula el tiempo transcurrido
        $elapsed = $current - $start;
        
        // Calcula el tiempo restante
        $remaining = $delayInSeconds - $elapsed;
        
        if ($remaining <= 0) {
            return 'El pedido ya se excedio del tiempo estipulado';
        }
        
        // Convierte el tiempo restante a horas, minutos y segundos
        $remainingHours = floor($remaining / 3600);
        $remainingMinutes = floor(($remaining % 3600) / 60);
        $remainingSeconds = $remaining % 60;
        
        return sprintf('%02d:%02d:%02d', $remainingHours, $remainingMinutes, $remainingSeconds);
    }
    
}
