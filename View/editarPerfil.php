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
    <div class="container">

        
        <?php if(isset($_SESSION["txtMensaje"])): ?>
            <div class="alert alert-<?= isset($_SESSION["CambioExitoso"]) ? 'success' : 'danger' ?> text-center mt-3 mb-4">
                <?= $_SESSION["txtMensaje"]; ?>
            </div>
            <?php unset($_SESSION["txtMensaje"], $_SESSION["CambioExitoso"]); ?>
        <?php endif; ?>


        
        <div class="row justify-content-center">
            <div class="profile-card shadow-sm" data-aos="fade-up">
                <div class="profile-header">
                    <h4 class="mb-0">Perfil</h4>
                    <small class="text-muted">Actualice sus datos personales</small>
                </div>

                
                <form method="POST" name="contactForm" class="row g-3 p-4">
                    <input type="hidden" name="IdUsuario" id="IdUsuario"
                        value="<?= $usuario['IdUsuario']; ?>" required>

                   
                    <div class="col-md-6">
                        <h6 class="profile-section-title">Datos Personales</h6>
                        <label class="form-label">Cédula</label>
                        <input type="text" name="Cedula" class="form-control mb-3"
                               value="<?= $usuario['Cedula']; ?>" required>

                        <label class="form-label">Nombre</label>
                        <input type="text" name="Nombre" class="form-control mb-3"
                               value="<?= $usuario['Nombre']; ?>" required>

                        <label class="form-label">Primer Apellido</label>
                        <input type="text" name="Apellido" class="form-control mb-3"
                               value="<?= $usuario['Apellido']; ?>" required>

                        <label class="form-label">Segundo Apellido</label>
                        <input type="text" name="ApellidoDos" class="form-control mb-3"
                               value="<?= $usuario['ApellidoDos']; ?>" required>

                        <label class="form-label">Fecha Nacimiento</label>
                        <input type="date" name="FechaNacimiento" class="form-control mb-3"
                               value="<?= $usuario['FechaNacimiento']; ?>">
                    </div>
                
                    <div class="col-md-6">
                        <h6 class="profile-section-title">Contacto</h6>
                        <label class="form-label">Número de Teléfono</label>
                        <input type="text" name="Telefono" class="form-control mb-3"
                               value="<?= $usuario['Telefono']; ?>" required>

                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="CorreoElectronico" class="form-control mb-3"
                               value="<?= $usuario['CorreoElectronico']; ?>" required>

                        <label class="form-label">Dirección</label>
                        <input type="text" name="Direccion" class="form-control mb-3"
                               value="<?= $usuario['Direccion']; ?>" required>
                    </div>

                   
                    <div class="col-12 text-center mt-3">
                        <button type="submit" class="btn btn-outline-primary px-4"
                                id="btnEditarPerfil" name="btnEditarPerfil">
                            <i class="bi bi-pencil-square"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

</body>
</html>