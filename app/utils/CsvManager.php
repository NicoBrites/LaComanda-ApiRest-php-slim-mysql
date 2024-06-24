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

        if ($table == "usuarios"){
            $usuarios = Usuario::obtenerTodos();
        } 
        $usuariosArray = [];

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
            $usuariosArray[] = $usuarioArray;
        }

        // Abrir un archivo CSV para escribir
        $filename = 'usuarios.csv';
        $fp = fopen($filename, 'w');

        // Verificar si hay datos
        if (!empty($usuariosArray)) {
            // Escribir el encabezado del CSV usando las claves del primer usuario
            $header = array_keys($usuariosArray[0]);
            fputcsv($fp, $header);

            // Escribir los datos de los usuarios
            foreach ($usuariosArray as $usuario) {
                fputcsv($fp, $usuario);
            }
        }

        // Cerrar el archivo
        fclose($fp);
        return $fp;
    }
}