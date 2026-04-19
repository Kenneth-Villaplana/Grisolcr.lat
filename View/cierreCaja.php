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

    <main class="container py-5 pv-wrapper">

        <!-- Botón arriba a la derecha -->
        <div class="d-flex justify-content-end mt-3 mb-2">
            <a href="puntoVenta.php" class="btn btn-back-custom">
                ← Volver a Punto de Venta
            </a>
        </div>

        <!-- Header centrado -->
        <div class="pv-header text-center mb-4">
            <h2 class="pv-title mb-1">Cierre de Caja</h2>
            <p class="text-muted mb-0">Resumen del día y registro del cierre.</p>
        </div>

        <div class="cc-dashboard-card">
            <div class="row g-2">


                <!-- RESUMEN DEL DÍA -->
                <div class="col-12">
                    <div class="card pv-products-card">
                        <div class="pv-products-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-muted fw-semibold">Resumen del día</h5>
                            <span class="badge bg-light text-dark border" id="cc-fecha">Hoy</span>
                        </div>

                        <div class="card-body pt-0">
                            <div class="row g-2">

                                <!-- Facturas -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="border rounded-4 p-3 h-100 card-facturas">
                                        <div class="text-muted small">Facturas</div>
                                        <div class="fs-4 fw-bold" id="cc-cantidad">0</div>
                                        <div class="text-muted small mt-2">Total facturado</div>
                                        <div class="fs-5 fw-semibold">₡<span id="cc-total-facturado">0.00</span></div>
                                    </div>
                                </div>

                                <!-- Cobros -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="border rounded-4 p-3 h-100 shadow-sm card-cobros">
                                        <div class="text-muted small">Cobros (incluye abonos)</div>
                                        <div class="fs-5 fw-semibold">₡<span id="cc-cobros">0.00</span></div>
                                    </div>
                                </div>

                                <!-- Totales -->
                                <div class="col-lg-4 col-md-12">
                                    <div
                                        class="border rounded-4 p-3 h-100 shadow-sm card-totales d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Subtotal</span>
                                                <span class="fw-semibold">₡<span id="cc-subtotal">0.00</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Descuento</span>
                                                <span class="fw-semibold">-₡<span id="cc-descuento">0.00</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">IVA</span>
                                                <span class="fw-semibold">₡<span id="cc-iva">0.00</span></span>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between border-top pt-2 mt-3">
                                            <span class="fw-bold">Total</span>
                                            <span class="fw-bold">₡<span id="cc-total">0.00</span></span>
                                        </div>

                                    </div>

                                </div>

                                <div class="card-header text-white fw-semibold pv-cart-header text-center">
                                    Control de caja
                                </div>

                                <!-- IZQUIERDA -->
                                <div class="col-lg-7">
                                    <div class="cc-metodos-box">
                                        <div class="text-muted small mb-3">Desglose por método</div>

                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Efectivo</span>
                                            <span class="fw-semibold">₡<span id="cc-efectivo">0.00</span></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tarjeta</span>
                                            <span class="fw-semibold">₡<span id="cc-tarjeta">0.00</span></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>SINPE</span>
                                            <span class="fw-semibold">₡<span id="cc-sinpe">0.00</span></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Transferencia</span>
                                            <span class="fw-semibold">₡<span id="cc-transferencia">0.00</span></span>
                                        </div>

                                        <hr>

                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Efectivo esperado</span>
                                            <span class="fw-bold">₡<span id="cc-efectivo-esperado">0.00</span></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- DERECHA -->
                                <div class="col-lg-5">
                                    <div class="cc-action-panel d-flex flex-column">
                                        <div class="text-center fw-semibold mb-3">Cierre manual</div>

                                        <div class="mb-2">
                                            <input type="number" id="cc-efectivo-contado" class="form-control"
                                                placeholder="Ej. 25000">
                                        </div>

                                        <small class="text-muted mb-3 d-block">
                                            Ingrese lo contado en caja para calcular la diferencia.
                                        </small>

                                        <div class="cc-diferencia-box mb-3">
                                            <div class="text-muted small mb-1">Diferencia</div>
                                            <div class="fw-bold fs-2">₡<span id="cc-diferencia">0.00</span></div>
                                        </div>

                                        <div id="cc-error-msg" class="pv-error-msg mt-2" style="display:none;"></div>

                                        <button class="btn mt-auto" id="btnCerrarCaja">
                                            <i class="bi bi-check2-circle me-2"></i>Cerrar caja
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal aviso estilo POS -->
                    <div class="modal fade" id="modalAlertaCC" tabindex="-1">
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <div class="modal-content shadow rounded-4">

                                <div class="modal-header border-0 pb-0 text-center">
                                    <h6 class="modal-title fw-bold text-center">Aviso</h6>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body text-center" id="modalAlertaCCBody"
                                    style="font-size: 0.95rem; padding-top: 0;">
                                </div>

                                <div class="modal-footer border-0 pt-0 d-flex justify-content-center">
                                    <button class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">
                                        Aceptar
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Modal confirmación cierre de caja -->
                    <div class="modal fade" id="modalConfirmarCierre" tabindex="-1">
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <div class="modal-content shadow rounded-4">

                                <div class="modal-header border-0 pb-0 text-center">
                                    <h6 class="modal-title fw-bold w-100">Confirmar cierre</h6>
                                </div>

                                <div class="modal-body text-center" style="font-size: 0.95rem;">
                                    ¿Está seguro que desea cerrar la caja del día?<br>
                                    <span class="text-muted small">
                                        Esta acción no se puede deshacer.
                                    </span>
                                </div>

                                <div class="modal-footer border-0 pt-0 d-flex justify-content-center gap-2">
                                    <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                        Cancelar
                                    </button>
                                    <button class="btn btn-danger rounded-pill px-4" id="btnConfirmarCierre">
                                        Sí, cerrar
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
    </main>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>

    <script src="../assets/js/cierreCaja.js?v=<?= time(); ?>"></script>
</body>

</html>