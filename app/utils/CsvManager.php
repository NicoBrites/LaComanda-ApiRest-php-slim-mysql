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
    public static function DescargarCsvUsuarios($archivoCsv){

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
























}