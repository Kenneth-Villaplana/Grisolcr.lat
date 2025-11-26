<?php
include('layout.php');
include_once __DIR__ . '/../Controller/productoController.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Óptica Grisol - Editar Producto</title>
  <?php IncluirCSS(); ?>
</head>

<body>
  <?php MostrarMenu(); ?>

  <section>
    <div class="container" data-aos="fade-up">

    
      <div class="d-flex justify-content-end mt-3">
        <a href="inventario.php" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> Volver al inventario
        </a>
      </div>

     
      <?php
      if (isset($_SESSION["txtMensaje"])) {
          $clase = isset($_SESSION["CambioExitoso"]) ? 'success' : 'danger';
          echo '<div class="alert alert-' . $clase . ' text-center mt-4 mb-4">' . $_SESSION["txtMensaje"] . '</div>';
          unset($_SESSION["txtMensaje"]);
          unset($_SESSION["CambioExitoso"]);
      }
      ?>

      <div class="row justify-content-center">
        <div class="col-md-8">

          <div class="profile-card shadow-sm rounded-4 p-4" data-aos="fade-up">
            <div class="profile-header text-center mb-4">
              <h4 class="mb-0">Editar Producto</h4>
            </div>

            <form method="POST" name="editarProductoForm" class="row justify-content-center">
              <div class="col-md-8 col-lg-6">

                <h6 class="profile-section-title text-center mb-4">Datos</h6>

                <div class="mb-3">
                  <label for="ProductoId" class="form-label">Producto ID</label>
                  <input type="text" id="ProductoId" name="ProductoId" class="form-control"
                         value="<?php echo $producto['ProductoId']; ?>" readonly>
                </div>

                <div class="mb-3">
                  <label for="Nombre" class="form-label">Nombre</label>
                  <input type="text" id="Nombre" name="Nombre" class="form-control"
                         value="<?php echo $producto['Nombre']; ?>" required>
                </div>

                <div class="mb-3">
                  <label for="Descripcion" class="form-label">Descripción</label>
                  <textarea name="Descripcion" id="Descripcion" class="form-control" rows="5" required><?php echo htmlspecialchars($producto['Descripcion']); ?></textarea>
                </div>

                <div class="mb-3">
                  <label for="Precio" class="form-label">Precio</label>
                  <input type="number" name="Precio" id="Precio" class="form-control"
                         value="<?php echo $producto['Precio']; ?>" required>
                </div>

                <div class="mb-3">
                  <label for="Cantidad" class="form-label">Cantidad</label>
                  <input type="number" name="Cantidad" id="Cantidad" class="form-control"
                         value="<?php echo $producto['Cantidad']; ?>" required>
                </div>

              </div>

              <div class="col-12 text-center mt-4">
                <button type="submit" class="btn btn-custom px-4" name="btnEditarProducto">
                  <i class="bi bi-pencil-square"></i> Guardar Cambios
                </button>
              </div>

            </form>

          </div>
        </div>
      </div>
    </div>
  </section>

  <?php MostrarFooter(); ?>
  <?php IncluirScripts(); ?>
</body>
</html>