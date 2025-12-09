<?php
session_start();
$historial = $_SESSION['historialClinico'] ?? [];
if (empty($historial))
    die("No hay expedientes registrados para este paciente.");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial Clínico del Paciente</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'exito'): ?>
        <div class="alert alert-success text-center shadow fw-bold">
            ¡Expediente creado correctamente!
        </div>
    <?php endif; ?>
    <main class="container my-5">
        <h2 class="fw-bold text-primary mb-4">🩺 Historial Clínico del Paciente</h2>
        <a href="historialExpedientes.php" class="btn btn-secondary mb-3">⬅ Volver</a>

        <table class="table table-striped table-hover align-middle text-center shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Fecha Registro</th>
                    <th>Motivo Consulta</th>
                    <th>Diagnóstico</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial as $fila): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['IdExpediente']) ?></td>
                        <td><?= htmlspecialchars($fila['FechaRegistro']) ?></td>
                        <td><?= htmlspecialchars($fila['MotivoConsulta']) ?></td>
                        <td><?= htmlspecialchars($fila['Diagnostico']) ?></td>
                        <td><?= htmlspecialchars($fila['Estado']) ?></td>
                        <td>
                            <!-- ver expediente -->
                            <a href="verExpediente.php?ExpedienteId=<?= urlencode($fila['IdExpediente']) ?>"
                                class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Ver Expediente
                            </a>
                            <!-- imprimir -->
                            <button class="btn btn-sm btn-info" onclick="cargarReceta(<?= $fila['IdExpediente'] ?>)">
                                <i class="bi bi-printer"></i> Imprimir
                            </button>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    <div class="modal fade" id="modalImprimir" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Receta del Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="contenedorReceta"></div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="window.print()">🖨️ Imprimir</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>
    <script src="../assets/js/receta.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>