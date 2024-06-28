<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];
    $tipoUsuario = $parametros['tipoUsuario'];
    $sector = $parametros['sector'];

    // Creamos el usuario
    $usr = new Usuario();
    $usr->usuario = $usuario;
    $usr->clave = $clave;
    $usr->tipoUsuario = $tipoUsuario;
    $usr->sector = $sector;

    try {

      $usr->crearUsuario();
      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

    } catch (UsuarioYaEnUsoException $e) {

      $payload = json_encode(array("mensaje" => $e->getMessage()));

    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['usuario'];
    $usuario = Usuario::obtenerUsuario($usr);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function ModificarUno($request, $response, $args) 
  {
    $putdata = file_get_contents('php://input');
    $params = json_decode($putdata, true);

    $usuario = $args['usuario'];
    $clave = $params['clave'];
    $tipoUsuario = $params['tipoUsuario'];
    $sector = $params['sector'];

    $usuarioModif = new Usuario();
    $usuarioModif->usuario = $usuario;
    $usuarioModif->clave = $clave;
    $usuarioModif->tipoUsuario = $tipoUsuario;
    $usuarioModif->sector = $sector;
    try{
        Usuario::modificarUsuarioPorUsuario($usuarioModif);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        
    } catch (Exception $e) {

        $payload = json_encode(array("Error" => $e->getMessage()));

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
