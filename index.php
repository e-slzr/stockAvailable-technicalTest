<?php

declare(strict_types=1);

// Define una constante para la ruta raíz del proyecto.
define('APP_PATH', __DIR__ . '/app');

/**
 * Función simple de enrutamiento.
 */
function route_request()
{
    // Obtiene la ruta de la solicitud, eliminando el nombre del directorio del script y la query string.
    $request_uri = str_replace('/stockAvailable-technicalTest', '', $_SERVER['REQUEST_URI']);
    $request_path = trim(parse_url($request_uri, PHP_URL_PATH), '/');

    // Divide la ruta en segmentos.
    $segments = $request_path ? explode('/', $request_path) : [];

    // Determina el nombre del controlador. Por defecto será 'StockController'.
    $controller_name = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'StockController';
    $controller_file = APP_PATH . '/controllers/' . $controller_name . '.php';

    if (file_exists($controller_file)) {
        require_once $controller_file;
        if (class_exists($controller_name)) {
            $controller = new $controller_name();
            // Por ahora, llamamos a un método 'index' por defecto.
            // Más adelante, podemos usar $segments[1] para determinar el método.
            if (method_exists($controller, 'index')) {
                $controller->index();
            } else {
                send_not_found("El método 'index' no existe en el controlador '{$controller_name}'.");
            }
        } else {
            send_not_found("La clase '{$controller_name}' no se encontró en el archivo.");
        }
    } else {
        send_not_found("El controlador '{$controller_name}' no existe.");
    }
}

/**
 * Envía una respuesta 404 Not Found.
 */
function send_not_found(string $message = 'La página que buscas no existe.')
{
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "<p>{$message}</p>";
    exit;
}

// Inicia el enrutamiento. 
route_request();