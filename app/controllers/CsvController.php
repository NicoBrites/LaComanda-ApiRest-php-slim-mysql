<?php
require_once './utils/CsvManager.php';

class CsvController  
{
    public function CargarCsvUsuarios($request, $response, $args)
    {
        $uploadedFiles = $request->getUploadedFiles();

        $archivoCsv = $uploadedFiles['archivo_csv'];

        try{
            CsvManager::CargarCsvUsuarios($archivoCsv);
            $response->getBody()->write(json_encode(["message" => "Datos importados exitosamente."]));            
        } catch (Exception $e) {

            $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }


        return $response->withHeader('Content-Type', 'application/json');
    }

    public function DescargarCsvUsuarios($request, $response, $args){

        


    }

}