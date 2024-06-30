<?php

class ArchivosJson{

    public static function LeerJson($path) {
        if(file_exists($path))
        {
            $archivo = fopen($path, "r");
            $lectura = fread($archivo,10000);
            fclose($archivo);
            $datoJson = json_decode($lectura,true);
            return $datoJson;
        }
        
    }

    public static function GuardarJson($path, $datos) {
        $datosJson = json_encode($datos,JSON_PRETTY_PRINT);
        $archivo = fopen($path, "w");
        fwrite($archivo, $datosJson);
        fclose($archivo);
    
    }

}