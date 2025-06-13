<?php
require_once __DIR__ . '/../../config/config.php';

class BoxModel {
    private $logFile;
    
    public function __construct() {
        $this->logFile = __DIR__ . '/../../logs/api_errors.log';
        
        // Crear directorio de logs si no existe
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    private function logError($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
    
    public function getBoxesByProductId($productId) {
        try {
            $api_url = API_BASE_URL . "/api/products/{$productId}/details";
            
            $this->logError("Intentando conectar a: $api_url");
            
            $options = [
                'http' => [
                    'method' => 'GET',
                    'header' => 'Content-Type: application/json',
                    'ignore_errors' => true
                ]
            ];
            
            $context = stream_context_create($options);
            $response = @file_get_contents($api_url, false, $context);
            
            if ($response === false) {
                $error = error_get_last();
                $this->logError("Error de conexión: " . ($error ? $error['message'] : 'Desconocido'));
                return ['error' => 'No se pudo conectar con el servicio de detalles del producto.'];
            }
            
            // Registrar la respuesta para depuración
            $this->logError("Respuesta recibida: " . substr($response, 0, 500) . (strlen($response) > 500 ? '...' : ''));
            
            $data = json_decode($response, true);
            
            // Revisa el código de estado HTTP de la respuesta
            $http_status = $http_response_header[0];
            if (strpos($http_status, '200 OK') === false) {
                $this->logError("Error HTTP: $http_status");
                return ['error' => 'El servicio devolvió un error.', 'details' => $data];
            }
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logError("Error JSON: " . json_last_error_msg());
                return ['error' => 'La respuesta del servicio no es un JSON válido.'];
            }
            
            return $data;
        } catch (Exception $e) {
            $this->logError("Excepción: " . $e->getMessage());
            return ['error' => 'Error inesperado', 'message' => $e->getMessage()];
        }
    }
}
