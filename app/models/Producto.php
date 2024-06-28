<?php
class Producto
{
    public $id;
    public $nombre;
    public $precio;
    public $sector;
    public $tiempoPreparacion;
    public $borrado;

    public function crearProducto()
    {   
        $this->ValidarProducto($this->nombre);
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (nombre, precio, sector, tiempoPreparacion, borrado) VALUES (:nombre, :precio, :sector, :tiempoPreparacion , :borrado)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoPreparacion', $this->tiempoPreparacion, PDO::PARAM_STR);
        $consulta->bindValue(':borrado', false, PDO::PARAM_BOOL);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE borrado = :borrado");
        $consulta->bindValue(':borrado', false, PDO::PARAM_BOOL);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerProducto($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE id = :id AND borrado = :borrado");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->bindValue(':borrado', false, PDO::PARAM_BOOL);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function modificarProducto($producto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET nombre = :nombre, precio = :precio, tiempoPreparacion = :tiempoPreparacion WHERE id = :id");
        $consulta->bindValue(':nombre', $producto->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $producto->precio, PDO::PARAM_INT);
        $consulta->bindValue(':sector', $producto->sector, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoPreparacion', $producto->tiempoPreparacion, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function modificarProductoPorNombre($producto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET precio = :precio, sector = :sector, tiempoPreparacion = :tiempoPreparacion  WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $producto->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $producto->precio, PDO::PARAM_INT);
        $consulta->bindValue(':sector', $producto->sector, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoPreparacion', $producto->tiempoPreparacion, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarProducto($productoId)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET borrado = :borrado WHERE id = :id");
        $consulta->bindValue(':id', $productoId, PDO::PARAM_INT);
        $consulta->bindValue(':borrado', true, PDO::PARAM_BOOL);
        $consulta->execute();
    }

    private function ValidarProducto($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();

        if ($consulta->fetchObject('Producto') !== false){
            throw new NombreYaEnUsoException("El nombre del producto ya esta en uso");
        }

    } 

}