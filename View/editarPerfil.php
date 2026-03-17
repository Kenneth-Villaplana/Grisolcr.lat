<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

include('layout.php'); 
require_once __DIR__ . '/../Controller/usuarioController.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Óptica Grisol</title>
    <?php IncluirCSS(); ?>
</head>

<body>

<?php MostrarMenu(); ?>

<main class="editar-section">
    <div class="container mt-5">

        <!-- ALERTA -->
        <?php if(isset($_SESSION["txtMensaje"])): ?>
            <div class="alert modern-alert alert-<?= isset($_SESSION["CambioExitoso"]) ? 'success' : 'danger' ?> text-center mt-3 mb-4">
                <?= $_SESSION["txtMensaje"]; ?>
            </div>
            <?php unset($_SESSION["txtMensaje"], $_SESSION["CambioExitoso"]); ?>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="edit-card shadow-modern">

                    <div class="edit-header text-center">
                        <h4>Perfil</h4>
                        <p>Actualice sus datos personales</p>
                    </div>

                    <form method="POST" class="p-4 row g-4">

                        <input type="hidden" name="IdUsuario" value="<?= $usuario['IdUsuario'] ?? '' ?>">

                        <!-- IZQUIERDA -->
                        <div class="col-md-6">
                            <h6 class="section-title">Datos personales</h6>

                            <label>Cédula</label>
                            <input type="text" name="Cedula" class="form-control mb-3"
                                value="<?= $usuario['Cedula'] ?? '' ?>" readonly>

                            <label>Nombre</label>
                            <input type="text" name="Nombre" class="form-control mb-3"
                                value="<?= $usuario['Nombre'] ?? '' ?>" readonly>

                            <label>Primer Apellido</label>
                            <input type="text" name="Apellido" class="form-control mb-3"
                                value="<?= $usuario['Apellido'] ?? '' ?>" readonly>

                            <label>Segundo Apellido</label>
                            <input type="text" name="ApellidoDos" class="form-control mb-3"
                                value="<?= $usuario['ApellidoDos'] ?? '' ?>" readonly>

                            <label>Fecha de Nacimiento</label>
                            <input type="date" name="FechaNacimiento" class="form-control mb-3"
                                value="<?= $usuario['FechaNacimiento'] ?? '' ?>">
                        </div>

                        <!-- DERECHA -->
                        <div class="col-md-6">
                            <h6 class="section-title">Contacto</h6>

                            <label>Teléfono</label>
                            <input type="text" name="Telefono" class="form-control mb-3"
                                value="<?= $usuario['Telefono'] ?? '' ?>" required>

                            <label>Correo</label>
                            <input type="email" name="CorreoElectronico" class="form-control mb-3"
                                value="<?= $usuario['CorreoElectronico'] ?? '' ?>" required>

                            <label>Dirección</label>
                            <input type="text" name="Direccion" class="form-control mb-3"
                                value="<?= $usuario['Direccion'] ?? '' ?>" required>
                        </div>

                        <div class="col-12 text-center mt-3">
                            <button type="submit" name="btnEditarPerfil" class="btn btn-primary">
                                Guardar Cambios
                            </button>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
</main>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

</body>
</html>