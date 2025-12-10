<?php 
if(session_status() == PHP_SESSION_NONE) { session_start(); }
include_once 'layout.php';
include_once __DIR__ . '/../Controller/loginController.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Óptica Grisol - Login</title>
    <?php IncluirCSS(); ?>
</head>

<body>
    <?php MostrarMenu(); ?>

<section class="login-modern-wrapper">
    <div class="login-modern-container">

    <?php
            if(isset($_SESSION["txtMensaje"])){
                echo '<div class="alert alert-danger text-center py-2">' . $_SESSION["txtMensaje"] . '</div>';
                unset($_SESSION["txtMensaje"]);
            }
            ?>

        <!-- Panel Izquierdo -->
        <div class="login-modern-left">
            <h1 class="login-title text-center">Bienvenido</h1>
            <p class="login-subtitle text-center">Acceda a su cuenta para continuar.</p>

            <form method="POST">

                <div class="input-group-modern mb-3">
                    <i class="bi bi-envelope"></i>
                    <input type="email" class="modern-input" name="CorreoElectronico" placeholder="Correo Electrónico" required>
                </div>

                <div class="input-group-modern mb-2">
                    <i class="bi bi-lock"></i>
                    <input type="password" class="modern-input" name="Contrasenna" placeholder="Contraseña" required>
                </div>

                <div class="text-center mb-3">
                    <a href="recuperarCuenta.php" class="forgot-modern-link">¿Olvidó su contraseña?</a>
                </div>

                <button type="submit" class="btn-modern-login" name="btnIniciarSesion">
                    Iniciar Sesión
                </button>

                <div class="text-center mt-4">
                    <span class="register-text">¿No tienes cuenta?</span>
                    <a href="RegistrarPaciente.php" class="register-modern-link">Registrarse</a>
                </div>

            </form>
        </div>

        <!-- Panel Derecho -->
        <div class="login-modern-right">
            <div class="login-right-overlay"></div>
        </div>

    </div>
</section>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>
</body>
</html>