<?php

require_once './models/Encuesta.php';
require_once './utils/PDFManager.php';
require_once './models/Usuario.php';

class SocioController  
{
    public function MejoresComentarios($request, $response, $args)
    {
        $lista = Encuesta::obtenerMejoresComentarios();
        $payload = json_encode(array("MejoresComentarios" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function MesaMasUsada($request, $response, $args)
    {
        $mesa = Pedido::MesaMasUsada();
        $payload = json_encode(array("Mesa mas usada" => $mesa));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

    }

    public function SuspenderUsuario($request, $response, $args)
    {
        
        $parametros = $request->getParsedBody();
            
        $usuario = $parametros['usuario'];

        $validacionUsuario = Usuario::obtenerUsuario($usuario); 

        if ($validacionUsuario != null ) {

            if (!$validacionUsuario->estaSuspendido) {

                try {
                    Usuario::SuspenderUsuario($validacionUsuario->usuario, true);
                    $payload = json_encode(array("mensaje" =>"Usuario suspendido exitosamente"));
                } catch (Exception $e) {

                    $payload = json_encode(array("error" => $e->getMessage()));
                }

            } else {

                try {
                    Usuario::SuspenderUsuario($validacionUsuario->usuario, false);
                    $payload = json_encode(array("mensaje" =>"Finalizacion de la suspencion del usuario exitosamente"));
                } catch (Exception $e) {

                    $payload = json_encode(array("error" => $e->getMessage()));
                }

            }

        } else {

            $payload = json_encode(array("mensaje" => "No existe ese usuario o esta dado de baja"));

        }
    
        $response->getBody()->write($payload);     
        return $response
              ->withHeader('Content-Type', 'application/json');


    }

    public function LogueoDeUsuariosEnPDF($request, $response, $args)
    {
        
        $parametros = $request->getParsedBody();
            
        $usuario = $parametros['usuario'];

        $validacionUsuario = Usuario::obtenerUsuarioConBaja($usuario); 
        
        if ($validacionUsuario != null ) {
            
            $logData = ArchivosJson::LeerJson('./logs/log_sesion.json');

            if ($logData !== null){
                $listadoDeHorarios= "";

                foreach($logData as $array =>$dato ){ 
                    if ($dato["usuario"] == $usuario){
    
                        $fecha = $dato["fecha"];
                        $listadoDeHorarios .= "Fecha: $fecha \n";
                    }        
                }

                $titulo = "Lista de Horarios de $usuario";
                $pdf = PDFManager::CrearPdf($titulo , $listadoDeHorarios);

                $pdfOutput = $pdf->Output('I');

                $response->getBody()->write($pdfOutput);

                return $response->withHeader('Content-Type', 'application/pdf')
                                ->withHeader('Content-Disposition', 'attachment; filename="' . $titulo . '.pdf"');

            } else {

                $payload = json_encode(array("Error" =>"Error al leer el log"));

            }
         
        } else {

            $payload = json_encode(array("Error" =>"Ese usuario no existe"));
        }

    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function DarDeBajaUsuario($request, $response, $args)
    {
        
        $parametros = $request->getParsedBody();
            
        $usuario = $parametros['usuario'];

        $validacionUsuario = Usuario::obtenerUsuario($usuario); 

        if ($validacionUsuario != null ) {

            try {
                Usuario::SuspenderUsuario($validacionUsuario->usuario, true);
                $payload = json_encode(array("mensaje" =>"Usuario suspendido exitosamente"));
            } catch (Exception $e) {

                $payload = json_encode(array("error" => $e->getMessage()));
            }          

        } else {

            $payload = json_encode(array("mensaje" => "No existe ese usuario o esta dado de baja"));

        }
    
        $response->getBody()->write($payload);     
        return $response
              ->withHeader('Content-Type', 'application/json');


    }
}
