<?php

include('layout.php');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Óptica Grisol - Cierre de Caja</title>
    <?php IncluirCSS(); ?>
</head>

<body>
    <?php MostrarMenu(); ?>
    <main class="cierre-main-wrapper">
        <div class="container-xl px-4">

            <div class="text-end mb-4">
                <a href="puntoVenta.php" class="btn btn-staff-outline rounded-pill px-4">
                    ← Volver a Punto de Venta
                </a>
            </div>

            <div class="cierre-card-wrapper">
                <div class="cierre-card-inner">

                    <div class="cierre-header text-center mb-3">
                        <h5 class="fw-semibold mb-1">Historial de cierres de caja</h5>

                        <span class="cierre-chip-count">
                            <i class="bi bi-cash-stack"></i>
                            <span id="cantidadCierres">0</span> cierres registrados
                        </span>
                    </div>
                    <div class="cierre-resumen-grid mb-4">

                        <div class="cierre-resumen-item">
                            <span class="resumen-label">Total Cobrado</span>
                            <span class="resumen-value" id="resumen-total">₡0.00</span>
                        </div>

                        <div class="cierre-resumen-item">
                            <span class="resumen-label">Efectivo</span>
                            <span class="resumen-value" id="resumen-efectivo">₡0.00</span>
                        </div>

                        <div class="cierre-resumen-item">
                            <span class="resumen-label">Tarjeta</span>
                            <span class="resumen-value" id="resumen-tarjeta">₡0.00</span>
                        </div>

                        <div class="cierre-resumen-item">
                            <span class="resumen-label">SINPE</span>
                            <span class="resumen-value" id="resumen-sinpe">₡0.00</span>
                        </div>

                        <div class="cierre-resumen-item">
                            <span class="resumen-label">Transferencia</span>
                            <span class="resumen-value" id="resumen-transferencia">₡0.00</span>
                        </div>

                    </div>
                    <div class="table-responsive">
                        <table class="table cierre-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cajero</th>
                                    <th>Facturas</th>
                                    <th>Total</th>
                                    <th>Efectivo</th>
                                    <th>Tarjeta</th>
                                    <th>SINPE</th>
                                    <th>Transferencia</th>
                                    <th>Efectivo contado</th>
                                    <th>Diferencia</th>
                                    <th>Hora cierre</th>
                                </tr>
                            </thead>

                            <tbody id="tablaCierres">
                                <!-- JS inserta filas aquí -->
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </main>


    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>

    <script src="../assets/js/cierreCaja.js?v=<?= time(); ?>"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            cargarHistorialCierres();
        });
    </script>

</body>

</html>