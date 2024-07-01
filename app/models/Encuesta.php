<?php

require_once './exceptions/Exceptions.php';
require_once 'Dto/EncuestaMejoresComentariosDto.php';

class Encuesta
{ 
    public $codigoPedido;
    public $puntajeMesa;
    public $puntajeRestaurante;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $textoExperiencia;
    public $fecha;

    public function crearEncuesta()
    {   
        $this->ValidarEncuesta($this->codigoPedido);
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (codigoPedido, puntajeMesa, puntajeRestaurante, puntajeMozo, puntajeCocinero, textoExperiencia, fecha) VALUES (:codigoPedido,:puntajeMesa, :puntajeRestaurante, :puntajeMozo, :puntajeCocinero, :textoExperiencia, :fecha)");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fecha', date_format($fecha, 'Y-m-d H:i:s') , PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':puntajeMesa', $this->puntajeMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeRestaurante', $this->puntajeRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMozo', $this->puntajeMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeCocinero', $this->puntajeCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':textoExperiencia', $this->textoExperiencia, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerMejoresComentarios()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT textoExperiencia, codigoPedido, fecha,(puntajeMesa + puntajeRestaurante + puntajeMozo + puntajeCocinero) / 5.0 
        AS promedioPuntajes FROM encuestas ORDER BY promedio DESC LIMIT 3");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'EncuestaMejoresComentariosDto');
    } 

    private function ValidarEncuesta($pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $pedido, PDO::PARAM_STR);
        $consulta->execute();

        if ($consulta->fetchObject('Encuesta') !== false){
            throw new EncuestaYaRealizadaException("Ya se completo una encuesta por ese pedido");
        }

    } 
}