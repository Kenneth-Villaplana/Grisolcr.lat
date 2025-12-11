<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/citaModel.php';

$model = new CitaModel();

// ==============================================
// AJAX: consultar horarios ocupados
// ==============================================
if (isset($_POST['action']) && $_POST['action'] === 'get_busy_slots') {

    header("Content-Type: application/json");

    $doctorId = intval($_POST['doctor_id']);
    $fecha = $_POST['date'];

    $horas = $model->obtenerHorasOcupadas($doctorId, $fecha);

    echo json_encode([
        "success" => true,
        "busy" => $horas
    ]);
    exit;
}

// ==============================================
// AGENDAR CITA
// ==============================================
if (isset($_POST['action']) && $_POST['action'] === 'agendar_cita') {

    $doctorId = intval($_POST['doctor_id']);
    $fechaHora = $_POST['fecha_hora'];
    $motivo = trim($_POST['motivo']);

    $pacienteId = $model->obtenerPacienteId($_SESSION['UsuarioID']);

    $nuevaCita = $model->insertarCita(
        $fechaHora,
        30,
        $motivo,
        "pendiente",
        $pacienteId,
        $doctorId
    );

    $mensajeExito = "Cita agendada exitosamente (#$nuevaCita)";
}

$doctores = $model->obtenerDoctores();