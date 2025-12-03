<?php
if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'exito') {
    echo '<div class="alert alert-success text-center">El expediente se ha creado correctamente.</div>';
}

include('layout.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Expedientes Digitales</title>
    <?php IncluirCSS(); ?>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <?php MostrarMenu(); ?>

    <main class="container my-5">

        <?php if (isset($_SESSION['mensajeInfo'])): ?>
            <div class="alert alert-warning text-center">
                <?= $_SESSION['mensajeInfo']; ?>
            </div>
            <?php unset($_SESSION['mensajeInfo']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensajeError'])): ?>
            <div class="alert alert-danger text-center">
                <?= $_SESSION['mensajeError']; ?>
            </div>
            <?php unset($_SESSION['mensajeError']); ?>
        <?php endif; ?>

        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'actualizado'): ?>
            <div class="alert alert-success text-center"> El expediente se actualizó correctamente.</div>
        <?php endif; ?>

        <div class="banner-expediente">
            <div class="banner-text">
                <h2><i class="bi bi-folder2-open"></i> Historial de Expedientes Digitales</h2>
                <p class="lead mb-0">Consulta, gestiona y administra la información de tus pacientes</p>
            </div>
        </div>

        <!-- Formulario de búsqueda -->
        <div class="container mt-4">
            <div class="card p-4 shadow">
                <h4 class="mb-3">Buscar Paciente por Cédula</h4>
                <input type="text" id="cedula" class="form-control mb-3" placeholder="Ingrese la cédula">
                <button class="btn btn-outline-primary" onclick="buscarPaciente()">Buscar</button>

                <div id="resultado" class="mt-4"></div>

                
                <div class="col-md-8 mb-3 text-center d-flex flex-column justify-content-center mt-3">
                    <div class="btn-group-vertical mx-auto" style="width: 70%;">
                        <a id="btnAgregarExpediente" class="btn btn-outline-primary mb-2" style="display:none;">
                            <i class="bi bi-plus-square"></i> Agregar Expediente
                        </a>
                        <a id="btnHistorial" class="btn btn-outline-secondary mb-2" style="display:none;">
                            <i class="bi bi-clock-history"></i> Historial Clínico
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
<script src="../assets/js/expediente.js"></script>
</body>
</html>