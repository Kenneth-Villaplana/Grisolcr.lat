<?php
include('layout.php');
include_once __DIR__ . '/../Model/productoModel.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/* VALIDAR ID */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Producto inválido");
}

$productoId = intval($_GET['id']);

/* OBTENER PRODUCTO */

$producto = ObtenerProductoPorId($productoId);

if (!$producto) {
    die("Producto no encontrado");
}
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
                <a href="inventario.php" class="btn btn-back-custom">
                    <i class="bi bi-arrow-left"></i> Volver al inventario
                </a>
            </div>

            <?php if (isset($_SESSION["txtMensaje"])): ?>
                <?php $clase = isset($_SESSION["CambioExitoso"]) ? 'success' : 'danger'; ?>
                <div class="alert alert-<?php echo $clase; ?> text-center mb-4">
                    <?php echo $_SESSION["txtMensaje"]; ?>
                </div>
                <?php unset($_SESSION["txtMensaje"]);
                unset($_SESSION["CambioExitoso"]); ?>
            <?php endif; ?>

            <div class="col-12 d-flex justify-content-center">
                <div class="register-container edit-product-card">

                    <div class="edit-product-header">
                        <h4 class="mb-0">Editar Producto</h4>
                        <small class="text-muted">Actualice los datos del producto</small>
                    </div>

                    <div class="p-4">
                        <div id="mensajeError" class="alert alert-danger text-center d-none"></div>
                        <form method="POST" action="../Controller/productoController.php" name="editarProductoForm"
                            id="formEditarProducto" class="row g-3 justify-content-center"
                            enctype="multipart/form-data">

                            <input type="hidden" name="btnEditarProducto" value="1">

                            <div class="col-12 col-md-8">

                                <h6 class="edit-section-title text-center mb-3">
                                    Información
                                </h6>

                                <label class="form-label">Producto ID</label>

                                <input type="text" id="ProductoId" name="ProductoId" class="form-control mb-3"
                                    value="<?php echo htmlspecialchars($producto['ProductoId']); ?>" readonly>

                                <label class="form-label">Nombre</label>

                                <input type="text" id="Nombre" name="Nombre" class="form-control mb-3"
                                    value="<?php echo htmlspecialchars($producto['Nombre']); ?>" required>

                                <label class="form-label">Descripción</label>

                                <textarea name="Descripcion" id="Descripcion" class="form-control mb-3 auto-grow"
                                    rows="1"
                                    required><?php echo htmlspecialchars($producto['Descripcion']); ?></textarea>

                                <label class="form-label">Precio</label>

                                <input type="number" name="Precio" id="Precio" min="1" class="form-control mb-3"
                                    value="<?php echo htmlspecialchars($producto['Precio']); ?>" required>

                                <label class="form-label">Cantidad</label>

                                <input type="number" name="Cantidad" id="Cantidad" min="1" class="form-control mb-3"
                                    value="<?php echo htmlspecialchars($producto['Cantidad']); ?>" required>

                                <label class="form-label">Imagen actual</label>

                                <div class="current-product-image-box text-center mb-4">
                                    <img src="/assets/img/<?php echo htmlspecialchars($producto['Imagen'] ?? 'no-image.jpg'); ?>"
                                        alt="Imagen actual del producto" class="current-product-image">
                                </div>

                                <label class="form-label">Cambiar imagen</label>

                                <div class="upload-modern-box" id="uploadBoxEditar">
                                    <div class="upload-modern-icon">
                                        <i class="bi bi-cloud-arrow-up"></i>
                                    </div>

                                    <p class="upload-modern-text mb-3">Arrastra tu imagen o selecciónala</p>

                                    <label for="Imagen" class="upload-btn-custom">
                                        Seleccionar archivo
                                    </label>

                                    <input type="file" name="Imagen" id="Imagen" class="upload-hidden-input"
                                        accept=".jpg,.jpeg,.png,.webp">

                                    <p id="nombreArchivoImagenEditar" class="upload-file-name mt-3 mb-0">
                                        Ningún archivo seleccionado
                                    </p>
                                </div>

                                <small class="text-muted d-block mt-2 mb-2">
                                    Si no selecciona una nueva imagen, se conservará la actual.
                                </small>

                            </div>

                            <div class="col-12 text-center mt-2">

                                <button type="button" class="btn-save-modern px-5" id="btnAbrirModalEditar">

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


    <!-- MODAL CONFIRMAR -->

    <div class="modal fade" id="modalConfirmarEdicion" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Confirmar cambios
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">
                    ¿Desea guardar los cambios realizados en este producto?
                </div>

                <div class="modal-footer">

                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button class="btn btn-outline-primary" id="btnConfirmarCambios">

                        Sí, guardar

                    </button>

                </div>

            </div>
        </div>
    </div>

    <script src="/assets/js/inventario.js"></script>

    <script>
document.addEventListener("DOMContentLoaded", function () {
    const inputImagen = document.getElementById("Imagen");
    const nombreArchivo = document.getElementById("nombreArchivoImagenEditar");
    const uploadBox = document.getElementById("uploadBoxEditar");

    function actualizarNombreArchivo(files) {
        if (files && files.length > 0) {
            nombreArchivo.textContent = files[0].name;
            uploadBox.classList.add("active");
        } else {
            nombreArchivo.textContent = "Ningún archivo seleccionado";
            uploadBox.classList.remove("active");
        }
    }

    if (inputImagen && nombreArchivo && uploadBox) {
        inputImagen.addEventListener("change", function () {
            actualizarNombreArchivo(this.files);
        });

        ["dragenter", "dragover"].forEach(eventName => {
            uploadBox.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                uploadBox.classList.add("dragover");
            });
        });

        ["dragleave", "drop"].forEach(eventName => {
            uploadBox.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                uploadBox.classList.remove("dragover");
            });
        });

        uploadBox.addEventListener("drop", function (e) {
            const files = e.dataTransfer.files;

            if (files && files.length > 0) {
                inputImagen.files = files;
                actualizarNombreArchivo(files);
            }
        });
    }
});
</script>

</body>

</html>