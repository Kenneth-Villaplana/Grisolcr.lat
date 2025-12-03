<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../Model/pacienteModel.php';
include_once '../Model/baseDatos.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'crearPaciente' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioId = $_POST['UsuarioId'] ?? '';

    if (!$usuarioId) {
        echo json_encode(['success' => false, 'error' => 'Falta UsuarioId']);
        exit;
    }

    $conexion = AbrirBD();
   
    $stmt = $conexion->prepare("CALL CrearPacienteDesdeUsuario(?)");
    $stmt->bind_param("i", $usuarioId);
    if ($stmt->execute()) {
        $resultado = $stmt->get_result();
        $paciente = $resultado->fetch_assoc();
        echo json_encode(['success' => true, 'PacienteId' => $paciente['PacienteId']]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo crear el paciente']);
    }
    $stmt->close();
    CerrarBD($conexion);
    exit;
}

//  búsqueda por cédula
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cedula = trim($_POST['cedula'] ?? '');

    if (!$cedula) {
        echo json_encode(['error' => 'Debe ingresar una cédula']);
        exit;
    }

    $conexion = AbrirBD();

    $stmt = $conexion->prepare("CALL BuscarPacientePorCedulaUsuario(?)");
    $stmt->bind_param("s", $cedula);
    $stmt->execute();

  $resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $paciente = $resultado->fetch_assoc();

    echo json_encode([
         'PacienteId' => $paciente['PacienteId'],
    'nombre' => $paciente['Nombre'] ?? '',
    'apellido' => $paciente['Apellido'] ?? '',
    'apellidoDos' => $paciente['ApellidoDos'] ?? '',
    'cedula' => $paciente['Cedula'] ?? '',
    'telefono' => $paciente['Telefono'] ?? '',
    'direccion' => $paciente['Direccion'] ?? '',
    'fechaNacimiento' => $paciente['FechaNacimiento'] ?? null
]);
} else {
    echo json_encode(['error' => 'No se encontró ningún paciente con esa cédula']);
}

$stmt->close();
$conexion->next_result();  
CerrarBD($conexion);
    exit;
}
?>