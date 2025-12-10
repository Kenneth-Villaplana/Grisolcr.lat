<?php
include('layout.php'); 
include_once __DIR__ . '/../Controller/usuarioController.php';
?>

<!DOCTYPE html>
<html lang="en">
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

                <!-- CARD -->
                <div class="edit-card shadow-modern">

                    <!-- HEADER -->
                    <div class="edit-header text-center">
                        <h4>Perfil</h4>
                        <p>Actualice sus datos personales</p>
                    </div>

                    <!-- FORM -->
                    <form method="POST" class="p-4 row g-4">

                        <input type="hidden" name="IdUsuario" value="<?= $usuario['IdUsuario']; ?>">

                        <!-- COLUMNA IZQUIERDA -->
                        <div class="col-md-6">
                            <h6 class="section-title">Datos personales</h6>

                            <label class="form-label">Cédula</label>
                            <input type="text" name="Cedula" class="form-control input-modern mb-3"
                                value="<?= $usuario['Cedula']; ?>" required readonly>

                            <label class="form-label">Nombre</label>
                            <input type="text" name="Nombre" class="form-control input-modern mb-3"
                                value="<?= $usuario['Nombre']; ?>" required readonly>

                            <label class="form-label">Primer Apellido</label>
                            <input type="text" name="Apellido" class="form-control input-modern mb-3"
                                value="<?= $usuario['Apellido']; ?>" required readonly>

                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" name="ApellidoDos" class="form-control input-modern mb-3"
                                value="<?= $usuario['ApellidoDos']; ?>" required readonly>

                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" name="FechaNacimiento" class="form-control input-modern mb-3"
                                value="<?= $usuario['FechaNacimiento']; ?>">
                        </div>

                        <!-- COLUMNA DERECHA -->
                        <div class="col-md-6">
                            <h6 class="section-title">Contacto</h6>

                            <label class="form-label">Número Telefónico</label>
                            <input type="text" name="Telefono" class="form-control input-modern mb-3"
                                value="<?= $usuario['Telefono']; ?>" required>

                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="CorreoElectronico" class="form-control input-modern mb-3"
                                value="<?= $usuario['CorreoElectronico']; ?>" required>

                            <label class="form-label">Dirección</label>
                            <input type="text" name="Direccion" class="form-control input-modern mb-3"
                                value="<?= $usuario['Direccion']; ?>" required>
                        </div>

                        <!-- BOTÓN -->
                        <div class="col-12 text-center mt-2">
                            <button type="submit" id="btnEditarPerfil" name="btnEditarPerfil"
                                class="btn-save-modern">
                                <i class="bi bi-pencil-square"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>

                </div><!-- END CARD -->

            </div>
        </div>
    </div>
</main>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

</body>
</html>