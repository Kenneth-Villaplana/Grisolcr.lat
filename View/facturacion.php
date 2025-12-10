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

  <!-- CARD PRINCIPAL -->
  <div class="facturacion-card shadow-lg rounded-4 p-4 p-md-5">

    <!-- TÍTULO + NUEVA FACTURA -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
      <div>
        <h2 class="mb-1 titulo-facturacion">Facturación</h2>
        <span class="text-muted small">Consulta, gestiona y administra las facturas.</span>
      </div>

      <div>
        <a href="puntoVenta.php"
           class="btn btn-staff-outline rounded-pill d-flex align-items-center gap-2 px-4 py-2 justify-content-center">
                <i class="bi bi-plus-circle"></i>
                <span>Nueva factura</span>
        </a>
      </div>
    </div>

    <!-- FILTROS -->
    <div class="filter-card shadow-sm rounded-4 mb-4">
      <form class="filter-form" style="max-width: 780px; margin: 0 auto;">

        <div class="filtros-grid mb-3">
          <!-- Filtro número -->
          <div class="form-group">
            <label class="form-label mb-1">Número de factura</label>
            <input type="text"
                   id="codigoInput"
                   class="form-control filtro-input text-center"
                   placeholder="Ej. 1023">
          </div>

          <!-- Filtro cédula -->
          <div class="form-group">
            <label class="form-label mb-1">Cédula del cliente</label>
            <input type="text"
                   id="cedulaInput"
                   class="form-control filtro-input text-center"
                   placeholder="Ej. 01XXXXXX">
          </div>
        </div>

        <div class="d-flex justify-content-center gap-3">
          <button type="button" id="btnBuscar" class="btn-inv-primary d-inline-flex align-items-center gap-2">
            <i class="bi bi-search"></i><span>Buscar</span>
          </button>

          <button type="button" id="btnLimpiar" class="btn-inv-ghost">
            Limpiar
          </button>
        </div>

      </form>
    </div>

    <!-- TABLA CON SCROLL -->
    <div class="table-responsive tabla-facturas-wrapper rounded-4">
      <table class="table align-middle tabla-facturas" id="facturasTable">
        <thead>
          <tr>
            <th>Número</th>
            <th>Fecha</th>
            <th>Cédula</th>
            <th>Cliente</th>
            <th>Teléfono</th>
            <th>Productos</th>
            <th class="text-end">Total</th>
            <th class="text-end">Saldo pendiente</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>

        <tbody id="facturas-body">
          <tr>
            <td colspan="10" class="text-center text-muted py-4">
              Cargando facturas...
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div> 
</main>




<div class="modal fade" id="modalFactura" tabindex="-1" aria-labelledby="modalFacturaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content modal-factura rounded-4">

          <div class="modal-header border-0 pb-0">
              <div>
                <h5 class="modal-title fw-bold" id="modalFacturaLabel">Factura digital</h5>
                <p class="mb-0 text-muted small">Detalle de la venta seleccionada.</p>
              </div>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body" id="facturaContenido">
              <div class="text-center py-5 text-muted">Seleccione una factura para visualizar.</div>
          </div>

      

      </div>
  </div>
</div>



<div class="modal fade" id="modalAbono" tabindex="-1">
  <div class="modal-dialog">
      <div class="modal-content modal-abono rounded-4">

          <div class="modal-header border-0 pb-0">
              <div>
                <h5 class="modal-title fw-bold">Registrar abono</h5>
                <p class="mb-0 text-muted small">Aplica un pago parcial a la factura seleccionada.</p>
              </div>
              <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
              <input type="hidden" id="abonoFacturaId">

              <label class="form-label fw-semibold">Saldo pendiente</label>
              <input type="text" id="abonoSaldo" class="form-control mb-3" readonly>

              <label class="form-label fw-semibold">Monto a abonar</label>
              <input type="number" id="abonoMonto" class="form-control" min="0" step="0.01"
                     placeholder="Ej. 15000">
          </div>

          <div class="modal-footer border-0 pt-0">
             
              <button class="btn btn-save-modern rounded-pill d-flex align-items-center gap-2" onclick="guardarAbono()">
                  <i class=" "></i> Confirmar
              </button>
          </div>

      </div>
  </div>
</div>



<div class="modal fade" id="modalReciboAbono" tabindex="-1">
  <div class="modal-dialog">
      <div class="modal-content modal-recibo rounded-4">

          <div class="modal-header border-0 pb-0">
              <h5 class="modal-title fw-bold">Recibo de abono</h5>
              <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body" id="reciboAbonoBody"></div>

          <div class="modal-footer border-0 pt-0">
              <button class="btn btn-primary rounded-pill d-flex align-items-center gap-2" onclick="imprimirReciboAbono()">
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