<?php 
include('layout.php');
include_once __DIR__ . '/../Controller/FacturacionController.php';
include_once __DIR__ . '/../Controller/puntoVentaController.php';


$controller = new PuntoVentaController();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<title>Óptica Grisol - Facturación</title>
<?php IncluirCSS(); ?>
</head>

<body>
<?php MostrarMenu(); ?>

<main class="container py-5 facturacion-wrapper">
  
  <div class="facturacion-card shadow-lg bg-white rounded-4 p-4 p-md-5">


    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
      <div>
        <h2 class="mb-1 titulo-facturacion">Facturación</h2>
      
      </div>

      <div>
        <a href="puntoVenta.php" class="btn btn-nueva-factura d-inline-flex align-items-center gap-2">
          <i class="bi bi-plus-circle"></i>
          <span>Nueva Factura</span>
        </a>
      </div>
    </div>

    <!-- Filtros -->
    <div class="filter-card shadow-sm rounded-3 p-3 p-md-4 mb-4">
      <form class="filter-form text-center" style="max-width: 450px; width: 100%; margin: 0 auto;">
        <div class="mb-3">
          <label for="codigoInput" class="form-label mb-1 fw-semibold text-muted small">
            Número de factura
          </label>
          <input type="text" id="codigoInput" class="form-control text-center filtro-input"
                 placeholder="">
        </div>
        <div class="mb-3">
          <label for="cedulaInput" class="form-label mb-1 fw-semibold text-muted small">
            Cédula del cliente
          </label>
          <input type="text" id="cedulaInput" class="form-control text-center filtro-input"
                 placeholder="">
        </div>
        <div class="d-flex justify-content-center gap-2 mt-2">
          <button type="button" id="btnBuscar" class="btn btn-custom px-4">
            <i class="bi bi-search"></i> Buscar
          </button>
          <button type="button" id="btnLimpiar" class="btn btn-outline-secondary px-4">
            Limpiar
          </button>
        </div>
      </form>
    </div>

   
    <div class="table-responsive tabla-facturas-wrapper">
      <table class="table align-middle mb-0 tabla-facturas" id="facturasTable">
        <thead>
          <tr>
            <th>Número</th>
            <th>Fecha</th>
            <th>Cédula</th>
            <th>Cliente</th>
            <th>Telefono</th>
            <th>Productos</th>
            <th class="text-end">Total</th>
            <th class="text-end">Saldo pendiente</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody id="facturas-body">
          <tr>
            <td colspan="9" class="text-center text-muted py-4">
              Cargando facturas...
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</main>

<!-- Modal de Factura Digital -->
<div class="modal fade" id="modalFactura" tabindex="-1" aria-labelledby="modalFacturaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content modal-factura">

          <div class="modal-header border-0 pb-0">
              <div>
                <h5 class="modal-title fw-bold" id="modalFacturaLabel">Factura digital</h5>
                <p class="mb-0 text-muted small">Detalle completo de la venta seleccionada.</p>
              </div>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>

          <div class="modal-body" id="facturaContenido">
              <div class="text-center py-5 text-muted">Seleccione una factura para visualizar.</div>
          </div>

          <div class="modal-footer border-0 pt-0">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
      </div>
  </div>
</div>

<!-- Modal de Abonos -->
<div class="modal fade" id="modalAbono" tabindex="-1">
  <div class="modal-dialog">
      <div class="modal-content modal-abono">

          <div class="modal-header border-0 pb-0">
              <div>
                <h5 class="modal-title fw-bold">Registrar abono</h5>
                <p class="mb-0 text-muted small">Aplica un pago parcial a la factura seleccionada.</p>
              </div>
              <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
              <input type="hidden" id="abonoFacturaId">

              <div class="mb-3">
                  <label class="form-label fw-semibold">Saldo pendiente</label>
                  <input type="text" id="abonoSaldo" class="form-control" readonly>
              </div>

              <div>
                  <label class="form-label fw-semibold">Monto a abonar</label>
                  <input type="number" id="abonoMonto" class="form-control" min="0" step="0.01"
                         placeholder="Ej. 15000">
              </div>
          </div>

          <div class="modal-footer border-0 pt-0">
              <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button class="btn btn-success" onclick="guardarAbono()">
                  <i class="bi bi-cash-coin"></i> Confirmar abono
              </button>
          </div>

      </div>
  </div>
</div>

<!-- Modal para imprimir recibo de abono -->
<div class="modal fade" id="modalReciboAbono" tabindex="-1">
  <div class="modal-dialog">
      <div class="modal-content modal-recibo">

          <div class="modal-header border-0 pb-0">
              <h5 class="modal-title fw-bold">Recibo de abono</h5>
              <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body" id="reciboAbonoBody">
          </div>

          <div class="modal-footer border-0 pt-0">
              <button class="btn btn-primary" onclick="imprimirReciboAbono()">
                  <i class="bi bi-printer"></i> Imprimir
              </button>
          </div>

      </div>
  </div>
</div>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>


<script src="../assets/js/facturacion.js?v=<?= time(); ?>"></script>

</body>
</html>