<?php
// Activar el reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en la salida

// Asegurarse de que la salida sea siempre JSON
header('Content-Type: application/json');

try {
    // Incluir los archivos necesarios
    require_once __DIR__ . '/../app/controllers/BoxController.php';
    
    // Verificar que se recibió un ID de producto
    if (!isset($_GET['productId']) || empty($_GET['productId'])) {
        throw new Exception('Se requiere un ID de producto');
    }
    
    // Obtener el ID del producto
    $productId = $_GET['productId'];
    
    // Crear una instancia del controlador
    $controller = new BoxController();
    
    // Obtener las cajas del producto
    $controller->getBoxesByProductId($productId);
    
} catch (Exception $e) {
    // Devolver un error en formato JSON
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en el servidor', 
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
