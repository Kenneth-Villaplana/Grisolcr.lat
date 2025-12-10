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

    
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'exito'): ?>
        <div class="alert alert-success text-center d-flex align-items-center gap-2 justify-content-center">
            <i data-lucide="check-circle"></i>
            El expediente se ha creado correctamente.
        </div>
    <?php endif; ?>

   >
    <section class="expediente-section-card">
        <h5 class="section-title d-flex align-items-center gap-2">
            <i data-lucide="id-card"></i>
            Información del Paciente
        </h5>

        <div class="row g-3">

            <input type="hidden" name="PacienteId" id="PacienteId" value="<?= $_GET['PacienteId'] ?? '' ?>">

            <div class="col-md-3">
                <label class="form-label">Cédula</label>
                <input type="text" name="cedula" class="form-control input-modern"
                       value="<?= $_GET['cedula'] ?? '' ?>" readonly>
            </div>

            <div class="col-md-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control input-modern"
                       value="<?= $_GET['nombre'] ?? '' ?>" readonly>
            </div>

            <div class="col-md-3">
                <label class="form-label">Primer Apellido</label>
                <input type="text" name="apellido" class="form-control input-modern"
                       value="<?= $_GET['apellido1'] ?? '' ?>" readonly>
            </div>

            <div class="col-md-3">
                <label class="form-label">Segundo Apellido</label>
                <input type="text" name="apellidoDos" class="form-control input-modern"
                       value="<?= $_GET['apellido2'] ?? '' ?>" readonly>
            </div>

        </div>
    </section>


    
    <form action="../Controller/HistorialController.php" method="POST" id="formExpediente">

        <input type="hidden" name="PacienteId" id="PacienteIdHidden"
               value="<?= $_GET['PacienteId'] ?? '' ?>">

        <div class="accordion" id="accordionExpediente">



            
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
                                <input type="text" name="MotivoConsulta" class="form-control input-modern campo-obligatorio">
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
                                <textarea name="Descripcion" rows="2" class="form-control input-modern"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="accordion-item expediente-section-card mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed d-flex gap-2" type="button" 
                            data-bs-toggle="collapse" data-bs-target="#panelLens">
                        <i data-lucide="focus"></i>
                        Lensometría y Agudeza Visual
                    </button>
                </h2>

                <div id="panelLens" class="accordion-collapse collapse">
                    <div class="accordion-body">

                        <h6 class="section-title d-flex align-items-center gap-2">
                            <i data-lucide="scan"></i> Lensometría
                        </h6>

                        <table class="table table-hover text-center shadow-sm table-header-blue mb-4">
                            <thead>
                                <tr>
                                    <th>Ojo</th>
                                    <th>Esfera</th>
                                    <th>Cilindro</th>
                                    <th>Eje</th>
                                    <th>Agudeza Visual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="text-primary">OD</th>
                                    <td><input type="text" name="lens_esfera_od" class="form-control input-modern"></td>
                                    <td><input type="text" name="lens_cil_od" class="form-control input-modern"></td>
                                    <td><input type="text" name="lens_eje_od" class="form-control input-modern"></td>
                                    <td><input type="text" name="lens_av_od" class="form-control input-modern"></td>
                                </tr>

                                <tr>
                                    <th class="text-primary">OI</th>
                                    <td><input type="text" name="lens_esfera_oi" class="form-control input-modern"></td>
                                    <td><input type="text" name="lens_cil_oi" class="form-control input-modern"></td>
                                    <td><input type="text" name="lens_eje_oi" class="form-control input-modern"></td>
                                    <td><input type="text" name="lens_av_oi" class="form-control input-modern"></td>
                                </tr>
                            </tbody>
                        </table>


                        <h6 class="section-title d-flex align-items-center gap-2">
                            <i data-lucide="eye"></i> Agudeza Visual
                        </h6>

                        <table class="table table-hover text-center shadow-sm table-header-blue">
                            <thead>
                                <tr>
                                    <th>Ojo</th>
                                    <th>AV VL SC</th>
                                    <th>PH</th>
                                    <th>AV VP SC</th>
                                    <th>Distancia Optotipo</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <th class="text-primary">OD</th>
                                    <td><input type="text" name="AV_VL_SC_OD" class="form-control input-modern"></td>
                                    <td><input type="text" name="PH_OD" class="form-control input-modern"></td>
                                    <td><input type="text" name="AV_VP_SC_OD" class="form-control input-modern"></td>
                                    <td><input type="text" name="DISTANCIA_OPTOTIPO_OD" class="form-control input-modern"></td>
                                </tr>

                                <tr>
                                    <th class="text-primary">OI</th>
                                    <td><input type="text" name="AV_VL_SC_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="PH_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="AV_VP_SC_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="DISTANCIA_OPTOTIPO_OI" class="form-control input-modern"></td>
                                </tr>

                                <tr>
                                    <th class="text-primary">AO</th>
                                    <td><input type="text" name="AV_VL_SC_AO" class="form-control input-modern"></td>
                                    <td><input type="text" name="PH_AO" class="form-control input-modern"></td>
                                    <td><input type="text" name="AV_VP_SC_AO" class="form-control input-modern"></td>
                                    <td><input type="text" name="DISTANCIA_OPTOTIPO_AO" class="form-control input-modern"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



          
            <div class="accordion-item expediente-section-card mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed d-flex gap-2" type="button" 
                            data-bs-toggle="collapse" data-bs-target="#panelExterno">
                        <i data-lucide="scan-eye"></i> Examen Externo
                    </button>
                </h2>

                <div id="panelExterno" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <textarea name="orbitaCejas" class="form-control input-modern mb-2" placeholder="Órbita / Cejas"></textarea>
                        <textarea name="parpadosPestanas" class="form-control input-modern mb-2" placeholder="Párpados / Pestañas"></textarea>
                        <textarea name="sistemaLagrimal" class="form-control input-modern mb-2" placeholder="Sistema lagrimal"></textarea>
                    </div>
                </div>
            </div>


            <div class="accordion-item expediente-section-card mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed d-flex gap-2" type="button" 
                            data-bs-toggle="collapse" data-bs-target="#panelOfta">
                        <i data-lucide="eye"></i>
                        Oftalmoscopía
                    </button>
                </h2>

                <div id="panelOfta" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <label class="fw-bold">Ojo derecho (OD)</label>
                        <textarea name="DescripcionOD" class="form-control input-modern mb-3"></textarea>

                        <label class="fw-bold">Ojo izquierdo (OI)</label>
                        <textarea name="DescripcionOI" class="form-control input-modern mb-2"></textarea>
                    </div>
                </div>
            </div>


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


                        <h6 class="section-title d-flex align-items-center gap-2">
                            <i data-lucide="pipette"></i> Fórmula Final
                        </h6>

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
                                    <td><input type="text" name="DP_OD" class="form-control input-modern"></td>
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
                                    <td><input type="text" name="DP_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="Prisma_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="Base_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="AV_OI" class="form-control input-modern"></td>
                                    <td><input type="text" name="AO_OI" class="form-control input-modern"></td>
                                </tr>
                            </tbody>
                        </table>



                        <h6 class="section-title d-flex align-items-center gap-2">
                            <i data-lucide="file-output"></i> Datos Adicionales
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <textarea name="Observaciones" class="form-control input-modern"
                                    placeholder="Uso, material, color, segmento, etc."></textarea>
                            </div>

                            <div class="col-md-6">
                                <textarea name="Altura" class="form-control input-modern"
                                    placeholder="Altura de montaje u otros datos"></textarea>
                            </div>

                            <div class="col-12">
                                <textarea name="Diagnostico" class="form-control input-modern campo-obligatorio"
                                    placeholder="Diagnóstico final / recomendaciones"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>



        <div class="text-center mt-4">
            <button type="submit" class="btn-save-expediente px-5 d-flex align-items-center gap-2 mx-auto">
                <i data-lucide="save"></i>
                Guardar expediente
            </button>
        </div>
    </form>
</main>


<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

<script>
window.addEventListener('DOMContentLoaded', () => {
    const paciente = JSON.parse(sessionStorage.getItem('paciente') || 'null');
    if (!paciente) return;

    const campos = {
        cedula: 'cedula',
        nombre: 'nombre',
        apellido: 'apellido',
        apellidoDos: 'apellidoDos',
        id: 'PacienteIdHidden'
    };

    if (document.querySelector(`input[name="${campos.cedula}"]`))
        document.querySelector(`input[name="${campos.cedula}"]`).value = paciente.cedula ?? '';

    if (document.querySelector(`input[name="${campos.nombre}"]`))
        document.querySelector(`input[name="${campos.nombre}"]`).value = paciente.nombre ?? '';

    if (document.querySelector(`input[name="${campos.apellido}"]`))
        document.querySelector(`input[name="${campos.apellido}"]`).value = paciente.apellido ?? '';

    if (document.querySelector(`input[name="${campos.apellidoDos}"]`))
        document.querySelector(`input[name="${campos.apellidoDos}"]`).value = paciente.apellidoDos ?? '';

    const hidden = document.getElementById(campos.id);
    if (hidden) hidden.value = paciente.PacienteId ?? '';
});


document.getElementById('formExpediente').addEventListener('submit', function (e) {
    const obligatorios = document.querySelectorAll('.campo-obligatorio');
    let primeroInvalido = null;

    obligatorios.forEach(campo => {
        if (campo.value.trim() === '') {
            campo.classList.add('is-invalid');
            if (!primeroInvalido) primeroInvalido = campo;
        } else {
            campo.classList.remove('is-invalid');
        }
    });

    if (primeroInvalido) {
        e.preventDefault();
        primeroInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
        primeroInvalido.focus();
    }
});
</script>
<script> lucide.createIcons(); </script>
</body>
</html>