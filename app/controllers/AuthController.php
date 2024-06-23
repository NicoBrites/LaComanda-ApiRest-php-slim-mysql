<?php
require_once './utils/AutentificadorJWT.php';
require_once './models/Usuario.php';


class AuthController  
{
    public function Login($request, $response, $args)
    {
        $uploadedFiles = $request->getUploadedFiles();

        // Verifica si el archivo CSV está presente en la solicitud
        if (!isset($uploadedFiles['archivo_csv'])) {
            $response->getBody()->write(json_encode(["error" => "Por favor sube un archivo CSV."]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        $archivoCsv = $uploadedFiles['archivo_csv'];
    
        // Verifica si hubo algún error en la carga del archivo
        if ($archivoCsv->getError() !== UPLOAD_ERR_OK) {
            $response->getBody()->write(json_encode(["error" => "Error al subir el archivo."]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        $tempPath = $archivoCsv->getStream()->getMetadata('uri');
        $handle = fopen($tempPath, 'r');
    
        // Saltar la primera línea si contiene encabezados
        fgetcsv($handle, 1000, ",");
    
        $servername = "localhost";
        $username = "tu_usuario";
        $password = "tu_contraseña";
        $dbname = "tu_base_de_datos";
    
        $conn = new mysqli($servername, $username, $password, $dbname);
    
        if ($conn->connect_error) {
            $response->getBody()->write(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $tabla = $data[0];
            $nombre = $data[1];
            $edad = $data[2];
            $correo = $data[3];
    
            // Validar la tabla para evitar inyecciones SQL
            $tablas_permitidas = ['usuarios', 'clientes'];
            if (!in_array($tabla, $tablas_permitidas)) {
                continue;
            }
    
            // Prepara la consulta para evitar inyecciones SQL
            $stmt = $conn->prepare("INSERT INTO $tabla (nombre, edad, correo) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $nombre, $edad, $correo);
    
            if ($stmt->execute() !== TRUE) {
                $response->getBody()->write(json_encode(["error" => "Error: " . $stmt->error]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
            $stmt->close();
        }
    
        fclose($handle);
        $conn->close();
    
        $response->getBody()->write(json_encode(["message" => "Datos importados exitosamente."]));
        return $response->withHeader('Content-Type', 'application/json');
    }

}