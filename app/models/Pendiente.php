<?php

class Pendiente
{
    public $id;
    public $usuario;
    public $codigoPedido;
    public $idProducto;
    public $nombreSector;
    public $estado;
    public $fechaHoraIngreso;
    public $tiempoDemora;

    public function cambiarEstadoPedido($id, $usuario)
    {

        $pendiente = $this->obtenerPendiente($id);
        if ($pendiente != false){

            $objAccesoDato = AccesoDatos::obtenerInstancia();

            if ($pendiente->sector == $usuario->sector){

                if ($pendiente->estado == "pendiente"){

                    $consulta = $objAccesoDato->prepararConsulta("UPDATE pendientes SET estado = :estado, usuario = :usuario WHERE id = :id");
                    $consulta->bindValue(':id', $id, PDO::PARAM_STR);
                    $consulta->bindValue(':estado', "en preparacion", PDO::PARAM_STR);
                    $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);

                } elseif ($pendiente->estado == "en preparacion") {

                    if ($pendiente->usuario == $usuario){
                        $consulta = $objAccesoDato->prepararConsulta("UPDATE pendientes SET estado = :estado WHERE id = :id");
                        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
                        $consulta->bindValue(':estado', "listo para servir", PDO::PARAM_STR);
                    } else {
                        return -1; 
                    }

                }
                
            } else {
                return -2;
            }

            $consulta->execute();
            
        } else {
            return null;
        }
    }
    /*
    public function crearPendiente()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pendientes (idUsuario, codigoPedido, idProducto, nombreSector, estado, fechaHoraIngreso, tiempoDemora) VALUES (:idUsuario, :codigoPedido, :idProducto, :nombreSector, :estado, :fechaHoraIngreso, :idProducto)");
        $estado = "Pendiente";
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':nombreSector', $this->nombreSector, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':fechaHoraIngreso', $this->fechaHoraIngreso, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoDemora', $this->tiempoDemora, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public function prepararPendiente($codigoPedido, $idEmpleado, $fechaHoraIngresoMesa)
    {
       
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET codigoPedido = :codigoPedido, idEmpleadoMozo = :idEmpleadoMozo, estado = :estado, fechaHoraIngresoMesa = :fechaHoraIngresoMesa WHERE codigo = :codigo");

        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleadoMozo', $idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':estado', "con cliente esperando pedido", PDO::PARAM_STR);
        $consulta->bindValue(':fechaHoraIngresoMesa', $fechaHoraIngresoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $this->$codigoPedido, PDO::PARAM_STR);

        // AGREGA LOGICA DE FOTO

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    
    }


    public function cargarPedido($codigoPedido, $idEmpleado, $fechaHoraIngresoMesa)#FALTA
    {
       
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET codigoPedido = :codigoPedido, idEmpleadoMozo = :idEmpleadoMozo, estado = :estado, fechaHoraIngresoMesa = :fechaHoraIngresoMesa WHERE codigo = :codigo");

        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleadoMozo', $idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':estado', "con cliente esperando pedido", PDO::PARAM_STR);
        $consulta->bindValue(':fechaHoraIngresoMesa', $fechaHoraIngresoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $this->$codigoPedido, PDO::PARAM_STR);

        // AGREGA LOGICA DE FOTO

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
    */
    public static function obtenerPendiente($id) 
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pendiente');
    }/*

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
    }

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
    */
}