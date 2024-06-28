<?php

class Encuesta
{ 
    public $puntajeMesa;
    public $puntajeRestaurante;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $textoExperiencia;
    public $borrado;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuenstas (puntajeMesa, puntajeRestaurante, puntajeMozo, puntajeCocinero, textoExperiencia, borrado) VALUES (:puntajeMesa, :puntajeRestaurante, :puntajeMozo, :puntajeCocinero, :textoExperiencia, :borrado)");
        $consulta->bindValue(':puntajeMesa', $this->puntajeMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeRestaurante', $this->puntajeRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMozo', $this->puntajeMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeCocinero', $this->puntajeCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':textoExperiencia', $this->textoExperiencia, PDO::PARAM_STR);
        $consulta->bindValue(':borrado', false, PDO::PARAM_BOOL);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
}