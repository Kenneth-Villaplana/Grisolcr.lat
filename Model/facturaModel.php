<?php
include_once __DIR__ . '/baseDatos.php';

class FacturaModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function limpiarResultados(){
        while ($this->conn->more_results() && $this->conn->next_result()) {;}
    }


    public function obtenerClientePorCedula($cedula) {

        try {

            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL ObtenerClientePorCedula(?)");
            $stmt->bind_param("s", $cedula);
            $stmt->execute();

            $res = $stmt->get_result();
            $data = $res->fetch_assoc();

            $res->free();
            $stmt->close();

            $this->limpiarResultados();

            return $data ?: [];

        } catch (\Throwable $e) {

            error_log("Error obtenerClientePorCedula: " . $e->getMessage());
            return [];
        }
    }


    public function obtenerFacturas($numero = null, $cedula = null) {

        try {

            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL ObtenerFacturas(?, ?)");

            if(!$stmt){
                throw new Exception($this->conn->error);
            }

            $stmt->bind_param(
                "ss",
                $numero,
                $cedula
            );

            $stmt->execute();

            $res = $stmt->get_result();
            $facturas = $res->fetch_all(MYSQLI_ASSOC);

            $res->free();
            $stmt->close();

            $this->limpiarResultados();

            return $facturas;

        } catch (\Throwable $e) {

            error_log("Error obtenerFacturas: " . $e->getMessage());
            return [];
        }
    }


    public function obtenerFacturaEncabezado($facturaId) {

        try {

            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL ObtenerFacturaEncabezado(?)");
            $stmt->bind_param("i", $facturaId);
            $stmt->execute();

            $result = $stmt->get_result();
            $encabezado = $result->fetch_assoc();

            $result->free();
            $stmt->close();

            $this->limpiarResultados();

            return $encabezado;

        } catch (\Throwable $e) {

            error_log("Error obtenerFacturaEncabezado: " . $e->getMessage());
            return null;
        }
    }


    public function obtenerDetalleFactura($facturaId) {

        try {

            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL ObtenerDetalleFactura(?)");
            $stmt->bind_param("i", $facturaId);
            $stmt->execute();

            $res = $stmt->get_result();
            $detalle = $res->fetch_all(MYSQLI_ASSOC);

            $res->free();
            $stmt->close();

            $this->limpiarResultados();

            return $detalle;

        } catch (\Throwable $e) {

            error_log("Error obtenerDetalleFactura: " . $e->getMessage());
            return [];
        }
    }


    public function registrarAbono($facturaId, $monto) {

        try {

            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL RegistrarAbono(?, ?)");
            $stmt->bind_param("id", $facturaId, $monto);
            $stmt->execute();

            $stmt->close();

            $this->limpiarResultados();

            return true;

        } catch (\Throwable $e) {

            error_log("Error registrarAbono: " . $e->getMessage());
            return false;
        }
    }


    public function obtenerHistorialAbonos($facturaId) {

        try {

            $this->limpiarResultados();

            $stmt = $this->conn->prepare("CALL ObtenerHistorialAbonos(?)");
            $stmt->bind_param("i", $facturaId);
            $stmt->execute();

            $res = $stmt->get_result();
            $data = $res->fetch_all(MYSQLI_ASSOC);

            $res->free();
            $stmt->close();

            $this->limpiarResultados();

            return $data;

        } catch (\Throwable $e) {

            error_log("Error obtenerHistorialAbonos: " . $e->getMessage());
            return [];
        }
    }
}