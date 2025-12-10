<?php
include_once('../Model/baseDatos.php');

class HistorialModel {

    // Obtener historial usando el procedimiento almacenado
    public static function obtenerHistorialPorPaciente($pacienteId) {
        $conn = AbrirBD();

        // Llamada al procedimiento almacenado
        $stmt = $conn->prepare("CALL ObtenerHistorialPorPaciente(?)");
        $stmt->bind_param("i", $pacienteId);
        $stmt->execute();

        $result = $stmt->get_result();
        $expedientes = [];

        while ($row = $result->fetch_assoc()) {
            $expedientes[] = $row;
        }

        $stmt->close();
        CerrarBD($conn);

        return $expedientes;
    }
}
?>