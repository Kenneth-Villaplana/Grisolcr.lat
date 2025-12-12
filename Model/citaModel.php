<?php
include_once __DIR__ . '/baseDatos.php';

class CitaModel {



   public function obtenerDoctores()
{
    $conn = AbrirBD();

    $stmt = $conn->prepare("CALL sp_ObtenerDoctores()");
    $stmt->execute();

    $result = $stmt->get_result();
    $doctores = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $doctores[] = $row;
        }
    }


    $stmt->close();
    while ($conn->more_results() && $conn->next_result()) {;}

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

    public function insertarCitaExterna(
    $fecha,
    $duracion,
    $motivo,
    $estado,
    $doctorId,
    $nombreExt,
    $apellidoExt,
    $telefonoExt,
    $correoExt
) {
    $conn = AbrirBD();

    $stmt = $conn->prepare("CALL sp_InsertarCitaExterna(?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sississss",
        $fecha,
        $duracion,
        $motivo,
        $estado,
        $doctorId,
        $nombreExt,
        $apellidoExt,
        $telefonoExt,
        $correoExt
    );

    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    CerrarBD($conn);

    return $result["NuevaCitaId"];
}


public function insertarCitaPaciente($fecha, $duracion, $motivo, $estado, $pacienteId, $doctorId)
{
    $conn = AbrirBD();

    $stmt = $conn->prepare("CALL sp_InsertarCitaPaciente(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissii", $fecha, $duracion, $motivo, $estado, $pacienteId, $doctorId);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    $stmt->close();
    CerrarBD($conn);

    return $result["NuevaCitaId"];
}



    public function obtenerCitasPaciente($usuarioId)
    {
        $conn = AbrirBD();
        $citas = [];

        $stmt = $conn->prepare("CALL sp_obtener_citas_paciente(?)");
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $citas[] = $row;
        }

        $stmt->close();
        CerrarBD($conn);

        return $citas;
    }

    public function obtenerTodasLasCitas()
    {
        $conn = AbrirBD();
        $citas = [];

        $stmt = $conn->prepare("CALL sp_obtener_citas_todas()");
        $stmt->execute();

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $citas[] = $row;
        }

        $stmt->close();
        CerrarBD($conn);

        return $citas;
    }

    public function obtenerCitaPorId($citaId)
    {
        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL sp_obtener_cita_por_id(?)");
        $stmt->bind_param("i", $citaId);
        $stmt->execute();

        $result = $stmt->get_result();
        $cita = $result->fetch_assoc();

        $stmt->close();
        CerrarBD($conn);

        return $cita;
    }

    public function obtenerCitaPorPacienteYUsuario($citaId, $usuarioId)
    {
        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL sp_obtener_cita_paciente_usuario(?, ?)");
        $stmt->bind_param("ii", $citaId, $usuarioId);
        $stmt->execute();

        $result = $stmt->get_result();
        $cita = $result->fetch_assoc();

        $stmt->close();
        CerrarBD($conn);

        return $cita;
    }

    public function cancelarCitaDb($citaId)
    {
        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL sp_cancelar_cita(?)");
        $stmt->bind_param("i", $citaId);
        $ok = $stmt->execute();

        $stmt->close();
        CerrarBD($conn);

        return $ok;
    }

    public function reagendarCitaDb($citaId, $nuevaFechaHora)
    {
        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL sp_reagendar_cita(?, ?)");
        $stmt->bind_param("is", $citaId, $nuevaFechaHora);
        $ok = $stmt->execute();

        $stmt->close();
        CerrarBD($conn);

        return $ok;
    }

    public function finalizarCitaDb($citaId)
    {
        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL sp_FinalizarCita(?)");
        $stmt->bind_param("i", $citaId);
        $success = $stmt->execute();

        $stmt->close();
        CerrarBD($conn);

        return $success;
    }
}
