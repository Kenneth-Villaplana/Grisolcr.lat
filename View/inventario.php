<?php
include('layout.php');
include_once __DIR__ . '/../Model/productoModel.php';

$productoFiltro = $_GET['idProducto'] ?? null;
$listaProductos = ObtenerProductos($productoFiltro);


$productosBajos = array_filter($listaProductos, function ($producto) {
    return $producto['Cantidad'] <= 10;
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

    <!-- Para las alertas de bajo inventario-->
<?php if (!empty($productosBajos)): ?>
<div class="container mt-4">
  

  <div class="text-center mb-3">
    <div class="alert alert-warning py-2">
      <strong>⚠ Productos con inventario bajo</strong>
    </div>
  </div>

  
  <div class="row justify-content-center">
    <?php foreach ($productosBajos as $p): ?>
      <div class="col-md-4 mb-3">
        <div class="low-stock-card p-3 shadow-sm rounded-3 border">
          <div class="d-flex align-items-center mb-2">
            
            <strong class="text-danger fs-6">Inventario bajo</strong>
          </div>

          
          <p class="fw-semibold mb-1">
            <?php echo htmlspecialchars($p['Nombre']); ?>
          </p>

          <p class="text-muted mb-0">
            Cantidad restante: 
           <strong class="text-dark fw-bold"> 
              <?php echo $p['Cantidad']; ?> unidades
            </strong>
          </p>

        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>
<?php endif; ?>


  <section class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Inventario de Productos</h2>
      <a href="agregarProducto.php" class="btn btn-custom">
        <i class="bi bi-plus-circle"></i> Agregar Producto
      </a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'eliminado') { ?>
      <div class="alert alert-success text-center"> Producto eliminado con éxito.</div>
    <?php } ?>
    <?php if (isset($_GET['error'])) { ?>
      <div class="alert alert-danger text-center"> <?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php } ?>

<div class="mb-4 d-flex justify-content-center">
  <div class="filter-form text-center" style="max-width: 300px; width: 100%;">
    <label for="codigoInput" class="form-label">Filtrar por ID</label>
    <input type="text" id="codigoInput" class="form-control mb-3 text-center" placeholder="Ej. 555">

    <div class="d-flex justify-content-center gap-2">
      <button type="button" id="btnBuscar" class="btn btn-custom">Buscar</button>
      <button type="button" id="btnLimpiar" class="btn btn-outline-secondary">Limpiar</button>
    </div>
  </div>
</div>

    <div class="row" id="listaProductos">
      <?php if (!empty($listaProductos)) {
        foreach ($listaProductos as $producto) { ?>
          <div class="col-md-6 mb-4 producto">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h5 class="card-title mb-0"><?php echo htmlspecialchars($producto['Nombre']); ?></h5>
                  <span class="fw-bold text-primary">₡<?php echo number_format($producto['Precio'], 2); ?></span>
                </div>

                <p class="mb-2">Cantidad disponible: <strong><?php echo $producto['Cantidad']; ?></strong></p>
                <div class="progress mb-3">
                  <div class="progress-bar <?php echo $producto['ColorBarra']; ?>"
                    role="progressbar"
                    style="width: <?php echo $producto['AnchoBarra']; ?>%;"
                    aria-valuenow="<?php echo $producto['Cantidad']; ?>"
                    aria-valuemin="0"
                    aria-valuemax="100">
                  </div>
                </div>

                <h6 class="text-muted">ID: <?php echo $producto['ProductoId']; ?></h6>

                <div class="d-flex justify-content-end mt-3">
                  <a href="editarProducto.php?id=<?php echo $producto['ProductoId']; ?>" class="btn btn-custom me-2">Editar</a>
                  <button 
                    class="btn btn-danger btn-sm btn-confirmar-eliminar" 
                    data-id="<?php echo $producto['ProductoId']; ?>" 
                    data-nombre="<?php echo htmlspecialchars($producto['Nombre']); ?>" 
                    data-bs-toggle="modal" 
                    data-bs-target="#confirmarEliminarModal">
                    Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php }
      } else { ?>
        <div class="col-12 text-center text-muted">
          <p>No se encontraron productos.</p>
        </div>
      <?php } ?>
    </div>
  </section>

  <?php MostrarFooter(); ?>
  <?php IncluirScripts(); ?>


<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-labelledby="confirmarEliminarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmarEliminarLabel">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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

  <!--  para que se  actualice el modal -->
  <script>
document.querySelectorAll('.btn-confirmar-eliminar').forEach(boton => {
  boton.addEventListener('click', () => {
    const idProducto = boton.getAttribute('data-id');
    const nombre = boton.getAttribute('data-nombre');
    document.getElementById('textoModalEliminar').innerText =
      `¿Estás seguro de eliminar el producto "${nombre}" (ID: ${idProducto})?`;
    document.getElementById('enlaceEliminar').href =
      `../Controller/productoController.php?eliminarProducto=${idProducto}`;
  });
});
  </script>

</body>
</html>