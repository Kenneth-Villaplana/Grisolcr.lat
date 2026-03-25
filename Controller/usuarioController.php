<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../Model/usuarioModel.php';


$idUsuario = $_SESSION['IdUsuario'] ?? $_SESSION['UsuarioID'] ?? 0;
$idUsuario = (int)$idUsuario;

if ($idUsuario <= 0) {
    header('Location: /View/iniciarSesion.php');
    exit();
}


if (isset($_POST['btnEditarPerfil'])) {
    try {

        // Seguridad: validar ID contra sesión
        $idUsuarioPost = isset($_POST['IdUsuario']) ? (int)$_POST['IdUsuario'] : 0;

        if ($idUsuarioPost !== $idUsuario) {
            throw new Exception('Intento de manipulación de usuario');
        }

        
        $cedula = preg_replace('/\D/', '', $_POST['Cedula'] ?? '');
        $nombre            = trim($_POST['Nombre'] ?? '');
        $apellido          = trim($_POST['Apellido'] ?? '');
        $apellidoDos       = trim($_POST['ApellidoDos'] ?? '');
        $correoElectronico = trim($_POST['CorreoElectronico'] ?? '');
        $telefono          = trim($_POST['Telefono'] ?? '');
        $direccion         = trim($_POST['Direccion'] ?? '');
        $fechaNacimiento   = $_POST['FechaNacimiento'] ?? null;

        //  Validaciones mínimas
        if (empty($nombre) || empty($apellido) || empty($correoElectronico)) {
            throw new Exception('Campos obligatorios incompletos');
        }

        if (!filter_var($correoElectronico, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Correo electrónico inválido');
        }

       
        if (!empty($fechaNacimiento)) {
            $fechaNacimiento = date('Y-m-d', strtotime($fechaNacimiento));
        } else {
            $fechaNacimiento = null;
        }

        
        $resultadoEdit = EditarPerfil(
            $idUsuario,
            $cedula,
            $nombre,
            $apellido,
            $apellidoDos,
            $correoElectronico,
            $telefono,
            $direccion,
            $fechaNacimiento
        );

        
        $_SESSION['txtMensaje'] = $resultadoEdit['mensaje'] ?? 'Operación realizada';

        if (($resultadoEdit['resultado'] ?? 0) == 1) {
            $_SESSION['CambioExitoso'] = true;
        } else {
         
            error_log('SP EditarPerfil FALLÓ: ' . json_encode($resultadoEdit));
        }

    } catch (Throwable $e) {

     
        error_log('EditarPerfil ERROR: ' . $e->getMessage());

   
        $_SESSION['txtMensaje'] = $e->getMessage();
    }

    header('Location: /View/editarPerfil.php');
    exit();
}


$usuario = [];

try {
    $usuario = ObtenerPerfil($idUsuario);
} catch (Throwable $e) {
    error_log('ObtenerPerfil Model ERROR: ' . $e->getMessage());
    $usuario = [];
}

if (empty($usuario)) {
    try {
        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL ObtenerPerfilUsuario(?)");

        if (!$stmt) {
            throw new Exception('Error en prepare SP: ' . $conn->error);
        }

        $stmt->bind_param('i', $idUsuario);

        if (!$stmt->execute()) {
            throw new Exception('Error en execute SP: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $usuario = $result ? ($result->fetch_assoc() ?: []) : [];

        $stmt->close();
        $conn->next_result(); 
        CerrarBD($conn);

    } catch (Throwable $e) {
        error_log('SP perfil ERROR: ' . $e->getMessage());
        $usuario = [];
    }
} 

if (empty($usuario)) {
    error_log('Perfil no cargó ni por model ni por fallback. ID: ' . $idUsuario);
    die('Error al cargar el perfil');
}