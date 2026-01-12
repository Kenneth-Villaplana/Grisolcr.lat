<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include_once __DIR__ . '/../Model/baseDatos.php';
include_once __DIR__ . '/../Model/cierreCajaModel.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = AbrirBD();
    $model = new CierreCajaModel($conn);

    $input = json_decode(file_get_contents("php://input"), true);
    if(!is_array($input)) $input = $_POST;

    $accion = $input["action"] ?? "";

    switch($accion) {

        case "resumen":
            $fecha = date("Y-m-d");
            echo json_encode([
                "resumen" => $model->resumenDia($fecha),
                "metodos" => $model->metodosPago($fecha)
            ], JSON_UNESCAPED_UNICODE);
            break;

        case "cerrar":
            if(!isset($_SESSION['UsuarioID'])) {
                http_response_code(401);
                echo json_encode(["error"=>"Sesión no válida"]);
                exit;
            }

            $fecha = date("Y-m-d");

            if($model->existeCierreHoy($fecha)) {
                echo json_encode(["error"=>"La caja ya fue cerrada hoy, no se puede cerrar nuevamente."]);
                exit;
            }

            $campos = ["fecha","facturas","subtotal","descuento","iva","totalFacturado","totalCobrado",
                       "efectivo","tarjeta","sinpe","transferencia","efectivoEsperado","efectivoContado","diferencia"];
            foreach($campos as $campo) {
                if(!isset($input[$campo])) $input[$campo] = 0;
            }

            $input['usuarioId'] = intval($_SESSION['UsuarioID']);

            $model->guardarCierre($input);
            echo json_encode(["success"=>true]);
            break;

        case "historial":
            echo json_encode($model->obtenerHistorial(), JSON_UNESCAPED_UNICODE);
            break;

        default:
            http_response_code(400);
            echo json_encode(["error"=>"Acción no válida"]);
    }

    CerrarBD($conn);

} catch(Throwable $e) {
    http_response_code(500);
    echo json_encode(["error"=>"Error interno en el servidor","detalle"=>$e->getMessage()]);
}
?>
