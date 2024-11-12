<?php

require_once "../../config/configApi.php";
require_once "../middlewares/autenticacion.php";
require_once "../controllers/usuarios.php";
require_once "../controllers/login.php";
require_once "../controllers/token.php";
require_once "../utils/respuesta.php";
require_once "../DBObjects/usuariosDB.php";
require_once "../controllers/SolarEdgeController.php";
require_once "../services/ApiControladorService.php";
require_once "../services/GoodWeService.php";

$respuesta = new Respuesta;
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
            // Nuevo caso para obtener los detalles de una planta por ID
            case (preg_match('/^plants\/details\/([\w-]+)$/', $request, $matches) ? true : false):
                $powerStationId = $matches[1];
                
                // Verificamos que el usuario esté autenticado y sea administrador
                if ($authMiddleware->verificarTokenUsuarioActivo()) {
                    if ($authMiddleware->verificarAdmin()) {
                        // Instanciar el controlador de plantas y obtener detalles
                        $solarEdgeController = new ApiControladorService();
                        $solarEdgeController->getSiteDetail($powerStationId);

                    } else {
                        $respuesta->_403();
                        $respuesta->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($respuesta->code);
                        echo json_encode($respuesta);
                    }
                }
                break;
            case ($request === 'usuarios'):
                //Verificamos que existe el usuario CREADOR del token y sino manejamos el error dentro de la funcion
                if ($authMiddleware->verificarTokenUsuarioActivo()) {
                    // Verificar si el usuario es administrador
                    if ($authMiddleware->verificarAdmin()) {
                        $usuarios = new UsuariosController;
                        $usuarios->getAllUsers();
                    } else {
                        $respuesta->_403();
                        $respuesta->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($error->code);
                        echo json_encode($error);
                    }
                }
                break;

            case (preg_match('/^usuarios\/(\d+)$/', $request, $matches)):
                $id = $matches[1];
                //Verificamos que existe el usuario CREADOR del token y sino manejamos el error dentro de la funcion
                if ($authMiddleware->verificarTokenUsuarioActivo()) {
                    // Verificar si el usuario es administrador
                    if ($authMiddleware->verificarAdmin()) {
                        $usuarios = new UsuariosController;
                        $usuarios->getUser($id);
                    } else {
                        $respuesta->_403();
                        $respuesta->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($error->code);
                        echo json_encode($error);
                    }
                }
                break;
            //Devuelve una lista de todas las plantas (Admin)
            case ($request === 'plants'):
                //Verificamos que existe el usuario CREADOR del token y sino manejamos el error dentro de la funcion
                if ($authMiddleware->verificarTokenUsuarioActivo()) {
                    // Verificar si el usuario es administrador
                    if ($authMiddleware->verificarAdmin()) {
                        $solarEdgeController = new ApiControladorService();
                        $solarEdgeController->getAllPlants();
                    } else {
                        $respuesta->_403();
                        $respuesta->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($error->code);
                        echo json_encode($error);
                    }
                }
                break;
             // Ruta para getSiteEnergy con siteId, startDate y endDate en la URL
             case (preg_match('/^plants\/(\d+)$/', $request, $matches) && isset($_GET['timeUnit']) && isset($_GET['startDate']) && isset($_GET['endDate'])):
                $siteId = $matches[1];
                
                // Obtener startDate y endDate desde la query string
                $startDate = $_GET['startDate'];
                $endDate = $_GET['endDate'];
                $timeUnit = $_GET['timeUnit'];

                // Verificar que el usuario esté autenticado y sea administrador
                if ($authMiddleware->verificarTokenUsuarioActivo() && $authMiddleware->verificarAdmin()) {
                    switch($timeUnit){
                        case 'DAY':
                            $solarEdgeService = new ApiControladorService();
                            $solarEdgeService->getSiteEnergy($siteId, $startDate, $endDate);
                            break;
                        case 'QUARTER_OF_AN_HOUR':
                            $solarEdgeService = new ApiControladorService();
                            $solarEdgeService->getQuarterHourlyEnergy($siteId, $startDate, $endDate);
                            break;
                        case 'YEAR':
                            $solarEdgeService = new ApiControladorService();
                            $solarEdgeService->getYearlyEnergy($siteId, $startDate, $endDate);
                            break;
                    default:
                        $respuesta->_400();
                        $respuesta->message = 'El endpoint timeUnit no es valido asegurese de pasar parametros validos como YEAR, DAY o QUARTER_OF_AN_HOUR revisa la documentación para mas información';
                        http_response_code($respuesta->code);
                        echo json_encode($respuesta);
                    break;  
                    }
                } else {
                    $respuesta->_403();
                    $respuesta->message = 'No tienes permisos para hacer esta consulta';
                    http_response_code($respuesta->code);
                    echo json_encode($respuesta);
                }
                break;

            case (preg_match('/^plants\/(\d+)$/', $request, $matches) ? true : false):
                // Extraer el ID del usuario desde la URL
                $id = $matches[1];
                //Verificamos que existe el usuario CREADOR del token y sino manejamos el error dentro de la funcion
                if ($authMiddleware->verificarTokenUsuarioActivo()) {
                    // Verificar si el usuario es administrador
                    if ($authMiddleware->verificarAdmin()) {
                        $solarEdgeController = new ApiControladorService();
                        $solarEdgeController->getSiteDetail($id);
                    } else {
                        $respuesta->_403();
                        $respuesta->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($respuesta->code);
                        echo json_encode($respuesta);
                    }
                }
                break;

            default:
                $respuesta->_400();
                $respuesta->message = 'El End Point no existe en la API';
                http_response_code($respuesta->code);
                echo json_encode($respuesta);
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
                if ($authMiddleware->verificarTokenUsuarioActivo()) {
                    // Verificar si el usuario es administrador
                    if ($authMiddleware->verificarAdmin()) {
                        $usuarios = new UsuariosController;
                        $usuarios->crearUser();
                    } else {
                        $respuesta->_403();
                        $respuesta->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($respuesta->code);
                        echo json_encode($respuesta);
                    }
                }
                break;


            default:
                $respuesta->_400();
                $respuesta->message = 'El End Point no existe en la API';
                http_response_code($respuesta->code);
                echo json_encode($respuesta);
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
                if ($authMiddleware->verificarTokenUsuarioActivo()) {
                    // Verificar si el usuario es administrador
                    if ($authMiddleware->verificarAdmin()) {
                        $usuarios = new UsuariosController;
                        $usuarios->actualizarUser($id); // Pasar el ID al método de actualización
                    } else {
                        $respuesta->_403();
                        $respuesta->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($respuesta->code);
                        echo json_encode($respuesta);
                    }
                }
                break;

            default:
                $respuesta->_400();
                $respuesta->message = 'El End Point no existe en la API';
                http_response_code($respuesta->code);
                echo json_encode($respuesta);
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
                if ($authMiddleware->verificarTokenUsuarioActivo()) {
                    // Verificar si el usuario es administrador
                    if ($authMiddleware->verificarAdmin()) {
                        $usuarios = new UsuariosController;
                        $usuarios->eliminarUser($id); // Pasar el ID al método de actualización
                    } else {
                        $respuesta->_403();
                        $respuesta->message = 'No tienes permisos para hacer esta consulta';
                        http_response_code($respuesta->code);
                        echo json_encode($respuesta);
                    }
                }
                break;

            default:
                $respuesta->_400();
                $respuesta->message = 'El End Point no existe en la API';
                http_response_code($respuesta->code);
                echo json_encode($respuesta);
                break;
        }
        break;

    default:
        $respuesta->_405();
        $respuesta->message = 'Este método no está permitido en la API. Para cualquier duda o asesoría contactar por favor con soporte@galagaagency.com';
        http_response_code($respuesta->code);
        echo json_encode($respuesta);
        break;
}
