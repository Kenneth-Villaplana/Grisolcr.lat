<?php
include_once 'baseDatos.php';
include_once 'facturaModel.php';


class PuntoVentaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    
    public function obtenerProductos() {
        $productos = [];
        $sql = "CALL MostrarProductos()";
        if ($stmt = $this->conn->query($sql)) {
            while ($row = $stmt->fetch_assoc()) {
                $productos[] = $row;
            }
            $stmt->close();
            $this->conn->next_result(); 
        }
        return $productos;
    }
}
?>