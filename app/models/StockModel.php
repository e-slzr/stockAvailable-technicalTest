<?php

class StockModel
{
    public function getStockData()
    {
        require_once __DIR__ . '/../../config/config.php';

        $api_url = API_BASE_URL . '/api/products/stock-available';

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => 'Content-Type: application/json',
                'ignore_errors' => true // Permite capturar respuestas de error (ej. 404, 500)
            ]
        ];

        $context = stream_context_create($options);

        // Usamos @ para suprimir warnings si la conexión falla, lo manejaremos manualmente
        $response = @file_get_contents($api_url, false, $context);

        // Verifica si la conexión falló
        if ($response === false) {
            error_log("Error al conectar con la API en: " . $api_url);
            return ['error' => 'No se pudo conectar con el servicio de stock.'];
        }

        $data = json_decode($response, true);

        // Revisa el código de estado HTTP de la respuesta
        $http_status = $http_response_header[0];
        if (strpos($http_status, '200 OK') === false) {
            error_log("La API devolvió un error: " . $http_status . " - " . $response);
            return ['error' => 'El servicio de stock devolvió un error.', 'details' => $data];
        }

        // Verifica si hubo un error al decodificar el JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Error al decodificar JSON de la API. Respuesta: " . $response);
            return ['error' => 'La respuesta del servicio no tiene un formato válido.'];
        }

        return $data;
    }
}
