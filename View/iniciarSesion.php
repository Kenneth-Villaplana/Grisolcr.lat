<?php 
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

    include_once 'layout.php';
  include_once __DIR__ . '/../Controller/loginController.php';
?>
<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Óptica Grisol</title>
   <?php IncluirCSS();?>
</head>
    <body>
       <?php MostrarMenu();?>
    
   <section class="login-page-bg">
    <div class="container">
        
        <div class="row justify-content-center">

            <?php
                if(isset($_SESSION["txtMensaje"])){
                    echo '<div class="alert alert-danger w-50 text-center">' . $_SESSION["txtMensaje"] . '</div>';
                    unset($_SESSION["txtMensaje"]);
                }
            ?>

            <div class="col-lg-5 col-md-7">
                <div class="login-card glass-card">

                    <h3 class="text-center mb-3 login-title">Iniciar Sesión</h3>
                    <p class="text-center mb-4 login-subtitle">
                        Accede a tu cuenta para continuar
                    </p>

                    <form method="POST">

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" class="form-control login-input" name="CorreoElectronico" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contraseña</label>
                            <input type="password" class="form-control login-input" name="Contrasenna" required>
                        </div>

                        <div class="text-center mb-3">
                            <a href="recuperarCuenta.php" class="login-link fw-semibold">Recuperar acceso</a>
                        </div>

                        <div class="text-center mb-3">
                            <p class="small">
                                ¿No tienes cuenta?
                                <a href="RegistrarPaciente.php" class="login-link fw-semibold">Registrarse</a>
                            </p>
                        </div>

                        <button type="submit" class="btn-login w-100" name="btnIniciarSesion">
                            Iniciar sesión
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</section>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
</body>

</html>
