<?php
require_once __DIR__ . '/../models/BoxModel.php';

class BoxController {
    private $model;
    
    public function __construct() {
        $this->model = new BoxModel();
    }
    
    public function getBoxesByProductId($productId) {
        try {
            $data = $this->model->getBoxesByProductId($productId);
            
            // Devolver los datos en formato JSON
            header('Content-Type: application/json');
            echo json_encode($data);
        } catch (Exception $e) {
            // Asegurarse de que cualquier error se devuelva como JSON
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => 'Error al obtener datos', 
                'message' => $e->getMessage()
            ]);
        }
    }
}
