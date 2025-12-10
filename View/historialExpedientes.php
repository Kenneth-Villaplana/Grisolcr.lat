<?php
include('layout.php');

if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'exito') {
    echo '<div class="alert alert-success text-center">El expediente se ha creado correctamente.</div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Expedientes Digitales</title>
    <?php IncluirCSS(); ?>
</head>

<body class="expedientes-page">

<?php MostrarMenu(); ?>


  <header class="hero-img-header personal-hero">
    <div class="container position-relative">
    <h1 class="expediente-title">Historial de Expedientes</h1>
    <p class="expediente-subtitle">
        Consulta, administra y gestiona la información de tus pacientes
    </p>
</header>

<main class="container my-5">

    <!-- Mensajes -->
    <?php if (isset($_SESSION['mensajeInfo'])): ?>
        <div class="alert alert-warning text-center"><?= $_SESSION['mensajeInfo']; ?></div>
        <?php unset($_SESSION['mensajeInfo']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensajeError'])): ?>
        <div class="alert alert-danger text-center"><?= $_SESSION['mensajeError']; ?></div>
        <?php unset($_SESSION['mensajeError']); ?>
    <?php endif; ?>

    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'actualizado'): ?>
        <div class="alert alert-success text-center">El expediente se actualizó correctamente.</div>
    <?php endif; ?>


   
    <section class="expediente-card mx-auto shadow-lg">
        <div class="text-center mb-4">
            <h3 class="fw-bold">
                <i class="bi bi-search"></i> Buscar Paciente
            </h3>
        </div>

        <div class="row g-3 justify-content-center">

            <div class="col-md-8">
                <label class="form-label fw-semibold">Cédula del Paciente:</label>
                <input type="text" id="cedula" class="form-control inventario-input" placeholder="Ej. 801230456">
            </div>

            <div class="col-md-8 d-flex justify-content-center gap-3">
                <button class="btn-inv-primary" onclick="buscarPaciente()">
                    <i class="bi bi-search me-1"></i> Buscar
                </button>

                <button class="btn-inv-ghost" onclick="location.href='expedientes.php'">
                    Limpiar
                </button>
            </div>

            <div id="resultado" class="col-12 mt-4"></div>

            
            <div class="col-md-8 text-center mt-3">
                <div class="btn-group-vertical w-75 mx-auto">
                    <a id="btnAgregarExpediente" class="btn btn-outline-primary mb-2" style="display:none;">
                        <i class="bi bi-plus-square"></i> Agregar Expediente
                    </a>
                    <a id="btnHistorial" class="btn btn-outline-secondary mb-2" style="display:none;">
                        <i class="bi bi-clock-history"></i> Historial Clínico
                    </a>
                </div>
            </div>

        </div>
    </section>

</main>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

<script src="../assets/js/expediente.js"></script>

</body>
</html>