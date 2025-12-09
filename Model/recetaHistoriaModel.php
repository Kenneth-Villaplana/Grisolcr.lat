<?php
include_once('../Model/baseDatos.php');

class RecetaHistorialModel {

public static function obtenerRecetasPorPaciente($pacienteId) {

        $conn = AbrirBD();
        $stmt = $conn->prepare("CALL ObtenerRecetasPaciente(?)");

        $stmt->bind_param("i", $pacienteId);
        $stmt->execute();

        $result = $stmt->get_result();
        $recetas = [];

        while ($row = $result->fetch_assoc()) {
            $recetas[] = $row;
        }

        $stmt->close();
        CerrarBD($conn);

        return $recetas;
    }
}
