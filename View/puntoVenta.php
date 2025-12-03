<?php
include('layout.php');
include_once __DIR__ . '/../Controller/puntoVentaController.php';

$controller = new PuntoVentaController();
$productos = $controller->getProductos();
$cedulaPrefill = $_GET['cedula'] ?? '';
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

<main class="container py-5 pv-wrapper ">

<div class="d-flex justify-content-end mb-3">
    <button id="toggleDarkMode" class="btn btn-outline-secondary align-items-center gap-2 px-4 py-2">
        Modo oscuro
    </button>
</div>

    
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pv-header">
        <div>
            <h2 class="mb-1 pv-title">Punto de Venta</h2>
            <p class="mb-0 text-muted small">Registra las ventas y controla el carrito en tiempo real.</p>
        </div>
    </div>

    
    <div class="pv-search-card shadow-sm mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <label for="searchInput" class="form-label fw-semibold mb-2">Buscar producto</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control buscador-producto"
                           placeholder="Ingrese nombre del producto...">
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 pv-main">
        <div class="col-lg-7 pv-products">
            <div class="pv-products-card">
                <div class="pv-products-header d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0 text-muted fw-semibold">Productos disponibles</h5>
                </div>
                <div class="row gy-3" id="productos-container"></div>
            </div>
        </div>

       
        <div class="col-lg-5">
            <div class="card shadow-sm pv-cart-card">
                <div class="card-header pv-cart-header text-white fw-bold text-center">
                    Carrito de compras
                </div>

                <div class="card-body">
                    <div class="mb-3" id="bloqueCedulaCliente">
                        <label for="cedulaCliente" class="form-label fw-semibold">Cédula del cliente</label>
                        <input type="text" id="cedulaCliente" class="form-control"
                               placeholder="Ingrese la cédula"
                               value="<?= htmlspecialchars($cedulaPrefill) ?>">

                        <span id="nombreCliente" class="mt-1 d-block text-muted small">
                            Nombre del cliente aparecerá aquí
                        </span>
                    </div>

                 
                    <div class="mb-3" id="telefonoClienteDiv" style="display:none;">
                        <label for="telefonoCliente" class="form-label fw-semibold">Teléfono</label>
                        <input type="text" id="telefonoCliente" class="form-control" placeholder="Ingrese teléfono">
                    </div>

                 
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

                    
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="facturaElectronica">
                        <label class="form-check-label fw-semibold" for="facturaElectronica">
                            Solicitar factura electrónica
                        </label>
                    </div>

                    <hr>

                    <div id="cart-items" class="pv-cart-items"></div>
                    <hr>
                    <div class="pv-totals">
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

                        <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-1">
                            <span>Total:</span>
                            <span>₡<span id="cart-total">0.00</span></span>
                        </div>
                    </div>

                    
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Monto abonado</label>
                        <input type="number" id="montoAbono" class="form-control" placeholder="Ej. 5000">
                        <small class="text-muted">Si deja este campo vacío, se cobrará el total.</small>
                    </div>

                    
                    <div class="d-flex align-items-center gap-2 mt-3">
                        <label class="form-label fw-semibold mb-0">Método de pago</label>
                        <select id="metodoPago" class="form-select pv-metodo-select">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="sinpe">SINPE Móvil</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>

                    
                    <div class="d-grid mt-3">
                        <button class="btn btn-outline-success w-100" id="btnFinalizar">
                            <i class="bi bi-check2-circle"></i> Finalizar venta
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

   
    <div class="modal fade" id="modalFactura" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content pv-modal-factura">

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

window.productos = <?php echo json_encode($productos, JSON_UNESCAPED_UNICODE); ?>;
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const cedulaGET = "<?= isset($_GET['cedula']) ? $_GET['cedula'] : '' ?>";
    const input = document.getElementById("cedulaCliente");

    console.log("GET detectado:", cedulaGET);

    if (cedulaGET !== "" && input) {
        input.value = cedulaGET;

        setTimeout(() => {
            buscarCliente();
        }, 200);
    }
});
</script>

<script src="../assets/js/puntoVenta.js?v=<?= time(); ?>"></script>

</body>
</html>