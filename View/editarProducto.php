<?php 
include('layout.php'); 
include_once __DIR__ . '/../Controller/productoController.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Óptica Grisol - Editar Producto</title>
    <?php IncluirCSS(); ?>
</head>

<body>

<?php MostrarMenu(); ?>

<main class="editar-section">
    <div class="container">

        <div class="d-flex justify-content-end mt-3 mb-3">
            <a href="inventario.php" class="btn btn-outline-secondary btn-back-custom">
                ← Volver al inventario
            </a>
        </div>

        <?php if (isset($_SESSION["txtMensaje"])): ?>
            <?php $clase = isset($_SESSION["CambioExitoso"]) ? 'success' : 'danger'; ?>
            <div class="alert alert-<?php echo $clase; ?> text-center mb-4">
                <?php echo $_SESSION["txtMensaje"]; ?>
            </div>
            <?php unset($_SESSION["txtMensaje"]); unset($_SESSION["CambioExitoso"]); ?>
        <?php endif; ?>

        
        <div class="col-12 d-flex justify-content-center">
            <div class="register-container edit-product-card">

                <div class="edit-product-header">
                    <h4 class="mb-0">Editar Producto</h4>
                    <small class="text-muted">Actualice los datos del producto</small>
                </div>

                <div class="p-4">
                   <form method="POST" name="editarProductoForm" id="formEditarProducto" class="row g-3 justify-content-center">

  
                    <input type="hidden" name="btnEditarProducto" value="1">
                    <div class="col-12 col-md-8">
                        <h6 class="edit-section-title text-center mb-3">Información</h6>

                        <label class="form-label">Producto ID</label>
                        <input type="text" id="ProductoId" name="ProductoId" class="form-control mb-3"
                            value="<?php echo $producto['ProductoId']; ?>" readonly>

                        <label class="form-label">Nombre</label>
                        <input type="text" id="Nombre" name="Nombre" class="form-control mb-3"
                            value="<?php echo $producto['Nombre']; ?>" required>

                        <label class="form-label">Descripción</label>
                    <textarea name="Descripcion" id="Descripcion" class="form-control mb-3 auto-grow"
                    rows="1" required><?php echo htmlspecialchars($producto['Descripcion']); ?></textarea>

                        <label class="form-label">Precio</label>
                        <input type="number" name="Precio" id="Precio" class="form-control mb-3"
                            value="<?php echo $producto['Precio']; ?>" required>

                        <label class="form-label">Cantidad</label>
                        <input type="number" name="Cantidad" id="Cantidad" class="form-control mb-3"
                            value="<?php echo $producto['Cantidad']; ?>" required>

                    </div>

                    <div class="col-12 text-center mt-2">
                        <button type="button"
                            class="btn btn-outline-primary btn-save-custom px-5"
                            id="btnAbrirModalEditar">
                            Guardar Cambios
                        </button>
                    </div>

                </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>


<div class="modal fade" id="modalConfirmarEdicion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Confirmar cambios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                ¿Desea guardar los cambios realizados en este producto?
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-outline-primary" id="btnConfirmarCambios">Sí, guardar</button>
            </div>
        </div>
    </div>
</div>


<script src="../assets/js/inventario.js"></script>
</body>
</html>