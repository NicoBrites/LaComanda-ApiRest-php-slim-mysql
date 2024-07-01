<?php

class Pendiente
{
    public $id;
    public $usuario;
    public $codigoPedido;
    public $idProducto;
    public $sector;
    public $estado;
    public $fechaHoraIngreso;
    public $tiempoDemora;

    public function cambiarEstadoPedido($id, $credencial)
    {

        $pendiente = $this->obtenerPendiente($id);
        if ($pendiente != false){
            $objAccesoDato = AccesoDatos::obtenerInstancia();

            if ($pendiente->sector == $credencial->sector){
                if ($pendiente->estado == "pendiente"){

                    $producto = Producto::obtenerProducto($pendiente->idProducto);

                    $consulta = $objAccesoDato->prepararConsulta("UPDATE pendientes SET estado = :estado, usuario = :usuario WHERE id = :id");
                    $consulta->bindValue(':id', $id, PDO::PARAM_STR);
                    $consulta->bindValue(':estado', "en preparacion", PDO::PARAM_STR);
                    $consulta->bindValue(':usuario', $credencial->usuario, PDO::PARAM_STR);

                    $consulta->execute();

                    return true;


                } elseif ($pendiente->estado == "en preparacion") {
                    
                    if ($pendiente->usuario == $credencial->usuario){
                        $consulta = $objAccesoDato->prepararConsulta("UPDATE pendientes SET estado = :estado WHERE id = :id");
                        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
                        $consulta->bindValue(':estado', "listo para servir", PDO::PARAM_STR);

                        $consulta->execute();
            
                        return true;

                    } else {
                        return -1; 
                    }

                } 

            } else {

                return -2;
            }

        } else {

            return null;
        }
    }
    public static function obtenerPendiente($id) 
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pendiente');
    }
    
    public static function obtenerPendientePorPedido($codigoPedido) 
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pendiente');
    }
    
}