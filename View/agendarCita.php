<?php
session_start();
require_once __DIR__ . '/../Controller/citaController.php';
include('layout.php');

$rol = $_SESSION['RolID'] ?? null;  
$nombrePaciente = ($rol === 'Paciente') 
    ? (($_SESSION["Nombre"] ?? '') . " " . ($_SESSION["Apellido"] ?? ''))
    : ""; 

$mensajeExito = $_SESSION['mensaje_exito'] ?? "";
$mensajeError = $_SESSION['mensaje_error'] ?? "";

unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Óptica Grisol - Agendar Cita</title>

    <?php IncluirCSS(); ?>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="stylesheet" href="/assets/css/cita.css">

</head>

<body class="bg-main">
<?php MostrarMenu(); ?>

<input type="hidden" id="userRole" value="<?= $rol ?>">

<div class="container py-4">

<div class="app-header text-center header-premium">
    <div class="header-premium-icon">
        <i data-lucide="calendar-days"></i>
    </div>

    <h1 class="header-premium-title">Agendar Cita</h1>

    <p class="header-premium-subtitle">
        Una experiencia fluida para reservar su próxima cita
    </p>
</div>


<?php if (!empty($mensajeExito)): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i data-lucide="check-circle" class="me-2"></i>
        <?= htmlspecialchars($mensajeExito) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($mensajeError)): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <i data-lucide="alert-triangle" class="me-2"></i>
        <?= htmlspecialchars($mensajeError) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>


<div class="appointment-wizard shadow-sm">

    <input type="hidden" id="pacienteNombre" value="<?= htmlspecialchars(trim($nombrePaciente)) ?>">

 
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

  
    <div class="wizard-step active" id="step1">
        <h4 class="mb-4" style="color: var(--verde-primario);">
            <i data-lucide="stethoscope" class="me-2"></i>Selecciona un Doctor
        </h4>

        <p class="text-muted mb-4">Seleccione su profesional de preferencia para la consulta</p>

        <?php if (empty($doctores)): ?>
            <div class="alert alert-warning d-flex align-items-center gap-2">
                <i data-lucide="info"></i>
                No hay doctores disponibles.
            </div>
        <?php else: ?>

            <div class="doctor-selection-grid" id="doctoresGrid">
                <?php foreach ($doctores as $doctor): ?>
                    <div class="doctor-card" data-doctor-id="<?= $doctor['EmpleadoId']; ?>">
                        <div class="doctor-avatar">
                            <i data-lucide="stethoscope"></i>
                        </div>
                        <h5 class="fw-bold">
                            <?= htmlspecialchars($doctor['Nombre'] . ' ' . $doctor['Apellido']); ?>
                        </h5>

                        <div class="doctor-specialty">Doctor Especialista</div>

                        <p class="text-muted small mb-2">
                            <i data-lucide="mail" class="me-1"></i>
                            <?= htmlspecialchars($doctor['CorreoElectronico']); ?>
                        </p>

                        <p class="doctor-availability text-success mb-0">
                            <i data-lucide="calendar-check" class="me-1"></i>Disponibilidad en tiempo real
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

        <div class="wizard-navigation">
            <div></div>
            <button class="btn btn-primary btn-wizard" onclick="wizard.nextStep(2)">
                Siguiente <i data-lucide="arrow-right" class="ms-2"></i>
            </button>
        </div>
    </div>


    <div class="wizard-step" id="step2">

        <h4 class="mb-4" style="color: var(--verde-primario);">
            <i data-lucide="calendar" class="me-2"></i>Selecciona Fecha y Hora
        </h4>
        <div class="date-time-selection">

            <div class="calendar-sidebar">
                <h6 class="fw-bold mb-3">Selecciona una fecha</h6>

                <input type="text" id="datePicker" class="form-control form-control-lg" placeholder="Elige una fecha">

                <div class="mt-4">
                    <h6 class="fw-bold mb-3">Doctor seleccionado</h6>
                    <div id="selectedDoctorInfo"></div>
                </div>
            </div>

            <div>
                <h6 class="fw-bold mb-3">Horarios disponibles</h6>

                <div id="availabilityStatus" class="availability-status" style="display:none;"></div>

                <div class="time-slots-container" id="timeSlotsContainer"></div>
            </div>
        </div>

        <div class="wizard-navigation">
            <button class="btn btn-outline-secondary btn-wizard" onclick="wizard.previousStep(1)">
                <i data-lucide="arrow-left" class="me-2"></i>Anterior
            </button>

            <button class="btn btn-primary btn-wizard" onclick="wizard.nextStep(3)">
                Siguiente <i data-lucide="arrow-right" class="ms-2"></i>
            </button>
        </div>
    </div>

 
<div class="wizard-step" id="step3">
    <h4 class="mb-4" style="color: var(--verde-primario);">
        <i data-lucide="clipboard-check" class="me-2"></i>Confirmar Cita
    </h4>

    <div class="appointment-summary-card">
        <h5 class="fw-bold mb-3">Resumen de su cita</h5>
        <div id="appointmentSummary"></div>
    </div>

    <?php $rol = $_SESSION['RolID'] ?? null; ?>

    <form id="confirmAppointmentForm" method="POST">
        <input type="hidden" name="action" value="agendar_cita">
        <input type="hidden" name="doctor_id" id="formDoctorId">
        <input type="hidden" name="fecha_hora" id="formFechaHora">


<?php if (isset($_SESSION['RolID']) && $_SESSION['RolID'] !== 'Paciente'): ?>
    <div class="external-form mt-4">

        <div class="row g-3">

            <!-- CÉDULA PRIMERO -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Cédula *</label>
                <input type="text" class="form-control" id="extCedula" name="cedula" oninput="consultarCedulaAPI()">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Nombre *</label>
                <input type="text" class="form-control" id="extNombre" name="nombre">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Apellido *</label>
                <input type="text" class="form-control" id="extApellido" name="apellido">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Teléfono *</label>
                <input type="text" class="form-control" id="extTelefono" name="telefono">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Correo *</label>
                <input type="email" class="form-control" id="extCorreo" name="correo">
            </div>

        </div>

    </div>
<?php endif; ?>

        
        <div class="mb-4">
            <label for="motivo" class="form-label fw-bold">Motivo de la consulta *</label>
            <textarea class="form-control" id="motivo" name="motivo" rows="4"
                      placeholder="Describa brevemente el motivo de su consulta..." required></textarea>
        </div>

        <div class="wizard-navigation">
            <button type="button" class="btn btn-outline-secondary btn-wizard" onclick="wizard.previousStep(2)">
                <i data-lucide="arrow-left" class="me-2"></i>Anterior
            </button>
            <button type="button" class="btn btn-success btn-wizard" id="btnConfirmarCita">
                <i data-lucide="calendar-check" class="me-2"></i>Confirmar Cita
            </button>
        </div>
    </form>
</div>

<!-- Modal Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-confirm">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i data-lucide="calendar-check" class="me-2"></i>Confirmar Cita
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="modal-icon">
                    <i data-lucide="calendar-plus"></i>
                </div>

                <h5 class="mb-3">¿Confirmar agendamiento de cita?</h5>

                <p class="confirmation-text">
                    Verifique que toda la información sea correcta.
                </p>

                <div class="appointment-details" id="modalAppointmentDetails"></div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-cancel btn-modal" data-bs-dismiss="modal">
                    Revisar Datos
                </button>

                <button type="button" class="btn btn-confirm btn-modal" id="modalConfirmButton">
                    Sí, Confirmar Cita
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Error -->
<div class="modal fade custom-error-modal" id="errorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-error-modal-content">

            <div class="custom-error-header">
                <h5 class="m-0">
                    <i data-lucide="alert-triangle"></i> Aviso Importante
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
</div>
</div>
<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="/assets/js/cita.js"></script>

<script>
    lucide.createIcons();
</script>

</body>
</html>
