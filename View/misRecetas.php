<?php
include('layout.php');
include_once('../Controller/recetaController.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SI NO EXISTE PacienteId EN SESIÓN → TOMAMOS LA CÉDULA DEL LOGIN
if (!isset($_SESSION["PacienteId"])) {

    if (isset($_SESSION["Cedula"])) {

        include_once('../Model/baseDatos.php');
        $conn = AbrirBD();

        // LLAMAR AL PROCEDIMIENTO ALMACENADO
        $stmt = $conn->prepare("CALL ObtenerPacienteIdPorCedula(?)");
        $stmt->bind_param("s", $_SESSION["Cedula"]);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        // Limpiar resultsets extra del SP
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


// SI AÚN ASÍ NO TENEMOS PacienteId → ERROR
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

        <h2 class="fw-bold text-primary mb-4">📄 Mis Recetas</h2>

        <?php if (empty($recetas)): ?>
            <div class="alert alert-info text-center">
                🔎 No se encontraron recetas.
            </div>
        <?php else: ?>

            <table class="table table-hover shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Diagnóstico</th>
                        <th>Ver</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($recetas as $index => $r): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $r['FechaRegistro'] ?></td>
                            <td><?= $r['Diagnostico'] ?></td>
                            <td>
                                <a href="verReceta.php?IdExpediente=<?= $r['IdExpediente'] ?>" class="btn btn-sm btn-primary">
                                    👁 Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>

    </main>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>

</body>

</html>