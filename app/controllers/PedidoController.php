<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './interfaces/IPedido.php';

class PedidoController extends Pedido implements IApiUsable, IPedido
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigoMesa = $parametros['codigoMesa'];
        $idEmpleado = $parametros['idEmpleado'];
        $nombreCliente = $parametros['nombreCliente'];

        // Creamos el Pedido
        $pedido = new Pedido();
        $pedido->codigoMesa = $codigoMesa;
        $pedido->idEmpleado = $idEmpleado;
        $pedido->nombreCliente = $nombreCliente;
        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos producto por nombre
        $pedido = $args['pedido'];
        $pedido = Pedido::obtenerPedido($pedido);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args) # FALTA
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CargarProductos($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedido = $args['pedido'];
        $idProducto = $parametros['idProducto'];


        // Creamos el Pedido
        $pedido = new Pedido();
        $pedido->codigo = $pedido;
        $pedido->CargarProductosAlPedido($idProducto);

        $payload = json_encode(array("mensaje" => "Producto cargado al pedido con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}