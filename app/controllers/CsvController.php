<?php
require_once './utils/CsvManager.php';

class CsvController  
{
    public function CargarCsvUsuarios($request, $response, $args)
    {
        $tabla = $args['tabla'];

        $uploadedFiles = $request->getUploadedFiles();

        $archivoCsv = $uploadedFiles['archivo_csv'];

        try{
            $bool = CsvManager::CargarCsv($archivoCsv, $tabla);
            if ($bool){
                $response->getBody()->write(json_encode(["message" => "Datos de $tabla importados exitosamente."]));    
            } else {
                $response->getBody()->write(json_encode(["error" => "Datos de $tabla no se pudieron importar"]));    
            }    
        } catch (Exception $e) {

            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
        }


        return $response->withHeader('Content-Type', 'application/json');
    }

    public function DescargarCsv($request, $response, $args){

        $tabla = $args['tabla'];

        try{
            $csv = CsvManager::DescargarCsv($tabla);
            if ($csv != null){

                $output = fopen('php://output', 'w');

                // Verificar si hay datos
                if (!empty($csv)) {
                    // Escribir el encabezado del CSV usando las claves del primer usuario
                    $header = array_keys($csv[0]);
                    fputcsv($output, $header);

                    // Escribir los datos de los usuarios
                    foreach ($csv as $tableEntity) {
                        fputcsv($output, $tableEntity);
                    }
                }

                fclose($output);

                $response = $response->withHeader('Content-Type', 'text/csv')
                            ->withHeader('Content-Disposition', 'attachment; filename="' . $tabla . '.csv"');

                        
                            
            } else {

                $response->getBody()->write(json_encode(["error" => "No hay datos en la tabla $tabla. Revise el nombre de la tabla"]));

            }

        } catch (Exception $e){

            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));           

        }
        
        return $response->withHeader('Content-Type', 'application/json');

    }

}