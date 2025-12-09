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

    <main>

        <a href="misRecetas.php" class="btn btn-outline-secondary mb-3">⬅ Volver</a>

        <div class="receta-container">

            <!-- ENCABEZADO -->
            <div class="header">
                <div class="logo">
                    <img src="../assets/img/logo.jpg" alt="Logo" height="80">
                </div>
                <div class="doctor">
                    <h3>Dr. LEONARDO SOLANO GRIJALBA</h3>
                    <p>Lic. en Optometría</p>
                    <p>2592-5460 | 8813-9883</p>
                    <p>opticagrisol@gmail.com</p>
                    <p>Av 1, Bo. El Molino, Cartago</p>
                </div>
            </div>

            <hr>

            <!-- DATOS GENERALES -->
            <div class="datos">
                <p><strong>Paciente:</strong>
                    <?= $receta['NombrePaciente'] . " " . $receta['ApellidoPaciente'] . " " . $receta['Apellido2Paciente'] ?>
                </p>
                <p><strong>Cédula:</strong> <?= $receta['CedulaPaciente'] ?></p>
                <p><strong>Fecha:</strong> <?= $receta['FechaRegistro'] ?></p>
                <p><strong>Diagnóstico:</strong> <?= $receta['Diagnostico'] ?></p>
            </div>

            <!-- TABLA DE GRADUACIÓN -->
            <table class="tabla-receta">
                <thead>
                    <tr>
                        <th></th>
                        <th>ESF</th>
                        <th>CIL</th>
                        <th>EJE</th>
                        <th>PRISMA</th>
                        <th>ADD</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>OD</strong></td>
                        <td><?= $receta['Esfera_OD'] ?></td>
                        <td><?= $receta['Cilindro_OD'] ?></td>
                        <td><?= $receta['Eje_OD'] ?></td>
                        <td><?= $receta['Prisma_OD'] ?></td>
                        <td><?= $receta['Adicion_OD'] ?></td>
                    </tr>

                    <tr>
                        <td><strong>OI</strong></td>
                        <td><?= $receta['Esfera_OI'] ?></td>
                        <td><?= $receta['Cilindro_OI'] ?></td>
                        <td><?= $receta['Eje_OI'] ?></td>
                        <td><?= $receta['Prisma_OI'] ?></td>
                        <td><?= $receta['Adicion_OI'] ?></td>
                    </tr>
                </tbody>
            </table>

            <br>

            <!-- OBSERVACIONES -->
            <div class="observaciones">
                <p><strong>Observaciones:</strong></p>
                <p><?= $receta['Diagnostico'] ?></p>
            </div>

            <br><br>

            <!-- FIRMA -->
            <div class="firma-block">
                <p>_______________________________</p>
                <p>Firma del Optometrista</p>
            </div>

        </div>


    </main>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>

</body>

</html>