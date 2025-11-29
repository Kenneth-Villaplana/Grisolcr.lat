<?php
include_once('../Model/baseDatos.php');

class HistorialModel {

    //  historial completo de expedientes de un paciente
    public static function obtenerHistorialPorPaciente($pacienteId) {
        $conn = AbrirBD();

        $sql = "SELECT IdExpediente, PacienteId, Ocupacion, MotivoConsulta, UsaLentes, UltimoControl, FechaRegistro, Estado 
                FROM Expediente 
                WHERE PacienteId = ? 
                ORDER BY FechaRegistro DESC";

        $stmt = $conn->prepare($sql);
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
