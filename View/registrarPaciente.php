<?php
require_once __DIR__ . '/seguridad.php';
include_once 'layout.php';

include_once __DIR__ . '/../Controller/loginController.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Óptica Grisol</title>

    <?php IncluirCSS(); ?>
</head>

<body>
    <?php MostrarMenu(); ?>

    <section class="register-modern-wrapper">

        <div class="register-modern-container">

            <!-- FORMULARIO -->
            <div class="register-modern-left">

                <h2 class="register-title">Crear Cuenta</h2>
                <p class="register-subtitle">Complete sus datos para registrarse</p>

                <?php
                $mostrarModalRegistro = !empty($_SESSION["registroExitoso"]);
                ?>

                <!-- MENSAJES -->
                <?php if (isset($_SESSION["txtMensaje"]) && empty($_SESSION["registroExitoso"])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION["txtMensaje"]; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="register-form">

                    <?php $cedulaPrefill = $_GET['cedula'] ?? ''; ?>

                    <!-- GRID -->
                    <div class="register-grid">

                        <div class="input-modern-login">
                            <i class="bi bi-person-badge"></i>
                            <input type="text" name="Cedula" id="Cedula" value="<?= htmlspecialchars($cedulaPrefill) ?>"
                                placeholder="Cédula" required onkeyup="ConsultarNombre();">
                        </div>

                        <div class="input-modern-login">
                            <i class="bi bi-person"></i>
                            <input type="text" name="Nombre" id="Nombre" placeholder="Nombre" required>
                        </div>

                        <div class="input-modern-login">
                            <i class="bi bi-person"></i>
                            <input type="text" name="Apellido" id="Apellido" placeholder="Primer Apellido" required>
                        </div>

                        <div class="input-modern-login">
                            <i class="bi bi-person"></i>
                            <input type="text" name="ApellidoDos" id="ApellidoDos" placeholder="Segundo Apellido"
                                required>
                        </div>

                        <div class="input-modern-login">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="CorreoElectronico" placeholder="Correo electrónico" required>
                        </div>

                        <div class="input-modern-login">
                            <i class="bi bi-lock"></i>
                            <input type="password" name="Contrasenna" placeholder="Contraseña" required>
                        </div>

                        <div class="input-modern-login">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" name="ConfirmarContrasenna" placeholder="Confirmar contraseña"
                                required>
                        </div>

                        <div class="input-modern-login">
                            <i class="bi bi-telephone"></i>
                            <input type="text" name="Telefono" placeholder="Teléfono" required>
                        </div>

                        <div class="input-modern-login full">
                            <i class="bi bi-geo-alt"></i>
                            <input type="text" name="Direccion" placeholder="Dirección">
                        </div>

                        <div class="input-modern-login full">
                            <i class="bi bi-calendar"></i>
                            <input type="date" name="FechaNacimiento" required max="<?= date('Y-m-d') ?>"> 
                        </div>

                    </div>

                    <div class="text-center mt-3">
                        <small>¿Ya tienes cuenta?
                            <a href="iniciarSesion.php" class="register-link">Iniciar sesión</a>
                        </small>
                    </div>

                    <button type="submit" class="btn-register-modern" name="btnRegistrarPaciente">
                        Registrarse
                    </button>

                </form>
            </div>


        </div>
    </section>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cedulaInput = document.getElementById('Cedula');

            cedulaInput.addEventListener('input', function () {
                let valor = this.value.replace(/\D/g, ''); // solo números

                if (valor.length > 9) {
                    valor = valor.substring(0, 9);
                }

                let formateado = '';

                if (valor.length > 0) {
                    formateado = valor.substring(0, 1);
                }
                if (valor.length >= 2) {
                    formateado += '-' + valor.substring(1, 5);
                }
                if (valor.length >= 6) {
                    formateado += '-' + valor.substring(5, 9);
                }

                this.value = formateado;
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const form = document.querySelector('form');
            const cedulaInput = document.getElementById('Cedula');

            if (form && cedulaInput) {
                form.addEventListener('submit', function () {


                    cedulaInput.value = cedulaInput.value.replace(/\D/g, '');

                });
            }

        });
    </script>

    <!-- MODAL REGISTRO EXITOSO -->
    <div class="modal fade" id="modalRegistroExitoso" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">

                <div class="modal-gradient-header">
                    <div class="modal-success-icon">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <h5 class="fw-bold text-white m-0">
                        ¡Cuenta creada!
                    </h5>
                </div>

                <div class="modal-body modal-body-custom">
                    <p class="text-muted">
                        Su cuenta fue creada correctamente.
                    </p>
                </div>

                <div class="text-center pb-4">
                    <a href="iniciarSesion.php" class="btn-reset-modal px-4">
                        Ir a iniciar sesión
                    </a>
                </div>

            </div>
        </div>
    </div>

    <?php if ($mostrarModalRegistro): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let modal = new bootstrap.Modal(document.getElementById("modalRegistroExitoso"));
                modal.show();
            });
        </script>
    <?php endif; ?>

    <?php
    unset($_SESSION["txtMensaje"], $_SESSION["registroExitoso"]);
    ?>

</body>

</html>