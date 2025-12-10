<?php
include_once __DIR__ . '/baseDatos.php';

class RecetaModel {

    public static function obtenerRecetasPorPaciente($pacienteId) {
        $conn = AbrirBD();
        $stmt = $conn->prepare("CALL ObtenerRecetasPorPaciente(?)");
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


    public static function obtenerRecetaPorExpediente($expedienteId) {
        $conn = AbrirBD();
        $stmt = $conn->prepare("CALL ObtenerRecetaPorExpediente(?)");
        $stmt->bind_param("i", $expedienteId);
        $stmt->execute();

        $result = $stmt->get_result();
        $receta = $result->fetch_assoc();

        $stmt->close();
        CerrarBD($conn);

        return $receta;
    }
}
?>