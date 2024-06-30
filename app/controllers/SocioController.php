<?php

require_once './models/Encuesta.php';
require_once './utils/PDFManager.php';

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

}
