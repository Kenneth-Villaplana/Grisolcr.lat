<?php
require_once __DIR__ . '/seguridad.php';
include('layout.php');
include_once __DIR__ . '/../Controller/loginController.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Óptica Grisol</title>
    <?php IncluirCSS(); ?>
</head>

<body>

    <?php MostrarMenu(); ?>

    <main class="editar-section">

        <div class="container">

            <div class="mb-3 text-end mt-3">
                <a href="personal.php" class="btn btn-back-custom">
                    ← Volver a personal
                </a>
            </div>

            <div class="row justify-content-center mb-3">
                <div class="col-12 col-lg-8">
                    <?php
                    if (isset($_SESSION["txtMensaje"])) {
                        echo '<div class="alert alert-' .
                            (isset($_SESSION["registroExitoso"]) ? 'success' : 'danger') . '">' .
                            $_SESSION["txtMensaje"] .
                            '</div>';
                        unset($_SESSION["txtMensaje"]);
                        unset($_SESSION["registroExitoso"]);
                    }
                    ?>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="register-card-compact">

                        <div class="register-card-header">
                            <h4 class="mb-0">Registrar Personal</h4>
                            <small>Ingrese los datos del nuevo miembro del personal</small>
                        </div>

                        <div class="p-4">
                            <form method="POST" name="contactForm" class="row g-4">

                                <div class="col-12 col-md-6">
                                    <input type="text" class="form-control" name="Cedula" id="Cedula"
                                        placeholder="Cédula" required onkeyup="ConsultarNombre();">
                                </div>

                                <div class="col-12 col-md-6">
                                    <input type="text" class="form-control" name="Nombre" id="Nombre"
                                        placeholder="Nombre" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <input type="text" class="form-control" name="Apellido" id="Apellido"
                                        placeholder="Primer Apellido" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <input type="text" class="form-control" name="ApellidoDos" id="ApellidoDos"
                                        placeholder="Segundo Apellido" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <input type="email" class="form-control" name="CorreoElectronico"
                                        id="CorreoElectronico" placeholder="Correo Electrónico" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <input type="password" class="form-control" name="Contrasenna" id="Contrasenna"
                                        placeholder="Contraseña" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <input type="password" class="form-control" name="ConfirmarContrasenna"
                                        id="ConfirmarContrasenna" placeholder="Confirmar Contraseña" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <input type="text" class="form-control" name="Telefono" id="Telefono"
                                        placeholder="Teléfono" required>
                                </div>

                                <div class="col-12">
                                    <input type="text" class="form-control" name="Direccion" id="Direccion"
                                        placeholder="Dirección">
                                </div>

                                <div class="col-12">
                                    <input type="date" class="form-control" name="FechaNacimiento" id="FechaNacimiento"
                                        required max="<?= date('Y-m-d') ?>">
                                </div>

                                <div class="col-12">
                                    <select name="RolId" id="RolId" class="form-select" required>
                                        <option value="">Seleccione el rol</option>
                                        <option value="1">Administrador/a</option>
                                        <option value="2">Asistente</option>
                                        <option value="3">Doctor/a</option>
                                        <option value="4">Cajero/a</option>
                                    </select>
                                </div>

                                <div class="col-12 text-center mt-1">
                                    <button type="submit" class="btn btn-primary btn-register-custom"
                                        id="btnRegistrarPersonal" name="btnRegistrarPersonal">
                                        Registrar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cedulaInput = document.getElementById('Cedula');

            cedulaInput.addEventListener('input', function () {
                let valor = this.value.replace(/\D/g, '');

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
</body>

</html>