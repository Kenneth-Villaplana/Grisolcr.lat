<?php
include('layout.php');
include_once __DIR__ . '/../Model/productoModel.php';

$productoFiltro = $_GET['idProducto'] ?? null;
$listaProductos = ObtenerProductos($productoFiltro);

// Productos con inventario bajo
$productosBajos = array_filter($listaProductos, function ($producto) {
    $cantidad = isset($producto['Cantidad']) ? (int)$producto['Cantidad'] : 0;
    return $cantidad <= 10;
});
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Óptica Grisol - Inventario</title>
  <?php IncluirCSS(); ?>
</head>

<body>

<?php MostrarMenu(); ?>

<?php if (!empty($productosBajos)): ?>
<div class="container mt-4">

  <div class="alert alert-warning text-center py-2 mb-3 inventario-low-title">
    ⚠ <strong>Productos con inventario bajo</strong>
  </div>

  <div class="row justify-content-center">
    <?php foreach ($productosBajos as $p): ?>
      <div class="col-md-4 mb-3">
        <div class="low-stock-card shadow-sm rounded-4">

          <div class="low-stock-pill mb-2">
            <span class="dot"></span>
            Inventario bajo
          </div>

          <p class="fw-semibold mt-1 mb-1">
            <?php echo htmlspecialchars($p['Nombre'] ?? ''); ?>
          </p>

          <p class="text-muted mb-0 small">
            Cantidad restante:
            <strong class="text-dark"><?php echo isset($p['Cantidad']) ? $p['Cantidad'] : 0; ?> unidades</strong>
          </p>

        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>
<?php endif; ?>


<section class="container my-5 inventario-wrapper">

  <!-- Título + botón agregar -->
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
      <h2 class="section-title mb-1">Inventario de Productos</h2>
      <p class="text-muted mb-0 small">
        Controle existencias, precios y movimientos de su inventario de forma visual.
      </p>
    </div>

    <a href="agregarProducto.php" 
       class="btn btn-staff-outline rounded-pill d-flex align-items-center gap-2 px-4 py-2">
      <i class="bi bi-plus-circle"></i> Agregar Producto
    </a>
  </div>

  <!-- Mensajes -->
  <?php if (isset($_GET['msg']) && $_GET['msg'] == 'eliminado') { ?>
    <div class="alert alert-success text-center">Producto eliminado con éxito.</div>
  <?php } ?>

  <?php if (isset($_GET['error'])) { ?>
    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($_GET['error']); ?></div>
  <?php } ?>

  <!-- Filtros en tarjeta -->
  <div class="inventario-filters shadow-sm rounded-4 mb-5">
    <div class="row g-3 align-items-end">

      <div class="col-md-6">
        <label for="searchInput" class="form-label fw-semibold inventario-label">
          Buscar producto
        </label>
        <input type="text"
               id="searchInput"
               class="form-control buscador-producto inventario-input"
               placeholder="Ingrese nombre del producto...">
      </div>

      <div class="col-md-3">
        <label for="codigoInput" class="form-label fw-semibold inventario-label">
          Filtrar por ID
        </label>
        <input type="text"
               id="codigoInput"
               class="form-control inventario-input"
               placeholder="Ej. 555"
               value="<?php echo isset($_GET['idProducto']) ? htmlspecialchars($_GET['idProducto']) : ''; ?>">
      </div>

      <div class="col-md-3 text-md-end">
        <label class="form-label d-none d-md-block">&nbsp;</label>
        <div class="d-flex justify-content-md-end justify-content-center gap-2">
          <button type="button" id="btnBuscar" class="btn-inv-primary">
            <i class="bi bi-search me-2"></i> Buscar
          </button>
          <button type="button" id="btnLimpiar" class="btn-inv-ghost">
            Limpiar
          </button>
        </div>
      </div>

    </div>
  </div>

  <!-- LISTA DE PRODUCTOS -->
  <div class="row g-4" id="listaProductos">

    <?php
    if (!empty($listaProductos)) {
      foreach ($listaProductos as $producto) {
        $cantidad = isset($producto['Cantidad']) ? $producto['Cantidad'] : 0;
    ?>
        <div class="col-xl-6 col-lg-6 col-md-12 producto">
          <div class="card inventory-card shadow-sm h-100">
            <div class="card-body d-flex flex-column">

              <!-- Nombre + Precio -->
              <div class="d-flex justify-content-between align-items-start mb-2 gap-3">
                <h5 class="card-title mb-0 product-name">
                  <?php echo htmlspecialchars($producto['Nombre'] ?? ''); ?>
                </h5>
                <span class="product-price text-nowrap">
                  ₡<?php echo isset($producto['Precio']) ? number_format($producto['Precio'], 2) : '0.00'; ?>
                </span>
              </div>

              <!-- Cantidad -->
              <p class="mb-2 small text-muted">
                Cantidad disponible:
                <strong class="text-dark"><?php echo $cantidad; ?></strong>
              </p>

              <!-- Barra de progreso -->
              <div class="progress inv-progress mb-3">
                <div class="progress-bar <?php echo $producto['ColorBarra'] ?? ''; ?>"
                     role="progressbar"
                     style="width: <?php echo $producto['AnchoBarra'] ?? 0; ?>%;"
                     aria-valuenow="<?php echo $cantidad; ?>"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
              </div>

              <!-- ID -->
              <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="inv-id small text-muted">
                  ID: <?php echo $producto['ProductoId'] ?? ''; ?>
                </span>

                <!-- Acciones -->
                <div class="d-flex gap-2">
                  <a href="editarProducto.php?id=<?php echo $producto['ProductoId'] ?? ''; ?>" 
                     class="btn btn-sm btn-inv-edit">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                  </a>

                  <button 
                    class="btn btn-sm btn-inv-delete btn-confirmar-eliminar" 
                    data-id="<?php echo $producto['ProductoId'] ?? ''; ?>" 
                    data-nombre="<?php echo htmlspecialchars($producto['Nombre'] ?? ''); ?>" 
                    data-bs-toggle="modal" 
                    data-bs-target="#confirmarEliminarModal">
                    <i class="bi bi-trash me-1"></i> Eliminar
                  </button>
                </div>
              </div>

            </div>
          </div>
        </div>
    <?php
      }
    } else {
    ?>
      <div class="col-12 text-center text-muted">
        <p>No se encontraron productos.</p>
      </div>
    <?php } ?>

  </div>

</section>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>


<!-- MODAL ELIMINAR -->
<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-labelledby="confirmarEliminarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="confirmarEliminarLabel">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="textoModalEliminar">
        ¿Estás seguro que quieres eliminar este producto?
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a id="enlaceEliminar" href="#" class="btn btn-danger">Eliminar</a>
      </div>

    </div>
  </div>
</div>

<script src="../assets/js/inventario.js"></script>

</body>
</html>