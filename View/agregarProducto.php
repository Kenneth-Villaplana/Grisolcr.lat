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
                <div id="mensajeError" class="alert alert-danger text-center d-none"></div>
                <form id="formAgregarProducto" action="../Controller/productoController.php" method="POST"
                    enctype="multipart/form-data">

                    <!-- Nombre -->
                    <div class="mb-4">
                        <label for="Nombre" class="form-label fw-semibold">Nombre del producto</label>
                        <input type="text" name="Nombre" id="Nombre" class="form-control input-modern" required>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label for="Descripcion" class="form-label fw-semibold">Descripción</label>
                        <textarea name="Descripcion" id="Descripcion" class="form-control input-modern" rows="3"
                            required></textarea>
                    </div>

                    <!-- Fila precio y cantidad -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="Precio" class="form-label fw-semibold">Precio</label>
                            <input type="number" name="Precio" id="Precio" min="1" class="form-control input-modern"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label for="Cantidad" class="form-label fw-semibold">Cantidad</label>
                            <input type="number" name="Cantidad" id="Cantidad" min="1" class="form-control input-modern"
                                required>
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Imagen del producto</label>

                        <div id="uploadBox" class="upload-box">
                            <input type="file" name="Imagen" id="Imagen" class="d-none" accept=".jpg,.jpeg,.png,.webp">

                            <div id="uploadPlaceholder" class="upload-placeholder">
                                <div class="upload-icon">🖼️</div>
                                <p class="mb-1 fw-semibold">Arrastra una imagen aquí</p>
                                <p class="text-muted small mb-2">o haz clic para seleccionarla</p>
                                <button type="button" id="btnSeleccionarImagen"
                                    class="btn btn-outline-primary btn-sm rounded-pill">
                                    Seleccionar imagen
                                </button>
                            </div>

                            <div id="previewContainer" class="preview-container d-none">
                                <img id="previewImagen" src="" alt="Vista previa" class="preview-img">
                                <p id="nombreArchivo" class="small text-muted mt-2 mb-2"></p>

                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" id="btnCambiarImagen"
                                        class="btn btn-outline-primary btn-sm rounded-pill">
                                        Cambiar
                                    </button>
                                    <button type="button" id="btnQuitarImagen"
                                        class="btn btn-outline-danger btn-sm rounded-pill">
                                        Quitar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="mensajeImagen" class="invalid-feedback d-block text-center mt-2" style="display:none;">
                        </div>
                        <small class="text-muted d-block text-center mt-2">Formatos permitidos: JPG, JPEG, PNG,
                            WEBP</small>
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