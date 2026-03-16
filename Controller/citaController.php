<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/citaModel.php';

$model = new CitaModel();


/*
|--------------------------------------------------------------------------
| OBTENER HORAS OCUPADAS (AJAX)
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'get_busy_slots') {

    header("Content-Type: application/json");

    $doctorId = intval($_POST['doctor_id'] ?? 0);
    $fecha = $_POST['date'] ?? null;

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
|--------------------------------------------------------------------------
| AGENDAR CITA
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'agendar_cita') {

    require_once __DIR__ . '/../Model/pacienteModel.php';

    $pacienteModel = new PacienteModel();

    $doctorId  = intval($_POST['doctor_id'] ?? 0);
    $fechaHora = $_POST['fecha_hora'] ?? null;
    $motivo    = trim($_POST['motivo'] ?? '');

    $rol        = $_SESSION['RolID'] ?? null;
    $usuarioId  = $_SESSION['UsuarioID'] ?? null;

    if (!$usuarioId) {

        $_SESSION['mensaje_error'] = "Debe iniciar sesión para agendar citas.";
        header("Location: /View/iniciarSesion.php");
        exit;
    }

    try {

        if (!$doctorId || !$fechaHora || !$motivo) {
            throw new Exception("Datos incompletos para agendar la cita.");
        }

        /*
        |--------------------------------------------------------------------------
        | PACIENTE
        |--------------------------------------------------------------------------
        */
        if ($rol === 'Paciente') {

            $pacienteId = $model->obtenerPacienteId($usuarioId);

            if (!$pacienteId) {
                throw new Exception("No se encontró el paciente.");
            }

            $nuevaCita = $model->insertarCitaPaciente(
                $fechaHora,
                30,
                $motivo,
                "pendiente",
                $pacienteId,
                $doctorId
            );

            $_SESSION['mensaje_exito'] = "Cita agendada exitosamente (#$nuevaCita)";

            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | EMPLEADO
        |--------------------------------------------------------------------------
        */
        if ($rol === 'Empleado') {

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

            $_SESSION['mensaje_exito'] = "Cita agendada exitosamente (#$nuevaCita)";

            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        throw new Exception("No tienes permisos para agendar citas.");

    } catch (Exception $e) {

        $_SESSION['mensaje_error'] = "Error al agendar cita: " . $e->getMessage();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| OBTENER DOCTORES
|--------------------------------------------------------------------------
*/
try {

    $doctores = $model->obtenerDoctores();

} catch (Exception $e) {

    $doctores = [];
}


/*
|--------------------------------------------------------------------------
| FUNCIONES DE GESTIÓN
|--------------------------------------------------------------------------
*/
function puedeGestionarCitas($rolId)
{
    return $rolId != 4;
}


function obtenerCitasSegunRol()
{

    global $model;

    if (!isset($_SESSION['UsuarioID'])) {
        header('Location: /View/iniciarSesion.php');
        exit;
    }

    $usuarioId = $_SESSION['UsuarioID'];
    $rolId = $_SESSION['Id_rol'] ?? null;

    if ($rolId == 4) {
        return [];
    }

    if (($_SESSION['RolID'] ?? '') === 'Paciente') {

        return $model->obtenerCitasPaciente($usuarioId);

    } else {

        return $model->obtenerTodasLasCitas();
    }
}