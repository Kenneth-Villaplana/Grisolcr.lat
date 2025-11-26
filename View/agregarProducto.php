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
          <div class="d-flex justify-content-end mt-3">
        <a href="inventario.php" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> Volver al inventario
        </a>
      </div>
      
    <div class="register-container">

      <h4 class="text-center mb-4">Agregar Producto</h4>
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

        <div class="text-center">
          <button type="submit" class="btn btn-custom">Guardar Producto</button>
        </div>
      </form>
    </div>
  </main>

  <?php MostrarFooter(); ?>
  <?php IncluirScripts(); ?>
</body>
</html>