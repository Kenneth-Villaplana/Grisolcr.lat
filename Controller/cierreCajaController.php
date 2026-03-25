<?php

session_start();

include_once __DIR__ . '/../Model/baseDatos.php';
include_once __DIR__ . '/../Model/cierreCajaModel.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = AbrirBD();
    $model = new CierreCajaModel($conn);

    // 🔥 Leer input (JSON o POST)
    $rawInput = file_get_contents("php://input");
    $input = json_decode($rawInput, true);

    if (!is_array($input)) {
        $input = $_POST;
    }

    // 🔒 Validar estructura mínima
    if (!is_array($input)) {
        throw new Exception("Entrada inválida");
    }

    $accion = $input["action"] ?? "";

    switch ($accion) {

        // =====================================================
        // RESUMEN DEL DÍA
        // =====================================================
        case "resumen":
            $fecha = date("Y-m-d");

            echo json_encode([
                "resumen" => $model->resumenDia($fecha),
                "metodos" => $model->metodosPago($fecha)
            ], JSON_UNESCAPED_UNICODE);
            break;

        // =====================================================
        // CERRAR CAJA
        // =====================================================
        case "cerrar":

            if (!isset($_SESSION['UsuarioID'])) {
                http_response_code(401);
                echo json_encode(["error" => "Sesión no válida"]);
                exit;
            }

            $fecha = date("Y-m-d");

            // 🔒 Validar doble cierre
            if ($model->existeCierreHoy($fecha)) {
                echo json_encode([
                    "error" => "La caja ya fue cerrada hoy, no se puede cerrar nuevamente."
                ]);
                exit;
            }

            // 🔥 Campos numéricos (SIN fecha)
            $campos = [
                "facturas","subtotal","descuento","iva","totalFacturado","totalCobrado",
                "efectivo","tarjeta","sinpe","transferencia",
                "efectivoEsperado","efectivoContado","diferencia"
            ];

            foreach ($campos as $campo) {
                if (!isset($input[$campo]) || $input[$campo] === "") {
                    $input[$campo] = 0;
                }
            }

            // 🔥 Forzar fecha correcta (CRÍTICO FIX)
            $input["fecha"] = $fecha;

            // 🔥 Usuario
            $input["usuarioId"] = intval($_SESSION['UsuarioID']);

            // 🔥 Normalizar tipos (PRO)
            foreach ($input as $k => $v) {
                if (is_numeric($v)) {
                    $input[$k] = floatval($v);
                }
            }

            // 🔥 Guardar cierre
            $model->guardarCierre($input);

            echo json_encode([
                "success" => true,
                "message" => "Cierre de caja realizado correctamente",
                "fecha" => $fecha
            ]);
            break;

        // =====================================================
        // HISTORIAL
        // =====================================================
        case "historial":
            echo json_encode(
                $model->obtenerHistorial(),
                JSON_UNESCAPED_UNICODE
            );
            break;

        default:
            http_response_code(400);
            echo json_encode(["error" => "Acción no válida"]);
    }

    CerrarBD($conn);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "error" => "Error interno en el servidor",
        "detalle" => $e->getMessage()
    ]);
}
?>