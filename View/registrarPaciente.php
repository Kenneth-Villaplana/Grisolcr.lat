<?php 
    include_once 'layout.php';
    include_once __DIR__ . '/../Controller/loginController.php';
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
    
    <section class="registrer-section">
        <div class="container ">
            <?php
                if(isset($_SESSION["txtMensaje"])){
                 echo '<div class="alert alert-' . (isset($_SESSION["registroExitoso"]) ? 'success' : 'danger') . '">' . $_SESSION["txtMensaje"] . '</div>';
                 unset($_SESSION["txtMensaje"]);   
                 unset($_SESSION["registroExitoso"]);        
          }
          ?>  
        <div class="register-container" data-aos="fade-up">
                  <h4 class="text-center mb-1" style="color: black;">Ingrese sus Datos</h4>
                   <form method="POST" name="contactForm" class="row g-3" >
                   
                      
                                          <div class="col-md-6">           
                                                <label for="Cedula" class="form-label" style="color: black;">Cédula</label>
                                                <input type="text" class="form-control" name="Cedula" id="Cedula"
                                                placeholder="" required>
                                            </div>

                                          <div class="col-md-6">          
                                           <label for="Nombre" class="form-label" style="color: black;">Nombre</label>
                                                <input type="text" class="form-control" name="Nombre" id="Nombre"
                                                placeholder="" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="Apellido" class="form-label" style="color: black;">Primer Apellido</label>
                                                <input type="text" class="form-control" name="Apellido" id="Apellido"
                                                    placeholder="" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="ApellidoDos" class="form-label" style="color: black;">Segundo Apellido</label>
                                                <input type="text" class="form-control" name="ApellidoDos" id="ApellidoDos"
                                                    placeholder="" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="CorreoElectronico" class="form-label" style="color: black;">Correo Electrónico</label>
                                                <input type="email" class="form-control" name="CorreoElectronico" id="CorreoElectronico"
                                                    placeholder="" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="Contrasenna" class="form-label" style="color: black;">Contraseña</label>
                                                <input type="password" class="form-control" name="Contrasenna"
                                                    id="Contrasenna" placeholder="" required>
                                            </div>

                                           <div class="col-md-6">
                                                <label for="ConfirmarContrasenna" class="form-label" style="color: black;">Confirmar Contraseña</label>
                                                <input type="password" class="form-control" name="ConfirmarContrasenna"
                                                    id="ConfirmarContrasenna" placeholder="" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="Telefono" class="form-label" style="color: black;">Teléfono</label>
                                                <input type="text" class="form-control" name="Telefono" id="Telefono"
                                                    placeholder="" required>
                                            </div>

                                            <div class="col-md-12">
                                                <label for="Direccion" class="form-label" style="color: black;">Dirección</label>
                                                <input type="text" class="form-control" name="Direccion" id="Direccion"
                                                    placeholder="">
                                            </div>
                                             <div class="col-md-12">
                                                <label for="FechaNacimiento" class="form-label" style="color: black;">Fecha Nacimiento</label>
                                                <input type="date" class="form-control" name="FechaNacimiento" id="FechaNacimiento"
                                                required max="<?= date('Y-m-d') ?>" placeholder="">
                                            </div>

                                            <div class="col-md-12 text-center">
                                                <div class="form-group">
                                                    <p style="color: black;">¿Ya tienes cuenta? <a class="link-azul" href="iniciarSesion.php">Iniciar sesión</a></p>
                                                </div>
                                            
                                            <div class="col-md-12 text-center ">
                                                <button type="submit" class="btn btn-custom" name="btnRegistrarPaciente">Registrarse</button>
                                            </div>
                                      </form>
                                </div>
                             </div>
                             
                   </section>
    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
</body>
</html>
