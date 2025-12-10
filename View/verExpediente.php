<?php
include('layout.php');
include('../Model/baseDatos.php');

$expedienteId = $_GET['ExpedienteId'] ?? null;
if (!$expedienteId)
    die("Expediente no especificado.");

$conn = AbrirBD();

$stmt = $conn->prepare("CALL ObtenerExpedienteCompleto(?)");
$stmt->bind_param("i", $expedienteId);
$stmt->execute();

/* Cada select devuelve un resultset */
$exp = [];
if ($result = $stmt->get_result())
    $exp['expediente'] = $result->fetch_assoc();
$stmt->next_result();
if ($result = $stmt->get_result())
    $exp['antecedente'] = $result->fetch_assoc();
$stmt->next_result();
if ($result = $stmt->get_result())
    $exp['lensometria'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->next_result();
if ($result = $stmt->get_result())
    $exp['examenExterno'] = $result->fetch_assoc();
$stmt->next_result();
if ($result = $stmt->get_result())
    $exp['oftalmoscopia'] = $result->fetch_assoc();
$stmt->next_result();
if ($result = $stmt->get_result())
    $exp['examenFinal'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->next_result();
if ($result = $stmt->get_result())
    $exp['datosAdicionales'] = $result->fetch_assoc();

$stmt->close();
CerrarBD($conn);

function campo($label, $valor)
{
    return "
        <div class='col-md-6'>
            <label class='fw-bold'>$label</label>
            <input type='text' class='form-control' value='" . htmlspecialchars($valor) . "' disabled>
        </div>
    ";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ver Expediente</title>
    <?php IncluirCSS(); ?>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <?php MostrarMenu(); ?>

 <main class="container my-5">
<div class="d-flex justify-content-end mb-4" data-aos="fade-down">
            <a href="javascript:history.back();" class="btn btn-back-custom">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

    <!-- Encabezado principal -->
    <div class="expediente-header d-flex justify-content-between align-items-center flex-wrap">
        <h2 class="m-0 d-flex align-items-center gap-2">
            <i data-lucide="notebook"></i>
            Expediente Clínico
        </h2>


    </div>

    <div class="accordion" id="accordionVer">


        <!-- ===========================
             DATOS GENERALES
        ============================ -->
        <div class="accordion-item expediente-section-card">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#dg">
                    <i data-lucide="file-text" class="me-2"></i>
                    Datos Generales
                </button>
            </h2>

            <div id="dg" class="accordion-collapse collapse show">
                <div class="accordion-body">

                    <h5 class="section-title d-flex align-items-center gap-2">
                        <i data-lucide="info"></i>
                        Información General
                    </h5>

                    <div class="row g-3">
                        <?= campo("Ocupación", $exp['expediente']['Ocupacion'] ?? '') ?>
                        <?= campo("Motivo de Consulta", $exp['expediente']['MotivoConsulta'] ?? '') ?>
                        <?= campo("Usa Lentes", $exp['expediente']['UsaLentes'] ?? '') ?>
                        <?= campo("Último Control", $exp['expediente']['UltimoControl'] ?? '') ?>
                    </div>

                    <h5 class="section-title mt-4 d-flex align-items-center gap-2">
                        <i data-lucide="book-open"></i>
                        Antecedentes
                    </h5>

                    <textarea class="form-control input-modern" rows="2" disabled>
                        <?= $exp['antecedente']['Descripcion'] ?? '' ?>
                    </textarea>

                </div>
            </div>
        </div>


        <!-- ===========================
             LENSOMETRÍA
        ============================ -->
        <div class="accordion-item expediente-section-card">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lens">
                    <i data-lucide="focus" class="me-2"></i>
                    Lensometría
                </button>
            </h2>

            <div id="lens" class="accordion-collapse collapse">
                <div class="accordion-body">

                    <h5 class="section-title d-flex align-items-center gap-2">
                        <i data-lucide="scan"></i>
                        Medición
                    </h5>

                    <table class="table table-bordered text-center table-header-blue shadow-sm">
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
                            <?php foreach ($exp['lensometria'] as $row): ?>
                            <tr>
                                <td><?= $row['Ojo'] ?></td>
                                <td><?= $row['Esfera'] ?></td>
                                <td><?= $row['Cilindro'] ?></td>
                                <td><?= $row['Eje'] ?></td>
                                <td><?= $row['AgudezaVisual'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>


        <!-- ===========================
             EXAMEN EXTERNO
        ============================ -->
        <div class="accordion-item expediente-section-card">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#exext">
                    <i data-lucide="scan-eye" class="me-2"></i>
                    Examen Externo
                </button>
            </h2>

            <div id="exext" class="accordion-collapse collapse">
                <div class="accordion-body">

                    <h5 class="section-title d-flex align-items-center gap-2">
                        <i data-lucide="list"></i>
                        Observaciones
                    </h5>

                    <textarea disabled class="form-control input-modern mb-2">
                        <?= $exp['examenExterno']['orbitaCejas'] ?? '' ?>
                    </textarea>

                    <textarea disabled class="form-control input-modern mb-2">
                        <?= $exp['examenExterno']['parpadosPestanas'] ?? '' ?>
                    </textarea>

                    <textarea disabled class="form-control input-modern mb-2">
                        <?= $exp['examenExterno']['sistemaLagrimal'] ?? '' ?>
                    </textarea>

                </div>
            </div>
        </div>


        <!-- ===========================
             OFTALMOSCOPÍA
        ============================ -->
        <div class="accordion-item expediente-section-card">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#oft">
                    <i data-lucide="eye" class="me-2"></i>
                    Oftalmoscopía
                </button>
            </h2>

            <div id="oft" class="accordion-collapse collapse">
                <div class="accordion-body">

                    <h5 class="section-title">Ojo Derecho (OD)</h5>
                    <textarea disabled class="form-control input-modern mb-3">
                        <?= $exp['oftalmoscopia']['DescripcionOD'] ?? '' ?>
                    </textarea>

                    <h5 class="section-title">Ojo Izquierdo (OI)</h5>
                    <textarea disabled class="form-control input-modern">
                        <?= $exp['oftalmoscopia']['DescripcionOI'] ?? '' ?>
                    </textarea>

                </div>
            </div>
        </div>


        <!-- ===========================
             FÓRMULA FINAL
        ============================ -->
        <div class="accordion-item expediente-section-card">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#final">
                    <i data-lucide="ruler" class="me-2"></i>
                    Fórmula Final y Datos Adicionales
                </button>
            </h2>

            <div id="final" class="accordion-collapse collapse">
                <div class="accordion-body">

                    <h5 class="section-title d-flex align-items-center gap-2">
                        <i data-lucide="pipette"></i>
                        Fórmula Final
                    </h5>

                    <table class="table table-bordered text-center table-header-blue shadow-sm mb-4">
                        <thead>
                            <tr>
                                <th>Ojo</th>
                                <th>Esfera</th>
                                <th>Cilindro</th>
                                <th>Eje</th>
                                <th>DP</th>
                                <th>Prisma</th>
                                <th>Base</th>
                                <th>AV</th>
                                <th>AO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exp['examenFinal'] as $row): ?>
                            <tr>
                                <td><?= $row['Ojo'] ?></td>
                                <td><?= $row['Esfera'] ?></td>
                                <td><?= $row['Cilindro'] ?></td>
                                <td><?= $row['Eje'] ?></td>
                                <td><?= $row['DP'] ?></td>
                                <td><?= $row['Prisma'] ?></td>
                                <td><?= $row['Base'] ?></td>
                                <td><?= $row['AV'] ?></td>
                                <td><?= $row['AO'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>


                    <h5 class="section-title d-flex align-items-center gap-2">
                        <i data-lucide="file-output"></i>
                        Datos Adicionales
                    </h5>

                    <textarea disabled class="form-control input-modern mb-2">
                        <?= $exp['datosAdicionales']['Observaciones'] ?? '' ?>
                    </textarea>

                    <textarea disabled class="form-control input-modern mb-2">
                        <?= $exp['datosAdicionales']['Altura'] ?? '' ?>
                    </textarea>

                    <textarea disabled class="form-control input-modern">
                        <?= $exp['datosAdicionales']['Diagnostico'] ?? '' ?>
                    </textarea>

                </div>
            </div>
        </div>

    </div>

</main>


    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
<script> lucide.createIcons(); </script>
</body>

</html>