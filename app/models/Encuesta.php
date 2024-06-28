<?php

class Encuesta
{ 
    public $puntajeMesa;
    public $puntajeRestaurante;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $textoExperiencia;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (puntajeMesa, puntajeRestaurante, puntajeMozo, puntajeCocinero, textoExperiencia) VALUES (:puntajeMesa, :puntajeRestaurante, :puntajeMozo, :puntajeCocinero, :textoExperiencia)");
        $consulta->bindValue(':puntajeMesa', $this->puntajeMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeRestaurante', $this->puntajeRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMozo', $this->puntajeMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeCocinero', $this->puntajeCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':textoExperiencia', $this->textoExperiencia, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
}