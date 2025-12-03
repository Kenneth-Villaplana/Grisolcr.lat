<?php
include_once('../Model/baseDatos.php');
$conn = AbrirBD();

function limpiarValorOptico($valor) {
    if ($valor === "" || $valor === null) return null;

    // Reemplaza coma por punto por si acaso
    $valor = str_replace(",", ".", $valor);

    // Verifica número válido
    return is_numeric($valor) ? floatval($valor) : null;
}

try {
    // recibe datos del formulario

    $pacienteId = $_POST['PacienteId'] ?? null;

    $ocupacion = $_POST['Ocupacion'] ?? null;
    $motivoConsulta = $_POST['MotivoConsulta'] ?? null;
    $usaLentes = $_POST['usaLentes'] ?? 'No';
    $ultimoControl = $_POST['UltimoControl'] ?? null;

    $antecedente = $_POST['Descripcion'] ?? null;

    $orbitaCejas = $_POST['orbitaCejas'] ?? null;
    $parpadosPestanas = $_POST['parpadosPestanas'] ?? null;
    $sistemaLagrimal = $_POST['sistemaLagrimal'] ?? null;

    $descripcionOD = $_POST['DescripcionOD'] ?? null;
    $descripcionOI = $_POST['DescripcionOI'] ?? null;

    // Ojo Derecho
    $esferaOD = $_POST['Esfera_OD'] ?? null;
    $cilindroOD = $_POST['Cilindro_OD'] ?? null;
    $ejeOD = $_POST['Eje_OD'] ?? null;
    $dpOD = $_POST['DP_OD'] ?? null;
    $prismaOD = $_POST['Prisma_OD'] ?? null;
    $baseOD = $_POST['Base_OD'] ?? null;
    $avOD = $_POST['AV_OD'] ?? null;
    $aoOD = $_POST['AO_OD'] ?? null;

    // Ojo Izquierdo
    $esferaOI = $_POST['Esfera_OI'] ?? null;
    $cilindroOI = $_POST['Cilindro_OI'] ?? null;
    $ejeOI = $_POST['Eje_OI'] ?? null;
    $dpOI = $_POST['DP_OI'] ?? null;
    $prismaOI = $_POST['Prisma_OI'] ?? null;
    $baseOI = $_POST['Base_OI'] ?? null;
    $avOI = $_POST['AV_OI'] ?? null;
    $aoOI = $_POST['AO_OI'] ?? null;

    $observaciones = $_POST['Observaciones'] ?? null;
    $altura = $_POST['Altura'] ?? null;
    $diagnostico = $_POST['Diagnostico'] ?? null;

    //Crear expediente
    $stmt = $conn->prepare("CALL CrearExpedienteCompleto(?,?,?,?,?)");
    $stmt->bind_param("issss", $pacienteId, $ocupacion, $motivoConsulta, $usaLentes, $ultimoControl);
    $stmt->execute();

    $result = $stmt->get_result();
    $nuevo = $result->fetch_assoc();
    $nuevoId = $nuevo['IdExpediente'] ?? null;
    $stmt->close();

    if (!$nuevoId) {
        throw new Exception("No se pudo obtener el ID del nuevo expediente.");
    }

    // Antecedente
    $stmt = $conn->prepare("CALL InsertarAntecedente(?,?)");
    $stmt->bind_param("is", $nuevoId, $antecedente);
    $stmt->execute();
    $stmt->close();

    // Lensometría OD
    $stmt = $conn->prepare("CALL InsertarLensometria(?,?,?,?,?,?)");
    $ojo = 'Derecho';
    $stmt->bind_param(
        "isddss",
        $nuevoId,
        $ojo,
        $_POST['lens_esfera_od'],
        $_POST['lens_cil_od'],
        $_POST['lens_eje_od'],
        $_POST['lens_av_od']
    );
    $stmt->execute();
    $stmt->close();

    // Lensometría OI
    $stmt = $conn->prepare("CALL InsertarLensometria(?,?,?,?,?,?)");
    $ojo = 'Izquierdo';
    $stmt->bind_param(
        "isddss",
        $nuevoId,
        $ojo,
        $_POST['lens_esfera_oi'],
        $_POST['lens_cil_oi'],
        $_POST['lens_eje_oi'],
        $_POST['lens_av_oi']
    );
    $stmt->execute();
    $stmt->close();

    // Examen externo
    $stmt = $conn->prepare("CALL InsertarExamenExterno(?,?,?,?)");
    $stmt->bind_param("isss", $nuevoId, $orbitaCejas, $parpadosPestanas, $sistemaLagrimal);
    $stmt->execute();
    $stmt->close();

    // Oftalmoscopía
    $stmt = $conn->prepare("CALL InsertarOftalmoscopia(?,?,?)");
    $stmt->bind_param("iss", $nuevoId, $descripcionOD, $descripcionOI);
    $stmt->execute();
    $stmt->close();

    // Examen Final OD
    $stmt = $conn->prepare("CALL InsertarExamenFinal(?,?,?,?,?,?,?,?,?,?,?)");
    $ojo = 'Derecho';
    $stmt->bind_param(
        "isddsdssddd",
        $nuevoId,
        $ojo,
        $esferaOD,
        $cilindroOD,
        $ejeOD,
        $dpOD,      
        $dpOD,     
        $prismaOD,
        $baseOD,
        $avOD,
        $aoOD
    );
    $stmt->execute();
    $stmt->close();

    // Examen Final OI
    $stmt = $conn->prepare("CALL InsertarExamenFinal(?,?,?,?,?,?,?,?,?,?,?)");
    $ojo = 'Izquierdo';
    $stmt->bind_param(
        "isddsdssddd",
        $nuevoId,
        $ojo,
        $esferaOI,
        $cilindroOI,
        $ejeOI,
        $dpOI,
        $dpOI,
        $prismaOI,
        $baseOI,
        $avOI,
        $aoOI
    );
    $stmt->execute();
    $stmt->close();

    // Datos adicionales
    $stmt = $conn->prepare("CALL InsertarDatosAdicionales(?,?,?,?)");
    $stmt->bind_param("isss", $nuevoId, $observaciones, $altura, $diagnostico);
    $stmt->execute();
    $stmt->close();

    
    CerrarBD($conn);
    header("Location: ../Controller/historialExpedientePacienteController.php?PacienteId=$pacienteId");
    exit;

} catch (Exception $e) {
    if (isset($stmt))
        $stmt->close();
    CerrarBD($conn);
    die("<h3 style='color:red;'>❌ Error al ejecutar SP:</h3> " . $e->getMessage());
}
