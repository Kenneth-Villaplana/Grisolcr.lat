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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Óptica Grisol</title>
    <?php IncluirCSS();?>
</head>
<body>
    <?php MostrarMenu();?>

   
    <section class="registrer-section" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        <div class="container" style="max-width: 600px;">
            
            <?php
            if(isset($_SESSION["txtMensaje"])){
                echo '<div class="alert alert-danger">' . $_SESSION["txtMensaje"] . '</div>';
                unset($_SESSION['txtMensaje']);           
            }
            ?>    

            <h4 class="text-center my-3" >Iniciar Sesión</h4>

            <form method="POST" id="contactForm" name="contactForm" 
                  class="bg-white p-4 rounded shadow" 
                  style="border: 1px solid #ccc;">
                
                <div class="mb-3">
                    <label for="CorreoElectronico" class="form-label" style="color: black;">Correo Electrónico</label>
                    <input type="email" class="form-control" name="CorreoElectronico" id="CorreoElectronico" required>
                    <div id="emailHelp" class="form-text">No se compartirá su correo con nadie.</div>
                </div>
                
                <div class="mb-3">
                    <label for="Contrasenna" class="form-label" style="color: black;">Contraseña</label>
                    <input type="password" class="form-control" name="Contrasenna" id="Contrasenna" required>
                </div>
                
                <button type="submit" class="btn btn-custom w-100" name="btnIniciarSesion">Iniciar sesión</button>
                
                <p class="text-end mt-3" style="color: black;">
                    ¿No tienes cuenta? <a class="link-azul" href="RegistrarPaciente.php">Registrarse</a>
                </p>
            </form>
        </div>
    </section>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
</body>
</html>
