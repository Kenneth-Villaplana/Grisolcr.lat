 <?php
 include('layout.php'); 
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
        <div class="container" data-aos="fade-up">
       <div class="row justify-content-center">
        <div class="col-md-6">  
                   
                   <h4 class="text-center mb-3 ">Recuperar Contraseña</h4>
                   <p class="text-center mb-4">Por favor ingrese el correo electronico que tiene asignado su cuenta.</p>
                   <form method="POST" id="contactForm" name="contactForm" class="contactForm">
                     <div class="mb-3 text-center">           
                            <label for="Email" class="form-label">Correo Electrónico</label>
                                                <input type="email" class="form-control" name="Email" id="Email"
                                                    placeholder="" required>
                                            </div>
                                            <div class="text-center mb-3"> 
                                                <p>¿No tienes cuenta? <a class="link-azul" href="RegistrarPaciente.php">Registrarse</a></p>
                                                </div>
                                            

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-custom" id="btnRecuperar" name="btnRecuperar">Recuperar</button>
                                            </div>
                                       
                                    </form>
                                </div>
                             </div>
                        </div>
                    </div>
            </section>
    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
</body>

</html>