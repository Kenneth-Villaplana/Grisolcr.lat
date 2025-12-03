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

        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary mb-0">
                👁️ Historia Clínica de Optometría
            </h2>
            <a href="historialExpedientes.php" class="btn btn-outline-secondary">
                ⬅ Volver
            </a>
        </div>

       
        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'exito'): ?>
            <div class="alert alert-success text-center">
                 El expediente se ha creado correctamente.
            </div>
        <?php endif; ?>

        <!--Información Paciente lo trae por medio del ID-->
        <section class="mb-4 p-3 border rounded-3 shadow-sm bg-white">
            <h5 class="text-primary mb-3">🧑 Información del Paciente</h5>
            <div class="row g-3">
                <!-- ID oculto -->
                <input type="hidden" name="PacienteId" id="PacienteId" value="<?= $_GET['PacienteId'] ?? '' ?>">

                <div class="col-md-3">
                    <label class="form-label">Cédula</label>
                    <input type="text" name="cedula" class="form-control" value="<?= $_GET['cedula'] ?? '' ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= $_GET['nombre'] ?? '' ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Primer Apellido</label>
                    <input type="text" name="apellido" class="form-control" value="<?= $_GET['apellido1'] ?? '' ?>"
                        readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Segundo Apellido</label>
                    <input type="text" name="apellidoDos" class="form-control" value="<?= $_GET['apellido2'] ?? '' ?>"
                        readonly>
                </div>
            </div>
        </section>

        <!--Crear expediente-->
        <form action="../Controller/HistorialController.php" method="POST" id="formExpediente">

            <!-- Le pasamos también el PacienteId dentro del form -->
            <input type="hidden" name="PacienteId" id="PacienteIdHidden" value="<?= $_GET['PacienteId'] ?? '' ?>">

            <div class="accordion" id="accordionExpediente">

                <!--datos generales y antecedentes-->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingDatos">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#panelDatos" aria-expanded="true" aria-controls="panelDatos">
                            🧩 Datos Generales y Antecedentes
                        </button>
                    </h2>
                    <div id="panelDatos" class="accordion-collapse collapse show" aria-labelledby="headingDatos"
                        data-bs-parent="#accordionExpediente">
                        <div class="accordion-body">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Ocupación</label>
                                    <input type="text" name="Ocupacion" class="form-control"
                                        placeholder="Ej: Estudiante, Contador, Ama de casa...">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Motivo de consulta</label>
                                    <input type="text" name="MotivoConsulta" class="form-control"
                                        placeholder="Ej: Dolores de cabeza, visión borrosa...">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Usa lentes</label>
                                    <select name="usaLentes" class="form-select">
                                        <option value="Sí">Sí</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Último control visual</label>
                                    <input type="date" name="UltimoControl" class="form-control">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Antecedentes generales</label>
                                    <textarea name="Descripcion" rows="2" class="form-control"
                                        placeholder="Antecedentes médicos oculares y generales relevantes"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!--lensometria y agudeza-->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingLens">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#panelLens" aria-expanded="false" aria-controls="panelLens">
                            👁️ Lensometría y Agudeza Visual
                        </button>
                    </h2>
                    <div id="panelLens" class="accordion-collapse collapse" aria-labelledby="headingLens"
                        data-bs-parent="#accordionExpediente">
                        <div class="accordion-body">

                            <!--lensometria-->
                            <h6 class="text-primary mb-3">👁️ Lensometría</h6>
                            <table class="table table-hover text-center align-middle shadow-sm table-header-blue">
                                <thead class="table-primary">
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
                                        <td>
                                            <input type="text" name="lens_esfera_od" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" name="lens_cil_od" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" name="lens_eje_od" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" name="lens_av_od" class="form-control">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-primary">OI</th>
                                        <td>
                                            <input type="text" name="lens_esfera_oi" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" name="lens_cil_oi" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" name="lens_eje_oi" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" name="lens_av_oi" class="form-control">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!--agudeza visual-->
                            <h6 class="text-primary mt-4 mb-3">👁️ Agudeza Visual</h6>
                            <table class="table table-hover text-center align-middle shadow-sm table-header-blue">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Ojo</th>
                                        <th>AV VL SC</th>
                                        <th>PH</th>
                                        <th>AV VP SC</th>
                                        <th>DISTANCIA OPTOTIPO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="text-primary">OD</th>
                                        <td><input type="text" name="AV_VL_SC_OD" class="form-control"></td>
                                        <td><input type="text" name="PH_OD" class="form-control"></td>
                                        <td><input type="text" name="AV_VP_SC_OD" class="form-control"></td>
                                        <td><input type="text" name="DISTANCIA_OPTOTIPO_OD" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-primary">OI</th>
                                        <td><input type="text" name="AV_VL_SC_OI" class="form-control"></td>
                                        <td><input type="text" name="PH_OI" class="form-control"></td>
                                        <td><input type="text" name="AV_VP_SC_OI" class="form-control"></td>
                                        <td><input type="text" name="DISTANCIA_OPTOTIPO_OI" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-primary">AO</th>
                                        <td><input type="text" name="AV_VL_SC_AO" class="form-control"></td>
                                        <td><input type="text" name="PH_AO" class="form-control"></td>
                                        <td><input type="text" name="AV_VP_SC_AO" class="form-control"></td>
                                        <td><input type="text" name="DISTANCIA_OPTOTIPO_AO" class="form-control"></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <!--examen externo-->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingExterno">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#panelExterno" aria-expanded="false" aria-controls="panelExterno">
                            🔬 Examen Externo
                        </button>
                    </h2>
                    <div id="panelExterno" class="accordion-collapse collapse" aria-labelledby="headingExterno"
                        data-bs-parent="#accordionExpediente">
                        <div class="accordion-body">
                            <textarea name="orbitaCejas" class="form-control mb-2"
                                placeholder="Órbita / Cejas"></textarea>
                            <textarea name="parpadosPestanas" class="form-control mb-2"
                                placeholder="Párpados / Pestañas"></textarea>
                            <textarea name="sistemaLagrimal" class="form-control mb-2"
                                placeholder="Sistema lagrimal"></textarea>
                        </div>
                    </div>
                </div>

                <!--oftalmoscopia-->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOfta">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#panelOfta" aria-expanded="false" aria-controls="panelOfta">
                            🩻 Oftalmoscopía
                        </button>
                    </h2>
                    <div id="panelOfta" class="accordion-collapse collapse" aria-labelledby="headingOfta"
                        data-bs-parent="#accordionExpediente">
                        <div class="accordion-body">
                            <label class="fw-bold">Ojo derecho (OD)</label>
                            <textarea name="DescripcionOD" class="form-control mb-2"
                                placeholder="Descripción del fondo de ojo derecho"></textarea>

                            <label class="fw-bold">Ojo izquierdo (OI)</label>
                            <textarea name="DescripcionOI" class="form-control mb-2"
                                placeholder="Descripción del fondo de ojo izquierdo"></textarea>
                        </div>
                    </div>
                </div>

                <!--formula final y datos adicionales-->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFinal">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#panelFinal" aria-expanded="false" aria-controls="panelFinal">
                            👓 Fórmula Final y Datos Adicionales
                        </button>
                    </h2>
                    <div id="panelFinal" class="accordion-collapse collapse" aria-labelledby="headingFinal"
                        data-bs-parent="#accordionExpediente">
                        <div class="accordion-body">

                            <!--formula final-->
                            <h6 class="text-primary mb-3">👁️ Fórmula Final</h6>
                            <table class="table table-hover text-center align-middle shadow-sm table-header-blue">
                                <thead class="table-primary">
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
                                        <td><input type="text" name="Esfera_OD" class="form-control"></td>
                                        <td><input type="text" name="Cilindro_OD" class="form-control"></td>
                                        <td><input type="text" name="Eje_OD" class="form-control"></td>
                                        <td><input type="text" name="DP_OD" class="form-control"></td>
                                        <td><input type="text" name="Prisma_OD" class="form-control"></td>
                                        <td><input type="text" name="Base_OD" class="form-control"></td>
                                        <td><input type="text" name="AV_OD" class="form-control"></td>
                                        <td><input type="text" name="AO_OD" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-primary">OI</th>
                                        <td><input type="text" name="Esfera_OI" class="form-control"></td>
                                        <td><input type="text" name="Cilindro_OI" class="form-control"></td>
                                        <td><input type="text" name="Eje_OI" class="form-control"></td>
                                        <td><input type="text" name="DP_OI" class="form-control"></td>
                                        <td><input type="text" name="Prisma_OI" class="form-control"></td>
                                        <td><input type="text" name="Base_OI" class="form-control"></td>
                                        <td><input type="text" name="AV_OI" class="form-control"></td>
                                        <td><input type="text" name="AO_OI" class="form-control"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <!--datos adicionales-->
                            <h6 class="text-primary mt-4 mb-3">🧾 Datos Adicionales</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <textarea name="Observaciones" class="form-control"
                                        placeholder="Uso, color, material, tipo de lente, segmento, disposición"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <textarea name="Altura" class="form-control"
                                        placeholder="Altura de montaje u otros datos técnicos"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <textarea name="Diagnostico" class="form-control"
                                        placeholder="Diagnóstico final / recomendaciones"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-outline-primary px-4">
                     Guardar expediente
                </button>
            </div>

        </form>

    </main>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>

    <script>
        // Cargar datos de paciente desde sessionStorage si viene del buscador
        window.addEventListener('DOMContentLoaded', () => {
            const paciente = JSON.parse(sessionStorage.getItem('paciente') || 'null');
            if (!paciente) return;

            const cedula = document.querySelector('input[name="cedula"]');
            const nombre = document.querySelector('input[name="nombre"]');
            const apellido1 = document.querySelector('input[name="apellido"]');
            const apellido2 = document.querySelector('input[name="apellidoDos"]');
            const idOculto = document.getElementById('PacienteIdHidden');

            if (cedula) cedula.value = paciente.cedula ?? '';
            if (nombre) nombre.value = paciente.nombre ?? '';
            if (apellido1) apellido1.value = paciente.apellido ?? '';
            if (apellido2) apellido2.value = paciente.apellidoDos ?? '';
            if (idOculto) idOculto.value = paciente.PacienteId ?? '';
        });

        // Validación al enviar
        document.getElementById('formExpediente').addEventListener('submit', function (e) {
            let errores = [];

            const motivo = document.querySelector('input[name="MotivoConsulta"]');
            const diagnostico = document.querySelector('textarea[name="Diagnostico"]');

            if (motivo && motivo.value.trim() === '') {
                errores.push('Motivo de consulta');
                motivo.classList.add('is-invalid');
            } else if (motivo) {
                motivo.classList.remove('is-invalid');
            }

            if (diagnostico && diagnostico.value.trim() === '') {
                errores.push('Diagnóstico');
                diagnostico.classList.add('is-invalid');
            } else if (diagnostico) {
                diagnostico.classList.remove('is-invalid');
            }

            if (errores.length > 0) {
                e.preventDefault();
                alert('Por favor complete los siguientes campos obligatorios:\n- ' + errores.join('\n- '));
            }
        });
    </script>
</body>
</html>