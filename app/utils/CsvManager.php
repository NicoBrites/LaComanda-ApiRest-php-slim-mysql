<?php

class CsvManager{

    public static function CargarCsv($archivoCsv, $tabla){

        
        if ($tabla == "usuarios"){
            $bool = CsvManager::CargarCsvUsuario($archivoCsv);
        } else if ($tabla == "productos"){
            $bool = CsvManager::CargarCsvProducto($archivoCsv);
        }

        return $bool;
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
    
        return $tableArray;
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

    private static function CargarCsvUsuario($archivoCsv){

        $tempPath = $archivoCsv->getStream()->getMetadata('uri');
        $handle = fopen($tempPath, 'r');

        // Saltar la primera línea si contiene encabezados
        fgetcsv($handle, 1000, ",");

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $usuario = $data[0];
            $clave = $data[1];
            $tipoUsuario = $data[2];
            $sector = $data[3];

            $usr = new Usuario();
            $usr->usuario = $usuario;
            $usr->clave = $clave;
            $usr->tipoUsuario = $tipoUsuario;
            $usr->sector = $sector;

            try {

                $usr->crearUsuario();
          
            } catch (UsuarioYaEnUsoException $e) {

                $usr->modificarUsuarioPorUsuario($usr);    

            } catch (Exception $e){

                throw new Exception($e->getMessage());
            }
            
        }

        fclose($handle);

        return true;


    }

    private static function CargarCsvProducto($archivoCsv){

        $tempPath = $archivoCsv->getStream()->getMetadata('uri');
        $handle = fopen($tempPath, 'r');

        // Saltar la primera línea si contiene encabezados
        fgetcsv($handle, 1000, ",");

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $nombre = $data[0];
            $precio = $data[1];
            $sector = $data[2];
            $tiempoPreparacion = $data[3];

            $prod = new Producto();
            $prod->nombre = $nombre;
            $prod->precio = $precio;
            $prod->sector = $sector;
            $prod->tiempoPreparacion = $tiempoPreparacion;

            try {

                $prod->crearProducto();
          
            } catch (NombreYaEnUsoException $e) {

                $prod->modificarProductoPorNombre($prod);    

            } catch (Exception $e){

                throw new Exception($e->getMessage());
            }
            
        }

        fclose($handle);
        return true;
    }
}