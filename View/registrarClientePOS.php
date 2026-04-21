<?php 

require_once __DIR__ . '/layout.php';
include_once __DIR__ . '/../Controller/loginController.php';

$origen = $_GET['origen'] ?? 'POS';
$cedulaPrefill = $_GET['cedula'] ?? '';
$redirect = $_GET['redirect'] ?? '';
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
        <?php if ($origen === 'EXPEDIENTES'): ?>
            <a href="historialExpedientes.php" class="btn btn-back-custom">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        <?php else: ?>
            <a href="puntoVenta.php" class="btn btn-back-custom">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        <?php endif; ?>
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

                <input type="hidden" name="origen" value="<?= $origen ?>">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect ?: '') ?>">

                <!-- CÉDULA -->
                 <div class="mb-3">
                    <label class="form-label fw-semibold">Cédula</label>
                    <input type="text" 
                        class="form-control"
                        id="Cedula"
                        name="Cedula"
                        value="<?= htmlspecialchars($cedulaPrefill) ?>"
                        placeholder="Cédula"
                        required
                        readonly>
                </div>

                <div class="row">
                    <!-- NOMBRE -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" class="form-control" id="Nombre" name="Nombre" placeholder="Nombre" readonly required>
                    </div>

                    <!-- APELLIDO 1 -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Primer Apellido</label>
                        <input type="text" class="form-control" id="Apellido" name="Apellido" placeholder="Primer Apellido" readonly required>
                    </div>
                </div>

                <div class="row">
                    <!-- APELLIDO 2 -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Segundo Apellido</label>
                        <input type="text" class="form-control" id="ApellidoDos" name="ApellidoDos" placeholder="Segundo Apellido" readonly required>
                    </div>

                    <!-- EMAIL -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Correo Electrónico</label>
                        <input type="email" class="form-control" name="CorreoElectronico" placeholder="Correo electrónico" required>
                    </div>
                </div>

                <div class="row">
                    <!-- CONTRASEÑA -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Contraseña</label>
                        <input type="password" class="form-control" name="Contrasenna" placeholder="Contraseña" required>
                    </div>

                    <!-- CONFIRMAR CONTRASEÑA -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Confirmar Contraseña</label>
                        <input type="password" class="form-control" name="ConfirmarContrasenna" placeholder="Confirmar contraseña" required>
                    </div>
                </div>

                <div class="row">
                    <!-- TELÉFONO -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" class="form-control" name="Telefono" placeholder="Teléfono">
                    </div>

                    <!-- DIRECCIÓN -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Dirección</label>
                        <input type="text" class="form-control" name="Direccion" placeholder="Dirección">
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

    const cedulaInput = document.getElementById("Cedula");
    const form = document.querySelector("form");

    if (!cedulaInput) return;

//formatear la cedula
    const formatearCedula = () => {
        let valor = cedulaInput.value.replace(/\D/g, '');

        if (valor.length > 9) valor = valor.substring(0, 9);

        let f = '';
        if (valor.length > 0) f = valor.substring(0,1);
        if (valor.length >= 2) f += '-' + valor.substring(1,5);
        if (valor.length >= 6) f += '-' + valor.substring(5,9);

        cedulaInput.value = f;
    };

  formatearCedula();


const ejecutarConsulta = () => {

    const ced = cedulaInput.value.replace(/\D/g, '');

    if (ced.length >= 9 && typeof ConsultarNombre === "function") {
        ConsultarNombre();
        return true;
    }

        return false;
    };


    let intentos = 0;

    const intervalo = setInterval(() => {

        if (ejecutarConsulta() || intentos > 15) {
            clearInterval(intervalo);
        }

        intentos++;

    }, 120);

  //limpiar
    if (form) {
        form.addEventListener("submit", function () {
            cedulaInput.value = cedulaInput.value.replace(/\D/g, '');
        });
    }

});
</script>
</body>
</html>