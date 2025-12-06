<?php
session_start();
include('layout.php');
include_once __DIR__ . '/../Model/baseDatos.php';

// Verificar si el usuario está loggeado
if (!isset($_SESSION['UsuarioID'])) {
    header('Location: /login');
    exit;
}

$usuarioId = $_SESSION['UsuarioID'];
$rolId = $_SESSION['Id_rol'] ?? null;
$mensajeExito = '';
$mensajeError = '';

// [Todas las funciones PHP permanecen igual...]
// Función para verificar permisos de citas
function puedeGestionarCitas($rolId) {
    return $rolId != 4;
}

// Obtener citas del usuario o todas las citas según el rol
function obtenerCitas($conn, $usuarioId, $rolId) {
    if ($rolId == 4) {
        return [];
    }
    
    if ($_SESSION['RolID'] === 'Paciente') {
        return obtenerCitasPaciente($conn, $usuarioId);
    }
    
    return obtenerTodasLasCitas($conn);
}

// Obtener citas del paciente
function obtenerCitasPaciente($conn, $usuarioId) {
    $queryPaciente = "SELECT p.PacienteId 
                      FROM paciente p 
                      WHERE p.UsuarioId = ?";
    $stmtPaciente = mysqli_prepare($conn, $queryPaciente);
    mysqli_stmt_bind_param($stmtPaciente, "i", $usuarioId);
    mysqli_stmt_execute($stmtPaciente);
    $resultPaciente = mysqli_stmt_get_result($stmtPaciente);
    $paciente = mysqli_fetch_assoc($resultPaciente);
    
    if (!$paciente) {
        return [];
    }
    
    $pacienteId = $paciente['PacienteId'];
    
    $query = "SELECT c.*, 
                     u.Nombre as EmpleadoNombre,
                     u.Apellido as EmpleadoApellido,
                     u.CorreoElectronico as EmpleadoEmail,
                     p.Nombre as PacienteNombre
              FROM cita c 
              LEFT JOIN usuario u ON c.id_empleado = u.IdUsuario 
              LEFT JOIN paciente p ON c.ID_Paciente = p.PacienteId
              WHERE c.ID_Paciente = ? 
              ORDER BY c.Fecha DESC";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $pacienteId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $citas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $citas[] = $row;
    }
    
    return $citas;
}

// Obtener todas las citas (para empleados con permisos)
function obtenerTodasLasCitas($conn) {
    $query = "SELECT c.*, 
                     u.Nombre as EmpleadoNombre,
                     u.Apellido as EmpleadoApellido,
                     u.CorreoElectronico as EmpleadoEmail,
                     p.Nombre as PacienteNombre,
                     p.PacienteId
              FROM cita c 
              LEFT JOIN usuario u ON c.id_empleado = u.IdUsuario 
              LEFT JOIN paciente p ON c.ID_Paciente = p.PacienteId
              ORDER BY c.Fecha DESC";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $citas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $citas[] = $row;
    }
    
    return $citas;
}

// Cancelar cita
function cancelarCita($conn, $citaId, $usuarioId, $rolId) {
    if (!puedeGestionarCitas($rolId)) {
        throw new Exception("No tienes permisos para cancelar citas");
    }
    
    if ($_SESSION['RolID'] !== 'Paciente') {
        $queryVerificar = "SELECT * FROM cita WHERE IdCita = ?";
        $stmtVerificar = mysqli_prepare($conn, $queryVerificar);
        mysqli_stmt_bind_param($stmtVerificar, "i", $citaId);
    } else {
        $queryVerificar = "SELECT c.* 
                           FROM cita c 
                           JOIN paciente p ON c.ID_Paciente = p.PacienteId 
                           WHERE c.IdCita = ? AND p.UsuarioId = ?";
        $stmtVerificar = mysqli_prepare($conn, $queryVerificar);
        mysqli_stmt_bind_param($stmtVerificar, "ii", $citaId, $usuarioId);
    }
    
    mysqli_stmt_execute($stmtVerificar);
    $resultVerificar = mysqli_stmt_get_result($stmtVerificar);
    $cita = mysqli_fetch_assoc($resultVerificar);
    
    if (!$cita) {
        throw new Exception("Cita no encontrada o no tienes permisos para cancelarla");
    }
    
    if (!in_array($cita['Estado'], ['pendiente', 'confirmada'])) {
        throw new Exception("No se puede cancelar una cita en estado: " . $cita['Estado']);
    }
    
    $queryUpdate = "UPDATE cita SET Estado = 'cancelada' WHERE IdCita = ?";
    $stmtUpdate = mysqli_prepare($conn, $queryUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "i", $citaId);
    
    return mysqli_stmt_execute($stmtUpdate);
}

// Reagendar cita
function reagendarCita($conn, $citaId, $usuarioId, $rolId, $nuevaFechaHora) {
    if (!puedeGestionarCitas($rolId)) {
        throw new Exception("No tienes permisos para reagendar citas");
    }
    
    if ($_SESSION['RolID'] !== 'Paciente') {
        $queryVerificar = "SELECT * FROM cita WHERE IdCita = ?";
        $stmtVerificar = mysqli_prepare($conn, $queryVerificar);
        mysqli_stmt_bind_param($stmtVerificar, "i", $citaId);
    } else {
        $queryVerificar = "SELECT c.* 
                           FROM cita c 
                           JOIN paciente p ON c.ID_Paciente = p.PacienteId 
                           WHERE c.IdCita = ? AND p.UsuarioId = ?";
        $stmtVerificar = mysqli_prepare($conn, $queryVerificar);
        mysqli_stmt_bind_param($stmtVerificar, "ii", $citaId, $usuarioId);
    }
    
    mysqli_stmt_execute($stmtVerificar);
    $resultVerificar = mysqli_stmt_get_result($stmtVerificar);
    $cita = mysqli_fetch_assoc($resultVerificar);
    
    if (!$cita) {
        throw new Exception("Cita no encontrada o no tienes permisos para modificarla");
    }
    
    if (!in_array($cita['Estado'], ['pendiente', 'confirmada'])) {
        throw new Exception("No se puede reagendar una cita en estado: " . $cita['Estado']);
    }
    
    if (strtotime($nuevaFechaHora) <= time()) {
        throw new Exception("La nueva fecha y hora deben ser futuras");
    }
    
    $queryUpdate = "UPDATE cita SET Fecha = ?, Estado = 'reagendada' WHERE IdCita = ?";
    $stmtUpdate = mysqli_prepare($conn, $queryUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "si", $nuevaFechaHora, $citaId);
    
    return mysqli_stmt_execute($stmtUpdate);
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = AbrirBD();
    
    try {
        if (isset($_POST['action']) && $_POST['action'] === 'cancelar_cita') {
            $citaId = intval($_POST['cita_id']);
            
            if (cancelarCita($conn, $citaId, $usuarioId, $rolId)) {
                $mensajeExito = "Cita cancelada exitosamente";
            } else {
                throw new Exception("Error al cancelar la cita");
            }
        }
        
        if (isset($_POST['action']) && $_POST['action'] === 'reagendar_cita') {
            $citaId = intval($_POST['cita_id']);
            $nuevaFecha = $_POST['nueva_fecha'];
            $nuevaHora = $_POST['nueva_hora'];
            $nuevaFechaHora = $nuevaFecha . ' ' . $nuevaHora . ':00';
            
            if (reagendarCita($conn, $citaId, $usuarioId, $rolId, $nuevaFechaHora)) {
                $mensajeExito = "Cita reagendada exitosamente";
            } else {
                throw new Exception("Error al reagendar la cita");
            }
        }
    } catch (Exception $e) {
        $mensajeError = $e->getMessage();
    }
    
    CerrarBD($conn);
    
    if (!empty($mensajeExito)) {
        $_SESSION['mensaje_exito'] = $mensajeExito;
    }
    if (!empty($mensajeError)) {
        $_SESSION['mensaje_error'] = $mensajeError;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Mostrar mensajes de sesión
if (isset($_SESSION['mensaje_exito'])) {
    $mensajeExito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}
if (isset($_SESSION['mensaje_error'])) {
    $mensajeError = $_SESSION['mensaje_error'];
    unset($_SESSION['mensaje_error']);
}

// Obtener citas según el rol
$conn = AbrirBD();
$citas = obtenerCitas($conn, $usuarioId, $rolId);
CerrarBD($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Óptica Grisol - <?php echo ($_SESSION['RolID'] === 'Paciente') ? 'Mis Citas' : 'Gestión de Citas'; ?></title>
    <?php IncluirCSS(); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="/OptiGestion/assets/css/styles.css">
    <style>
        .citas-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .cita-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .cita-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .cita-pasada {
            opacity: 0.7;
            background-color: #f8f9fa;
            border-color: #e9ecef;
        }
        .cita-pasada:hover {
            opacity: 0.8;
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .cita-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .estado-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .estado-pendiente { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .estado-confirmada { background-color: #d1edff; color: #0c5460; border: 1px solid #b8daff; }
        .estado-completada { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .estado-cancelada { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .estado-reagendada { background-color: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }
        .estado-pasada { background-color: #e9ecef; color: #6c757d; border: 1px solid #dee2e6; }
        .cita-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-item i {
            width: 20px;
            color: #6c757d;
        }
        .cita-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .btn-reagendar {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        .btn-reagendar:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }
        .google-sync-badge {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            color: #28a745;
        }
        .no-permission {
            background: #f8d7da;
            color: #721c24;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
        }
        
        /* Estilos para modales modernos */
        .modal-confirm {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .modal-confirm .modal-header {
            border-bottom: none;
            padding: 2rem 2rem 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px 16px 0 0;
        }
        .modal-confirm .modal-body {
            padding: 2rem;
            text-align: center;
        }
        .modal-confirm .modal-footer {
            border-top: none;
            padding: 0 2rem 2rem;
            justify-content: center;
            gap: 1rem;
        }
        .modal-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        }
        .btn-confirm {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
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
        .cita-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            text-align: left;
        }
        .cita-details strong {
            color: #495057;
        }
    </style>
</head>
<body>
    <?php MostrarMenu(); ?>

    <div class="container py-4">
        <!-- Header dinámico según el rol -->
        <div class="app-header text-center mb-5">
            <h1 class="display-5 fw-bold mb-3">
                <?php if ($_SESSION['RolID'] === 'Paciente'): ?>
                    📋 Mis Citas
                <?php else: ?>
                    📋 Gestión de Citas
                <?php endif; ?>
            </h1>
            <p class="lead mb-0">
                <?php if ($_SESSION['RolID'] === 'Paciente'): ?>
                    Gestiona y revisa todas tus citas programadas
                <?php else: ?>
                    Sistema de gestión de citas de pacientes
                <?php endif; ?>
            </p>
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

        <!-- Verificar permisos para Cajero -->
        <?php if ($rolId == 4): ?>
            <div class="no-permission">
                <i class="fas fa-ban fa-3x mb-3"></i>
                <h4>Acceso Restringido</h4>
                <p class="mb-0">El rol de Cajero/a no tiene permisos para gestionar citas.</p>
            </div>
        <?php else: ?>
            <!-- Botón para nueva cita -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <?php if ($_SESSION['RolID'] === 'Paciente'): ?>
                        Tus Citas Programadas
                    <?php else: ?>
                        Todas las Citas del Sistema
                    <?php endif; ?>
                </h4>
                <?php if ($_SESSION['RolID'] === 'Paciente'): ?>
                    <a href="appointmentform.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Cita
                    </a>
                <?php endif; ?>
            </div>

            <!-- Lista de Citas -->
            <div class="citas-container">
                <?php if (empty($citas)): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h4>
                            <?php if ($_SESSION['RolID'] === 'Paciente'): ?>
                                No tienes citas programadas
                            <?php else: ?>
                                No hay citas en el sistema
                            <?php endif; ?>
                        </h4>
                        <p class="text-muted mb-4">
                            <?php if ($_SESSION['RolID'] === 'Paciente'): ?>
                                Agenda tu primera cita para comenzar
                            <?php else: ?>
                                Los pacientes pueden agendar citas desde el sistema
                            <?php endif; ?>
                        </p>
                        <?php if ($_SESSION['RolID'] === 'Paciente'): ?>
                            <a href="appointmentform.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-calendar-plus me-2"></i>Agendar Primera Cita
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($citas as $cita): 
                        $fechaHora = new DateTime($cita['Fecha']);
                        $fechaFormateada = $fechaHora->format('d/m/Y');
                        $horaFormateada = $fechaHora->format('H:i');
                        $fechaParaInput = $fechaHora->format('Y-m-d');
                        $horaParaInput = $fechaHora->format('H:i');
                        
                        // Determinar si la cita ya pasó
                        $ahora = new DateTime();
                        $citaPasada = $fechaHora < $ahora;
                        
                        $puedeModificar = in_array($cita['Estado'], ['pendiente', 'confirmada']);
                        // Si la cita ya pasó, no se puede modificar aunque esté pendiente/confirmada
                        if ($citaPasada) {
                            $puedeModificar = false;
                        }
                    ?>
                        <div class="cita-card <?php echo $citaPasada ? 'cita-pasada' : ''; ?>">
                            <div class="cita-header">
                                <div>
                                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($cita['Nombre']); ?></h5>
                                    <p class="text-muted mb-0">
                                        ID: #<?php echo $cita['IdCita']; ?>
                                        <?php if ($_SESSION['RolID'] !== 'Paciente' && !empty($cita['PacienteNombre'])): ?>
                                            | Paciente: <?php echo htmlspecialchars($cita['PacienteNombre']); ?>
                                        <?php endif; ?>
                                        <?php if ($citaPasada): ?>
                                            | <span class="text-muted"><i class="fas fa-clock me-1"></i>Cita pasada</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <span class="estado-badge estado-<?php echo strtolower($cita['Estado']); ?> <?php echo $citaPasada ? 'estado-pasada' : ''; ?>">
                                    <?php echo ucfirst($cita['Estado']); ?>
                                </span>
                            </div>

                            <div class="cita-info-grid">
                                <div class="info-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                        <strong>Fecha:</strong><br>
                                        <?php echo $fechaFormateada; ?>
                                        <?php if ($citaPasada): ?>
                                            <br><small class="text-muted"><i class="fas fa-history me-1"></i>Pasada</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <strong>Hora:</strong><br>
                                        <?php echo $horaFormateada; ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-stopwatch"></i>
                                    <div>
                                        <strong>Duración:</strong><br>
                                        <?php echo $cita['Duracion']; ?> minutos
                                    </div>
                                </div>
                                <?php if (!empty($cita['EmpleadoNombre'])): ?>
                                    <div class="info-item">
                                        <i class="fas fa-user-md"></i>
                                        <div>
                                            <strong>Profesional:</strong><br>
                                            <?php echo htmlspecialchars($cita['EmpleadoNombre'] . ' ' . $cita['EmpleadoApellido']); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Información adicional -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if ($cita['google_calendar_synced'] == 1): ?>
                                        <span class="google-sync-badge">
                                            <i class="fab fa-google me-1"></i>Sincronizado con Google Calendar
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">
                                    Creada: <?php echo date('d/m/Y H:i', strtotime($cita['Fecha'])); ?>
                                </small>
                            </div>

                            <!-- Acciones -->
                            <?php if ($puedeModificar): ?>
                                <div class="cita-actions mt-3 pt-3 border-top">
                                    <!-- Botón Reagendar -->
                                    <button type="button" class="btn btn-reagendar btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#reagendarModal"
                                            data-cita-id="<?php echo $cita['IdCita']; ?>"
                                            data-cita-fecha="<?php echo $fechaParaInput; ?>"
                                            data-cita-hora="<?php echo $horaParaInput; ?>"
                                            data-cita-nombre="<?php echo htmlspecialchars($cita['Nombre']); ?>">
                                        <i class="fas fa-calendar-event me-1"></i>Reagendar
                                    </button>
                                    
                                    <!-- Botón Cancelar -->
                                    <button type="button" class="btn btn-danger btn-sm btn-cancelar-cita"
                                            data-cita-id="<?php echo $cita['IdCita']; ?>"
                                            data-cita-nombre="<?php echo htmlspecialchars($cita['Nombre']); ?>"
                                            data-cita-fecha="<?php echo $fechaFormateada; ?>"
                                            data-cita-hora="<?php echo $horaFormateada; ?>">
                                        <i class="fas fa-times-circle me-1"></i>Cancelar
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <?php if ($citaPasada): ?>
                                            Esta cita ya pasó y no se puede modificar.
                                        <?php else: ?>
                                            Esta cita no se puede modificar en su estado actual.
                                        <?php endif; ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para Reagendar -->
    <div class="modal fade" id="reagendarModal" tabindex="-1" aria-labelledby="reagendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-confirm">
                <div class="modal-header">
                    <h5 class="modal-title" id="reagendarModalLabel">
                        <i class="fas fa-calendar-alt me-2"></i>Reagendar Cita
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="reagendarForm">
                    <input type="hidden" name="action" value="reagendar_cita">
                    <input type="hidden" name="cita_id" id="modalCitaId">
                    
                    <div class="modal-body">
                        <div class="modal-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <h5 class="mb-3">Reagendar tu cita</h5>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cita actual</label>
                            <div id="modalCitaInfo" class="cita-details">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nueva_fecha" class="form-label fw-bold">Nueva Fecha *</label>
                            <input type="date" class="form-control" name="nueva_fecha" id="nueva_fecha" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="nueva_hora" class="form-label fw-bold">Nueva Hora *</label>
                            <input type="time" class="form-control" name="nueva_hora" id="nueva_hora" required>
                        </div>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                La cita se marcará como "reagendada" y mantendrá su duración de 
                                <strong><?php echo isset($citas[0]) ? $citas[0]['Duracion'] : '30'; ?> minutos</strong>.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel btn-modal" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-confirm btn-modal">
                            <i class="fas fa-calendar-check me-1"></i>Confirmar Reagendación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Cancelar Cita -->
    <div class="modal fade" id="cancelarModal" tabindex="-1" aria-labelledby="cancelarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-confirm">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelarModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Cancelar Cita
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h5 class="mb-3">¿Estás seguro de cancelar esta cita?</h5>
                    
                    <p class="confirmation-text">
                        Esta acción cancelará permanentemente la cita seleccionada.
                    </p>
                    
                    <div class="cita-details" id="cancelarCitaInfo">
                        <!-- Se llena dinámicamente -->
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <small>
                            <i class="fas fa-exclamation-circle me-1"></i>
                            <strong>Importante:</strong> Esta acción no se puede deshacer.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel btn-modal" data-bs-dismiss="modal">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </button>
                    <form method="POST" id="cancelarForm" class="d-inline">
                        <input type="hidden" name="action" value="cancelar_cita">
                        <input type="hidden" name="cita_id" id="cancelarCitaId">
                        <button type="submit" class="btn btn-danger btn-modal">
                            <i class="fas fa-times-circle me-1"></i>Sí, Cancelar Cita
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php MostrarFooter(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal de Reagendar
            const reagendarModal = document.getElementById('reagendarModal');
            if (reagendarModal) {
                reagendarModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const citaId = button.getAttribute('data-cita-id');
                    const citaFecha = button.getAttribute('data-cita-fecha');
                    const citaHora = button.getAttribute('data-cita-hora');
                    const citaNombre = button.getAttribute('data-cita-nombre');
                    
                    document.getElementById('modalCitaId').value = citaId;
                    document.getElementById('nueva_fecha').value = citaFecha;
                    document.getElementById('nueva_hora').value = citaHora;
                    
                    document.getElementById('modalCitaInfo').innerHTML = `
                        <strong>${citaNombre}</strong><br>
                        <span>Fecha actual: ${citaFecha} a las ${citaHora}</span>
                    `;
                });
            }
            
            // Modal de Cancelar
            const cancelarModal = document.getElementById('cancelarModal');
            const cancelarButtons = document.querySelectorAll('.btn-cancelar-cita');
            
            cancelarButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const citaId = this.getAttribute('data-cita-id');
                    const citaNombre = this.getAttribute('data-cita-nombre');
                    const citaFecha = this.getAttribute('data-cita-fecha');
                    const citaHora = this.getAttribute('data-cita-hora');
                    
                    document.getElementById('cancelarCitaId').value = citaId;
                    document.getElementById('cancelarCitaInfo').innerHTML = `
                        <strong>${citaNombre}</strong><br>
                        <span>Fecha: ${citaFecha} a las ${citaHora}</span>
                    `;
                    
                    const modal = new bootstrap.Modal(cancelarModal);
                    modal.show();
                });
            });
            
            // Validación de fecha futura
            const fechaInput = document.getElementById('nueva_fecha');
            if (fechaInput) {
                const today = new Date().toISOString().split('T')[0];
                fechaInput.setAttribute('min', today);
            }
            
            // Validación del formulario de reagendación
            document.getElementById('reagendarForm').addEventListener('submit', function(e) {
                const nuevaFecha = document.getElementById('nueva_fecha').value;
                const nuevaHora = document.getElementById('nueva_hora').value;
                const nuevaFechaHora = new Date(nuevaFecha + 'T' + nuevaHora);
                
                if (nuevaFechaHora <= new Date()) {
                    e.preventDefault();
                    alert('La nueva fecha y hora deben ser futuras');
                    return;
                }
            });
            
            // Efectos visuales para los botones del modal
            const modalButtons = document.querySelectorAll('.btn-modal');
            modalButtons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>