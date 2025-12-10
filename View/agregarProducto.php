<?php
include('layout.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Óptica Grisol - Agregar Producto</title>
  <?php IncluirCSS(); ?>
</head>

<body>

<?php MostrarMenu(); ?>


<main class="container py-5">

    <!-- Botón volver -->
    <div class="d-flex justify-content-end mb-4">
        <a href="inventario.php" class="btn btn-back-custom">
                <i class="bi bi-arrow-left"></i>Volver al inventario
        </a>
    </div>

    <!-- Card principal -->
    <div class="product-register-card shadow-lg">

        <!-- Header -->
        <div class="product-register-header text-center">
            <h4 class="mb-1 fw-bold">Agregar Producto</h4>
            <small>Complete los datos del nuevo producto</small>
        </div>

        <!-- Form -->
        <div class="px-4 py-4">

            <form action="../Controller/productoController.php" method="POST">

                <!-- Nombre -->
                <div class="mb-4">
                    <label for="Nombre" class="form-label fw-semibold">Nombre del producto</label>
                    <input type="text" name="Nombre" id="Nombre" class="form-control input-modern" required>
                </div>

                <!-- Descripción -->
                <div class="mb-4">
                    <label for="Descripcion" class="form-label fw-semibold">Descripción</label>
                    <textarea name="Descripcion" id="Descripcion" class="form-control input-modern" rows="3" required></textarea>
                </div>

                <!-- Fila precio y cantidad -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="Precio" class="form-label fw-semibold">Precio</label>
                        <input type="number" name="Precio" id="Precio" class="form-control input-modern" required>
                    </div>

                    <div class="col-md-6">
                        <label for="Cantidad" class="form-label fw-semibold">Cantidad</label>
                        <input type="number" name="Cantidad" id="Cantidad" class="form-control input-modern" required>
                    </div>
                </div>

                <!-- Botón -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn-save-modern px-5 py-2" name="btnAgregarProducto">
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