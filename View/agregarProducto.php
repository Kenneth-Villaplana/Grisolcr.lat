<?php
include('layout.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agregar Producto</title>
  <?php IncluirCSS(); ?>
</head>

<body>

<?php MostrarMenu(); ?>

<main class="container py-5">

 
    <div class="d-flex justify-content-end mt-3 mb-3">
      <a href="inventario.php" class="btn btn-outline-secondary btn-back-custom">
        ← Volver al inventario
      </a>
    </div>

   
    <div class="register-container product-register-card">

      <div class="product-register-header">
          <h4 class="mb-0">Agregar Producto</h4>
          <small class="text-muted">Complete los datos del nuevo producto</small>
      </div>

      <div class="p-4">

        <form action="../Controller/productoController.php" method="POST">

          <div class="mb-3">
            <label for="Nombre" class="form-label">Nombre</label>
            <input type="text" name="Nombre" id="Nombre" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="Descripcion" class="form-label">Descripción</label>
            <textarea name="Descripcion" id="Descripcion" class="form-control" rows="4" required></textarea>
          </div>

          <div class="mb-3">
            <label for="Precio" class="form-label">Precio</label>
            <input type="number" name="Precio" id="Precio" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="Cantidad" class="form-label">Cantidad</label>
            <input type="number" name="Cantidad" id="Cantidad" class="form-control" required>
          </div>

          <div class="text-center mt-3">
            <button type="submit" class="btn btn-outline-primary btn-save-custom px-5" name="btnAgregarProducto">
              Guardar Producto
            </button>
          </div>

        </form>

      </div>

    </div>

</main>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

</body>
</html>