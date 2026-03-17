<?php
session_start();
include('layout.php');
require_once __DIR__ . '/../Controller/citaController.php';


procesarAccionesCita();


$citas = obtenerCitasSegunRol();


$mensajeExito = $_SESSION['mensaje_exito'] ?? "";
$mensajeError = $_SESSION['mensaje_error'] ?? "";
unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);

if (!isset($_SESSION['UsuarioID'])) {
    header("Location: /login");
    exit;
}

$rolName    = $_SESSION['RolID']  ?? '';
$rolId      = $_SESSION['Id_rol'] ?? null;
$isPaciente = ($rolName === 'Paciente');
$isEmpleado = ($rolName === 'Empleado');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Óptica Grisol - <?= $isPaciente ? 'Mis Citas' : 'Gestión de Citas' ?></title>

    <?php IncluirCSS(); ?>

    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="/assets/css/cita.css">
</head>

<body class="bg-main">
<?php MostrarMenu(); ?>

<div class="container py-4">

    
    <div class="app-header text-center mb-5">
        <div class="header-premium-icon">
            <i data-lucide="calendar-clock"></i>
        </div>
        <h1 class="header-premium-title">
            <?= $isPaciente ? 'Mis Citas' : 'Gestión de Citas' ?>
        </h1>
        <p class="header-premium-subtitle">
            <?= $isPaciente
                ? 'Gestiona y revisa todas tus citas programadas'
                : 'Sistema interno para administrar las citas de los pacientes' ?>
        </p>
    </div>

    
    <div class="citas-filters mb-4">
        <div class="filter-card">
            <div class="filter-group">
                <label class="filter-label"><i data-lucide="calendar"></i> Desde</label>
                <input type="text" id="filterFrom" class="form-control" placeholder="Fecha inicio">
            </div>

            <div class="filter-group">
                <label class="filter-label"><i data-lucide="calendar"></i> Hasta</label>
                <input type="text" id="filterTo" class="form-control" placeholder="Fecha fin">
            </div>

            <div class="filter-group filter-actions">
                <button class="btn btn-outline-secondary" id="clearFilters">
                    <i data-lucide="trash-2"></i> Limpiar
                </button>
            </div>
        </div>
    </div>

  
    <?php if ($mensajeExito): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i data-lucide="check-circle-2" class="me-2"></i><?= htmlspecialchars($mensajeExito) ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($mensajeError): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i data-lucide="alert-triangle" class="me-2"></i><?= htmlspecialchars($mensajeError) ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($rolId == 4): ?>
        <div class="no-permission text-center">
            <i data-lucide="ban"></i>
            <h4 class="mt-3">Acceso Restringido</h4>
            <p>El rol de Cajero/a no tiene permisos para gestionar citas.</p>
        </div>
    <?php else: ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><?= $isPaciente ? 'Citas Programadas' : 'Todas las Citas del Sistema' ?></h4>

            <?php if ($isEmpleado): ?>
                <a href="agendarCita.php" class="btn btn-primary">
                    <i data-lucide="plus-circle"></i> Nueva Cita
                </a>
            <?php endif; ?>
        </div>

        <div class="citas-container">

            <?php if (empty($citas)): ?>
                <div class="empty-state">
                    <i data-lucide="calendar-x"></i>
                    <h4>No hay citas registradas</h4>
                </div>
            <?php else: ?>

                <?php foreach ($citas as $cita): ?>
                    <?php
                        $fechaHora  = new DateTime($cita['Fecha']);
                        $fechaForm  = $fechaHora->format('d/m/Y');
                        $horaForm   = $fechaHora->format('H:i');
                        $inputFecha = $fechaHora->format('Y-m-d');
                        $inputHora  = $fechaHora->format('H:i');
                        $citaPasada = ($fechaHora < new DateTime());
                        $puedeModificar = (!$citaPasada && in_array($cita['Estado'], ['pendiente','confirmada']));
                    ?>

                    <div class="cita-card <?= $citaPasada ? 'cita-pasada' : '' ?> estado-<?= strtolower($cita['Estado']) ?>"
                         data-fecha="<?= $inputFecha ?>">

                        <div class="cita-header">
                            <div>
                                <h5>
                                    <i data-lucide="file-text"></i>
                                    <span class="motivo-label">Motivo:</span>
                                    <?= htmlspecialchars($cita['Motivo'] ?? '—') ?>
                                </h5>
                                <p>
                                    ID #<?= (int)$cita['IdCita'] ?>
                                    <?php if (!$isPaciente && !empty($cita['PacienteNombre'])): ?>
                                        | Paciente: <?= htmlspecialchars($cita['PacienteNombre']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <span class="estado-badge estado-<?= strtolower($cita['Estado']) ?>">
                                <?= ucfirst($cita['Estado']) ?>
                            </span>
                        </div>

                        <div class="cita-info-grid">
                            <div class="info-item">
                                <i data-lucide="calendar"></i>
                                <div><strong>Fecha:</strong><br><?= $fechaForm ?></div>
                            </div>

                            <div class="info-item">
                                <i data-lucide="clock"></i>
                                <div><strong>Hora:</strong><br><?= $horaForm ?></div>
                            </div>

                            <div class="info-item">
                                <i data-lucide="user-round"></i>
                                <div>
                                    <strong>Profesional:</strong><br>
                                    <?= !empty($cita['EmpleadoNombre'])
                                        ? htmlspecialchars($cita['EmpleadoNombre'].' '.$cita['EmpleadoApellido'])
                                        : '<span class="text-muted">No asignado</span>' ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($puedeModificar): ?>
                        <div class="cita-actions border-top pt-3 mt-3">
                            <?php if ($isPaciente): ?>
                                <button class="btn btn-reagendar btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#reagendarModal"
                                        data-cita-id="<?= $cita['IdCita'] ?>"
                                        data-cita-fecha="<?= $inputFecha ?>"
                                        data-cita-hora="<?= $inputHora ?>"
                                        data-cita-nombre="<?= htmlspecialchars($cita['Motivo']) ?>"
                                        data-doctor-id="<?= $cita['id_empleado'] ?>">
                                    <i data-lucide="calendar-range"></i> Reagendar
                                </button>
                            <?php endif; ?>

                            <button class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelarModal"
                                    data-cita-id="<?= $cita['IdCita'] ?>"
                                    data-cita-nombre="<?= htmlspecialchars($cita['Motivo']) ?>"
                                    data-cita-fecha="<?= $fechaForm ?>"
                                    data-cita-hora="<?= $horaForm ?>">
                                <i data-lucide="x-circle"></i> Cancelar
                            </button>

                            <?php if ($isEmpleado): ?>
                                <button class="btn btn-success btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#finalizarModal"
                                        data-cita-id="<?= $cita['IdCita'] ?>"
                                        data-cita-nombre="<?= htmlspecialchars($cita['Motivo']) ?>"
                                        data-cita-fecha="<?= $fechaForm ?>"
                                        data-cita-hora="<?= $horaForm ?>">
                                    <i data-lucide="check-circle-2"></i> Finalizar
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

</div>



<div class="modal fade" id="reagendarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-confirm">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i data-lucide="calendar-range" class="me-2"></i>Reagendar Cita
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="reagendarForm">
                <input type="hidden" name="action" value="reagendar_cita">
                <input type="hidden" name="cita_id" id="modalCitaId">
                
                <input type="hidden" id="doctorId">

                <div class="modal-body">

                    <div class="modal-icon">
                        <i data-lucide="calendar-search"></i>
                    </div>

                    <h5 class="mb-3">Selecciona una nueva fecha y hora</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Cita actual</label>
                        <div id="modalCitaInfo" class="cita-details small text-muted"></div>
                    </div>

                    <div class="mb-3 text-center">
                    <label class="form-label fw-bold d-block">Nueva fecha *</label>

                    <div class="input-centered">
                        <input type="text"
                            class="form-control form-control-sm input-narrow"
                            name="nueva_fecha"
                            id="nueva_fecha"
                            placeholder="Selecciona una fecha"
                            required>
                    </div>
                </div>

                <div class="mb-3 text-center">
                    <label class="form-label fw-bold d-block">Nueva hora *</label>

                    <div class="input-centered">
                        <input type="time"
                            class="form-control form-control-sm input-narrow"
                            name="nueva_hora"
                            id="nueva_hora"
                            required>
                    </div>
                    </div>

                    <div id="availabilityStatusEdit"
                         class="availability-status"
                         style="display:none;"></div>

                    <div id="timeSlotsContainerEdit" class="time-slots-grid"></div>

                    <div class="alert alert-info mt-3">
                        <small>
                            <i data-lucide="info" class="me-1"></i>
                            La cita se actualizará con la nueva fecha y hora seleccionadas.
                        </small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel btn-modal" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-confirm btn-modal">
                        <i data-lucide="calendar-check" class="me-1"></i>Confirmar Reagendación
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal CANCELAR (PACIENTE / EMPLEADO) -->
<div class="modal fade" id="cancelarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-confirm">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i data-lucide="circle-slash" class="me-2"></i>Cancelar Cita
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="modal-icon">
                    <i data-lucide="calendar-x"></i>
                </div>

                <h5 class="mb-3">¿Deseas cancelar esta cita?</h5>

                <p class="confirmation-text">
                    Esta acción no se puede deshacer. La cita pasará al estado <strong>cancelada</strong>.
                </p>

                <div id="cancelarCitaInfo" class="cita-details"></div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-cancel btn-modal" data-bs-dismiss="modal">
                    Mantener Cita
                </button>

                <form method="POST" id="cancelarForm">
                    <input type="hidden" name="action" value="cancelar_cita">
                    <input type="hidden" name="cita_id" id="cancelarCitaId">
                </form>

                <button type="button" class="btn btn-danger btn-modal" id="cancelarConfirmarBtn">
                    <i data-lucide="trash-2" class="me-1"></i>Cancelar Cita
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Modal FINALIZAR (SOLO EMPLEADO) -->
<div class="modal fade" id="finalizarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-confirm">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i data-lucide="check-circle-2" class="me-2"></i>Finalizar Cita
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="modal-icon">
                    <i data-lucide="check"></i>
                </div>

                <h5 class="mb-3">¿Marcar esta cita como finalizada?</h5>

                <p class="confirmation-text">
                    La cita pasará al estado <strong>finalizada</strong> y no podrá modificarse.
                </p>

                <div id="finalizarCitaInfo" class="cita-details"></div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-cancel btn-modal" data-bs-dismiss="modal">
                    Volver
                </button>

                <form method="POST" id="finalizarForm">
                    <input type="hidden" name="action" value="finalizar_cita">
                    <input type="hidden" name="cita_id" id="finalizarCitaId">

                    <button type="submit" class="btn btn-success btn-modal">
                        <i data-lucide="check-circle" class="me-1"></i>Finalizar Cita
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>


<div class="modal fade custom-error-modal" id="errorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-error-modal-content">
            <div class="custom-error-header">
                <h5 class="m-0">
                    <i data-lucide="alert-triangle" class="me-2"></i> Aviso Importante
                </h5>
                <button class="btn-close custom-close-btn" data-bs-dismiss="modal"></button>
            </div>
            <div class="custom-error-body">
                <p id="errorModalMessage"></p>
            </div>
            <div class="custom-error-footer">
                <button class="btn custom-error-btn" data-bs-dismiss="modal">
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>

<?php MostrarFooter(); ?>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/gestionCita.js"></script>

<script>
    lucide.createIcons();
</script>

</body>
</html>
