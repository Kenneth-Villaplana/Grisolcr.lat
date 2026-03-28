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

 
    public function getProductos() {
        try {
            return $this->puntoVentaModel->obtenerProductos();

            $productosFiltrados = array_filter($productos, function($p) {
            return isset($p['Cantidad']) && intval($p['Cantidad']) > 0;
        });

        return array_values($productosFiltrados);
        
        } catch (\Throwable $e) {
            return ['error' => 'Error al obtener productos: '.$e->getMessage()];
        }
    }

public function obtenerClientePorCedula($cedula) {
    try {
        $stmt = $this->conn->prepare("CALL ObtenerClientePorCedula(?)");
        $stmt->bind_param("s", $cedula);
        $stmt->execute();

        $res = $stmt->get_result();
        $data = $res->fetch_assoc();

        $stmt->close();
        $this->conn->next_result();   

        return $data ?: [];

    } catch (\Throwable $e) {
        error_log("Error obtenerClientePorCedula: " . $e->getMessage());
        return [];
    }
}
public function obtenerStockProducto($productoId) {
    try {
        $stmt = $this->conn->prepare("CALL ObtenerStockProducto(?)");
        $stmt->bind_param("i", $productoId);
        $stmt->execute();

        $res = $stmt->get_result();
        $data = $res->fetch_assoc();

        $stmt->close();
        $this->conn->next_result();

        return $data ?: ["Stock" => 0];

    } catch (\Throwable $e) {
        error_log("Error obtenerStockProducto: " . $e->getMessage());
        return ["Stock" => 0];
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
    $telefono,
    $efectivo = 0,
    $cambio = 0
) {
    try {

        $cierreModel = new CierreCajaModel($this->conn);

        if ($cierreModel->cajaCerradaHoy()) {
            return ["error" => "CAJA_CERRADA"];
        }
        $pacienteId      = intval($pacienteId) ?: 0;
        $clienteNombre   = trim($clienteNombre);
        $empresaNombre   = trim($empresaNombre);
        $empresaIdentificacion = trim($empresaIdentificacion);
        $telefono        = trim($telefono);
        $metodoPago      = trim($metodoPago);
        $cedulaIngresada = trim($cedulaIngresada);

       
        $stmt = $this->conn->prepare(
            "CALL GenerarFacturaFlexible(?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

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
        $facturaId = $row["FacturaId"] ?? null;

        $stmt->close();
        $this->conn->next_result();

        if (!$facturaId) return false;

        $subtotal = 0;
        $descuentoTotal = 0;
        $impuestoTotal = 0;
        $detalleFactura = [];

        foreach ($productos as $p) {
             
            $preciounitario = (float)$p["precioUnitario"];
            $cantidad = (int)$p["cantidad"];
            $descuentoPorc = (float)$p["descuento"] ?? 0;
            $impuestoPorc = (float)$p["impuesto"] ?? 0;

            $totalProducto  = $p["precioUnitario"] * $p["cantidad"];
            $descuentoLinea = $totalProducto * ($descuentoPorc / 100);
            $baseLinea = $totalProducto - $descuentoLinea;
            $impuestoLinea = $baseLinea * ($impuestoPorc / 100); 
            $totalLinea = $baseLinea + $impuestoLinea;     

            $subtotal       += $totalProducto;
            $descuentoTotal += $descuentoLinea;
            $impuestoTotal  += $impuestoLinea;

            $detalleFactura[] = [
                "Nombre"         => $p["descripcion"],
                "Cantidad"       => $p["cantidad"],
                "PrecioUnitario" => $p["precioUnitario"],
                "Descuento"      => $p["descuento"],
                "Impuesto"       => $p["impuestoPorc"],
                "Total"          => number_format($totalLinea, 2, ".", "")
            ];
        }

        $total = $subtotal - $descuentoTotal + $impuestoTotal;

        $saldoPendiente = ($montoAbono > 0) ? ($total - $montoAbono) : 0;

        $productosJson = json_encode($productos, JSON_UNESCAPED_UNICODE);

        $stmt = $this->conn->prepare("CALL GenerarDetalleFactura(?, ?, ?)");
        $stmt->bind_param("isd", $facturaId, $productosJson, $saldoPendiente);
        $stmt->execute();
        $stmt->close();
        $this->conn->next_result();

        $fechaActual = date("Y-m-d H:i:s");

        return [
            "FacturaId" => $facturaId,
            "encabezado" => [
                "Id"                    => $facturaId,
                "Fecha"                 => $fechaActual,
                "Cliente"               => $clienteNombre,
                "Telefono"              => $telefono,
                "Empresa"               => $empresaNombre,
                "IdentificacionEmpresa" => $empresaIdentificacion,
                "MetodoPago"            => $metodoPago,
                "Subtotal"              => number_format($subtotal, 2, ".", ""),
                "Descuento"             => number_format($descuentoTotal, 2, ".", ""),
                "IVA"                   => number_format($impuestoTotal, 2, ".", ""),
                "Total"                 => number_format($total, 2, ".", ""),
                "Abono"                 => number_format($montoAbono, 2, ".", ""),
                "SaldoPendiente"        => number_format($saldoPendiente, 2, ".", ""),
                "Efectivo" => number_format($efectivo, 2, ".", ""),
                "Cambio"   => number_format($cambio, 2, ".", "")

            ],
            "detalle" => $detalleFactura
        ];

    } catch (\Throwable $e) {
        error_log("Error generarVenta: " . $e->getMessage());
        return false;
    }
}

    public function __destruct() {
        CerrarBD($this->conn);
    }
}


// acciones del POS

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) $input = $_POST;

    $controller = new PuntoVentaController();

    switch ($input["action"] ?? '') {

        case "obtenerProductos":
            echo json_encode($controller->getProductos(), JSON_UNESCAPED_UNICODE);
            break;

        case "obtenerCliente":
            echo json_encode($controller->ObtenerClientePorCedula($input["cedula"]), JSON_UNESCAPED_UNICODE);
            break;
    case "estadoCaja":
    $conn = AbrirBD();
    $cierreModel = new CierreCajaModel($conn);

    echo json_encode([
        "cerrada" => $cierreModel->cajaCerradaHoy()
    ], JSON_UNESCAPED_UNICODE);

    CerrarBD($conn);
    break;

    case "obtenerStock":
    echo json_encode(
        $controller->obtenerStockProducto($input["productoId"]),
        JSON_UNESCAPED_UNICODE
    );
    break;

        case "generarVenta":
        $factura = $controller->generarVenta(
            $input["clienteId"] ?? 0,
            $input["clienteNombre"] ?? "",
            $input["metodoPago"],
            $input["productos"],
            $input["facturarEmpresa"] ?? 0,
            $input["empresaNombre"] ?? "",
            $input["empresaIdentificacion"] ?? "",
            $input["facturaElectronica"] ?? 0,
            $input["montoAbono"] ?? 0,
            $input["cedulaIngresada"] ?? '',
            $input["telefono"] ?? "",
            $input["efectivo"] ?? 0,
            $input["cambio"] ?? 0
);
              if (isset($factura["error"]) && $factura["error"] === "CAJA_CERRADA") {
        echo json_encode([
            "success" => false,
            "error"   => "CAJA_CERRADA"
        ]);
        break;
        
    }

    echo json_encode([
        "success" => true,
        "factura" => $factura
    ], JSON_UNESCAPED_UNICODE);

    break;
    }
} 