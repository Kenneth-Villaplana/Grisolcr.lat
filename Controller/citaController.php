<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/citaModel.php';

$model = new CitaModel();


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


if (isset($_POST['action']) && $_POST['action'] === 'agendar_cita') {

    require_once __DIR__ . '/../Model/pacienteModel.php';
    $pacienteModel = new PacienteModel();

    $doctorId   = intval($_POST['doctor_id']);
    $fechaHora  = $_POST['fecha_hora'];
    $motivo     = trim($_POST['motivo']);


    $rol         = $_SESSION['RolID'] ?? null;          
    $empleadoRol = $_SESSION['EmpleadoRol'] ?? null;    
    $usuarioId  = $_SESSION['UsuarioID'];

    try {

        if ($rol === 'Paciente') {

        
            $pacienteId = $model->obtenerPacienteId($usuarioId);

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


        if ($rol === 'Empleado') {

  
            $cedula    = trim($_POST['cedula']);
            $nombre    = trim($_POST['nombre']);
            $apellido  = trim($_POST['apellido']);
            $telefono  = trim($_POST['telefono']);
            $correo    = trim($_POST['correo']);

            $paciente = $pacienteModel->buscarPacienteParaCita($cedula);

            if ($paciente && $paciente['PacienteId'] !== null) {


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
$doctores = $model->obtenerDoctores();



function puedeGestionarCitas($rolId)
{
    return $rolId != 4;
}

function obtenerCitasSegunRol()
{
    global $model;

    if (!isset($_SESSION['UsuarioID'])) {
        header('Location: /login');
        exit;
    }

    $usuarioId = $_SESSION['UsuarioID'];
    $rolId = $_SESSION['Id_rol'] ?? null;

    if ($rolId == 4) {
        return [];
    }

    if (isset($_SESSION['RolID']) && $_SESSION['RolID'] === 'Paciente') {

        return $model->obtenerCitasPaciente($usuarioId);

    } else {

        return $model->obtenerTodasLasCitas();
    }
}



function procesarAccionesCita()
{
    global $model;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $usuarioId = $_SESSION['UsuarioID'];
    $rolId = $_SESSION['Id_rol'];

    try {


        if ($_POST['action'] === 'cancelar_cita') {

            $citaId = intval($_POST['cita_id']);

            if (!puedeGestionarCitas($rolId)) {
                throw new Exception("No tienes permisos para cancelar citas");
            }


            if ($_SESSION['RolID'] === 'Paciente') {
                $cita = $model->obtenerCitaPorPacienteYUsuario($citaId, $usuarioId);
            } else {
                $cita = $model->obtenerCitaPorId($citaId);
            }

            if (!$cita) {
                throw new Exception("Cita no encontrada o no tienes permisos");
            }

            if (!in_array($cita['Estado'], ['pendiente', 'confirmada'])) {
                throw new Exception("La cita no puede cancelarse en su estado actual");
            }

            if (!$model->cancelarCitaDb($citaId)) {
                throw new Exception("Error al cancelar la cita");
            }

            $_SESSION['mensaje_exito'] = "Cita cancelada exitosamente";
        }

    
        if ($_POST['action'] === 'reagendar_cita') {

            $citaId = intval($_POST['cita_id']);
            $nuevaFecha = $_POST['nueva_fecha'];
            $nuevaHora = $_POST['nueva_hora'];

            $nuevaFechaHora = $nuevaFecha . ' ' . $nuevaHora . ':00';

            if (!puedeGestionarCitas($rolId)) {
                throw new Exception("No tienes permisos para reagendar citas");
            }

            if (strtotime($nuevaFechaHora) <= time()) {
                throw new Exception("La nueva fecha y hora deben ser futuras");
            }

    
            if ($_SESSION['RolID'] === 'Paciente') {
                $cita = $model->obtenerCitaPorPacienteYUsuario($citaId, $usuarioId);
            } else {
                $cita = $model->obtenerCitaPorId($citaId);
            }

            if (!$cita) {
                throw new Exception("Cita no encontrada o no tienes permisos");
            }

            if (!in_array($cita['Estado'], ['pendiente', 'confirmada'])) {
                throw new Exception("La cita no puede reagendarse en su estado actual");
            }

            if (!$model->reagendarCitaDb($citaId, $nuevaFechaHora)) {
                throw new Exception("Error al reagendar la cita");
            }

            $_SESSION['mensaje_exito'] = "Cita reagendada exitosamente";
        }

if ($_POST['action'] === 'finalizar_cita') {

    $citaId = intval($_POST['cita_id']);

    if (!puedeGestionarCitas($rolId)) {
        throw new Exception("No tienes permisos para finalizar citas");
    }

 
    if ($_SESSION['RolID'] === 'Paciente') {
        throw new Exception("Los pacientes no pueden finalizar citas");
    } else {
        $cita = $model->obtenerCitaPorId($citaId);
    }

    if (!$cita) {
        throw new Exception("Cita no encontrada");
    }


    if (!in_array($cita['Estado'], ['pendiente', 'confirmada'])) {
        throw new Exception("Solo se pueden finalizar citas pendientes o confirmadas");
    }

    if (!$model->finalizarCitaDb($citaId)) {
        throw new Exception("Error al finalizar la cita");
    }

    $_SESSION['mensaje_exito'] = "Cita finalizada exitosamente";
}

    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = $e->getMessage();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
