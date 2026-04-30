<?php
require_once __DIR__ . '/layout.php';
session_start();

$historial = $_SESSION['historialClinico'] ?? [];
$sinExpedientes = empty($historial);

$porPagina = 3;
$paginaActual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$paginaActual = max(1, $paginaActual);

$totalRegistros = count($historial);
$totalPaginas = ceil($totalRegistros / $porPagina);

$inicio = ($paginaActual - 1) * $porPagina;
$historialPaginado = array_slice($historial, $inicio, $porPagina);

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
        <?php if ($sinExpedientes): ?>
            <div id="mensajeSistema" class="mt-3">
                <div class="mensaje-sistema mensaje-warning text-center">
                    No hay expedientes registrados para este paciente.
                </div>
            </div>
        <?php endif; ?>

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
                        <?php foreach ($historialPaginado as $fila): ?>
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

            <?php if ($totalPaginas > 1): ?>
                <div class="d-flex justify-content-center mt-4 app-pagination">
                    <nav>
                        <ul class="pagination mb-0">

                            <!-- Anterior -->
                            <li class="page-item <?= $paginaActual <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $paginaActual - 1 ?>">
                                    ‹ Anterior
                                </a>
                            </li>

                            <?php
                            $rango = 2;
                            $inicioPag = max(1, $paginaActual - $rango);
                            $finPag = min($totalPaginas, $paginaActual + $rango);

                            if ($inicioPag > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?pagina=1">1</a></li>';
                                if ($inicioPag > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }

                            for ($i = $inicioPag; $i <= $finPag; $i++): ?>
                                <li class="page-item <?= $i == $paginaActual ? 'active' : '' ?>">
                                    <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor;

                            if ($finPag < $totalPaginas) {
                                if ($finPag < $totalPaginas - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?pagina=' . $totalPaginas . '">' . $totalPaginas . '</a></li>';
                            }
                            ?>

                            <!-- Siguiente -->
                            <li class="page-item <?= $paginaActual >= $totalPaginas ? 'disabled' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $paginaActual + 1 ?>">
                                    Siguiente ›
                                </a>
                            </li>

                        </ul>
                    </nav>
                </div>
            <?php endif; ?>

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
                    <button class="btn btn-primary" onclick="imprimirReceta()">Imprimir</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/receta.js?v=5"></script>
</body>

</html>