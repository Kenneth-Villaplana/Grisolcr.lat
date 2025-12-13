<?php 
include_once 'layout.php';

include_once __DIR__ . '/../Controller/loginController.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Óptica Grisol</title>
     <script src="../js/registro.js"></script>
    <?php IncluirCSS();?>
</head>
<body>
    <?php MostrarMenu();?>

    <section class="editar-section d-flex align-items-center justify-content-center py-5 my-5">

    <div class="container">

      
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

            <div class="register-card-compact">

                
                <div class="register-card-header">
                    <h4 class="mb-0">Registrarse</h4>
                    <small>Ingrese los datos para crear su cuenta</small>
                </div>

                
                <div class="p-4">
                    <form method="POST" name="contactForm" class="row g-4">

                        <?php $cedulaPrefill = $_GET['cedula'] ?? ''; ?>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Cédula</label>
                            <input type="text" class="form-control" name="Cedula" id="Cedula"
                                   value="<?= htmlspecialchars($cedulaPrefill) ?>"
                                   onkeyup="ConsultarNombre();" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="Nombre" id="Nombre" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Primer Apellido</label>
                            <input type="text" class="form-control" name="Apellido" id="Apellido" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" class="form-control" name="ApellidoDos" id="ApellidoDos" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="CorreoElectronico" id="CorreoElectronico" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="Contrasenna" id="Contrasenna" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" name="ConfirmarContrasenna" id="ConfirmarContrasenna" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="Telefono" id="Telefono" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="Direccion" id="Direccion">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Fecha de nacimiento</label>
                            <input type="date" class="form-control" name="FechaNacimiento" id="FechaNacimiento"
                                   required max="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="col-12 text-center mt-3">
                            <p class="small">¿Ya tienes cuenta?
                                <a class="link-azul" href="iniciarSesion.php">Iniciar sesión</a>
                            </p>
                        </div>

                        <div class="col-12 text-center mt-1">
                            <button type="submit"
                                    class="btn btn-primary btn-register-custom"
                                    name="btnRegistrarPaciente">
                                Registrarse
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


</body>
</html>