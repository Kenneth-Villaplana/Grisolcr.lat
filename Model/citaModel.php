<?php
require_once __DIR__ . "/baseDatos.php";

class CitaModel {

    public function obtenerDoctores()
    {
        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL sp_ObtenerDoctores()");
        $stmt->execute();
        $result = $stmt->get_result();

        $doctores = [];
        while ($row = $result->fetch_assoc()) {
            $doctores[] = $row;
        }

        $stmt->close();
        CerrarBD($conn);

        return $doctores;
    }

    public function obtenerPacienteId($usuarioId)
    {
        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL sp_CrearPacienteSiNoExiste(?)");
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        CerrarBD($conn);

        return $result["PacienteId"];
    }

    public function obtenerHorasOcupadas($doctorId, $fecha)
    {
        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL sp_ObtenerHorasOcupadas(?, ?)");
        $stmt->bind_param("is", $doctorId, $fecha);
        $stmt->execute();

        $result = $stmt->get_result();

        $horas = [];
        while ($row = $result->fetch_assoc()) {
            $horas[] = $row['Hora'];
        }

        $stmt->close();
        CerrarBD($conn);

        return $horas;
    }

    public function insertarCita($fecha, $duracion, $motivo, $estado, $pacienteId, $doctorId)
    {
        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL sp_InsertarCita(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissii", $fecha, $duracion, $motivo, $estado, $pacienteId, $doctorId);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        CerrarBD($conn);

        return $result["NuevaCitaId"];
    }
}
