<?php
include('layout.php');
include_once __DIR__ . '/../Controller/loginController.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Óptica Grisol</title>
    <?php IncluirCSS(); ?>
</head>

<body>

<?php MostrarMenu(); ?>

<main class="editar-section">

    <div class="container">

        <div class="mb-3 text-end mt-3">
            <a href="personal.php" class="btn btn-outline-secondary btn-back-custom">
                ← Volver a personal
            </a>
        </div>

    
        <div class="row justify-content-center mb-3">
            <div class="col-12 col-lg-8">
                <?php
                if (isset($_SESSION["txtMensaje"])) {
                    echo '<div class="alert alert-' .
                        (isset($_SESSION["registroExitoso"]) ? 'success' : 'danger') . '">' .
                        $_SESSION["txtMensaje"] .
                    '</div>';
                    unset($_SESSION["txtMensaje"]);
                    unset($_SESSION["registroExitoso"]);
                }
                ?>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="register-card-compact">

                <div class="register-card-header">
                    <h4 class="mb-0">Registrar Personal</h4>
                    <small>Ingrese los datos del nuevo miembro del personal</small>
                </div>

                <div class="p-4">
                    <form method="POST" name="contactForm" class="row g-4">

                        <div class="col-12 col-md-6">
                            <label class="form-label">Cédula</label>
                            <input type="text"
                                   class="form-control"
                                   name="Cedula"
                                   id="Cedula"
                                   required
                                   onkeyup="ConsultarNombre();">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text"
                                   class="form-control"
                                   name="Nombre"
                                   id="Nombre"
                                   required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Primer Apellido</label>
                            <input type="text"
                                   class="form-control"
                                   name="Apellido"
                                   id="Apellido"
                                   required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text"
                                   class="form-control"
                                   name="ApellidoDos"
                                   id="ApellidoDos"
                                   required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email"
                                   class="form-control"
                                   name="CorreoElectronico"
                                   id="CorreoElectronico"
                                   required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password"
                                   class="form-control"
                                   name="Contrasenna"
                                   id="Contrasenna"
                                   required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password"
                                   class="form-control"
                                   name="ConfirmarContrasenna"
                                   id="ConfirmarContrasenna"
                                   required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text"
                                   class="form-control"
                                   name="Telefono"
                                   id="Telefono"
                                   required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input type="text"
                                   class="form-control"
                                   name="Direccion"
                                   id="Direccion">
                        </div>
                        
                        <div class="col-md-12">
                        <label for="FechaNacimiento" class="form-label">Fecha Nacimiento</label>
                        <input type="date" class="form-control" name="FechaNacimiento" id="FechaNacimiento"
                               required max="<?= date('Y-m-d') ?>" placeholder="">
                    </div>
                        <div class="col-12">
                            <label class="form-label">Seleccione el rol</label>
                            <select name="RolId" id="RolId" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <option value="1">Administrador/a</option>
                                <option value="2">Asistente</option>
                                <option value="3">Doctor/a</option>
                                <option value="4">Cajero/a</option>
                            </select>
                        </div>

                        <div class="col-12 text-center mt-1">
                            <button type="submit"
                                    class="btn btn-primary btn-register-custom"
                                    id="btnRegistrarPersonal"
                                    name="btnRegistrarPersonal">
                                Registrar
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