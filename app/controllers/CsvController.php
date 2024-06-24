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

    public function DescargarCsv($request, $response, $args){

        $tabla = $args['tabla'];

        try{
            $csv = CsvManager::DescargarCsv($tabla);
            if ($csv != null){

                $response->getBody()->write(json_encode(["mensaje" => "Csv de $tabla creado correctamente"]));
                $response = $response->withHeader('Content-Type', 'text/csv')
                            ->withHeader('Content-Disposition', 'attachment; filename="' . $tabla . '.csv"');
                            
            } else {

                $response->getBody()->write(json_encode(["error" => "No hay datos en la tabla $tabla."]));

            }

        } catch (Exception $e){

            $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));           

        }
        
        return $response->withHeader('Content-Type', 'application/json');

    }

}