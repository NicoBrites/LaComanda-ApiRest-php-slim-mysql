<?php

require_once './exceptions/Exceptions.php';

class Encuesta
{ 
    public $codigoPedido;
    public $puntajeMesa;
    public $puntajeRestaurante;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $textoExperiencia;

    public function crearEncuesta()
    {   
        $this->ValidarEncuesta($this->codigoPedido);
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (codigoPedido, puntajeMesa, puntajeRestaurante, puntajeMozo, puntajeCocinero, textoExperiencia) VALUES (:puntajeMesa, :puntajeRestaurante, :puntajeMozo, :puntajeCocinero, :textoExperiencia)");
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':puntajeMesa', $this->puntajeMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeRestaurante', $this->puntajeRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMozo', $this->puntajeMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeCocinero', $this->puntajeCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':textoExperiencia', $this->textoExperiencia, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    private function ValidarEncuesta($pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas WHERE pedido = :pedido");
        $consulta->bindValue(':pedido', $pedido, PDO::PARAM_STR);
        $consulta->execute();

        if ($consulta->fetchObject('Encuesta') !== false){
            throw new EncuestaYaRealizadaException("Ya se completo una encuesta por ese pedido");
        }

    } 
}