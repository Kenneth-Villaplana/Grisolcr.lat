<?php
session_start();
require_once __DIR__ . '/../Controller/citaController.php';
include('layout.php');

// Nombre del paciente para el resumen
$nombrePaciente = $_SESSION["Nombre"] . " " . $_SESSION["Apellido"];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agendar Cita - Óptica Grisol</title>

    <?php IncluirCSS(); ?>

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>

<body class="bg-main">

<?php MostrarMenu(); ?>

<!-- ============================================================================
     HERO SUPERIOR
============================================================================ -->
<section class="cita-hero text-center">
    <h1 class="fw-bold">
        <i data-lucide="calendar-check"></i> Agendar una Cita
    </h1>
    <p class="cita-hero-subtitle">Programa tu consulta de manera rápida y sencilla</p>
</section>

<div class="container py-4">

    <!-- ============================================================================
         MENSAJES DE ÉXITO / ERROR
    ========================================================================= -->
    <?php if(!empty($mensajeExito)): ?>
        <div class="alert alert-success shadow-sm d-flex align-items-center gap-2">
            <i data-lucide="check-circle"></i> <?= $mensajeExito ?>
        </div>
    <?php endif; ?>

    <?php if(!empty($mensajeError)): ?>
        <div class="alert alert-danger shadow-sm d-flex align-items-center gap-2">
            <i data-lucide="alert-triangle"></i> <?= $mensajeError ?>
        </div>
    <?php endif; ?>


    <!-- ============================================================================
         CONTENEDOR PRINCIPAL DEL WIZARD
    ========================================================================= -->
    <div class="cita-wizard-wrapper shadow-lg">

        <!-- Hidden para JS -->
        <input type="hidden" id="pacienteNombre" value="<?= $nombrePaciente ?>">

        <!-- ============================================================================
             BARRA DE PROGRESO
        ========================================================================= -->
        <div class="cita-wizard-progress text-center">
            <div class="cita-progress-step active" data-step="1">
                <div class="cita-step-circle">1</div>
                <span>Doctor</span>
            </div>

            <div class="cita-progress-line"></div>

            <div class="cita-progress-step" data-step="2">
                <div class="cita-step-circle">2</div>
                <span>Fecha & Hora</span>
            </div>

            <div class="cita-progress-line"></div>

            <div class="cita-progress-step" data-step="3">
                <div class="cita-step-circle">3</div>
                <span>Confirmar</span>
            </div>
        </div>


        <!-- ============================================================================
             PASO 1 — SELECCIONAR DOCTOR
        ========================================================================= -->
        <div class="cita-step-content active" id="step1">

            <h3 class="cita-section-title">
                <i data-lucide="stethoscope"></i> Selecciona tu doctor
            </h3>

            <?php if (empty($doctores)): ?>
                <div class="alert alert-warning d-flex align-items-center gap-2">
                    <i data-lucide="info"></i> No hay doctores disponibles.
                </div>
            <?php else: ?>

            <div class="cita-doctor-grid">
            <?php foreach ($doctores as $doctor): ?>
                <div class="cita-doctor-card shadow-sm" data-doctor-id="<?= $doctor['EmpleadoId']; ?>">

                    <div class="cita-doctor-avatar">
                        <i data-lucide="user"></i>
                    </div>

                    <h4 class="cita-doctor-name text-center">
                        <?= $doctor['Nombre'] . " " . $doctor['Apellido']; ?>
                    </h4>

                    <p class="cita-doctor-role text-muted">Doctor Especialista</p>

                    <p class="cita-doctor-email">
                        <i data-lucide="mail"></i> <?= $doctor['CorreoElectronico']; ?>
                    </p>

                </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="mt-4 text-end">
                <button class="btn cita-btn-next" onclick="wizard.nextStep(2)">
                    Continuar <i data-lucide="arrow-right"></i>
                </button>
            </div>
        </div>


        <!-- ============================================================================
             PASO 2 — FECHA Y HORAS
        ========================================================================= -->
        <div class="cita-step-content" id="step2">

            <h3 class="cita-section-title">
                <i data-lucide="calendar"></i> Selecciona Fecha y Hora
            </h3>

            <div class="cita-date-hour-grid">

                <!-- PANEL IZQUIERDO -->
                <div class="cita-panel-box shadow-sm">

                    <label class="cita-panel-label">
                        <i data-lucide="calendar-days"></i> Fecha
                    </label>

                    <input type="text" id="datePicker" class="form-control form-control-lg highlight-input">
                    
                    <div id="selectedDoctorInfo" class="mt-4"></div>

                </div>

                <!-- PANEL DERECHO -->
                <div class="cita-panel-box shadow-sm">

                    <label class="cita-panel-label">
                        <i data-lucide="clock"></i> Horarios disponibles
                    </label>

                    <div id="availabilityStatus" class="availability-message"></div>

                    <div id="timeSlotsContainer" class="cita-time-grid"></div>

                </div>

            </div>

            <div class="cita-nav d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary" onclick="wizard.previousStep(1)">
                    <i data-lucide="arrow-left"></i> Volver
                </button>

                <button class="btn cita-btn-next" onclick="wizard.nextStep(3)">
                    Continuar <i data-lucide="arrow-right"></i>
                </button>
            </div>

        </div>


        <!-- ============================================================================
             PASO 3 — CONFIRMACIÓN
        ========================================================================= -->
        <div class="cita-step-content" id="step3">

            <h3 class="cita-section-title">
                <i data-lucide="file-text"></i> Confirmación de la cita
            </h3>

            <div class="cita-summary-box shadow-sm">
                <h5 class="fw-bold">Resumen de la cita</h5>
                <div id="appointmentSummary" class="mt-3"></div>
            </div>

            <form id="confirmAppointmentForm" method="POST">

                <input type="hidden" name="action" value="agendar_cita">
                <input type="hidden" name="doctor_id" id="formDoctorId">
                <input type="hidden" name="fecha_hora" id="formFechaHora">

                <label class="form-label fw-bold mt-4">Motivo de la consulta *</label>

                <textarea class="form-control cita-motivo-input"
                          id="motivo" name="motivo" rows="4"
                          placeholder="Describe el motivo de tu consulta..."></textarea>

                <div class="cita-nav d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-back-custom"
                            onclick="wizard.previousStep(2)">
                <i class="bi bi-arrow-left"></i></i> Volver
                    </button>
    
                    <button type="button" id="btnConfirmarCita"
                            class="btn cita-btn-submit">
                        <i data-lucide="check-circle"></i> Confirmar Cita
                    </button>
                </div>

            </form>

        </div>

    </div>
</div>


<!-- ============================================================================
     MODAL DE CONFIRMACIÓN
============================================================================ -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3 shadow-lg">

            <div class="modal-header border-0">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i data-lucide="calendar-check"></i> Confirmar Cita
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="lead mb-3">Confirma que los datos de tu cita sean correctos.</p>

                <div id="modalAppointmentDetails"
                     class="cita-summary-modal"></div>
            </div>

            <div class="modal-footer border-0 d-flex justify-content-center gap-3">
                <button class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i data-lucide="edit"></i> Revisar
                </button>
                <button class="btn cita-btn-submit px-4" id="modalConfirmButton">
                    <i data-lucide="check-circle"></i> Confirmar
                </button>
            </div>

        </div>
    </div>
</div>
<!-- ===========================================================
     MODAL DE ERROR — VERSIÓN QUE NO PUEDE SER IGNORADA
=========================================================== -->
<div class="modal fade custom-error-modal" id="errorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-error-modal-content">

            <div class="custom-error-header">
                <h5 class="m-0">
                    <i data-lucide="alert-octagon"></i> Aviso Importante
                </h5>
                <button class="btn-close custom-close-btn" data-bs-dismiss="modal"></button>
            </div>

            <div class="custom-error-body">
                <p id="errorModalMessage"></p>
            </div>

            <div class="custom-error-footer">
                <button class="btn custom-error-btn" data-bs-dismiss="modal">
                    <i data-lucide="x-circle"></i> Entendido
                </button>
            </div>

        </div>
    </div>
</div>


<!-- ============================================================================
     SCRIPTS
============================================================================ -->
<script> lucide.createIcons(); </script>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="/OptiGestion/assets/js/cita.js"></script>

</body>
</html>
