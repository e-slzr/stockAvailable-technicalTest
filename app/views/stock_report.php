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
            <div class="alert alert-danger">
                <strong>Error:</strong> <?php echo htmlspecialchars($stockData['error']); ?>
                <?php if (isset($stockData['details'])): ?>
                    <pre><?php echo htmlspecialchars(print_r($stockData['details'], true)); ?></pre>
                <?php endif; ?>
            </div>
        <?php elseif (empty($stockData)): ?>
            <div class="alert alert-info">
                <p>No hay datos de stock disponibles para mostrar.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Stock Disponible</th>
                            <th>Fecha Última Transacción</th>
                            <th>Tipo Última Operación</th>
                            <th>Cantidad Última Transacción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stockData as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars((string)$item['productId']); ?></td>
                                <td><?php echo htmlspecialchars($item['code']); ?></td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                                <td><?php echo htmlspecialchars($item['status']); ?></td>
                                <td class="<?php echo $item['availableStock'] > 0 ? 'stock-available' : 'stock-zero'; ?>">
                                    <?php echo htmlspecialchars((string)$item['availableStock']); ?>
                                </td>
                                <td>
                                    <?php if ($item['lastTransactionDate']): ?>
                                        <?php echo htmlspecialchars($item['lastTransactionDate']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">No disponible</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['lastOperationType']): ?>
                                        <?php echo htmlspecialchars($item['lastOperationType']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">No disponible</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['lastTransactionQuantity'] !== null): ?>
                                        <?php echo htmlspecialchars((string)$item['lastTransactionQuantity']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">No disponible</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="public/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/main.js"></script>
</body>
</html>
