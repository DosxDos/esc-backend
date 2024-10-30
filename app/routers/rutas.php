<?php

require_once "../../config/configApi.php";
require_once "../middlewares/autenticacion.php";
require_once "../controllers/usuarios.php";
require_once "../controllers/login.php";
require_once "../controllers/token.php";
require_once "../utils/respuesta.php";
require_once "../DBObjects/usuariosDB.php";

$respuesta = new Respuesta;
$error = new Errores;
$authMiddleware = new Autenticacion();

// Obtener la ruta solicitada
$request = $_SERVER['REQUEST_URI'];

// Obtener el método HTTP (GET, POST, PUT, DELETE, etc.)
$method = $_SERVER['REQUEST_METHOD'];

// Parsear la ruta para quitar parámetros o el prefijo del archivo
$request = trim(parse_url($request, PHP_URL_PATH), '/');

// Define la subcarpeta donde está el proyecto
$baseDir = 'esc-backend';

// Si la ruta comienza con el nombre de la subcarpeta, elimínala
if (strpos($request, $baseDir) === 0) {
    $request = substr($request, strlen($baseDir));
    $request = trim($request, '/'); // Elimina cualquier barra adicional al inicio o final
}

// Rutas y endpoints
switch ($method) {
    case 'GET':
        switch (true) {
            case ($request === 'usuarios'):
                //Verificamos que existe el usuario CREADOR del token y sino manejamos el error dentro de la funcion
                if($authMiddleware->verificarTokenUsuarioActivo()){
                    // Verificar si el usuario es administrador
                    if ($authMiddleware->verificarAdmin()) {
                        $usuarios = new UsuariosController;
                        $usuarios->getAllUsers();
                    } else {
                        $error->_403();
                        $error->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($error->code);
                        echo json_encode($error);
                    }
                }
                break;

            case (preg_match('/^usuarios\/(\d+)$/', $request, $matches)):
                $id = $matches[1];
                $usuarios = new UsuariosController;
                $usuarios->getUser($id);
                break;

            default:
                $error->_400();
                $error->message = 'El End Point no existe en la API';
                http_response_code($error->code);
                echo json_encode($error);
                break;
        }
        break;

    case 'POST':
        switch (true) {
            case ($request === 'login'):
                $postBody = file_get_contents("php://input");
                $loginController = new LoginController($postBody);
                $loginController->userLogin();
                break;

            case ($request === 'token'):
                $postBody = file_get_contents("php://input");
                $tokenController = new TokenController($postBody);
                $tokenController->validarToken();
                break;

            case ($request === 'usuarios'):
                if($authMiddleware->verificarTokenUsuarioActivo()){
                    // Verificar si el usuario es administrador
                    if ($authMiddleware->verificarAdmin()) {
                        $usuarios = new UsuariosController;
                        $usuarios->crearUser();
                    } else {
                        $error->_403();
                        $error->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($error->code);
                        echo json_encode($error);
                    }
                }
                break;
            

            default:
                $error->_400();
                $error->message = 'El End Point no existe en la API';
                http_response_code($error->code);
                echo json_encode($error);
                break;
        }
        break;

    case 'PUT':
        switch (true) {
            case (preg_match('/^products\/(\d+)$/', $request, $matches)):
                $productId = $matches[1];
                // Lógica para actualizar un producto específico por ID
                echo json_encode(['message' => 'Producto actualizado con ID: ' . $productId]);
                break;
            case (preg_match('/^usuarios\/(\d+)$/', $request, $matches) ? true : false):
                // Extraer el ID del usuario desde la URL
                $id = $matches[1];
                //Verificamos que existe el usuario CREADOR del token y sino manejamos el error dentro de la funcion
                if($authMiddleware->verificarTokenUsuarioActivo()){
                    // Verificar si el usuario es administrador
                    if ($authMiddleware->verificarAdmin()) {
                        $usuarios = new UsuariosController;
                        $usuarios->actualizarUser($id); // Pasar el ID al método de actualización
                    } else {
                        $error->_403();
                        $error->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($error->code);
                        echo json_encode($error);
                    }
                }
                break;

            default:
                $error->_400();
                $error->message = 'El End Point no existe en la API';
                http_response_code($error->code);
                echo json_encode($error);
                break;
        }
        break;

    case 'DELETE':
        switch (true) {
            case (preg_match('/^products\/(\d+)$/', $request, $matches)):
                $productId = $matches[1];
                // Lógica para eliminar un producto específico por ID
                echo json_encode(['message' => 'Producto eliminado con ID: ' . $productId]);
                break;

            case (preg_match('/^usuarios\/(\d+)$/', $request, $matches) ? true : false):
                    // Extraer el ID del usuario desde la URL
                $id = $matches[1];
                //Verificamos que existe el usuario CREADOR del token y sino manejamos el error dentro de la funcion
                if($authMiddleware->verificarTokenUsuarioActivo()){
                    // Verificar si el usuario es administrador
                    if ($authMiddleware->verificarAdmin()) {
                        $usuarios = new UsuariosController;
                        $usuarios->eliminarUser($id); // Pasar el ID al método de actualización
                    } else {
                        $error->_403();
                        $error->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($error->code);
                        echo json_encode($error);
                    }
                }
                break;

            default:
                $error->_400();
                $error->message = 'El End Point no existe en la API';
                http_response_code($error->code);
                echo json_encode($error);
                break;
        }
        break;

    default:
        $error->_405();
        $error->message = 'Este método no está permitido en la API. Para cualquier duda o asesoría contactar por favor con soporte@galagaagency.com';
        http_response_code($error->code);
        echo json_encode($error);
        break;
}
