<?php
class Mesa
{ 
    public $id;
    public $codigo;
    public $estado;
    public $codigoPedido;
    public $usuarioEmpleadoMozo;
    public $fechaHoraIngresoMesa;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigo, estado, codigoPedido, usuarioEmpleadoMozo, fechaHoraIngresoMesa) VALUES (:codigo, :estado, :codigoPedido, :usuarioEmpleadoMozo, :fechaHoraIngresoMesa)");
        $codigo = $this->generarCodigoAlfanumerico();
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':usuarioEmpleadoMozo', $this->usuarioEmpleadoMozo, PDO::PARAM_INT);
        $consulta->bindValue(':fechaHoraIngresoMesa', $this->fechaHoraIngresoMesa, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public function cargarPedido($codigoPedido, $idEmpleado, $fechaHoraIngresoMesa)
    {
       
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET codigoPedido = :codigoPedido, usuarioEmpleadoMozo = :usuarioEmpleadoMozo, estado = :estado, fechaHoraIngresoMesa = :fechaHoraIngresoMesa WHERE codigo = :codigo");

        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':usuarioEmpleadoMozo', $idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':estado', "con cliente esperando pedido", PDO::PARAM_STR);
        $consulta->bindValue(':fechaHoraIngresoMesa', $fechaHoraIngresoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($codigo) 
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }
    /*
    public static function modificarProducto($producto)#FALTA
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET nombre = :nombre, precio = :precio, tiempoPreparacion = :tiempoPreparacion WHERE id = :id");
        $consulta->bindValue(':nombre', $producto->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $producto->precio, PDO::PARAM_INT);
        $consulta->bindValue(':tiempoPreparacion', $producto->tiempoPreparacion, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarProducto($productoId)#FALTA
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $productoId, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }*/

    function generarCodigoAlfanumerico($longitud = 5) {
        // Definir el conjunto de caracteres alfanuméricos
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = '';
        $maxIndex = strlen($caracteres) - 1;
    
        // Generar el código alfanumérico
        for ($i = 0; $i < $longitud; $i++) {
            $codigo .= $caracteres[random_int(0, $maxIndex)];
        }
    
        return $codigo;
    }

    public static function CambiarEstadoMesa($codigo, $credencial)
    {   

        $mesa = Mesa::obtenerMesa($codigo);
        if ($mesa != false){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            if ($mesa->estado == "con cliente pagando" ){
                if ($credencial->tipoUsuario == "Socio"){
                
                $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado, codigoPedido = :codigoPedido, usuarioEmpleadoMozo = :usuarioEmpleadoMozo, fechaHoraIngresoMesa = :fechaHoraIngresoMesa WHERE codigo = :codigo");
    
                $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
                $consulta->bindValue(':estado', "cerrada", PDO::PARAM_STR);
                $consulta->bindValue(':codigoPedido', "", PDO::PARAM_STR);
                $consulta->bindValue(':usuarioEmpleadoMozo', "", PDO::PARAM_STR);
                $consulta->bindValue(':fechaHoraIngresoMesa', "", PDO::PARAM_STR);
                   
                $consulta->execute();

                return true;

                } else {
                    return -3;
                }

            }
        
            if( $mesa->usuarioEmpleadoMozo == $credencial->usuario){
                if ($mesa->estado == "con cliente esperando pedido"){
                   
                    $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE codigo = :codigo");

                    $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
                    $consulta->bindValue(':estado', "con cliente comiendo", PDO::PARAM_STR);

                } else if ($mesa->estado == "con cliente comiendo"){

                    $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE codigo = :codigo");

                    $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
                    $consulta->bindValue(':estado', "con cliente pagando", PDO::PARAM_STR);
                }

                $consulta->execute();

            } else {

                return -2;

            }
            
        } else {
            return -1;
        }
    }

}