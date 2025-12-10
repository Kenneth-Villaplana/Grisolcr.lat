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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'exito'): ?>
    <div class="alert alert-success text-center shadow fw-bold mt-3 mb-2">
        ¡Expediente creado correctamente!
    </div>
<?php endif; ?>


<main class="container hc-wrapper">

    
    <div class="hc-header mb-4 mt-4">
        <h2 class="hc-header-title">Historial Clínico del Paciente</h2>
        <p class="hc-header-subtitle">Listado de expedientes registrados para el paciente seleccionado.</p>
    </div>

   
    <div class="d-flex justify-content-end mb-3">
        <a href="historialExpedientes.php" class="btn btn-back-custom">
                <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    
    <div class="hc-table-wrapper">

        <div class="table-responsive">
            <table class="table hc-table align-middle mb-0">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha Registro</th>
                        <th>Motivo Consulta</th>
                        <th>Diagnóstico</th>
                        <th class="text-center">
                            <i class="bi bi-three-dots-vertical"></i>
                        </th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($historial as $fila): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['IdExpediente']) ?></td>
                        <td><?= htmlspecialchars($fila['FechaRegistro']) ?></td>
                        <td><?= htmlspecialchars($fila['MotivoConsulta']) ?></td>
                        <td><?= htmlspecialchars($fila['Diagnostico']) ?></td>
                        

                       <td class="acciones-col">
                        <a href="verExpediente.php?ExpedienteId=<?= urlencode($fila['IdExpediente']) ?>"
                        class="btn-hc btn-hc-info">
                            Ver
                        </a>

                        <button class="btn-hc btn-hc-secondary"
                                onclick="cargarReceta(<?= $fila['IdExpediente'] ?>)">
                            Imprimir
                        </button>
                    </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    </div>
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
                    <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>

<script src="../assets/js/receta.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>