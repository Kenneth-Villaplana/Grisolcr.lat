<?php
include_once __DIR__ . '/../Model/baseDatos.php';

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

class FacturacionController {

    private $conn;

    public function __construct() {
        $this->conn = AbrirBD();
    }

    public function obtenerFacturas($numero = null, $cedula = null) {
        try {
            $num = ($numero === null || $numero === '') ? null : (int)$numero;
            $ced = ($cedula === null || $cedula === '') ? null : $cedula;

            // Limpia posibles resultados anteriores
            $this->conn->next_result();

            $stmt = $this->conn->prepare("CALL ObtenerFacturas(?, ?)");
            $stmt->bind_param("is", $num, $ced);
            $stmt->execute();

            $res = $stmt->get_result();
            $facturas = $res->fetch_all(MYSQLI_ASSOC);
            $res->free_result();
            $stmt->close();
            $this->conn->next_result();

            $resultadoFinal = [];

            foreach ($facturas as $f) {
                $idFactura = (int)$f["FacturaId"];

                // Productos concatenados
                $stmt2 = $this->conn->prepare("CALL ObtenerProductosFactura(?)");
                $stmt2->bind_param("i", $idFactura);
                $stmt2->execute();
                $res2 = $stmt2->get_result();
                $prod = $res2->fetch_assoc();
                $res2->free_result();
                $stmt2->close();
                $this->conn->next_result();

                // Detalle
                $stmt3 = $this->conn->prepare("CALL ObtenerDetalleFactura(?)");
                $stmt3->bind_param("i", $idFactura);
                $stmt3->execute();
                $res3 = $stmt3->get_result();
                $detalle = $res3->fetch_all(MYSQLI_ASSOC);
                $res3->free_result();
                $stmt3->close();
                $this->conn->next_result();

                // une info
                $resultadoFinal[] = array_merge($f, [
                    "Productos" => $prod["Productos"] ?? "",
                    "Detalle"   => $detalle
                ]);
            }

            return $resultadoFinal;

        } catch (\Throwable $e) {
            error_log("ERR obtenerFacturas: " . $e->getMessage());
            return [];
        }
    }


    public function registrarAbono($facturaId, $monto) {
        try {
            $facturaId = (int)$facturaId;
            $monto = (float)$monto;

            $this->conn->next_result();

            $stmt = $this->conn->prepare("CALL RegistrarAbono(?, ?)");
            $stmt->bind_param("id", $facturaId, $monto);
            $stmt->execute();
            $stmt->close();

            $this->conn->next_result();

            return ["success" => true];

        } catch (\Throwable $e) {
            error_log("ERR registrarAbono: " . $e->getMessage());
            return ["success" => false, "error" => $e->getMessage()];
        }
    }


    public function obtenerFacturaCompleta($facturaId) {
        try {
            $facturaId = (int)$facturaId;

            $this->conn->next_result();

            $stmt = $this->conn->prepare("CALL ObtenerFacturaCompleta(?)");
            $stmt->bind_param("i", $facturaId);
            $stmt->execute();

           
            $res1 = $stmt->get_result();
            $enc = $res1->fetch_assoc();
            $res1->free_result();

           
            $stmt->next_result();

            
            $res2 = $stmt->get_result();
            $detalle = $res2->fetch_all(MYSQLI_ASSOC);
            $res2->free_result();

            $stmt->close();
            $this->conn->next_result();

            return [
                "encabezado" => $enc,
                "detalle"    => $detalle
            ];

        } catch (\Throwable $e) {
            error_log("ERR obtenerFacturaCompleta: " . $e->getMessage());
            return null;
        }
    }

    public function __destruct() {
        if ($this->conn) {
            CerrarBD($this->conn);
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) $input = $_POST;

    $controller = new FacturacionController();
    $action = $input["action"] ?? '';

    switch ($action) {

        case "obtenerFacturas":
            echo json_encode(
                $controller->obtenerFacturas(
                    $input["numero"] ?? null,
                    $input["cedula"] ?? null
                ),
                JSON_UNESCAPED_UNICODE
            );
            break;

        case "registrarAbono":
            echo json_encode(
                $controller->registrarAbono(
                    $input["facturaId"] ?? 0,
                    $input["monto"] ?? 0
                ),
                JSON_UNESCAPED_UNICODE
            );
            break;

        case "obtenerFacturaCompleta":
            echo json_encode(
                $controller->obtenerFacturaCompleta(
                    $input["facturaId"] ?? 0
                ),
                JSON_UNESCAPED_UNICODE
            );
            break;
    }

    exit;
}