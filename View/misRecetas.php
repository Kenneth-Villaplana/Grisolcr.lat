<?php
include('layout.php');
include_once('../Controller/recetaController.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION["PacienteId"])) {

    if (isset($_SESSION["Cedula"])) {

        include_once('../Model/baseDatos.php');
        $conn = AbrirBD();

       
        $stmt = $conn->prepare("CALL ObtenerClientePorCedula(?)");
        $stmt->bind_param("s", $_SESSION["Cedula"]);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

       
        while ($conn->more_results() && $conn->next_result()) {
            ;
        }

        if ($result) {
            $_SESSION["PacienteId"] = $result["PacienteId"];
        }

        $stmt->close();
        CerrarBD($conn);
    }
}



if (!isset($_SESSION["PacienteId"])) {
    echo "<h3>Error: No hay sesión activa de paciente.</h3>";
    exit;
}

$pacienteId = $_SESSION["PacienteId"];

$recetas = RecetaController::listarRecetasPaciente($pacienteId);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Recetas</title>
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

    <div class="expediente-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h2 class="mb-0 d-flex align-items-center gap-2">
            <i data-lucide="pill"></i>
            Mis Recetas
        </h2>

    </div>

    <?php if (empty($recetas)): ?>

     
        <section class="expediente-section-card text-center">
            <div class="d-flex flex-column align-items-center gap-2">
                <i data-lucide="search-x"></i>
                <p class="mb-0">No se encontraron recetas registradas para este paciente.</p>
            </div>
        </section>

    <?php else: ?>

        <section class="expediente-section-card">

            <h5 class="section-title d-flex align-items-center gap-2 mb-3">
                <i data-lucide="file-text"></i>
                Historial de recetas
            </h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle shadow-sm table-header-blue mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Diagnóstico</th>
                             <th class="text-center">
                            <i class="bi bi-three-dots-vertical"></i>
                        </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($recetas as $index => $r): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $r['FechaRegistro'] ?></td>
                                <td><?= $r['Diagnostico'] ?></td>
                                <td class="text-center">
                                    <a href="verReceta.php?IdExpediente=<?= $r['IdExpediente'] ?>" 
                                       class="d-inline-flex align-items-center gap-1">
                                        <i data-lucide="eye"></i>
                                        
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </section>

    <?php endif; ?>

</main>
    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
<script> lucide.createIcons(); </script>
</body>

</html>