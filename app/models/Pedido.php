<?php

require_once 'Dto/MesaMasUsadaDto.php';
require_once 'Pendiente.php';
class Pedido
{
    public $id;
    public $codigo;
    public $codigoMesa;
    public $usuario;
    public $nombreCliente;
    public $estado;
    public $horaIngreso;
    public $factura;

    public function crearPedido()
    {
        $mesa = Mesa::obtenerMesa($this->codigoMesa);
        if ($mesa != false ){ # VALIDACION MESA Y USUARIO EXISTAN
            if ($mesa->estado == "cerrada"){ # VALIDACION MESA NO OCUPADA
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, codigoMesa, usuario, nombreCliente, estado, horaIngreso, factura) VALUES (:codigo, :codigoMesa, :usuario, :nombreCliente, :estado, :horaIngreso, :factura )");

                $codigo = $this->generarCodigoAlfanumerico();

                $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
                $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
                $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_INT);
                $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_INT);
                $consulta->bindValue(':estado', "pendiente", PDO::PARAM_STR);
                $fecha = new DateTime();
                $consulta->bindValue(':horaIngreso', date_format($fecha, 'Y-m-d H:i:s'));
                $consulta->bindValue(':factura', 0, PDO::PARAM_INT);
        
                // AGREGA LOGICA DE FOTO
        
                $consulta->execute();
                
                $mesa->cargarPedido($codigo,$this->usuario,date_format($fecha, 'Y-m-d H:i:s'));
                

                return $codigo;
            } else {

                return null;
            }

        }else {
            return -1;
        }

       
    }

    public static function CargarProductosAlPedido($pedidoId,$idProducto) # SE CARGA A PENDIENTE
    {
        $prod = Producto::obtenerProducto($idProducto);
        $pedido = Pedido::obtenerPedido($pedidoId);
        if ($prod != false && $pedido != false){ # VALIDACION DE PRODUCTO EXISTE

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pendientes (codigoPedido, idProducto, estado, sector, fechaHoraIngreso, tiempoDemora) VALUES (:idPedido, :idProducto, :estado, :sector, :fechaHoraIngreso, :tiempoDemora)");
            
            $consulta->bindValue(':idPedido', $pedido->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
            $consulta->bindValue(':estado', "pendiente", PDO::PARAM_STR);
            $consulta->bindValue(':sector', $prod->sector, PDO::PARAM_STR);
            $fecha = new DateTime();
            $consulta->bindValue(':fechaHoraIngreso', date_format($fecha, 'Y-m-d H:i:s') , PDO::PARAM_STR);
            $consulta->bindValue(':tiempoDemora', $prod->tiempoPreparacion, PDO::PARAM_STR);
    
            // AGREGA LOGICA DE FOTO
    
            $consulta->execute();
            
            Pedido::SumarFactura($pedido, $prod->precio);

            return $objAccesoDatos->obtenerUltimoId();
        }else {
            return null;
        }
    
    }

    public static function listarPedidosEstado($tipoUsuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        if ($tipoUsuario == "Socio" || $tipoUsuario == "Mozo"){

            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes");

        } else if ($tipoUsuario == "Cocinero") {

            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes WHERE sector = 'Cocina' AND estado != 'listo para servir'");

        } else if ($tipoUsuario == "Bartender") {

            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes WHERE sector = 'Barra' AND estado != 'listo para servir'");

        } else if ($tipoUsuario == "Cervecero") {

            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes WHERE sector = 'Choperas' AND estado != 'listo para servir'");

        } 
    
        
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pendiente');
    }

    public static function listarPedidosEstadoPorPedido($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes where codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigo', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pendiente');
    }

    private static function SumarFactura($pedido, $saldo){

        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET factura = :factura WHERE codigo = :codigo");

        $facturaSumada = (int)$pedido->factura + (int)$saldo;

        $consulta->bindValue(':codigo', $pedido->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':factura', $facturaSumada, PDO::PARAM_INT);
        $consulta->execute();

    }

    public static function CalcularDemora($codigoPedido){

        $pendientes = Pedido::listarPedidosEstadoPorPedido($codigoPedido);




        
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($codigo) 
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }
 
    public static function cambiarEstadoPedido($codigoPedido, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
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

    public static function MesaMasUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigoMesa, COUNT(*) AS cantidadPedidos FROM pedidos
        GROUP BY codigoMesa ORDER BY cantidadPedidos DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetchObject('MesaMasUsadaDto');
    }
}