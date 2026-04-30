<?php
require_once __DIR__ . '/layout.php';
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
                        <input type="text" name="Nombre" id="Nombre" class="form-control input-modern"
                            placeholder="Nombre del producto" required>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label for="Descripcion" class="form-label fw-semibold">Descripción</label>
                        <textarea name="Descripcion" id="Descripcion" class="form-control input-modern" rows="3"
                            placeholder="Descripción del producto" required></textarea>
                    </div>

                    <!-- Fila precio y cantidad -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="Precio" class="form-label fw-semibold">Precio</label>
                            <input type="number" name="Precio" id="Precio" min="1" class="form-control input-modern"
                                placeholder="₡ Precio" required>
                        </div>

                        <div class="col-md-6">
                            <label for="Cantidad" class="form-label fw-semibold">Cantidad</label>
                            <input type="number" name="Cantidad" id="Cantidad" min="1" class="form-control input-modern"
                                placeholder="Cantidad disponible" required>
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div class="mb-4">
                        <label for="Imagen" class="form-label fw-semibold">Imagen del producto</label>

                        <div class="upload-modern-box upload-wide" id="uploadBox">
                            <div class="upload-modern-icon">
                                <i class="bi bi-cloud-arrow-up"></i>
                            </div>

                            <p class="upload-modern-text mb-3">Arrastra y suelta tu imagen aquí</p>

                            <label for="Imagen" class="upload-btn-custom">
                                Seleccionar archivo
                            </label>

                            <input type="file" name="Imagen" id="Imagen" class="upload-hidden-input"
                                accept=".jpg,.jpeg,.png,.webp">

                            <p id="nombreArchivoImagen" class="upload-file-name mt-3 mb-0">
                                Ningún archivo seleccionado
                            </p>
                        </div>

                        <small class="text-muted d-block mt-2 text-center">
                            Formatos permitidos: JPG, JPEG, PNG, WEBP
                        </small>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const inputImagen = document.getElementById("Imagen");
            const nombreArchivo = document.getElementById("nombreArchivoImagen");
            const uploadBox = document.getElementById("uploadBox");
            const formAgregar = document.getElementById("formAgregarProducto");
            const mensajeError = document.getElementById("mensajeError");

            const maxSizeMB = 1;
            const maxSizeBytes = maxSizeMB * 1024 * 1024;

            function mostrarError(msg) {
                if (!mensajeError) return;
                mensajeError.textContent = msg;
                mensajeError.classList.remove("d-none");
            }

            function ocultarError() {
                if (!mensajeError) return;
                mensajeError.classList.add("d-none");
                mensajeError.textContent = "";
            }

            function validarImagen(files) {
                if (files && files.length > 0) {
                    const archivo = files[0];

                    if (archivo.size > maxSizeBytes) {
                        mostrarError(`La imagen es muy pesada. Máximo permitido: ${maxSizeMB} MB.`);
                        inputImagen.value = "";
                        nombreArchivo.textContent = "Ningún archivo seleccionado";
                        uploadBox.classList.remove("active");
                        return false;
                    }
                }

                ocultarError();
                return true;
            }

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
                    if (validarImagen(this.files)) {
                        actualizarNombreArchivo(this.files);
                    }
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
                    e.preventDefault();
                    e.stopPropagation();
                    uploadBox.classList.remove("dragover");

                    const files = e.dataTransfer.files;

                    if (files && files.length > 0) {
                        if (validarImagen(files)) {
                            inputImagen.files = files;
                            actualizarNombreArchivo(files);
                        }
                    }
                });
            }

            if (formAgregar && inputImagen) {
                formAgregar.addEventListener("submit", function (e) {
                    if (!validarImagen(inputImagen.files)) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>

</html>