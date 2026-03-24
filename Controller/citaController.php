<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/citaModel.php';

$model = new CitaModel();


/*
 AJAX - HORAS OCUPADAS
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'get_busy_slots') {

    header("Content-Type: application/json");

    $doctorId = intval($_POST['doctor_id'] ?? 0);
    $fecha    = $_POST['date'] ?? null;

    if (!$doctorId || !$fecha) {
        echo json_encode([
            "success" => false,
            "message" => "Datos incompletos"
        ]);
        exit;
    }

    try {

        $horas = $model->obtenerHorasOcupadas($doctorId, $fecha);

        echo json_encode([
            "success" => true,
            "busy" => $horas
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }

    exit;
}


/*
AGENDAR CITA
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'agendar_cita') {

    require_once __DIR__ . '/../Model/pacienteModel.php';

    $pacienteModel = new PacienteModel();

    $doctorId  = intval($_POST['doctor_id'] ?? 0);
    $fechaHora = $_POST['fecha_hora'] ?? null;
    $motivo    = trim($_POST['motivo'] ?? '');

    $rolNombre = $_SESSION['RolID'] ?? null;
    $usuarioId = $_SESSION['UsuarioID'] ?? null;

    if (!$usuarioId) {

        $_SESSION['mensaje_error'] = "Debe iniciar sesión.";
        header("Location: /View/iniciarSesion.php");
        exit;
    }

    try {

        if (!$doctorId || !$fechaHora || !$motivo) {
            throw new Exception("Datos incompletos para agendar la cita.");
        }

        /*
         PACIENTE
        */
        if ($rolNombre === 'Paciente') {

            $pacienteId = $model->obtenerPacienteId($usuarioId);

            if (!$pacienteId) {
                throw new Exception("Paciente no encontrado.");
            }

            $nuevaCita = $model->insertarCitaPaciente(
                $fechaHora,
                30,
                $motivo,
                "pendiente",
                $pacienteId,
                $doctorId
            );

        }

        /*
        EMPLEADO
        */
        else {

            $cedula   = trim($_POST['cedula'] ?? '');
            $nombre   = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $correo   = trim($_POST['correo'] ?? '');

            if (!$cedula || !$nombre || !$apellido) {
                throw new Exception("Debe completar los datos del paciente.");
            }

            $paciente = $pacienteModel->buscarPacienteParaCita($cedula);

            if ($paciente && $paciente['PacienteId']) {

                $nuevaCita = $model->insertarCitaPaciente(
                    $fechaHora,
                    30,
                    $motivo,
                    "pendiente",
                    $paciente['PacienteId'],
                    $doctorId
                );

            } else {

                $nuevaCita = $model->insertarCitaExterna(
                    $fechaHora,
                    30,
                    $motivo,
                    "pendiente",
                    $doctorId,
                    $nombre,
                    $apellido,
                    $telefono,
                    $correo
                );
            }

        }

        $_SESSION['mensaje_exito'] = "Cita agendada exitosamente (#$nuevaCita)";

    } catch (Exception $e) {

        $_SESSION['mensaje_error'] = "Error al agendar cita: " . $e->getMessage();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


/*
 OBTENER DOCTORES
*/
try {

    $doctores = $model->obtenerDoctores();

} catch (Exception $e) {

    $doctores = [];
}



function procesarAccionesCita()
{

    global $model;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $usuarioId = $_SESSION['UsuarioID'] ?? null;
    $rolNombre = $_SESSION['RolID'] ?? null;
    $rolId     = $_SESSION['Id_rol'] ?? null;

    if (!$usuarioId) {
        return;
    }

    try {

      
        if (($_POST['action'] ?? '') === 'cancelar_cita') {

            $citaId = intval($_POST['cita_id']);

            if (!$model->cancelarCitaDb($citaId)) {
                throw new Exception("Error al cancelar la cita.");
            }

            $_SESSION['mensaje_exito'] = "Cita cancelada correctamente.";
        }


        if (($_POST['action'] ?? '') === 'reagendar_cita') {

            $citaId = intval($_POST['cita_id']);
            $fecha  = $_POST['nueva_fecha'];
            $hora   = $_POST['nueva_hora'];

            $fechaHora = $fecha . " " . $hora . ":00";

            if (!$model->reagendarCitaDb($citaId, $fechaHora)) {
                throw new Exception("Error al reagendar la cita.");
            }

            $_SESSION['mensaje_exito'] = "Cita reagendada correctamente.";
        }


        if (($_POST['action'] ?? '') === 'finalizar_cita') {

            $citaId = intval($_POST['cita_id']);

            if (!$model->finalizarCitaDb($citaId)) {
                throw new Exception("Error al finalizar la cita.");
            }

            $_SESSION['mensaje_exito'] = "Cita finalizada correctamente.";
        }

    } catch (Exception $e) {

        $_SESSION['mensaje_error'] = $e->getMessage();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


function obtenerCitasSegunRol()
{

    global $model;

    if (!isset($_SESSION['UsuarioID'])) {
        header('Location: /View/iniciarSesion.php');
        exit;
    }

    $usuarioId = $_SESSION['UsuarioID'];
    $rolNombre = $_SESSION['RolID'] ?? null;
    $rolId     = $_SESSION['Id_rol'] ?? null;

    
    if ($rolId == 4) {
        return [];
    }

    try {

        if ($rolNombre === 'Paciente') {

            $citas = $model->obtenerCitasPaciente($usuarioId);

        } else {

            $citas = $model->obtenerTodasLasCitas();
        }

        if (!is_array($citas)) {
            $citas = [];
        }

        return $citas;

    } catch (Exception $e) {

        return [];
    }
}