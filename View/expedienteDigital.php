<?php
include('layout.php');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historia Clínica de Optometría</title>
    <?php IncluirCSS(); ?>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
<?php MostrarMenu(); ?>

<main class="container my-5">

    <div class="expediente-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h2 class="mb-0 d-flex align-items-center gap-2">
            <i data-lucide="glasses"></i>
            Historia Clínica de Optometría
        </h2>

        <a href="historialExpedientes.php" class="btn btn-back-custom d-flex align-items-center gap-2">
            <i data-lucide="arrow-left"></i> Volver
        </a>
    </div>

    <form action="../Controller/HistorialController.php" method="POST" id="formExpediente">

        <input type="hidden" name="PacienteId" id="PacienteIdHidden"
               value="<?= $_GET['PacienteId'] ?? '' ?>">

        <div class="accordion" id="accordionExpediente">

            <!-- Datos Generales -->
            <div class="accordion-item expediente-section-card mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button d-flex gap-2" type="button"
                            data-bs-toggle="collapse" data-bs-target="#panelDatos">
                        <i data-lucide="file-text"></i>
                        Datos Generales y Antecedentes
                    </button>
                </h2>

                <div id="panelDatos" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Ocupación</label>
                                <input type="text" name="Ocupacion" class="form-control input-modern">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Motivo de consulta</label>
                                <input type="text" name="MotivoConsulta"
                                       class="form-control input-modern campo-obligatorio">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Usa lentes</label>
                                <select name="usaLentes" class="form-select input-modern">
                                    <option value="Sí">Sí</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Último control visual</label>
                                <input type="date" name="UltimoControl" class="form-control input-modern">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Antecedentes generales</label>
                                <textarea name="Descripcion" rows="2"
                                          class="form-control input-modern"></textarea>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Fórmula Final -->
            <div class="accordion-item expediente-section-card">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed d-flex gap-2" type="button"
                            data-bs-toggle="collapse" data-bs-target="#panelFinal">
                        <i data-lucide="ruler"></i>
                        Fórmula Final y Datos Adicionales
                    </button>
                </h2>

                <div id="panelFinal" class="accordion-collapse collapse">
                    <div class="accordion-body">

                        <table class="table table-hover text-center shadow-sm table-header-blue mb-4">
                            <thead>
                                <tr>
                                    <th>Ojo</th>
                                    <th>Esfera</th>
                                    <th>Cilindro</th>
                                    <th>Eje</th>
                                    <th>DP</th>
                                    <th>Prisma</th>
                                    <th>Base</th>
                                    <th>A.V</th>
                                    <th>A.O</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <th class="text-primary">OD</th>
                                    <td><input type="text" name="Esfera_OD" class="form-control input-modern"></td>
                                    <td><input type="text" name="Cilindro_OD" class="form-control input-modern"></td>
                                    <td><input type="text" name="Eje_OD" class="form-control input-modern"></td>
                                    <td>
                                        <input type="text"
                                               name="DP_OD"
                                               class="form-control input-modern campo-obligatorio campo-decimal">
                                    </td>
                                    <td><input type="text" name="Prisma_OD" class="form-control input-modern"></td>
                                    <td><input type="text" name="Base_OD" class="form-control input-modern"></td>
                                    <td><input type="text" name="AV_OD" class="form-control input-modern"></td>
                                    <td><input type="text" name="AO_OD" class="form-control input-modern"></td>
                                </tr>

                                <tr>
                                    <th class="text-primary">OI</th>
                                    <td><input type="text" name="Esfera_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="Cilindro_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="Eje_OI" class="form-control input-modern"></td>
                                    <td>
                                        <input type="text"
                                               name="DP_OI"
                                               class="form-control input-modern campo-obligatorio campo-decimal">
                                    </td>
                                    <td><input type="text" name="Prisma_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="Base_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="AV_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="AO_OI" class="form-control input-modern"></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row g-3">
                            <div class="col-12">
                                <textarea name="Diagnostico"
                                          class="form-control input-modern campo-obligatorio"
                                          placeholder="Diagnóstico final / recomendaciones"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="text-center mt-4">
            <button type="submit"
                    class="btn-save-expediente px-5 d-flex align-items-center gap-2 mx-auto">
                <i data-lucide="save"></i>
                Guardar expediente
            </button>
        </div>
    </form>
</main>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

<!-- VALIDACIÓN -->
<script>
document.getElementById('formExpediente').addEventListener('submit', function (e) {

    let invalido = null;

    // Obligatorios
    document.querySelectorAll('.campo-obligatorio').forEach(campo => {
        if (campo.value.trim() === '') {
            campo.classList.add('is-invalid');
            if (!invalido) invalido = campo;
        } else {
            campo.classList.remove('is-invalid');
        }
    });

    // Validación decimal (solo DP)
    document.querySelectorAll('.campo-decimal').forEach(campo => {
        const valor = campo.value.replace(',', '.');
        if (valor !== '' && isNaN(valor)) {
            campo.classList.add('is-invalid');
            if (!invalido) invalido = campo;
        }
    });

    if (invalido) {
        e.preventDefault();
        invalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
        invalido.focus();
    }
});
</script>

<script>lucide.createIcons();</script>
</body>
</html>