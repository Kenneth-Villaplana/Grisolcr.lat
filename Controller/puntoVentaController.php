<?php
include_once __DIR__ . '/../Model/puntoVentaModel.php';
include_once __DIR__ . '/../Model/facturaModel.php';
include_once __DIR__ . '/../Model/baseDatos.php';
include_once __DIR__ . '/../Model/cierreCajaModel.php';

class PuntoVentaController {

    private $puntoVentaModel;
    private $facturaModel;
    private $conn;

    public function __construct() {
        $this->conn = AbrirBD();
        $this->puntoVentaModel = new PuntoVentaModel($this->conn);
        $this->facturaModel = new FacturaModel($this->conn);
    }

    private function limpiarResultados() {
        while ($this->conn->more_results() && $this->conn->next_result()) {;}
    }

    public function getProductos() {
        try {
            return $this->puntoVentaModel->obtenerProductos();
        } catch (\Throwable $e) {
            return ['error' => 'Error al obtener productos: ' . $e->getMessage()];
        }
    }

    public function obtenerClientePorCedula($cedula) {
        try {
            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL ObtenerClientePorCedula(?)");

            if (!$stmt) {
                throw new Exception($this->conn->error);
            }

            $stmt->bind_param("s", $cedula);
            $stmt->execute();

            $res = $stmt->get_result();
            $data = $res ? $res->fetch_assoc() : null;

            if ($res) {
                $res->free();
            }

            $stmt->close();
            $this->limpiarResultados();

            return $data ?: [];

        } catch (\Throwable $e) {
            error_log("Error obtenerClientePorCedula: " . $e->getMessage());
            return [];
        }
    }

    public function generarVenta(
        $pacienteId,
        $clienteNombre,
        $metodoPago,
        $productos,
        $facturarEmpresa,
        $empresaNombre,
        $empresaIdentificacion,
        $facturaElectronica,
        $montoAbono,
        $cedulaIngresada,
        $telefono
    ) {
        try {
            $cierreModel = new CierreCajaModel($this->conn);

            if ($cierreModel->cajaCerradaHoy()) {
                return ["error" => "CAJA_CERRADA"];
            }

            $pacienteId = intval($pacienteId) ?: 0;
            $clienteNombre = trim((string)$clienteNombre);
            $empresaNombre = trim((string)$empresaNombre);
            $empresaIdentificacion = trim((string)$empresaIdentificacion);
            $telefono = trim((string)$telefono);
            $metodoPago = trim((string)$metodoPago);
            $cedulaIngresada = trim((string)$cedulaIngresada);
            $facturaElectronica = intval($facturaElectronica);
            $montoAbono = floatval($montoAbono);

            if (!is_array($productos) || count($productos) === 0) {
                return ["error" => "SIN_PRODUCTOS"];
            }

            $this->conn->begin_transaction();

            $this->limpiarResultados();

            $stmt = $this->conn->prepare(
                "CALL GenerarFacturaFlexible(?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                throw new Exception($this->conn->error);
            }

            $stmt->bind_param(
                "isssssids",
                $pacienteId,
                $clienteNombre,
                $cedulaIngresada,
                $metodoPago,
                $empresaNombre,
                $empresaIdentificacion,
                $facturaElectronica,
                $montoAbono,
                $telefono
            );

            $stmt->execute();

            $res = $stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;

            if ($res) {
                $res->free();
            }

            $facturaId = $row["FacturaId"] ?? null;

            $stmt->close();
            $this->limpiarResultados();

            if (!$facturaId) {
                throw new Exception("No se obtuvo FacturaId al generar el encabezado.");
            }

            $subtotal = 0;
            $descuentoTotal = 0;
            $detalleFactura = [];

            foreach ($productos as $p) {
                $precioUnitario = floatval($p["precioUnitario"] ?? 0);
                $cantidad = intval($p["cantidad"] ?? 0);
                $descuento = floatval($p["descuento"] ?? 0);
                $descripcion = trim((string)($p["descripcion"] ?? ''));

                $totalProducto = $precioUnitario * $cantidad;
                $descuentoLinea = $totalProducto * ($descuento / 100);

                $subtotal += $totalProducto;
                $descuentoTotal += $descuentoLinea;

                $detalleFactura[] = [
                    "Nombre" => $descripcion,
                    "Cantidad" => $cantidad,
                    "PrecioUnitario" => number_format($precioUnitario, 2, ".", ""),
                    "Descuento" => $descuento,
                    "Total" => number_format($totalProducto - $descuentoLinea, 2, ".", "")
                ];
            }

            $base = $subtotal - $descuentoTotal;
            $iva = $base * 0.13;
            $total = $base + $iva;
            $saldoPendiente = ($montoAbono > 0) ? ($total - $montoAbono) : 0;

            $productosJson = json_encode($productos, JSON_UNESCAPED_UNICODE);

            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL GenerarDetalleFactura(?, ?, ?)");

            if (!$stmt) {
                throw new Exception($this->conn->error);
            }

            $stmt->bind_param("isd", $facturaId, $productosJson, $saldoPendiente);
            $stmt->execute();
            $stmt->close();

            $this->limpiarResultados();

            $this->conn->commit();

            $fechaActual = date("Y-m-d H:i:s");

            return [
                "FacturaId" => $facturaId,
                "encabezado" => [
                    "Id" => $facturaId,
                    "Fecha" => $fechaActual,
                    "Cliente" => $clienteNombre,
                    "Telefono" => $telefono,
                    "Empresa" => $empresaNombre,
                    "IdentificacionEmpresa" => $empresaIdentificacion,
                    "MetodoPago" => $metodoPago,
                    "Subtotal" => number_format($subtotal, 2, ".", ""),
                    "Descuento" => number_format($descuentoTotal, 2, ".", ""),
                    "IVA" => number_format($iva, 2, ".", ""),
                    "Total" => number_format($total, 2, ".", ""),
                    "Abono" => number_format($montoAbono, 2, ".", ""),
                    "SaldoPendiente" => number_format($saldoPendiente, 2, ".", "")
                ],
                "detalle" => $detalleFactura
            ];

        } catch (\Throwable $e) {
            $this->conn->rollback();
            error_log("Error generarVenta: " . $e->getMessage());

            return [
                "error" => "GENERAR_VENTA_ERROR",
                "message" => $e->getMessage()
            ];
        }
    }

    public function __destruct() {
        if ($this->conn) {
            CerrarBD($this->conn);
        }
    }
}

/* acciones del POS */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json; charset=utf-8');

    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) $input = $_POST;

    $controller = new PuntoVentaController();

    switch ($input["action"] ?? '') {

        case "obtenerProductos":
            echo json_encode($controller->getProductos(), JSON_UNESCAPED_UNICODE);
            break;

        case "obtenerCliente":
            echo json_encode(
                $controller->obtenerClientePorCedula($input["cedula"] ?? ""),
                JSON_UNESCAPED_UNICODE
            );
            break;

        case "estadoCaja":
            $conn = AbrirBD();
            $cierreModel = new CierreCajaModel($conn);

            echo json_encode([
                "cerrada" => $cierreModel->cajaCerradaHoy()
            ], JSON_UNESCAPED_UNICODE);

            CerrarBD($conn);
            break;

        case "generarVenta":
            $factura = $controller->generarVenta(
                $input["clienteId"] ?? 0,
                $input["clienteNombre"] ?? "",
                $input["metodoPago"] ?? "",
                $input["productos"] ?? [],
                $input["facturarEmpresa"] ?? 0,
                $input["empresaNombre"] ?? "",
                $input["empresaIdentificacion"] ?? "",
                $input["facturaElectronica"] ?? 0,
                $input["montoAbono"] ?? 0,
                $input["cedulaIngresada"] ?? '',
                $input["telefono"] ?? ""
            );

            if (isset($factura["error"])) {
                echo json_encode([
                    "success" => false,
                    "error" => $factura["error"],
                    "message" => $factura["message"] ?? null
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            echo json_encode([
                "success" => true,
                "factura" => $factura
            ], JSON_UNESCAPED_UNICODE);
            break;

        default:
            echo json_encode([
                "success" => false,
                "error" => "ACCION_INVALIDA"
            ], JSON_UNESCAPED_UNICODE);
            break;
    }

    exit;
}