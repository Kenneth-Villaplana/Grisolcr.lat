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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary mb-0">🩺 Expediente Clínico</h2>
            <a href="javascript:history.back();" class="btn btn-outline-secondary">⬅ Volver</a>
        </div>

        <div class="accordion" id="accordionVer">

            <!--DATOS GENERALES-->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#dg">
                        🧩 Datos Generales
                    </button>
                </h2>
                <div id="dg" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <?= campo("Ocupación", $exp['expediente']['Ocupacion'] ?? '') ?>
                            <?= campo("Motivo de Consulta", $exp['expediente']['MotivoConsulta'] ?? '') ?>
                            <?= campo("Usa Lentes", $exp['expediente']['UsaLentes'] ?? '') ?>
                            <?= campo("Último Control", $exp['expediente']['UltimoControl'] ?? '') ?>
                        </div>

                        <label class="fw-bold mt-3">Antecedentes</label>
                        <textarea class="form-control" rows="2"
                            disabled><?= $exp['antecedente']['Descripcion'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

            <!--LENSOMETRÍA-->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#lens">
                        👁️ Lensometría
                    </button>
                </h2>
                <div id="lens" class="accordion-collapse collapse">
                    <div class="accordion-body">

                        <table class="table table-bordered text-center">
                            <thead class="table-light">
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

            <!--EXAMEN EXTERNO-->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#exext">
                        🔬 Examen Externo
                    </button>
                </h2>
                <div id="exext" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <textarea disabled
                            class="form-control mb-2"><?= $exp['examenExterno']['orbitaCejas'] ?? '' ?></textarea>
                        <textarea disabled
                            class="form-control mb-2"><?= $exp['examenExterno']['parpadosPestanas'] ?? '' ?></textarea>
                        <textarea disabled
                            class="form-control mb-2"><?= $exp['examenExterno']['sistemaLagrimal'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

            <!--OFTALMOSCOPÍA-->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#oft">
                        🩻 Oftalmoscopía
                    </button>
                </h2>
                <div id="oft" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <label class="fw-bold">Ojo Derecho</label>
                        <textarea disabled
                            class="form-control mb-2"><?= $exp['oftalmoscopia']['DescripcionOD'] ?? '' ?></textarea>

                        <label class="fw-bold">Ojo Izquierdo</label>
                        <textarea disabled
                            class="form-control mb-2"><?= $exp['oftalmoscopia']['DescripcionOI'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

           
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#final">
                        👓 Fórmula Final y Datos Adicionales
                    </button>
                </h2>
                <div id="final" class="accordion-collapse collapse">
                    <div class="accordion-body">

                        <table class="table table-bordered text-center mb-4">
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

                        <label class="fw-bold">Observaciones</label>
                        <textarea disabled
                            class="form-control mb-2"><?= $exp['datosAdicionales']['Observaciones'] ?? '' ?></textarea>

                        <label class="fw-bold">Altura</label>
                        <textarea disabled
                            class="form-control mb-2"><?= $exp['datosAdicionales']['Altura'] ?? '' ?></textarea>

                        <label class="fw-bold">Diagnóstico</label>
                        <textarea disabled
                            class="form-control mb-2"><?= $exp['datosAdicionales']['Diagnostico'] ?? '' ?></textarea>

                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>

</body>

</html>