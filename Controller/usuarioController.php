<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../Model/UsuarioModel.php';
include_once __DIR__ . '/../Model/baseDatos.php';

/*
|--------------------------------------------------------------------------
| VALIDAR SESIÓN
|--------------------------------------------------------------------------
*/
$idUsuario = $_SESSION['IdUsuario'] ?? $_SESSION['UsuarioID'] ?? 0;
$idUsuario = (int)$idUsuario;

if ($idUsuario <= 0) {
    header('Location: /View/iniciarSesion.php');
    exit();
}

/*
|--------------------------------------------------------------------------
| ACTUALIZAR PERFIL
|--------------------------------------------------------------------------
*/
if (isset($_POST['btnEditarPerfil'])) {
    try {
        $idUsuarioPost = isset($_POST['IdUsuario']) ? (int)$_POST['IdUsuario'] : 0;

        if ($idUsuarioPost !== $idUsuario) {
            throw new Exception('Intento de manipulación de usuario');
        }

        $cedula            = trim($_POST['Cedula'] ?? '');
        $nombre            = trim($_POST['Nombre'] ?? '');
        $apellido          = trim($_POST['Apellido'] ?? '');
        $apellidoDos       = trim($_POST['ApellidoDos'] ?? '');
        $correoElectronico = trim($_POST['CorreoElectronico'] ?? '');
        $telefono          = trim($_POST['Telefono'] ?? '');
        $direccion         = trim($_POST['Direccion'] ?? '');
        $fechaNacimiento   = $_POST['FechaNacimiento'] ?? null;

        if ($fechaNacimiento === '') {
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
        }
    } catch (Throwable $e) {
        error_log('EditarPerfil ERROR: ' . $e->getMessage());
        $_SESSION['txtMensaje'] = 'Error al actualizar el perfil';
    }

    header('Location: /View/editarPerfil.php');
    exit();
}

/*
|--------------------------------------------------------------------------
| OBTENER PERFIL
|--------------------------------------------------------------------------
| 1) Intenta con el Model
| 2) Si falla o viene vacío, usa query directa como respaldo
|--------------------------------------------------------------------------
*/
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

        $sql = "SELECT 
                    u.IdUsuario,
                    u.Cedula,
                    u.Nombre,
                    u.Apellido,
                    u.ApellidoDos,
                    u.CorreoElectronico,
                    u.Telefono,
                    u.Direccion,
                    p.FechaNacimiento
                FROM usuario u
                LEFT JOIN paciente p ON u.IdUsuario = p.usuarioId
                WHERE u.IdUsuario = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception('Error en prepare fallback: ' . $conn->error);
        }

        $stmt->bind_param('i', $idUsuario);

        if (!$stmt->execute()) {
            throw new Exception('Error en execute fallback: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $usuario = $result ? ($result->fetch_assoc() ?: []) : [];

        $stmt->close();
        CerrarBD($conn);
    } catch (Throwable $e) {
        error_log('Fallback perfil ERROR: ' . $e->getMessage());
        $usuario = [];
    }
}

if (empty($usuario)) {
    error_log('Perfil no cargó ni por model ni por fallback. ID: ' . $idUsuario);
    die('Error al cargar el perfil');
}