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

  <div class="mb-1 text-end mt-3">
    <a href="puntoVenta.php" class="btn btn-back-custom">
      ← Volver a Punto de Venta
    </a>
  </div>

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pv-header">
    <div>
      <h2 class="mb-1 pv-title">Cierre de Caja</h2>
      <p class="mb-0 text-muted small">Resumen del día y registro del cierre.</p>
    </div>
  </div>

  <div class="row mt-5">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header fw-bold">
          Historial de cierres de caja
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-striped table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Fecha</th>
                <th>Cajero</th>
                <th>Facturas</th>
                <th>Total cobrado</th>
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
              <!-- JS inserta aquí -->
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
