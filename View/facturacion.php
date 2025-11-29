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
        <h2 class="mb-1 titulo-facturacion fw-bold">Facturación</h2>
        <span class="text-muted small">Consulta, gestiona y administra las facturas.</span>
      </div>

      <div>
        <a href="puntoVenta.php" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
    <i class="bi bi-plus-circle"></i>
    <span>Nueva Factura</span>
</a>
      </div>
    </div>

  
   <div class="filter-card shadow-sm rounded-3 p-3 p-md-4 mb-4">
  <form class="filter-form" style="max-width: 700px; margin: 0 auto;">
      <div class="filtros-grid mb-3">

          <div class="form-group">
              <label class="form-label mb-1">Número de factura</label>
              <input type="text" id="codigoInput" class="form-control filtro-input text-center">
          </div>

          <div class="form-group">
              <label class="form-label mb-1">Cédula del cliente</label>
              <input type="text" id="cedulaInput" class="form-control filtro-input text-center">
          </div>

      </div>

      <div class="d-flex justify-content-center gap-3">
         <button type="button" id="btnBuscar" class="btn btn-outline-primary px-4">
    <i class="bi bi-search"></i> Buscar
</button>
          <button type="button" id="btnLimpiar" class="btn btn-outline-secondary px-4">
              Limpiar
          </button>
      </div>

  </form>

</div>

   
    <div class="table-responsive tabla-facturas-wrapper">
      <table class="table table-hover align-middle mb-0 tabla-facturas" id="facturasTable">
        <thead class="table-light">
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

          <div class="modal-footer border-0 pt-0">
              <button class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
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
              <button class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
              <button class="btn btn-success rounded-pill" onclick="guardarAbono()">
                  <i class="bi bi-cash-coin"></i> Confirmar abono
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
              <button class="btn btn-primary rounded-pill" onclick="imprimirReciboAbono()">
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