<?php

require_once __DIR__ . '/../models/StockModel.php';

class StockController
{
    public function index()
    {
        // 1. Crear una instancia del modelo
        $stockModel = new StockModel();

        // 2. Obtener los datos del stock desde el modelo
        $stockData = $stockModel->getStockData();

        // 3. Cargar la vista y pasarle los datos
        $this->load_view('stock_report', ['stockData' => $stockData]);
    }

    /**
     * Carga un archivo de vista.
     * @param string $view_name El nombre del archivo de la vista (sin .php)
     * @param array $data Los datos a pasar a la vista.
     */
    protected function load_view(string $view_name, array $data = [])
    {
        $view_file = APP_PATH . '/views/' . $view_name . '.php';

        if (file_exists($view_file)) {
            // Extrae los datos para que estén disponibles como variables en la vista
            extract($data);
            require_once $view_file;
        } else {
            // Usamos la función global de index.php para manejar errores
            send_not_found("La vista '{$view_name}' no fue encontrada.");
        }
    }
}
