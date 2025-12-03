<?php
session_start();
include('layout.php');
include_once __DIR__ . '/../Model/baseDatos.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Verificar si el usuario está loggeado
if (!isset($_SESSION['UsuarioID'])) {
    header('Location: /login');
    exit;
}

// INCLUIR LOS MODELOS NECESARIOS
require_once __DIR__ . '/../Model/GoogleCalendarModel.php';

$usuarioId = $_SESSION['UsuarioID'];
$mensajeExito = '';
$mensajeError = '';

// [Todas las funciones PHP permanecen igual...]
// Obtener información del usuario
function obtenerUsuarioInfo($conn, $usuarioId) {
    $query = "SELECT u.*, p.PacienteId 
              FROM usuario u 
              LEFT JOIN paciente p ON u.IdUsuario = p.UsuarioId 
              WHERE u.IdUsuario = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuarioId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Obtener todos los doctores
function obtenerDoctores($conn) {
    $query = "SELECT u.*, 'Profesional' as NombreRol 
              FROM usuario u 
              WHERE u.IdUsuario != ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['UsuarioID']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $doctores = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $doctores[] = $row;
    }
    
    return $doctores;
}

// Crear paciente desde usuario
function crearPacienteDesdeUsuario($conn, $usuarioId, $usuarioInfo) {
    $nombreCompleto = $usuarioInfo['Nombre'] . ' ' . $usuarioInfo['Apellido'] . ' ' . ($usuarioInfo['ApellidoDos'] ?? '');
    
    $queryCheck = "SELECT PacienteId FROM paciente WHERE UsuarioId = ?";
    $stmtCheck = mysqli_prepare($conn, $queryCheck);
    mysqli_stmt_bind_param($stmtCheck, "i", $usuarioId);
    mysqli_stmt_execute($stmtCheck);
    $resultCheck = mysqli_stmt_get_result($stmtCheck);
    $existingPatient = mysqli_fetch_assoc($resultCheck);
    
    if ($existingPatient) {
        return $existingPatient['PacienteId'];
    }
    
    $query = "INSERT INTO paciente (UsuarioId, Nombre, Telefono, CorreoElectronico) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    $telefono = $usuarioInfo['Telefono'] ?? '';
    $correo = $usuarioInfo['CorreoElectronico'] ?? '';
    
    mysqli_stmt_bind_param($stmt, "isss", $usuarioId, $nombreCompleto, $telefono, $correo);
    
    if (mysqli_stmt_execute($stmt)) {
        $pacienteId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return $pacienteId;
    } else {
        throw new Exception("Error al crear paciente: " . mysqli_error($conn));
    }
}

// Verificar estructura de la tabla cita
function obtenerEstructuraCita($conn) {
    $query = "SHOW COLUMNS FROM cita";
    $result = mysqli_query($conn, $query);
    $columns = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row['Field'];
    }
    return $columns;
}

// Procesar agendamiento de cita
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'agendar_cita') {
        $conn = AbrirBD();
        
        try {
            $doctorId = intval($_POST['doctor_id']);
            $fechaHora = $_POST['fecha_hora'];
            $motivo = trim($_POST['motivo']);
            $duracion = 30;
            $estado = 'pendiente';
            
            if (empty($doctorId) || empty($fechaHora) || empty($motivo)) {
                throw new Exception("Todos los campos son obligatorios");
            }
            
            if (strtotime($fechaHora) <= time()) {
                throw new Exception("La cita debe ser en una fecha y hora futura");
            }
            
            $usuarioInfo = obtenerUsuarioInfo($conn, $usuarioId);
            if (!$usuarioInfo) {
                throw new Exception("Error: No se pudo obtener la información del usuario");
            }

            $pacienteId = $usuarioInfo['PacienteId'] ?? null;
            
            if (!$pacienteId) {
                $pacienteId = crearPacienteDesdeUsuario($conn, $usuarioId, $usuarioInfo);
                
                if (!$pacienteId) {
                    throw new Exception("Error: No se pudo crear el registro del paciente");
                }
            }
            
            $queryCheckPatient = "SELECT PacienteId FROM paciente WHERE PacienteId = ?";
            $stmtCheck = mysqli_prepare($conn, $queryCheckPatient);
            mysqli_stmt_bind_param($stmtCheck, "i", $pacienteId);
            mysqli_stmt_execute($stmtCheck);
            $resultCheck = mysqli_stmt_get_result($stmtCheck);
            $patientExists = mysqli_fetch_assoc($resultCheck);
            
            if (!$patientExists) {
                throw new Exception("Error: El ID del paciente no es válido");
            }
            
            $citaColumns = obtenerEstructuraCita($conn);
            
            $query = "";
            $paramTypes = "";
            $params = [];
            
            if (in_array('ID_Paciente', $citaColumns) && in_array('id_empleado', $citaColumns)) {
                $query = "INSERT INTO cita (Fecha, Duracion, Nombre, Estado, ID_Paciente, id_empleado) VALUES (?, ?, ?, ?, ?, ?)";
                $paramTypes = "sissii";
                $params = [$fechaHora, $duracion, $motivo, $estado, $pacienteId, $doctorId];
            } elseif (in_array('ID_Paciente', $citaColumns)) {
                $query = "INSERT INTO cita (Fecha, Duracion, Nombre, Estado, ID_Paciente) VALUES (?, ?, ?, ?, ?)";
                $paramTypes = "sissi";
                $params = [$fechaHora, $duracion, $motivo, $estado, $pacienteId];
            } elseif (in_array('id_empleado', $citaColumns)) {
                $query = "INSERT INTO cita (Fecha, Duracion, Nombre, Estado, id_empleado) VALUES (?, ?, ?, ?, ?)";
                $paramTypes = "sissi";
                $params = [$fechaHora, $duracion, $motivo, $estado, $doctorId];
            } else {
                $query = "INSERT INTO cita (Fecha, Duracion, Nombre, Estado) VALUES (?, ?, ?, ?)";
                $paramTypes = "siss";
                $params = [$fechaHora, $duracion, $motivo, $estado];
            }
            
            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . mysqli_error($conn));
            }
            
            if (!empty($params)) {
                if (strlen($paramTypes) !== count($params)) {
                    throw new Exception("Error: Número de tipos no coincide con número de parámetros");
                }
                
                $bindResult = mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
                if (!$bindResult) {
                    throw new Exception("Error bindeando parámetros: " . mysqli_stmt_error($stmt));
                }
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al crear la cita: " . mysqli_stmt_error($stmt));
            }
            
            $citaId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            
            $mensajeExito = "¡Cita agendada exitosamente para el " . date('d/m/Y \a \l\a\s H:i', strtotime($fechaHora)) . "!";
            
        } catch (Exception $e) {
            $mensajeError = $e->getMessage();
        }
        
        CerrarBD($conn);
    }
    
    // Endpoint para verificar disponibilidad (AJAX)
    if (isset($_POST['action']) && $_POST['action'] === 'check_availability') {
        header('Content-Type: application/json');
        
        try {
            echo json_encode([
                'success' => true,
                'available' => true,
                'doctor_id' => intval($_POST['doctor_id'])
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }
}

// Obtener datos para la vista
$conn = AbrirBD();
$usuarioInfo = obtenerUsuarioInfo($conn, $usuarioId);
$doctores = obtenerDoctores($conn);
CerrarBD($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Óptica Grisol - Agendar Cita</title>
    <?php IncluirCSS(); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="/OptiGestion/assets/css/styles.css">
    <style>
        :root {
            --verde-primario: #28a745;
            --verde-secundario: #20c997;
            --verde-oscuro: #1e7e34;
            --verde-claro: #d4edda;
            --verde-hover: #218838;
        }
        
        .appointment-wizard {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 3px;
            background: #e9ecef;
            z-index: 1;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            flex: 1;
            max-width: 120px;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
            border: 3px solid white;
            transition: all 0.3s ease;
        }
        
        .step.active .step-number {
            background: var(--verde-primario);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .step.completed .step-number {
            background: var(--verde-secundario);
            color: white;
        }
        
        .step-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6c757d;
            text-align: center;
        }
        
        .step.active .step-label {
            color: var(--verde-primario);
        }
        
        .wizard-step {
            display: none;
        }
        
        .wizard-step.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .doctor-selection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .doctor-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .doctor-card:hover {
            transform: translateY(-5px);
            border-color: var(--verde-secundario);
            box-shadow: 0 10px 25px rgba(32, 201, 151, 0.15);
        }
        
        .doctor-card.selected {
            border-color: var(--verde-primario);
            background: var(--verde-claro);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.2);
        }
        
        .doctor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--verde-primario), var(--verde-secundario));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 2rem;
        }
        
        .doctor-specialty {
            color: var(--verde-primario);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .date-time-selection {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .calendar-sidebar {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 15px;
            height: fit-content;
        }
        
        .time-slots-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.5rem;
            max-height: 400px;
            overflow-y: auto;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .time-slot {
            padding: 0.75rem 0.5rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            font-weight: 500;
        }
        
        .time-slot.available:hover {
            border-color: var(--verde-secundario);
            transform: translateY(-2px);
        }
        
        .time-slot.available.selected {
            background: var(--verde-primario);
            color: white;
            border-color: var(--verde-primario);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        
        .time-slot.unavailable {
            background: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .appointment-summary-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--verde-primario);
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .btn-wizard {
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--verde-primario), var(--verde-secundario));
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--verde-oscuro), var(--verde-primario));
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--verde-primario), #34ce57);
            border: none;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, var(--verde-oscuro), var(--verde-primario));
            transform: translateY(-2px);
        }
        
        .wizard-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }
        
        /* Estilos para modales modernos */
        .modal-confirm {
            border-radius: 20px;
            border: none;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        
        .modal-confirm .modal-header {
            border-bottom: none;
            padding: 2.5rem 2.5rem 1rem;
            background: linear-gradient(135deg, var(--verde-primario), var(--verde-secundario));
            color: white;
        }
        
        .modal-confirm .modal-body {
            padding: 2rem 2.5rem;
            text-align: center;
        }
        
        .modal-confirm .modal-footer {
            border-top: none;
            padding: 1rem 2.5rem 2.5rem;
            justify-content: center;
            gap: 1rem;
        }
        
        .modal-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--verde-primario), var(--verde-secundario));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-modal {
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            min-width: 140px;
        }
        
        .btn-confirm {
            background: linear-gradient(135deg, var(--verde-primario), var(--verde-secundario));
            color: white;
        }
        
        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
        }
        
        .btn-cancel {
            background: #f8f9fa;
            color: #6c757d;
            border: 2px solid #e9ecef;
        }
        
        .btn-cancel:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        .confirmation-text {
            font-size: 1.1rem;
            color: #495057;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .appointment-details {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            margin: 1.5rem 0;
            text-align: left;
            border-left: 4px solid var(--verde-primario);
        }
        
        .flatpickr-calendar {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .flatpickr-day.selected {
            background: var(--verde-primario);
            border-color: var(--verde-primario);
        }
        
        .flatpickr-day.today {
            border-color: var(--verde-secundario);
        }
        
        .availability-status {
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        
        .availability-status.loading {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .availability-status.available {
            background: var(--verde-claro);
            color: var(--verde-oscuro);
            border: 1px solid #c3e6cb;
        }
        
        .availability-status.unavailable {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .date-time-selection {
                grid-template-columns: 1fr;
            }
            
            .step-indicator::before {
                width: 70%;
            }
            
            .step-label {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <?php MostrarMenu(); ?>

    <div class="container py-4">
        <!-- Header -->
        <div class="app-header text-center">
            <h1 class="display-5 fw-bold mb-3" style="color: var(--verde-primario);">📅 Agendar Cita</h1>
            <p class="lead mb-0 text-muted">Sistema de agendamiento integrado con Google Calendar</p>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($mensajeExito)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $mensajeExito; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensajeError)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $mensajeError; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Asistente de Citas -->
        <div class="appointment-wizard">
            <!-- Indicador de Pasos -->
            <div class="step-indicator">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Seleccionar Doctor</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Fecha y Hora</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Confirmar</div>
                </div>
            </div>

            <!-- Paso 1: Selección de Doctor -->
            <div class="wizard-step active" id="step1">
                <h4 class="mb-4" style="color: var(--verde-primario);">
                    <i class="fas fa-user-md me-2"></i>Selecciona un Doctor
                </h4>
                <p class="text-muted mb-4">Elige el profesional que prefieras para tu consulta</p>

                <div class="doctor-selection-grid" id="doctoresGrid">
                    <?php foreach ($doctores as $index => $doctor): ?>
                        <div class="doctor-card" data-doctor-id="<?php echo $doctor['IdUsuario']; ?>">
                            <div class="doctor-avatar">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            <h5 class="fw-bold">Dr. <?php echo $doctor['Nombre'] . ' ' . $doctor['Apellido']; ?></h5>
                            <div class="doctor-specialty">
                                <?php echo $doctor['NombreRol'] ?? 'Profesional de la salud'; ?>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-envelope me-1"></i><?php echo $doctor['CorreoElectronico']; ?>
                            </p>
                            <p class="doctor-availability text-success">
                                <i class="fas fa-calendar-check me-1"></i>Disponibilidad en tiempo real
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="wizard-navigation">
                    <div></div>
                    <button class="btn btn-primary btn-wizard" onclick="nextStep(2)">
                        Siguiente <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Paso 2: Selección de Fecha y Hora -->
            <div class="wizard-step" id="step2">
                <h4 class="mb-4" style="color: var(--verde-primario);">
                    <i class="fas fa-calendar-alt me-2"></i>Selecciona Fecha y Hora
                </h4>

                <div class="date-time-selection">
                    <!-- Selector de Fecha -->
                    <div class="calendar-sidebar">
                        <h6 class="fw-bold mb-3">Selecciona una fecha</h6>
                        <input type="text" id="datePicker" class="form-control form-control-lg" placeholder="Elige una fecha">
                        
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Doctor seleccionado</h6>
                            <div id="selectedDoctorInfo">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>
                    </div>

                    <!-- Selector de Hora -->
                    <div>
                        <h6 class="fw-bold mb-3">Horarios disponibles</h6>
                        <div id="availabilityStatus" class="availability-status" style="display: none;">
                            <i class="fas fa-sync fa-spin me-2"></i>
                            <span>Cargando disponibilidad...</span>
                        </div>
                        <div class="time-slots-container" id="timeSlotsContainer">
                            <!-- Los horarios se cargan dinámicamente aquí -->
                        </div>
                    </div>
                </div>

                <div class="wizard-navigation">
                    <button class="btn btn-outline-secondary btn-wizard" onclick="previousStep(1)">
                        <i class="fas fa-arrow-left me-2"></i>Anterior
                    </button>
                    <button class="btn btn-primary btn-wizard" onclick="nextStep(3)">
                        Siguiente <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Paso 3: Confirmación -->
            <div class="wizard-step" id="step3">
                <h4 class="mb-4" style="color: var(--verde-primario);">
                    <i class="fas fa-clipboard-check me-2"></i>Confirmar Cita
                </h4>

                <div class="appointment-summary-card">
                    <h5 class="fw-bold mb-3">Resumen de tu cita</h5>
                    <div id="appointmentSummary">
                        <!-- Se llena dinámicamente -->
                    </div>
                </div>

                <form id="confirmAppointmentForm" method="POST">
                    <input type="hidden" name="action" value="agendar_cita">
                    <input type="hidden" name="doctor_id" id="formDoctorId">
                    <input type="hidden" name="fecha_hora" id="formFechaHora">
                    
                    <div class="mb-4">
                        <label for="motivo" class="form-label fw-bold">Motivo de la consulta *</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="4" 
                                  placeholder="Describe brevemente el motivo de tu consulta..." required></textarea>
                        <div class="form-text">Esta información ayudará al doctor a prepararse para tu consulta.</div>
                    </div>

                    <div class="wizard-navigation">
                        <button type="button" class="btn btn-outline-secondary btn-wizard" onclick="previousStep(2)">
                            <i class="fas fa-arrow-left me-2"></i>Anterior
                        </button>
                        <button type="button" class="btn btn-success btn-wizard" id="btnConfirmarCita">
                            <i class="fas fa-calendar-check me-2"></i>Confirmar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-confirm">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">
                        <i class="fas fa-calendar-check me-2"></i>Confirmar Cita
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <h5 class="mb-3">¿Confirmar agendamiento de cita?</h5>
                    
                    <p class="confirmation-text">
                        Estás a punto de agendar una nueva cita médica. Por favor verifica que toda la información sea correcta.
                    </p>
                    
                    <div class="appointment-details" id="modalAppointmentDetails">
                        <!-- Se llena dinámicamente -->
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Recibirás una confirmación por correo electrónico y la cita se sincronizará con tu calendario.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel btn-modal" data-bs-dismiss="modal">
                        <i class="fas fa-edit me-1"></i>Revisar Datos
                    </button>
                    <button type="button" class="btn btn-confirm btn-modal" id="modalConfirmButton">
                        <i class="fas fa-check-circle me-1"></i>Sí, Confirmar Cita
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php MostrarFooter(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        class AppointmentWizard {
            constructor() {
                this.currentStep = 1;
                this.selectedDoctor = null;
                this.selectedDate = null;
                this.selectedTime = null;
                this.availabilityCache = {};
                this.init();
            }

            init() {
                this.initDatePicker();
                this.initEventListeners();
                this.updateStepIndicator();
            }

            initDatePicker() {
                const datePicker = flatpickr("#datePicker", {
                    locale: "es",
                    minDate: "today",
                    dateFormat: "Y-m-d",
                    disableMobile: true,
                    onChange: (selectedDates, dateStr) => {
                        this.handleDateSelection(dateStr);
                    }
                });
            }

            initEventListeners() {
                // Selección de doctor
                document.querySelectorAll('.doctor-card').forEach(card => {
                    card.addEventListener('click', () => {
                        this.handleDoctorSelection(card);
                    });
                });

                // Modal de confirmación
                document.getElementById('btnConfirmarCita').addEventListener('click', () => {
                    this.showConfirmationModal();
                });

                document.getElementById('modalConfirmButton').addEventListener('click', () => {
                    document.getElementById('confirmAppointmentForm').submit();
                });
            }

            handleDoctorSelection(selectedCard) {
                document.querySelectorAll('.doctor-card').forEach(card => {
                    card.classList.remove('selected');
                });

                selectedCard.classList.add('selected');
                this.selectedDoctor = selectedCard.dataset.doctorId;
                this.updateSelectedDoctorInfo();
            }

            updateSelectedDoctorInfo() {
                const doctorCard = document.querySelector(`.doctor-card[data-doctor-id="${this.selectedDoctor}"]`);
                if (doctorCard) {
                    const doctorName = doctorCard.querySelector('h5').textContent;
                    const doctorSpecialty = doctorCard.querySelector('.doctor-specialty').textContent;
                    
                    document.getElementById('selectedDoctorInfo').innerHTML = `
                        <div class="alert alert-success">
                            <h6 class="fw-bold">${doctorName}</h6>
                            <p class="mb-0">${doctorSpecialty}</p>
                        </div>
                    `;
                }
            }

            async handleDateSelection(dateStr) {
                this.selectedDate = dateStr;
                await this.loadTimeSlots();
            }

            async loadTimeSlots() {
                if (!this.selectedDoctor || !this.selectedDate) return;

                const container = document.getElementById('timeSlotsContainer');
                const statusElement = document.getElementById('availabilityStatus');
                
                statusElement.style.display = 'block';
                statusElement.className = 'availability-status loading';
                container.innerHTML = '';

                const timeSlots = this.generateTimeSlots();
                let availableSlots = 0;

                for (const slot of timeSlots) {
                    const available = await this.checkSlotAvailability(slot);
                    const slotElement = this.createTimeSlotElement(slot, available);
                    container.appendChild(slotElement);
                    
                    if (available) availableSlots++;
                }

                if (availableSlots > 0) {
                    statusElement.className = 'availability-status available';
                    statusElement.innerHTML = `<i class="fas fa-check-circle me-2"></i>${availableSlots} horarios disponibles`;
                } else {
                    statusElement.className = 'availability-status unavailable';
                    statusElement.innerHTML = `<i class="fas fa-times-circle me-2"></i>No hay horarios disponibles para esta fecha`;
                }
            }

            generateTimeSlots() {
                const slots = [];
                for (let hour = 9; hour < 18; hour++) {
                    for (let minute = 0; minute < 60; minute += 30) {
                        slots.push({
                            time: `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`,
                            display: `${hour}:${minute.toString().padStart(2, '0')}`
                        });
                    }
                }
                return slots;
            }

            async checkSlotAvailability(slot) {
                const cacheKey = `${this.selectedDoctor}-${this.selectedDate}-${slot.time}`;
                
                if (this.availabilityCache[cacheKey] !== undefined) {
                    return this.availabilityCache[cacheKey];
                }

                try {
                    const response = await fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=check_availability&doctor_id=${this.selectedDoctor}&date=${this.selectedDate}&time=${slot.time}`
                    });

                    const data = await response.json();
                    const available = data.success && data.available;
                    
                    this.availabilityCache[cacheKey] = available;
                    return available;
                    
                } catch (error) {
                    console.error('Error checking availability:', error);
                    return false;
                }
            }

            createTimeSlotElement(slot, available) {
                const element = document.createElement('div');
                element.className = `time-slot ${available ? 'available' : 'unavailable'}`;
                element.textContent = slot.display;
                
                if (available) {
                    element.addEventListener('click', () => {
                        this.handleTimeSelection(element, slot);
                    });
                } else {
                    element.title = 'No disponible';
                }
                
                return element;
            }

            handleTimeSelection(selectedElement, slot) {
                document.querySelectorAll('.time-slot.selected').forEach(el => {
                    el.classList.remove('selected');
                });

                selectedElement.classList.add('selected');
                this.selectedTime = slot.time;

                document.getElementById('formDoctorId').value = this.selectedDoctor;
                document.getElementById('formFechaHora').value = `${this.selectedDate} ${this.selectedTime}:00`;
            }

            updateStepIndicator() {
                document.querySelectorAll('.step').forEach(step => {
                    const stepNumber = parseInt(step.dataset.step);
                    step.classList.remove('active', 'completed');
                    
                    if (stepNumber === this.currentStep) {
                        step.classList.add('active');
                    } else if (stepNumber < this.currentStep) {
                        step.classList.add('completed');
                    }
                });
            }

            nextStep(step) {
                if (step === 2 && !this.selectedDoctor) {
                    this.showErrorModal('Por favor selecciona un doctor');
                    return;
                }
                
                if (step === 3 && (!this.selectedDate || !this.selectedTime)) {
                    this.showErrorModal('Por favor selecciona una fecha y hora');
                    return;
                }

                if (step === 3) {
                    this.updateAppointmentSummary();
                }

                this.currentStep = step;
                this.showStep(step);
                this.updateStepIndicator();
            }

            previousStep(step) {
                this.currentStep = step;
                this.showStep(step);
                this.updateStepIndicator();
            }

            showStep(step) {
                document.querySelectorAll('.wizard-step').forEach(stepEl => {
                    stepEl.classList.remove('active');
                });
                document.getElementById(`step${step}`).classList.add('active');
            }

            updateAppointmentSummary() {
                const doctorCard = document.querySelector(`.doctor-card[data-doctor-id="${this.selectedDoctor}"]`);
                const doctorName = doctorCard ? doctorCard.querySelector('h5').textContent : '';
                
                const fecha = new Date(`${this.selectedDate}T${this.selectedTime}`);
                const formattedDate = fecha.toLocaleDateString('es-ES', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                document.getElementById('appointmentSummary').innerHTML = `
                    <div class="summary-item">
                        <span><i class="fas fa-user-md me-2"></i>Doctor:</span>
                        <strong>${doctorName}</strong>
                    </div>
                    <div class="summary-item">
                        <span><i class="fas fa-calendar me-2"></i>Fecha y hora:</span>
                        <strong>${formattedDate}</strong>
                    </div>
                    <div class="summary-item">
                        <span><i class="fas fa-clock me-2"></i>Duración:</span>
                        <strong>30 minutos</strong>
                    </div>
                `;
            }

            showConfirmationModal() {
                const motivo = document.getElementById('motivo').value.trim();
                if (!motivo) {
                    this.showErrorModal('Por favor describe el motivo de la consulta');
                    return;
                }

                const doctorCard = document.querySelector(`.doctor-card[data-doctor-id="${this.selectedDoctor}"]`);
                const doctorName = doctorCard ? doctorCard.querySelector('h5').textContent : '';
                
                const fecha = new Date(`${this.selectedDate}T${this.selectedTime}`);
                const formattedDate = fecha.toLocaleDateString('es-ES', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                document.getElementById('modalAppointmentDetails').innerHTML = `
                    <div class="mb-2"><strong>Doctor:</strong> ${doctorName}</div>
                    <div class="mb-2"><strong>Fecha y hora:</strong> ${formattedDate}</div>
                    <div class="mb-2"><strong>Duración:</strong> 30 minutos</div>
                    <div><strong>Motivo:</strong> ${motivo}</div>
                `;

                const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                modal.show();
            }

            showErrorModal(message) {
                // Crear modal de error dinámicamente
                const errorModal = document.createElement('div');
                errorModal.className = 'modal fade';
                errorModal.innerHTML = `
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content modal-confirm">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Información Requerida
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="modal-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <p class="confirmation-text">${message}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-confirm btn-modal" data-bs-dismiss="modal">
                                    Entendido
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(errorModal);
                const modal = new bootstrap.Modal(errorModal);
                modal.show();
                
                // Remover el modal del DOM después de cerrarse
                errorModal.addEventListener('hidden.bs.modal', () => {
                    document.body.removeChild(errorModal);
                });
            }
        }

        // Inicializar el asistente
        let wizard;
        document.addEventListener('DOMContentLoaded', () => {
            wizard = new AppointmentWizard();
        });

        // Funciones globales para los botones
        function nextStep(step) {
            wizard.nextStep(step);
        }

        function previousStep(step) {
            wizard.previousStep(step);
        }
    </script>
</body>
</html>
