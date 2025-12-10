<?php
include_once __DIR__ . '/../Model/recuperarModel.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$token = $_GET["token"] ?? null;

if (!$token) {
    die("Token inválido");
}

$usuario = ObtenerUsuarioPorToken($token);

if (!$usuario) {
    die("El enlace ha expirado o no es válido.");
}

if (isset($_POST["btnCambiar"])) {

    $pass = $_POST["Contrasenna"];
    $conf = $_POST["Confirmar"];

    if ($pass !== $conf) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        ActualizarContrasenna($usuario["IdUsuario"], $hash);

        echo "<script>
                alert('Contraseña actualizada con éxito');
                window.location='iniciarSesion.php';
              </script>";
        exit;
    }
}

include('layout.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
    <?php IncluirCSS(); ?>
</head>

<body>
<?php MostrarMenu(); ?>

<section class="full-height-section">
    <div class="container" data-aos="fade-up">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <h4 class="text-center mb-3">Restablecer Contraseña</h4>

                <?php if (!empty($mensaje)) : ?>
                    <div class="alert alert-danger text-center"><?= $mensaje ?></div>
                <?php endif; ?>

                <form method="POST" class="contactForm">
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña</label>
                        <input 
                            type="password" 
                            name="Contrasenna" 
                            class="form-control" 
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar contraseña</label>
                        <input 
                            type="password" 
                            name="Confirmar" 
                            class="form-control" 
                            required>
                    </div>

                    <div class="text-center">
                        <button type="submit" name="btnCambiar" class="btn btn-custom">
                            Guardar nueva contraseña
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
