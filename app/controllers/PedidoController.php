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
        $nombreCliente = $parametros['nombreCliente'];

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $credencial = AutentificadorJWT::ObtenerData($token);
        
        if ($credencial->tipoUsuario == "Mozo"){

            // Creamos el Pedido
            $pedido = new Pedido();
            $pedido->codigoMesa = $codigoMesa;
            $pedido->usuario = $credencial->usuario;
            $pedido->nombreCliente = $nombreCliente;
        
            $validacion =  $pedido->crearPedido();
            if ($validacion == -1){ # VALIDACION EXISTENCIA DE MESA Y USUARIO
                $payload = json_encode(array("mensaje" => "Error al cargar el pedido, revise los ids"));
            } elseif  ($validacion != null){  # VALIDACION MESA LIBRE
                $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
            } else {
                $payload = json_encode(array("mensaje" => "Error al cargar el pedido, la mesa esta ocupada"));
            }

            if ($_FILES['fotoPedido'] != null && $validacion != null)
            {
                try{
                    $destino = "./imagenesPedido/".$validacion .".PNG";
                    move_uploaded_file($_FILES["fotoPedido"]["tmp_name"], $destino);
                }
                catch(Exception $exception)
                {
                    echo "Error al subir la foto del pedido. error-> " . $exception;
                }
            }
        } else {
            $payload = json_encode(array("Error" => "Solo los mozos pueden atender una mesa"));
        }
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
        try {
            $lista = Pedido::obtenerTodos();
            $payload = json_encode(array("listaPedidos" => $lista));
        } catch (Exception $e){
            
            $payload = json_encode(array("Error" => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args) # FALTA
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuarioPorUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)# FALTA
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

        $pedidoId = $args['pedido'];
        $idProducto = $parametros['idProducto'];


        $validacion = Pedido::CargarProductosAlPedido($pedidoId,$idProducto);
        if ($validacion != null){
            $payload = json_encode(array("mensaje" => "Producto cargado al pedido con exito"));
        } else {
        $payload = json_encode(array("mensaje" => "Error al cargar producto, revise los ids"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosPedidosEstado($request, $response, $args)
    {
        
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $credencial = AutentificadorJWT::ObtenerData($token);

        $lista = Pedido::listarPedidosEstado($credencial->tipoUsuario);
        $payload = json_encode(array("listaPedidosEstado" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    

}