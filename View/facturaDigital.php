<?php
include_once __DIR__ . '/../Model/baseDatos.php';
include_once __DIR__ . '/../Model/facturaModel.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturación</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="p-4">
    <?php if (!empty($factura['encabezado'])): 
        $encabezado = $factura['encabezado'];
        $detalle = $factura['detalle'];
    ?>
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1">Óptica Grisol</h4>
            <small>Factura #<?= htmlspecialchars($encabezado['NumeroFactura']) ?></small>
        </div>

        <div class="mb-3">
            <p><strong>Cliente:</strong> <?= htmlspecialchars($encabezado['Cliente']) ?></p>
            <p><strong>Cédula:</strong> <?= htmlspecialchars($encabezado['Cedula']) ?></p>
            <p><strong>Método de Pago:</strong> <?= htmlspecialchars($encabezado['MetodoPago']) ?></p>
            <p><strong>Fecha:</strong> <?= htmlspecialchars($encabezado['Fecha']) ?></p>
        </div>

        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Descuento</th>
                    <th>Total sin Desc.</th>
                    <th>Total con Desc.</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalle as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['Producto']) ?></td>
                        <td><?= htmlspecialchars($d['Cantidad']) ?></td>
                        <td>₡<?= number_format($d['PrecioUnitario'], 2) ?></td>
                        <td><?= htmlspecialchars($d['Descuento']) ?>%</td>
                        <td>₡<?= number_format($d['TotalSinDescuento'], 2) ?></td>
                        <td>₡<?= number_format($d['TotalConDescuento'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-end mt-3">
            <p><strong>Subtotal:</strong> ₡<?= number_format($encabezado['Subtotal'], 2) ?></p>
            <p><strong>Descuento General:</strong> <?= htmlspecialchars($encabezado['Bono']) ?>%</p>
            <h5><strong>Total Final:</strong> ₡<?= number_format($encabezado['Total'], 2) ?></h5>
        </div>
    <?php else: ?>
        <div class="text-center text-danger py-5">Factura no encontrada.</div>
    <?php endif; ?>
</div>
</body>
</html>