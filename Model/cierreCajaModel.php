<?php
include_once('../Model/baseDatos.php');

class CierreCajaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function resumenDia($fecha) {
        $stmt = $this->conn->prepare("CALL CC_ResumenDia(?)");
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $this->conn->next_result();
        return $res;
    }

    public function metodosPago($fecha) {
        $data = [];
        $stmt = $this->conn->prepare("CALL CC_MetodosPago(?)");
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $data[$row["MetodoPago"]] = $row["Monto"];
        }
        $stmt->close();
        $this->conn->next_result();
        return $data;
    }

    public function existeCierreHoy($fecha) {
        $stmt = $this->conn->prepare("CALL sp_VerificarCierreCaja(?)");
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        $res = $stmt->get_result();
        $existe = $res && $res->num_rows > 0;
        $stmt->close();
        $this->conn->next_result();
        return $existe;
    }

    public function guardarCierre($data) {
        $stmt = $this->conn->prepare("CALL CC_GuardarCierre(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param(
            "siddidddddddddd",
            $data["fecha"],
            $data["facturas"],
            $data["subtotal"],
            $data["descuento"],
            $data["iva"],
            $data["totalFacturado"],
            $data["totalCobrado"],
            $data["efectivo"],
            $data["tarjeta"],
            $data["sinpe"],
            $data["transferencia"],
            $data["efectivoEsperado"],
            $data["efectivoContado"],
            $data["diferencia"],
            $data["usuarioId"]
        );
        $stmt->execute();
        $stmt->close();
        while($this->conn->more_results() && $this->conn->next_result());
        return true;
    }

public function obtenerHistorial() {
    $cierres = [];

    $stmt = $this->conn->prepare("CALL ObtenerHistorialCierres()");
    if(!$stmt) {
        throw new Exception($this->conn->error);
    }

    $stmt->execute();

    $result = $stmt->get_result();
   
    while ($row = $result->fetch_assoc()) {
        $cierres[] = $row;
    }

    $stmt->close();
    $this->conn->next_result();

    return $cierres;
}

public function cajaCerradaHoy() {

    $fechaHoy = date("Y-m-d");

    $stmt = $this->conn->prepare("CALL CajaCerradaPorFecha(?)");
    $stmt->bind_param("s", $fechaHoy);
    $stmt->execute();

    $res = $stmt->get_result();
    $cerrada = $res && $res->num_rows > 0;

    $stmt->close();
    $this->conn->next_result();

    return $cerrada;
}
}
?>
