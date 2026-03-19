<?php
include('../Model/baseDatos.php');
$pacienteId = $_GET['PacienteId'] ?? null;
if (!$pacienteId) die("Paciente no especificado.");

$conn = AbrirBD();
$stmt = $conn->prepare("CALL ObtenerHistorialClinico(?)");
$stmt->bind_param("i", $pacienteId);
$stmt->execute();
$result = $stmt->get_result();

$expedientes = [];
while ($row = $result->fetch_assoc()) {
    $expedientes[] = $row;
}

$stmt->close();
CerrarBD($conn);

session_start();
$_SESSION['historialClinico'] = $expedientes;
header("Location: ../View/historialExpedientePaciente.php");
exit;
?>
