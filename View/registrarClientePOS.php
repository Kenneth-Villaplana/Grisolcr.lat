<?php 
include_once 'layout.php';
include_once __DIR__ . '/../Controller/loginController.php';


$cedulaPrefill = $_GET['cedula'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Registrar Cliente - POS</title>

    <?php IncluirCSS(); ?>
      
</head>

<body>

<?php MostrarMenu(); ?>

<div class="container">
     
    <div class="d-flex justify-content-end mt-5 mb-3">
      <a href="puntoVenta.php" class="btn btn-outline-secondary btn-back-custom">
        ← Volver al punto de venta
      </a>
    </div>
    <div class="pos-card">

        <h2 class="pos-title">Registrar Nuevo Cliente</h2>

        <form method="POST">

           
            <input type="hidden" name="origen" value="POS">
            <div class="mb-3">
                <label class="form-label fw-semibold">Cédula</label>
                <input type="text" 
                       class="form-control" 
                       id="Cedula" 
                       name="Cedula"
                       value="<?= htmlspecialchars($cedulaPrefill) ?>"
                       onkeyup="ConsultarNombre()"
                       required 
                       readonly >
            </div>

            <div class="row">
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input type="text" class="form-control" id="Nombre" name="Nombre" readonly required>
                </div>

                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Primer Apellido</label>
                    <input type="text" class="form-control" id="Apellido" name="Apellido" readonly required>
                </div>
            </div>

            <div class="row">
               
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Segundo Apellido</label>
                    <input type="text" class="form-control" id="ApellidoDos" name="ApellidoDos" readonly required>
                </div>

               
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Correo Electrónico</label>
                    <input type="email" class="form-control" name="CorreoElectronico" required>
                </div>
            </div>

            <div class="row">
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Contraseña</label>
                    <input type="password" class="form-control" name="Contrasenna" required>
                </div>

                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Confirmar Contraseña</label>
                    <input type="password" class="form-control" name="ConfirmarContrasenna" required>
                </div>
            </div>

            <div class="row">
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" class="form-control" name="Telefono">
                </div>

                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Dirección</label>
                    <input type="text" class="form-control" name="Direccion">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Fecha de nacimiento</label>
                <input type="date" class="form-control" name="FechaNacimiento" required>
            </div>

            <button class="btn btn-outline-primary btn-pos" type="submit" name="btnRegistrarPaciente">
                Registrar Cliente
            </button>

        </form>
    </div>
</div>

<?php IncluirScripts(); ?>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const cedField = document.getElementById("Cedula");
    if (!cedField) return;

    const ced = cedField.value.trim();

   
    const esperar = setInterval(() => {
        if (typeof ConsultarNombre === "function") {
            clearInterval(esperar);
            if (ced.length >= 9) {
                setTimeout(() => ConsultarNombre(), 200);
            }
        }
    }, 100);

});
</script>
</body>
</html>