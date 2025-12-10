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
        <a href="puntoVenta.php" class="btn btn-back-custom">
                <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <!-- ===== TARJETA COMPACTA DE REGISTRO ===== -->
    <div class="register-card-compact shadow-lg mx-auto" style="max-width: 750px; animation: fadeUp .5s ease;">
        
        <!-- HEADER -->
        <div class="register-card-header">
            <h4>Registrar Nuevo Cliente</h4>
            <small>Complete los datos para agregar el cliente al sistema</small>
        </div>

        <!-- CONTENIDO -->
        <div class="p-4">

            <form method="POST">

                <input type="hidden" name="origen" value="POS">

                <!-- CÉDULA -->
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
                    <!-- NOMBRE -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" class="form-control" id="Nombre" name="Nombre" readonly required>
                    </div>

                    <!-- APELLIDO 1 -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Primer Apellido</label>
                        <input type="text" class="form-control" id="Apellido" name="Apellido" readonly required>
                    </div>
                </div>

                <div class="row">
                    <!-- APELLIDO 2 -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Segundo Apellido</label>
                        <input type="text" class="form-control" id="ApellidoDos" name="ApellidoDos" readonly required>
                    </div>

                    <!-- EMAIL -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Correo Electrónico</label>
                        <input type="email" class="form-control" name="CorreoElectronico" required>
                    </div>
                </div>

                <div class="row">
                    <!-- CONTRASEÑA -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Contraseña</label>
                        <input type="password" class="form-control" name="Contrasenna" required>
                    </div>

                    <!-- CONFIRMAR CONTRASEÑA -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Confirmar Contraseña</label>
                        <input type="password" class="form-control" name="ConfirmarContrasenna" required>
                    </div>
                </div>

                <div class="row">
                    <!-- TELÉFONO -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" class="form-control" name="Telefono">
                    </div>

                    <!-- DIRECCIÓN -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Dirección</label>
                        <input type="text" class="form-control" name="Direccion">
                    </div>
                </div>

                <!-- NACIMIENTO -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Fecha de nacimiento</label>
                    <input type="date" class="form-control" name="FechaNacimiento" required>
                </div>

                <!-- BOTÓN REGISTRAR -->
                <div class="text-center mt-4">
                    <button class="btn-register-custom" type="submit" name="btnRegistrarPaciente">
                        Registrar Cliente
                    </button>
                </div>

            </form>

        </div>
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