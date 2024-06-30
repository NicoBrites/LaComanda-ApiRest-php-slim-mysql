<?php

require_once './models/Encuesta.php';
require_once './utils/PDFManager.php';
require_once './models/Usuario.php';

class SocioController  
{
    public function MejoresComentarios($request, $response, $args)
    {
        $lista = Encuesta::obtenerMejoresComentarios();
        $payload = json_encode(array("MejoresComentarios" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function MesaMasUsada($request, $response, $args)
    {
        $mesa = Pedido::MesaMasUsada();
        $payload = json_encode(array("Mesa mas usada" => $mesa));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function CrearPdf($request, $response, $args)
    {
        $titulo = 'prueba';

        $pdf = PDFManager::CrearPdf($titulo, ' pruebita aver');
        
        // Obtener el contenido del PDF como una cadena
        $pdfOutput = $pdf->Output('S');

        // Escribir el contenido del PDF en el cuerpo de la respuesta
        $response->getBody()->write($pdfOutput);

        return $response->withHeader('Content-Type', 'application/pdf')
                        ->withHeader('Content-Disposition', 'attachment; filename="' . $titulo . '.pdf"');
    }

    public function SuspenderUsuario($request, $response, $args)
    {
        
        $parametros = $request->getParsedBody();
            
        $usuario = $parametros['usuario'];

        $validacionUsuario = Usuario::obtenerUsuario($usuario); 

        if ($validacionUsuario != null ) {

            if (!$validacionUsuario->estaSuspendido) {

                try {
                    Usuario::SuspenderUsuario($validacionUsuario->usuario, true);
                    $payload = json_encode(array("mensaje" =>"Usuario suspendido exitosamente"));
                } catch (Exception $e) {

                    $payload = json_encode(array("error" => $e->getMessage()));
                }

            } else {

                try {
                    Usuario::SuspenderUsuario($validacionUsuario->usuario, false);
                    $payload = json_encode(array("mensaje" =>"Finalizacion de la suspencion del usuario exitosamente"));
                } catch (Exception $e) {

                    $payload = json_encode(array("error" => $e->getMessage()));
                }

            }

        } else {

            $payload = json_encode(array("mensaje" => "No existe ese usuario o esta dado de baja"));

        }
    
        $response->getBody()->write($payload);     
        return $response
              ->withHeader('Content-Type', 'application/json');


    }

}
