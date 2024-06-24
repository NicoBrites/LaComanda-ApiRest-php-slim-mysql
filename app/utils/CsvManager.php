<?php

class CsvManager{

    public static function CargarCsvUsuarios($archivoCsv){

        $tempPath = $archivoCsv->getStream()->getMetadata('uri');
        $handle = fopen($tempPath, 'r');

        // Saltar la primera línea si contiene encabezados
        fgetcsv($handle, 1000, ",");

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $usuario = $data[0];
            $clave = $data[1];
            $tipoUsuario = $data[2];
            $nombreSector = $data[3];

            $usr = new Usuario();
            $usr->usuario = $usuario;
            $usr->clave = $clave;
            $usr->tipoUsuario = $tipoUsuario;
            $usr->nombreSector = $nombreSector;

            try {

                $usr->crearUsuario();
          
            } catch (UsuarioYaEnUsoException $e) {

                $usr->modificarUsuarioPorUsuario($usr);    

            } catch (Exception $e){

                throw new Exception($e->getMessage());
            }
            
        }

        fclose($handle);

    }
    public static function DescargarCsv($table){

        $tableArray = [];

        if ($table == "usuarios"){
            
            $tableArray = CsvManager::DevolverArrayAsociativoUsuarios();
            
        } else if ($table == "productos") {
           
            $tableArray = CsvManager::DevolverArrayAsociativoProductos();
            
        } else if ($table == "mesas") {

            $tableArray = CsvManager::DevolverArrayAsociativoMesas();
        } 
        // Abrir un archivo CSV para escribir
        $filename = "$table.csv";
        $fp = fopen($filename, 'w');

        // Verificar si hay datos
        if (!empty($tableArray)) {
            // Escribir el encabezado del CSV usando las claves del primer usuario
            $header = array_keys($tableArray[0]);
            var_dump($tableArray);
            var_dump($header);
            fputcsv($fp, $header);

            // Escribir los datos de los usuarios
            foreach ($tableArray as $tableEntity) {
                fputcsv($fp, $tableEntity);
            }
        }

        fclose($fp);
        return $fp;
    }


    private static function DevolverArrayAsociativoUsuarios(){

        $usuarios = Usuario::obtenerTodos();

        $tableArray = [];
        // Convertir cada objeto Usuario a un array asociativo
        foreach ($usuarios as $usuario) {
            $usuarioArray = [
                'id' => $usuario->id,
                'usuario' => $usuario->usuario,
                'clave' => $usuario->clave,
                'tipoUsuario' => $usuario->tipoUsuario,
                'fechaIngreso' => $usuario->fechaIngreso,
                'nombreSector' => $usuario->nombreSector,
                'estaSuspendido' => $usuario->estaSuspendido,
                'fechaBaja' => $usuario->fechaBaja,
            ];
            $tableArray[] = $usuarioArray;
        }
        return $tableArray;
    }

    private static function DevolverArrayAsociativoProductos(){

        $productos = Producto::obtenerTodos();

        $tableArray = [];
        // Convertir cada objeto Usuario a un array asociativo
        foreach ($productos as $prod) {
            $productoArray = [
                'id' => $prod->id,
                'nombre' => $prod->nombre,
                'precio' => $prod->precio,
                'sector' => $prod->sector,
                'tiempoPreparacion' => $prod->tiempoPreparacion,
            ];
            $tableArray[] = $productoArray;
        }
        return $tableArray;
    }

    private static function DevolverArrayAsociativoMesas(){

        $mesas = Mesa::obtenerTodos();

        $tableArray = [];
        // Convertir cada objeto Usuario a un array asociativo
       /* foreach ($mesas as $mesa) {
            $productoArray = [
                'id' => $prod->id,
                'nombre' => $prod->nombre,
                'precio' => $prod->precio,
                'sector' => $prod->sector,
                'tiempoPreparacion' => $prod->tiempoPreparacion,
            ];
            $tableArray[] = $productoArray;
        }*/
        return $tableArray;
    }
}