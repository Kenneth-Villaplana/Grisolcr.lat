<?php
 include('layout.php'); 
 include_once __DIR__ . '/../Controller/personalController.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Óptica Grisol</title>
    <?php IncluirCSS();?>
</head>

<body>

<?php MostrarMenu(); ?>

<main class="editar-section">
    <div class="container">

        <div class="d-flex justify-content-end mt-3 mb-3">
            <a href="personal.php" class="btn btn-outline-secondary btn-back-custom">
                <i class="bi bi-arrow-left"></i> Volver a personal
            </a>
        </div>

        
        <?php if(isset($_SESSION["txtMensaje"])): ?>
            <div class="alert alert-<?= isset($_SESSION["CambioExitoso"]) ? 'success' : 'danger' ?> text-center mb-4">
                <?= $_SESSION["txtMensaje"]; ?>
            </div>
            <?php unset($_SESSION["txtMensaje"], $_SESSION["CambioExitoso"]); ?>
        <?php endif; ?>

       
        <div class="profile-card shadow-sm" data-aos="fade-up">
            <div class="profile-header">
                <h4 class="mb-0">Datos Personal</h4>
                <small class="text-muted">Actualice los datos del colaborador</small>
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

                    <label class="form-label">Seleccione el rol</label>
                    <select name="RolId" class="form-select mb-3" required>
                        <option value="">Seleccionar</option>
                        <option value="1" <?= $usuario['Id_rol']==1?'selected':'' ?>>Administrador/a</option>
                        <option value="2" <?= $usuario['Id_rol']==2?'selected':'' ?>>Asistente</option>
                        <option value="3" <?= $usuario['Id_rol']==3?'selected':'' ?>>Doctor/a</option>
                        <option value="4" <?= $usuario['Id_rol']==4?'selected':'' ?>>Cajero/a</option>
                    </select>

                </div>

                
                <div class="col-md-6">
                    <h6 class="profile-section-title">Contacto</h6>

                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="CorreoElectronico" class="form-control mb-3"
                           value="<?= $usuario['CorreoElectronico']; ?>" required>

                    <label class="form-label">Teléfono</label>
                    <input type="text" name="Telefono" class="form-control mb-3"
                           value="<?= $usuario['Telefono']; ?>" required>

                    <label class="form-label">Dirección</label>
                    <input type="text" name="Direccion" class="form-control mb-3"
                           value="<?= $usuario['Direccion']; ?>" required>

                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="FechaNacimiento" class="form-control mb-3"
                           value="<?= $usuario['FechaNacimiento']; ?>">
                </div>

                <
               <div class="col-12 text-center mt-2">
    <input type="hidden" name="Estado" value="1">

    <div class="form-check d-inline-block">
        <input class="form-check-input" 
               type="checkbox" 
               name="Estado" 
               id="Estado" 
               value="0"
               <?= $usuario['Estado']==0 ? 'checked' : '' ?>>
        <label class="form-check-label" for="Estado">
            Inactivar
        </label>
             </div>

        </div>
              
                <div class="col-12 text-center mt-3">
                    <button type="submit" class="btn btn-custom px-4"
                            id="btnEditarPersonal" name="btnEditarPersonal">
                        <i class="bi bi-pencil-square"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

</body>
</html>