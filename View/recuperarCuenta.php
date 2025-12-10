<?php 
include('layout.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Recuperar Contraseña</title>
    <?php IncluirCSS(); ?>
</head>
<body>

<?php MostrarMenu(); ?>

<section class="editar-section d-flex align-items-center justify-content-center py-5 my-5">
    <div class="container">

        
        <div class="row justify-content-center mb-3">
            <div class="col-12 col-lg-8">
                <?php
                if (isset($_SESSION["txtMensaje"])) {
                    echo '<div class="alert alert-info text-center">' .
                            $_SESSION["txtMensaje"] .
                        '</div>';
                    unset($_SESSION["txtMensaje"]);
                }
                ?>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="register-card-compact">
                <div class="register-card-header text-center">
                    <h4 class="mb-0">Recuperar Contraseña</h4>
                    <small class="text-muted">Ingrese el correo asociado a su cuenta</small>
                </div>

                
                <div class="p-4">
                    <form method="POST" action="../Controller/recuperarController.php" class="row g-4">

                        <div class="col-12">
                            <label class="form-label">Correo Electrónico</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                name="Email"
                                id="CorreoRecuperacion" 
                                required
                                placeholder="ejemplo@correo.com"
                            >
                        </div>

                        <div class="col-12 text-center mt-2">
                            <p class="small mb-0">
                                ¿No tienes cuenta?
                                <a class="link-azul" href="RegistrarPaciente.php">Registrarse</a>
                            </p>
                        </div>

                        <div class="col-12 text-center mt-1">
                            <button 
                                type="submit" 
                                class="btn btn-primary btn-register-custom"
                                name="btnRecuperar">
                                Recuperar
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

</body>
</html>