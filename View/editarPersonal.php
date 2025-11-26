<?php
 include('layout.php'); 
 include_once __DIR__ . '/../Controller/personalController.php';

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

      
 <section class="full-height-section">
  <main class="container py-4">
          <div class="d-flex justify-content-end mt-3">
        <a href="personal.php" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> Volver a personal
        </a>
      </div>
  <div class="container" data-aos="fade-up">
         <?php
                if(isset($_SESSION["txtMensaje"])){
                 echo '<div class="alert alert-' . (isset($_SESSION["CambioExitoso"]) ? 'success' : 'danger') . '">' . $_SESSION["txtMensaje"] . '</div>';
                 unset($_SESSION["txtMensaje"]);   
                 unset($_SESSION["CambioExitoso"]);        
          }
          ?>  
      

        <div class="profile-card" data-aos="fade-up">
            <div class="profile-header">
                  <h4 class="mb-0">Datos Personal</h4>
                  </div>

              <form method="POST" name="contactForm" class="row g-3 p-3" >
              <input type="hidden" name="IdUsuario" id="IdUsuario" class="form-control" value= "<?php echo $usuario['IdUsuario']; ?>" required>
            

            <div class="col-md-6"> 
                   <h6 class="profile-section-title">Datos Personales</h6>
                  <div class="mb-3">
              <label for="cedula" class="form-label">Cédula</label>
              <input type="text" name="Cedula" id="cedula" class="form-control" value= "<?php echo $usuario['Cedula']; ?>" required>
            </div>

            
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" name="Nombre" id="nombre" class="form-control" value= "<?php echo $usuario['Nombre']; ?>" required>
            </div>

            
            <div class="mb-3">
              <label for="apellido" class="form-label">Primer Apellido</label>
              <input type="text" name="Apellido" id="apellido" class="form-control" value= "<?php echo $usuario['Apellido']; ?>"required>
            </div>

            <div class="mb-3">
              <label for="apellidoDos" class="form-label">Segundo Apellido</label>
              <input type="text" name="ApellidoDos" id="apellidoDos" class="form-control" value= "<?php echo $usuario['ApellidoDos']; ?>" required>
            </div>
            
            <div class="mb-3">
                 <label for="RolId" class="form-label">Seleccione el rol</label>
                                   <select name="RolId" id="RolId" class="form-select" required="">
                                   <option value="">Seleccionar</option>
                                   <option value="1" <?php if ($usuario['Id_rol'] == 1) echo 'selected'; ?>>Administrador/a</option>
                                   <option value="2" <?php if ($usuario['Id_rol'] == 2) echo 'selected'; ?>>Asistente</option>
                                   <option value="3" <?php if ($usuario['Id_rol'] == 3) echo 'selected'; ?>>Doctor/a</option>
                                   <option value="4" <?php if ($usuario['Id_rol'] == 4) echo 'selected'; ?>>Cajero/a</option>
                                   </select>
                    </div>
                 </div>

               <div class="col-md-6"> 
                    <h6 class="profile-section-title">Contacto</h6>
                <div class="mb-3">
              <label for="correoElectronico" class="form-label">Correo Electrónico</label>
              <input type="email" name="CorreoElectronico" id="correoElectronico" class="form-control" value= "<?php echo $usuario['CorreoElectronico']; ?>" required>
            </div>

          
            <div class="mb-3">
              <label for="telefono" class="form-label">Número de Teléfono</label>
              <input type="text" name="Telefono" id="telefono" class="form-control"value= "<?php echo $usuario['Telefono']; ?>" required>
            </div>

           <div class="mb-3">
              <label for="direccion" class="form-label">Dirección</label>
              <input type="text" name="Direccion" id="direccion" class="form-control" value= "<?php echo $usuario['Direccion']; ?>" required>
            </div>
         </div>
            
            <div class="col-12 text-center mt-3">
              <div class="form-check d-inline-block">
                <input class="form-check-input" type="checkbox" name="Estado" id="Estado" value="0"
                <?php if ($usuario['Estado'] == 0) echo 'checked'; ?>
                <label class="form-check-label" for="Estado">Inactivar</label>
              </div>
            </div>

            
             <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-custom px-4" id="btnEditarPersonal" name="btnEditarPersonal">
                <i class="bi bi-pencil-square"></i> Guardar Cambios
              </button>
            </div>

          </div>
        </form>

      </div>
    </div>
  </div>
</section>
</main>
     <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
</body>
</html>
   