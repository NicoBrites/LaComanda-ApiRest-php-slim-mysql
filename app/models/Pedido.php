<?php

require_once 'PedidoProducto.php';
class Pedido
{
    public $codigo;
    public $codigoMesa;
    public $idEmpleado;
    public $nombreCliente;
    public $estado;
    public $horaIngreso;
    public $factura;

    public function crearPedido()
    {
        $mesa = Mesa::obtenerMesa($this->codigoMesa);
        $empleado = Usuario::obtenerUsuario($this->idEmpleado); # esto se valida despues con el usuario que inicio sesion
        if ($mesa != false && $empleado != false){ # VALIDACION MESA Y USUARIO EXISTAN
            if ($mesa->estado == "cerrado"){ # VALIDACION MESA NO OCUPADA
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, codigoMesa, idEmpleado, nombreCliente, estado, horaIngreso, factura) VALUES (:codigo, :codigoMesa, :idEmpleado, :nombreCliente, :estado, :horaIngreso, :factura )");

                $codigo = $this->generarCodigoAlfanumerico();

                $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
                $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
                $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);
                $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_INT);
                $consulta->bindValue(':estado', "pendiente", PDO::PARAM_STR);
                $fecha = new DateTime();
                $consulta->bindValue(':horaIngreso', date_format($fecha, 'Y-m-d H:i:s'));
                $consulta->bindValue(':factura', $this->factura, PDO::PARAM_INT);
        
                // AGREGA LOGICA DE FOTO
        
                $consulta->execute();
                
                $mesa->cargarPedido($codigo,$this->idEmpleado,date_format($fecha, 'Y-m-d H:i:s'));
                

                return $codigo;
            } else {

                return null;
            }

        }else {
            return -1;
        }

       
    }

    public function CargarProductosAlPedido($idProducto)
    {
        $prod = Producto::obtenerProducto($idProducto);
        if ($prod != false){ # VALIDACION DE PRODUCTO EXISTE
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos_productos (idPedido, idProducto, estado) VALUES (:idPedido, :idProducto, :estado)");
            
            $consulta->bindValue(':idPedido', $this->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
            $consulta->bindValue(':estado', "pediente", PDO::PARAM_STR);
    
            // AGREGA LOGICA DE FOTO
    
            $consulta->execute();
            
            $this->SumarFactura($prod->precio);

            return $objAccesoDatos->obtenerUltimoId();
        }else {
            return null;
        }
    
    }

    public static function listarPedidosEstado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos_productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public function SumarFactura($saldo){

        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET factura = :factura WHERE codigo = :codigo");

        $facturaSumada = $this->factura += $saldo;

        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':factura', $facturaSumada, PDO::PARAM_INT);
        $consulta->execute();

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

        return $consulta->fetchObject('Mesa');
    }

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
}