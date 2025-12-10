<?php
include_once('layout.php');
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

     
        $mostrarModalExito = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
    <?php IncluirCSS(); ?>

    <style>

    </style>
</head>

<body>
<?php MostrarMenu(); ?>
<section class="editar-section d-flex align-items-center justify-content-center py-5 my-5">
    <div class="reset-card">

        
        <div class="reset-header-consistent">
            <h4 class="mb-0">Restablecer Contraseña</h4>
            <small>Ingrese su nueva contraseña para continuar</small>
        </div>

        
        <div class="card-body">

            <?php if (!empty($mensaje)) : ?>
                <div class="alert alert-danger text-center"><?= $mensaje ?></div>
            <?php endif; ?>

            <form method="POST" class="row g-3">

                <div class="col-12">
                    <label class="form-label">Nueva contraseña</label>
                    <input 
                        type="password" 
                        name="Contrasenna" 
                        class="form-control campo-obligatorio" 
                        required>
                </div>

                <div class="col-12">
                    <label class="form-label">Confirmar contraseña</label>
                    <input 
                        type="password" 
                        name="Confirmar" 
                        class="form-control campo-obligatorio" 
                        required>
                </div>
                <div class="col-12 text-center mt-3">
                    <button type="submit" name="btnCambiar" class="btn-reset">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>


<div class="modal fade" id="modalExito" tabindex="-1" aria-labelledby="modalExitoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg">

     
      <div class="modal-gradient-header">
        <div class="modal-success-icon">
            <i class="bi bi-check-lg"></i>
        </div>
        <h5 class="fw-bold text-white m-0" id="modalExitoLabel">¡Contraseña actualizada!</h5>
      </div>

     
      <div class="modal-body modal-body-custom">
        <p class="text-muted">Su contraseña fue cambiada correctamente.</p>
      </div>

      <div class="text-center pb-4">
        <a href="iniciarSesion.php" class="btn-reset-modal px-4">
            Ir a iniciar sesión
        </a>
      </div>

    </div>
  </div>
</div>


<!-- Script que muestra el modal -->
<?php if (!empty($mostrarModalExito)) : ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let modal = new bootstrap.Modal(document.getElementById("modalExito"));
    modal.show();
});
</script>
<?php endif; ?>

</body>
</html>