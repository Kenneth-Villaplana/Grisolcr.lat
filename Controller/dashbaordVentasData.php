<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../Model/baseDatos.php';

try {
    $cn = AbrirBD(); // usa aquí tu función real de conexión

    // TOP 10 productos más vendidos (usa SP, ver más abajo)
    $rTop = mysqli_query($cn, "CALL sp_top_productos_vendidos()");
    if (!$rTop) {
        throw new Exception('Error en sp_top_productos_vendidos: ' . mysqli_error($cn));
    }

    $top = [];
    while ($row = mysqli_fetch_assoc($rTop)) {
        $top[] = [
            'producto' => $row['producto'],
            'unidades' => (int)$row['unidades_vendidas'],
            'total'    => (float)$row['total_vendido'],
        ];
    }
    // Consumir posibles resultsets extra de CALL
    mysqli_next_result($cn);

    // Ventas mensuales (unidades + total) – también usando SP
    $rMeses = mysqli_query($cn, "CALL sp_ventas_mensuales()");
    if (!$rMeses) {
        throw new Exception('Error en sp_ventas_mensuales: ' . mysqli_error($cn));
    }

    $meses = [];
    while ($row = mysqli_fetch_assoc($rMeses)) {
        $meses[] = [
            'mes'      => $row['mes'],
            'unidades' => (int)$row['unidades'],
            'total'    => (float)$row['total'],
        ];
    }
    mysqli_next_result($cn);

    echo json_encode([
        'ok'    => true,
        'top'   => $top,
        'meses' => $meses,
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        'ok'    => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
