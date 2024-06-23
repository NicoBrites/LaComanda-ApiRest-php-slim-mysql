<?php
require_once './utils/AutentificadorJWT.php';
require_once './models/Usuario.php';


class CsvController  
{
    public function CargarCsv($request, $response, $args)
    {
        $uploadedFiles = $request->getUploadedFiles();

        $archivoCsv = $uploadedFiles['archivo_csv'];

        $validador = 
    
        $response->getBody()->write(json_encode(["message" => "Datos importados exitosamente."]));
        return $response->withHeader('Content-Type', 'application/json');
    }

}