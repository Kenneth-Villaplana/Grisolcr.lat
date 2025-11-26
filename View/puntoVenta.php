<?php
include('layout.php');
include_once __DIR__ . '/../Controller/puntoVentaController.php';

$controller = new PuntoVentaController();
$productos = $controller->getProductos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<title>Óptica Grisol - Punto de Venta</title>
<?php IncluirCSS(); ?>
</head>

<body>
<?php MostrarMenu(); ?>

<main class="container py-5">
    <h2 class="text-center mb-4">Punto de Venta</h2>

    <!-- Buscar producto -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <label for="searchInput" class="form-label fw-semibold mb-2">Buscar producto:</label>
            <input type="text" id="searchInput" class="form-control buscador-producto" placeholder="Ingrese nombre del producto...">
        </div>
    </div>

    <div class="row">
        <!-- Productos -->
        <div class="col-lg-7">
            <div class="row" id="productos-container"></div>
        </div>

        <!-- Carrito -->
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold text-center">
                    Carrito de compras
                </div>

                <div class="card-body">

                    <!-- Buscar cliente -->
                    <div class="mb-3" id="bloqueCedulaCliente">
                        <label for="cedulaCliente" class="form-label fw-semibold">Cédula del cliente:</label>
                        <input type="text" id="cedulaCliente" class="form-control" placeholder="Ingrese la cédula">

                        <span id="nombreCliente" class="mt-1 d-block text-muted">
                            Nombre del cliente aparecerá aquí
                        </span>
                    </div>
                    <div class="mb-3" id="telefonoClienteDiv" style="display:none;">
    <label for="telefonoCliente" class="form-label fw-semibold">Teléfono:</label>
    <input type="text" id="telefonoCliente" class="form-control" placeholder="Ingrese teléfono">
</div>
                    <!-- Facturar Empresa -->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="facturarEmpresa">
                        <label class="form-check-label fw-semibold" for="facturarEmpresa">
                            Facturar a empresa
                        </label>
                    </div>

                    <div id="datosEmpresa" style="display:none;">
                        <input id="empresaNombre" class="form-control mb-2" placeholder="Nombre de la empresa">
                        <input id="empresaIdentificacion" class="form-control" placeholder="Identificación jurídica">
                    </div>

                    <!-- Factura electrónica -->
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="facturaElectronica">
                        <label class="form-check-label fw-semibold">
                            Solicitar factura electrónica
                        </label>
                    </div>

                    <hr>

                    <div id="cart-items"></div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span>₡<span id="cart-subtotal">0.00</span></span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Descuento:</span>
                        <span>-₡<span id="cart-discount">0.00</span></span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>IVA (13%):</span>
                        <span>₡<span id="cart-tax">0.00</span></span>
                    </div>

                    <div class="d-flex justify-content-between fw-bold border-top pt-2">
                        <span>Total:</span>
                        <span>₡<span id="cart-total">0.00</span></span>
                    </div>
                    <label>Monto abonado:</label>
<input type="number" id="montoAbono" class="form-control" placeholder="Ej. 5000">
<small class="text-muted">Si deja este campo vacío, se cobrará el total.</small>

                    <!-- Método de pago -->
                    <div class="d-flex align-items-center gap-2 mt-3">
                        <label class="form-label fw-semibold mb-0">Método de pago:</label>
                        <select id="metodoPago" class="form-select" style="width: 160px;">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="sinpe">SINPE Móvil</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>

                    
                    <div class="d-grid mt-3">
                        <button class="btn btn-success w-100" id="btnFinalizar">
                            <i class="bi bi-check2-circle"></i> Finalizar venta
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Factura -->
    <div class="modal fade" id="modalFactura" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Factura generada</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="modalFacturaBody"></div>

            </div>
        </div>
    </div>

</main>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

<script>
// listado inicial de productos
window.productos = <?php echo json_encode($productos, JSON_UNESCAPED_UNICODE); ?>;
</script>

<script src="../assets/js/puntoVenta.js?v=<?= time(); ?>"></script>

</body>
</html>