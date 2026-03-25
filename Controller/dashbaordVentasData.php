<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../Model/baseDatos.php';

try {
    $cn = AbrirBD(); 

    
    $rTop = mysqli_query($cn, "CALL ObtenerTopProductos()");
    if (!$rTop) {
        throw new Exception('Error en sp_top_productos: ' . mysqli_error($cn));
    }

    $top = [];
    while ($row = mysqli_fetch_assoc($rTop)) {
        $top[] = [
            'producto' => $row['producto'],
            'unidades' => (int)$row['unidades_vendidas'],
            'total'    => (float)$row['total_vendido'],
        ];
    }

    mysqli_next_result($cn);

    // Ventas mensuales (unidades + total) 
    $rMeses = mysqli_query($cn, "CALL ObtenerVentasMensuales()");
    if (!$rMeses) {
        throw new Exception('Error en sp_ventas_mensuales: ' . mysqli_error(mysql: $cn));
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
