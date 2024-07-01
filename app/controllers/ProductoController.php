<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $tiempoPreparacion = $parametros['tiempoPreparacion'];
        $sector = $parametros['sector'];

        // Creamos el producto
        $prod = new Producto();
        $prod->nombre = $nombre;
        $prod->precio = $precio;
        $prod->tiempoPreparacion = $tiempoPreparacion;
        $prod->sector = $sector;
        $prod->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos producto por id
        $prod = $args['producto'];
        $producto = Producto::obtenerProducto($prod);

        if ($producto){
          $payload = json_encode($producto);
        } else {
          $payload = json_encode(array("mensaje" => "No se encontro el producto"));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args) # FALTA
    {
      $putdata = file_get_contents('php://input');
      $params = json_decode($putdata, true);
  
      $id = $args['id'];

      $validacion = Producto::obtenerProducto($id);
      if ($validacion){
        $nombre = $params['nombre'];
        $precio = $params['precio'];
        $sector = $params['sector'];
        $tiempoPreparacion = $params['tiempoPreparacion'];
    
        $productoModif = new Producto();
        $productoModif->nombre = $nombre;
        $productoModif->precio = $precio;
        $productoModif->tiempoPreparacion = $tiempoPreparacion;
        $productoModif->sector = $sector;
        $productoModif->id = $id;
        try{
            Producto::modificarProducto($productoModif);
    
            $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
            
        } catch (Exception $e) {
    
            $payload = json_encode(array("error" => $e->getMessage()));
    
        }
      } else {
        $payload = json_encode(array("mensaje" => "No se encontro el producto a modificar"));
      }
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
}
