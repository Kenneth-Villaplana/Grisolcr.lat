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

<main class="editar-section py-5">
    <div class="container">

      
        <div class="d-flex justify-content-end mb-4" data-aos="fade-down">
            <a href="personal.php" class="btn btn-back-custom">
                <i class="bi bi-arrow-left"></i> Volver a personal
            </a>
        </div>

        <!-- Mensajes -->
        <?php if(isset($_SESSION["txtMensaje"])): ?>
            <div class="alert alert-<?= isset($_SESSION["CambioExitoso"]) ? 'success' : 'danger' ?> text-center modern-alert mb-4">
                <?= $_SESSION["txtMensaje"]; ?>
            </div>
            <?php unset($_SESSION["txtMensaje"], $_SESSION["CambioExitoso"]); ?>
        <?php endif; ?>

    
        <div class="edit-card shadow-modern" data-aos="fade-up">

            <div class="edit-header text-center">
                <h4>Editar Personal</h4>
                <p>Actualice los datos del colaborador</p>
            </div>

           <form method="POST" class="p-4 row g-4">

    <input type="hidden" name="IdUsuario" value="<?= $usuario['IdUsuario']; ?>">

    <!-- COLUMNA IZQUIERDA -->
    <div class="col-md-6">
        <h6 class="section-title">Datos Personales</h6>

        <label class="form-label mt-2">Cédula</label>
        <input type="text" name="Cedula" class="form-control input-modern"
            value="<?= $usuario['Cedula']; ?>" required readonly>

        <label class="form-label mt-2">Nombre</label>
        <input type="text" name="Nombre" class="form-control input-modern"
            value="<?= $usuario['Nombre']; ?>" required readonly>

        <label class="form-label mt-2">Primer Apellido</label>
        <input type="text" name="Apellido" class="form-control input-modern"
            value="<?= $usuario['Apellido']; ?>" required readonly>

        <label class="form-label mt-2">Segundo Apellido</label>
        <input type="text" name="ApellidoDos" class="form-control input-modern"
            value="<?= $usuario['ApellidoDos']; ?>" required readonly>

        <label class="form-label mt-2">Rol</label>
        <select name="RolId" class="form-select input-modern" required>
            <option value="">Seleccionar</option>
            <option value="1" <?= $usuario['Id_rol']==1?'selected':'' ?>>Administrador/a</option>
            <option value="2" <?= $usuario['Id_rol']==2?'selected':'' ?>>Asistente</option>
            <option value="3" <?= $usuario['Id_rol']==3?'selected':'' ?>>Doctor/a</option>
            <option value="4" <?= $usuario['Id_rol']==4?'selected':'' ?>>Cajero/a</option>
        </select>

        
        <label class="form-label mt-2">Fecha de nacimiento</label>
        <input type="date" name="FechaNacimiento" class="form-control input-modern"
            value="<?= $usuario['FechaNacimiento']; ?>">
    </div>

    <!-- COLUMNA DERECHA -->
    <div class="col-md-6">
        <h6 class="section-title">Contacto</h6>

        <label class="form-label mt-2">Correo Electrónico</label>
        <input type="email" name="CorreoElectronico" class="form-control input-modern"
            value="<?= $usuario['CorreoElectronico']; ?>" required>

        <label class="form-label mt-2">Teléfono</label>
        <input type="text" name="Telefono" class="form-control input-modern"
            value="<?= $usuario['Telefono']; ?>" required>

        <label class="form-label mt-2">Dirección</label>
        <input type="text" name="Direccion" class="form-control input-modern"
            value="<?= $usuario['Direccion']; ?>" required>
    </div>

   
    <div class="col-12 text-center">
        <div class="switch-container mt-2">
            <input class="form-check-input form-switch" 
                type="checkbox" name="Estado" id="Estado" value="0"
                <?= $usuario['Estado']==0 ? 'checked' : '' ?>>
            <label class="form-check-label ms-2" for="Estado">Inactivar</label>
        </div>
    </div>

  
    <div class="col-12 text-center">
        <button type="submit" class="btn-save-modern" name="btnEditarPersonal">
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