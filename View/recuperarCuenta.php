<?php
include('layout.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Óptica Grisol</title>
    <?php IncluirCSS(); ?>
</head>

<body>
<?php MostrarMenu(); ?>

<section class="full-height-section">
    <div class="container" data-aos="fade-up">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <h4 class="text-center mb-3">Recuperar Contraseña</h4>
                <p class="text-center mb-4">
                    Ingrese el correo electrónico asociado a su cuenta.
                </p>

                <!-- Mensajes -->
                <?php
                    if (isset($_SESSION["txtMensaje"])) {
                        echo '<div class="alert alert-info text-center">' . $_SESSION["txtMensaje"] . '</div>';
                        unset($_SESSION["txtMensaje"]);
                    }
                ?>

                <!-- FORMULARIO -->
                <form method="POST" action="../Controller/recuperarController.php" class="contactForm">

                    <div class="mb-3 text-center">
                        <label for="CorreoRecuperacion" class="form-label">Correo Electrónico</label>
                        <input type="email" 
                               class="form-control" 
                               id="CorreoRecuperacion"
                               name="Email"
                               required>
                    </div>

                    <div class="text-center mb-3">
                        <p>¿No tienes cuenta? 
                            <a class="link-azul" href="RegistrarPaciente.php">Registrarse</a>
                        </p>
                    </div>

                    <div class="text-center">
                        <button type="submit" 
                                class="btn btn-custom" 
                                id="btnRecuperar" 
                                name="btnRecuperar">
                            Recuperar
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</section>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>
</body>

</html>
