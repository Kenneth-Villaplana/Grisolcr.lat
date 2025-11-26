<?php
include('layout.php');
include_once __DIR__ . '/../Controller/loginController.php';

?>

<html lang="es">
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

    <section class="d-flex align-items-center justify-content-center min-vh-100">
    <main class="container py-4">
          <div class="d-flex justify-content-end mt-3">
        <a href="personal.php" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> Volver a personal
        </a>
      </div>    
    <div class="container">
            <?php
            if (isset($_SESSION["txtMensaje"])) {
                echo '<div class="alert alert-' . (isset($_SESSION["registroExitoso"]) ? 'success' : 'danger') . '">' . $_SESSION["txtMensaje"] . '</div>';
                unset($_SESSION["txtMensaje"]);   
                unset($_SESSION["registroExitoso"]);        
            }
            ?>  

            <div class="register-container" data-aos="fade-up">
                <h4 class="text-center mb-1">Ingrese Datos de Personal</h4>
                <form method="POST" name="contactForm" class="row g-3">

                    <div class="col-md-6">    
                        <label for="Cedula" class="form-label">Cédula</label>
                        <input type="text" class="form-control" name="Cedula" id="Cedula"
                               placeholder=""  required onkeyup="ConsultarNombre();">
                    </div>

                    <div class="col-md-6">            
                        <label for="Nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="Nombre" id="Nombre"
                               placeholder="" required>
                    </div>

                    <div class="col-md-6"> 
                        <label for="Apellido" class="form-label">Primer Apellido</label>
                        <input type="text" class="form-control" name="Apellido" id="Apellido"
                               placeholder="" required>
                    </div>

                    <div class="col-md-6"> 
                        <label for="ApellidoDos" class="form-label">Segundo Apellido</label>
                        <input type="text" class="form-control" name="ApellidoDos" id="ApellidoDos"
                               placeholder="" required>
                    </div>

                    <div class="col-md-6"> 
                        <label for="CorreoElectronico" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="CorreoElectronico" id="CorreoElectronico"
                               placeholder="" required>
                    </div>

                    <div class="col-md-6"> 
                        <label for="Contrasenna" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="Contrasenna" id="Contrasenna"
                               placeholder="" required>
                    </div>

                    <div class="col-md-6"> 
                        <label for="ConfirmarContrasenna" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" name="ConfirmarContrasenna" id="ConfirmarContrasenna"
                               placeholder="" required>
                    </div>

                    <div class="col-md-6"> 
                        <label for="Telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="Telefono" id="Telefono"
                               placeholder="" required>
                    </div>

                    <div class="col-md-12">
                        <label for="Direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="Direccion" id="Direccion"
                               placeholder="">
                    </div>

                    <div class="col-md-12">
                        <label for="RolId" class="form-label">Seleccione el rol</label>
                        <select name="RolId" id="RolId" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="1">Administrador/a</option>
                            <option value="2">Asistente</option>
                            <option value="3">Doctor/a</option>
                            <option value="4">Cajero/a</option>
                        </select>
                    </div>

                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-custom" id="btnRegistrarPersonal" name="btnRegistrarPersonal">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>

 
</body>
</html>