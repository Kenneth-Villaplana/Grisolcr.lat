<?php
include('layout.php');
include_once('../Controller/recetaController.php');

$expedienteId = $_GET["IdExpediente"] ?? null;

if (!$expedienteId) {
    echo "<h3>Error: No se especificó expediente.</h3>";
    exit;
}

$receta = RecetaController::verReceta($expedienteId);

if (!$receta) {
    echo "<h3>No se encontró la receta.</h3>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Receta</title>
    <?php IncluirCSS(); ?>
</head>

<body>
    <?php MostrarMenu(); ?>

   <main class="container my-5">

    <div class="d-flex justify-content-end mb-4" data-aos="fade-down">
            <a href="javascript:history.back();" class="btn btn-back-custom">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

    <div class="expediente-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h2 class="mb-0 d-flex align-items-center gap-2">
            <i data-lucide="file-text"></i>
            Receta Óptica
        </h2>
    </div>
    
    <section class="expediente-section-card">
        <div class="text-center mb-4">
            <h3 class="fw-bold mb-1">Dr. LEONARDO SOLANO GRIJALBA</h3>
            <p class="mb-0">Licenciado en Optometría</p>

            <p class="mb-0 mt-2">
                <i data-lucide="phone"></i> 2592-5460 | 8813-9883
            </p>
            <p class="mb-0">
                <i data-lucide="mail"></i> opticagrisol@gmail.com
            </p>
            <p class="mb-0">
                <i data-lucide="map-pin"></i> Av 1, Bo. El Molino, Cartago
            </p>
        </div>
        <hr class="my-4">

        <h5 class="section-title d-flex align-items-center gap-2 mb-3">
            <i data-lucide="id-card"></i>
            Datos del Paciente
        </h5>

        <div class="row g-3 mb-4">

            <div class="col-md-6">
                <label class="fw-bold">Paciente</label>
                <input type="text" class="form-control input-modern" disabled
                    value="<?= $receta['NombrePaciente'] . ' ' . $receta['ApellidoPaciente'] . ' ' . $receta['Apellido2Paciente'] ?>">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Cédula</label>
                <input type="text" class="form-control input-modern" disabled
                    value="<?= $receta['CedulaPaciente'] ?>">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Fecha</label>
                <input type="text" class="form-control input-modern" disabled
                    value="<?= $receta['FechaRegistro'] ?>">
            </div>

        </div>


        <h5 class="section-title d-flex align-items-center gap-2 mb-3">
            <i data-lucide="ruler"></i>
            Graduación Óptica
        </h5>

        <div class="table-responsive mb-4">
            <table class="table table-hover text-center shadow-sm table-header-blue">
                <thead>
                    <tr>
                        <th>Ojo</th>
                        <th>ESF</th>
                        <th>CIL</th>
                        <th>EJE</th>
                        <th>PRISMA</th>
                        <th>ADD</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="text-primary">OD</th>
                        <td><?= $receta['Esfera_OD'] ?></td>
                        <td><?= $receta['Cilindro_OD'] ?></td>
                        <td><?= $receta['Eje_OD'] ?></td>
                        <td><?= $receta['Prisma_OD'] ?></td>
                        <td><?= $receta['Adicion_OD'] ?></td>
                    </tr>

                    <tr>
                        <th class="text-primary">OI</th>
                        <td><?= $receta['Esfera_OI'] ?></td>
                        <td><?= $receta['Cilindro_OI'] ?></td>
                        <td><?= $receta['Eje_OI'] ?></td>
                        <td><?= $receta['Prisma_OI'] ?></td>
                        <td><?= $receta['Adicion_OI'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>


        
        <h5 class="section-title d-flex align-items-center gap-2 mb-3">
            <i data-lucide="file-search"></i>
            Observaciones
        </h5>

        <textarea class="form-control input-modern" rows="3" disabled><?= $receta['Diagnostico'] ?></textarea>

        <br>
    </section>
</main>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
<script> lucide.createIcons(); </script>
</body>

</html>