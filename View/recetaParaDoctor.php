<?php
include_once("../Controller/recetaController.php");

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

<div class="receta-container">

    <!-- ENCABEZADO -->
    <div class="header d-flex align-items-start gap-3">
        <img src="../assets/img/logo.jpg" alt="Logo" height="80">

        <div class="doctor">
            <h3>Dr. LEONARDO SOLANO GRIJALBA</h3>
            <p>Lic. en Optometría</p>
            <p>2592-5460 | 8813-9883</p>
            <p>opticagrisol@gmail.com</p>
            <p>Av 1, Bo. El Molino, Cartago</p>
        </div>
    </div>

    <hr>

    <!-- DATOS -->
    <p><strong>Paciente:</strong>
        <?= $receta['NombrePaciente'] . " " . $receta['ApellidoPaciente'] . " " . $receta['Apellido2Paciente'] ?>
    </p>
    <p><strong>Cédula:</strong> <?= $receta['CedulaPaciente'] ?></p>
    <p><strong>Fecha:</strong> <?= $receta['FechaRegistro'] ?></p>
    <p><strong>Diagnóstico:</strong> <?= $receta['Diagnostico'] ?></p>


    <!-- TABLA -->
    <table class="table table-bordered text-center mt-3">
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

    <div class="mt-3">
        <strong>Observaciones:</strong>
        <p><?= $receta['Diagnostico'] ?></p>
    </div>

    <div class="firma-block text-center mt-5">
        <p>_______________________________</p>
        <p>Firma del Optometrista</p>
    </div>

</div>