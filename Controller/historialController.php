
<?php

include_once __DIR__ . '/../Model/baseDatos.php';
$conn = AbrirBD();

function limpiarValorOptico($valor) {
    if ($valor === "" || $valor === null) return null;

    $valor = str_replace(",", ".", $valor);
    return is_numeric($valor) ? floatval($valor) : null;
}

try {
    // recibe datos del formulario
    $pacienteId = $_POST['PacienteId'] ?? null;

    $ocupacion = $_POST['ocupacion'] ?? null;
    $motivoConsulta = $_POST['motivoConsulta'] ?? null;
    $usaLentes = $_POST['usaLentes'] ?? 'No';
    $ultimoControl = $_POST['UltimoControl'] ?? null;

    $antecedente = $_POST['antecedentes'] ?? null;

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
    $addOD = $_POST['ADD_OD'] ?? null;

    // Ojo Izquierdo
    $esferaOI = $_POST['Esfera_OI'] ?? null;
    $cilindroOI = $_POST['Cilindro_OI'] ?? null;
    $ejeOI = $_POST['Eje_OI'] ?? null;
    $dpOI = $_POST['DP_OI'] ?? null;
    $prismaOI = $_POST['Prisma_OI'] ?? null;
    $baseOI = $_POST['Base_OI'] ?? null;
    $avOI = $_POST['AV_OI'] ?? null;
    $addOI = $_POST['ADD_OI'] ?? null;

    $observaciones = $_POST['Observaciones'] ?? null;
    $altura = $_POST['Altura'] ?? null;
    $diagnostico = $_POST['Diagnostico'] ?? null;

    
    // Crear expediente
   
    $stmt = $conn->prepare("CALL CrearExpedienteCompleto(?,?,?,?,?)");
    $stmt->bind_param("issss", $pacienteId, $ocupacion, $motivoConsulta, $usaLentes, $ultimoControl);
    $stmt->execute();

    $result = $stmt->get_result();
    $nuevo = $result->fetch_assoc();
    $nuevoId = $nuevo['IdExpediente'] ?? null;

    $stmt->close();
    while ($conn->more_results() && $conn->next_result()) {}

    if (!$nuevoId) {
        throw new Exception("No se pudo obtener el ID del nuevo expediente.");
    }

    
    // Antecedente
  
    $stmt = $conn->prepare("CALL InsertarAntecedente(?,?)");
    $stmt->bind_param("is", $nuevoId, $antecedente);
    $stmt->execute();
    $stmt->close();
    while ($conn->more_results() && $conn->next_result()) {}

   
    // Lensometría OD
  
    $stmt = $conn->prepare("CALL InsertarLensometria(?,?,?,?,?,?)");
    $ojo = 'Derecho';
    $stmt->bind_param("isddss", $nuevoId, $ojo,
        $_POST['lens_esfera_od'],
        $_POST['lens_cil_od'],
        $_POST['lens_eje_od'],
        $_POST['lens_av_od']
    );
    $stmt->execute();
    $stmt->close();
    while ($conn->more_results() && $conn->next_result()) {}

   
    // Lensometría OI
   
    $stmt = $conn->prepare("CALL InsertarLensometria(?,?,?,?,?,?)");
    $ojo = 'Izquierdo';
    $stmt->bind_param("isddss", $nuevoId, $ojo,
        $_POST['lens_esfera_oi'],
        $_POST['lens_cil_oi'],
        $_POST['lens_eje_oi'],
        $_POST['lens_av_oi']
    );
    $stmt->execute();
    $stmt->close();
    while ($conn->more_results() && $conn->next_result()) {}

   
    // Examen externo
    
    $stmt = $conn->prepare("CALL InsertarExamenExterno(?,?,?,?)");
    $stmt->bind_param("isss", $nuevoId, $orbitaCejas, $parpadosPestanas, $sistemaLagrimal);
    $stmt->execute();
    $stmt->close();
    while ($conn->more_results() && $conn->next_result()) {}

  
    // Oftalmoscopía
   
    $stmt = $conn->prepare("CALL InsertarOftalmoscopia(?,?,?)");
    $stmt->bind_param("iss", $nuevoId, $descripcionOD, $descripcionOI);
    $stmt->execute();
    $stmt->close();
    while ($conn->more_results() && $conn->next_result()) {}


    // Examen Final OD
  
    $stmt = $conn->prepare("CALL InsertarExamenFinal(?,?,?,?,?,?,?,?,?,?)");
    $ojo = 'Derecho';
    $stmt->bind_param("isddsddsds",
        $nuevoId, $ojo,
        $esferaOD, $cilindroOD, $ejeOD, $dpOD,
        $prismaOD, $baseOD, $avOD, $addOD
    );
    $stmt->execute();
    $stmt->close();
    while ($conn->more_results() && $conn->next_result()) {}

  
    // Examen Final OI
   
    $stmt = $conn->prepare("CALL InsertarExamenFinal(?,?,?,?,?,?,?,?,?,?)");
    $ojo = 'Izquierdo';
    $stmt->bind_param("isddsddsds",
        $nuevoId, $ojo,
        $esferaOI, $cilindroOI, $ejeOI, $dpOI,
        $prismaOI, $baseOI, $avOI, $addOI
    );
    $stmt->execute();
    $stmt->close();
    while ($conn->more_results() && $conn->next_result()) {}

   
    // Datos adicionales
    
    $stmt = $conn->prepare("CALL InsertarDatosAdicionales(?,?,?,?)");
    $stmt->bind_param("isss", $nuevoId, $observaciones, $altura, $diagnostico);
    $stmt->execute();
    $stmt->close();
    while ($conn->more_results() && $conn->next_result()) {}

  
    CerrarBD($conn);

    header("Location: /Controller/historialExpedientePacienteController.php?PacienteId=$pacienteId");
    exit;

} catch (Exception $e) {
    // ❌ NO cerrar stmt aquí (ya se cerró antes)
    CerrarBD($conn);
    die("<h3 style='color:red;'>❌ Error al ejecutar SP:</h3> " . $e->getMessage());
}
