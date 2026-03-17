<?php
include_once __DIR__ . '/../Model/baseDatos.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

class FacturacionController {

    private $conn;

    public function __construct() {
        $this->conn = AbrirBD();
    }

    private function limpiarResultados() {
        while ($this->conn->more_results() && $this->conn->next_result()) {;}
    }

    private function esc($valor) {
        return $this->conn->real_escape_string($valor);
    }

    public function obtenerFacturas($numero = null, $cedula = null) {
        try {
            $num = ($numero === null || $numero === '') ? null : (int)$numero;
            $ced = ($cedula === null || $cedula === '') ? null : trim((string)$cedula);

            $this->limpiarResultados();

            if ($num === null && $ced === null) {
                $sql = "CALL ObtenerFacturas(NULL, NULL)";
            } elseif ($num !== null && $ced === null) {
                $sql = "CALL ObtenerFacturas($num, NULL)";
            } elseif ($num === null && $ced !== null) {
                $cedEsc = $this->esc($ced);
                $sql = "CALL ObtenerFacturas(NULL, '$cedEsc')";
            } else {
                $cedEsc = $this->esc($ced);
                $sql = "CALL ObtenerFacturas($num, '$cedEsc')";
            }

            $res = $this->conn->query($sql);

            if (!$res) {
                throw new Exception("Error al ejecutar ObtenerFacturas: " . $this->conn->error);
            }

            $facturas = $res->fetch_all(MYSQLI_ASSOC);
            $res->free();

            $this->limpiarResultados();

            $resultadoFinal = [];

            foreach ($facturas as $f) {
                $idFactura = (int)($f["FacturaId"] ?? 0);

                if ($idFactura <= 0) {
                    continue;
                }

                $this->limpiarResultados();

                $stmt2 = $this->conn->prepare("CALL ObtenerProductosFactura(?)");
                if (!$stmt2) {
                    throw new Exception("Error prepare ObtenerProductosFactura: " . $this->conn->error);
                }

                $stmt2->bind_param("i", $idFactura);
                $stmt2->execute();
                $res2 = $stmt2->get_result();
                $prod = $res2 ? $res2->fetch_assoc() : null;

                if ($res2) {
                    $res2->free();
                }
                $stmt2->close();

                $this->limpiarResultados();

                $stmt3 = $this->conn->prepare("CALL ObtenerDetalleFactura(?)");
                if (!$stmt3) {
                    throw new Exception("Error prepare ObtenerDetalleFactura: " . $this->conn->error);
                }

                $stmt3->bind_param("i", $idFactura);
                $stmt3->execute();
                $res3 = $stmt3->get_result();
                $detalle = $res3 ? $res3->fetch_all(MYSQLI_ASSOC) : [];

                if ($res3) {
                    $res3->free();
                }
                $stmt3->close();

                $this->limpiarResultados();

                $resultadoFinal[] = array_merge($f, [
                    "Productos" => $prod["Productos"] ?? "",
                    "Detalle"   => $detalle
                ]);
            }

            return $resultadoFinal;

        } catch (\Throwable $e) {
            error_log("ERR obtenerFacturas: " . $e->getMessage());
            return [
                "__error" => true,
                "__message" => $e->getMessage()
            ];
        }
    }

    public function registrarAbono($facturaId, $monto) {
        try {
            $facturaId = (int)$facturaId;
            $monto = (float)$monto;

            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL RegistrarAbono(?, ?)");
            if (!$stmt) {
                throw new Exception($this->conn->error);
            }

            $stmt->bind_param("id", $facturaId, $monto);
            $stmt->execute();
            $stmt->close();

            $this->limpiarResultados();

            return ["success" => true];

        } catch (\Throwable $e) {
            error_log("ERR registrarAbono: " . $e->getMessage());
            return [
                "success" => false,
                "error" => $e->getMessage()
            ];
        }
    }

    public function obtenerFacturaCompleta($facturaId) {
        try {
            $facturaId = (int)$facturaId;

            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL ObtenerFacturaCompleta(?)");
            if (!$stmt) {
                throw new Exception($this->conn->error);
            }

            $stmt->bind_param("i", $facturaId);
            $stmt->execute();

            $res1 = $stmt->get_result();
            $enc = $res1 ? $res1->fetch_assoc() : null;
            if ($res1) {
                $res1->free();
            }

            $stmt->next_result();

            $res2 = $stmt->get_result();
            $detalle = $res2 ? $res2->fetch_all(MYSQLI_ASSOC) : [];
            if ($res2) {
                $res2->free();
            }

            $stmt->close();
            $this->limpiarResultados();

            return [
                "encabezado" => $enc,
                "detalle"    => $detalle
            ];

        } catch (\Throwable $e) {
            error_log("ERR obtenerFacturaCompleta: " . $e->getMessage());
            return [
                "__error" => true,
                "__message" => $e->getMessage()
            ];
        }
    }

    public function __destruct() {
        if ($this->conn) {
            CerrarBD($this->conn);
        }
    }
}

header('Content-Type: application/json; charset=utf-8');

$controller = new FacturacionController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        "ok" => true,
        "message" => "FacturacionController activo"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) {
        $input = $_POST;
    }

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

        default:
            echo json_encode([
                "error" => true,
                "message" => "Acción no válida"
            ], JSON_UNESCAPED_UNICODE);
            break;
    }

    exit;
}

echo json_encode([
    "error" => true,
    "message" => "Método no permitido"
], JSON_UNESCAPED_UNICODE);