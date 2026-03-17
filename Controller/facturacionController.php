<?php
include_once __DIR__ . '/../Model/baseDatos.php';

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

class FacturacionController {

    private $conn;

    public function __construct() {
        $this->conn = AbrirBD();
    }

    private function limpiarResultados() {
        while ($this->conn->more_results() && $this->conn->next_result()) {;}
    }

    public function obtenerFacturas($numero = null, $cedula = null) {

        try {

            $num = ($numero === null || $numero === '') ? null : $numero;
            $ced = ($cedula === null || $cedula === '') ? null : $cedula;

            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL ObtenerFacturas(?, ?)");

            if(!$stmt){
                throw new Exception($this->conn->error);
            }

            $stmt->bind_param("ss", $num, $ced);
            $stmt->execute();

            $res = $stmt->get_result();
            $facturas = $res->fetch_all(MYSQLI_ASSOC);

            $res->free();
            $stmt->close();

            $this->limpiarResultados();

            $resultadoFinal = [];

            foreach ($facturas as $f) {

                $idFactura = (int)$f["FacturaId"];

                /* PRODUCTOS */

                $stmt2 = $this->conn->prepare("CALL ObtenerProductosFactura(?)");

                if(!$stmt2){
                    throw new Exception($this->conn->error);
                }

                $stmt2->bind_param("i", $idFactura);
                $stmt2->execute();

                $res2 = $stmt2->get_result();
                $prod = $res2->fetch_assoc();

                $res2->free();
                $stmt2->close();

                $this->limpiarResultados();

                /* DETALLE */

                $stmt3 = $this->conn->prepare("CALL ObtenerDetalleFactura(?)");

                if(!$stmt3){
                    throw new Exception($this->conn->error);
                }

                $stmt3->bind_param("i", $idFactura);
                $stmt3->execute();

                $res3 = $stmt3->get_result();
                $detalle = $res3->fetch_all(MYSQLI_ASSOC);

                $res3->free();
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
            return [];
        }
    }


    public function registrarAbono($facturaId, $monto) {

        try {

            $facturaId = (int)$facturaId;
            $monto = (float)$monto;

            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL RegistrarAbono(?, ?)");

            if(!$stmt){
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

            if(!$stmt){
                throw new Exception($this->conn->error);
            }

            $stmt->bind_param("i", $facturaId);
            $stmt->execute();

            /* ENCABEZADO */

            $res1 = $stmt->get_result();
            $enc = $res1->fetch_assoc();
            $res1->free();

            $stmt->next_result();

            /* DETALLE */

            $res2 = $stmt->get_result();
            $detalle = $res2->fetch_all(MYSQLI_ASSOC);
            $res2->free();

            $stmt->close();

            $this->limpiarResultados();

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


/* API */

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