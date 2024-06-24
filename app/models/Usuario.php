<?php

require_once './exceptions/Exceptions.php';

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $tipoUsuario;
    public $fechaIngreso;
    public $nombreSector;
    public $estaSuspendido;
    public $fechaBaja;

    public function crearUsuario()
    {   
        $this->ValidarUsuario($this->usuario);
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, tipoUsuario, fechaIngreso, nombreSector, estaSuspendido, fechaBaja) VALUES (:usuario, :clave, :tipoUsuario, :fechaIngreso, :nombreSector, :estaSuspendido, :fechaBaja)");
        $claveHash = crypt($this->clave, "akhsdgkals");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':tipoUsuario', $this->tipoUsuario, PDO::PARAM_STR);
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fechaIngreso', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->bindValue(':nombreSector', $this->nombreSector, PDO::PARAM_STR);
        $consulta->bindValue(':estaSuspendido', false, PDO::PARAM_BOOL);
        $consulta->bindValue(':fechaBaja', null, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave, tipoUsuario = :tipoUsuario, nombreSector = :nombreSector  WHERE id = :id");
        $consulta->bindValue(':usuario', $usuario->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $usuario->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $usuario->id, PDO::PARAM_INT);
        $consulta->bindValue(':tipoUsuario', $usuario->tipoUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':nombreSector', $usuario->nombreSector, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function modificarUsuarioPorUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET clave = :clave, tipoUsuario = :tipoUsuario, nombreSector = :nombreSector  WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $usuario->clave, PDO::PARAM_STR);
        $consulta->bindValue(':tipoUsuario', $usuario->tipoUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':nombreSector', $usuario->nombreSector, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarUsuario($usuarioiD)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuarioiD, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    private function ValidarUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        if ($consulta->fetchObject('Usuario') !== false){
            throw new UsuarioYaEnUsoException("El usuario ya esta en uso");
        }

    } 
}