<?php
/**
 * Vista para mostrar el informe de stock disponible.
 * Se espera que la variable $stockData esté disponible, pasada desde el controlador.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Stock Disponible</title>
    <link rel="stylesheet" href="public/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Informe de Stock Disponible</h1>

        <?php if (isset($stockData['error'])): ?>
            <div class="error-message">
                <strong>Error:</strong> <?php echo htmlspecialchars($stockData['error']); ?>
                <?php if (isset($stockData['details'])): ?>
                    <pre><?php echo htmlspecialchars(print_r($stockData['details'], true)); ?></pre>
                <?php endif; ?>
            </div>
        <?php elseif (empty($stockData)): ?>
            <div class="no-data">
                <p>No hay datos de stock disponibles para mostrar.</p>
            </div>
        <?php else: ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Producto (Código)</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Stock Disponible</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stockData as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name'] . ' (' . $item['code'] . ')'); ?></td>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td><?php echo htmlspecialchars($item['status']); ?></td>
                            <td><?php echo htmlspecialchars((string)$item['availableStock']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="public/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/main.js"></script>
</body>
</html>
