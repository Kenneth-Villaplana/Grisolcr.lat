<?php
include_once 'baseDatos.php';
class FacturaModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
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
    public function obtenerFacturas($numero = null, $cedula = null) {
        $stmt = $this->conn->prepare("CALL ObtenerFacturas(?, ?)");
        $stmt->bind_param(
            "is",
            $numero,
            $cedula
        );
        $stmt->execute();
        $res = $stmt->get_result();
        $facturas = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $facturas;
    }

    public function obtenerFacturaEncabezado($facturaId) {
        $stmt = $this->conn->prepare("CALL ObtenerFacturaEncabezado(?)");
        $stmt->bind_param("i", $facturaId);
        $stmt->execute();
        $result = $stmt->get_result();
        $encabezado = $result->fetch_assoc();
        $stmt->close();

        return $encabezado;
    }

   
    public function obtenerDetalleFactura($facturaId) {
        $stmt = $this->conn->prepare("CALL ObtenerDetalleFactura(?)");
        $stmt->bind_param("i", $facturaId);
        $stmt->execute();
        $res = $stmt->get_result();
        $detalle = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $detalle;
    }

   
    public function registrarAbono($facturaId, $monto) {
        $stmt = $this->conn->prepare("CALL RegistrarAbono(?, ?)");
        $stmt->bind_param("id", $facturaId, $monto);
        $stmt->execute();
        $stmt->close();

        return true;
    }


 public function obtenerHistorialAbonos($facturaId) {
    try {
        $stmt = $this->conn->prepare("CALL ObtenerHistorialAbonos(?)");
        $stmt->bind_param("i", $facturaId);
        $stmt->execute();

        $res = $stmt->get_result();
        $data = $res->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        $this->conn->next_result();

        return $data;

    } catch (\Throwable $e) {
        error_log("Error obtenerHistorialAbonos: " . $e->getMessage());
        return [];
    }
}
}