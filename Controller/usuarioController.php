<?php

// 🔥 Mostrar errores (solo desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 🔐 Sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 📦 Models
require_once __DIR__ . '/../Model/UsuarioModel.php';

// 🔒 Validar sesión
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

        // 🔒 Seguridad: validar ID contra sesión
        $idUsuarioPost = isset($_POST['IdUsuario']) ? (int)$_POST['IdUsuario'] : 0;

        if ($idUsuarioPost !== $idUsuario) {
            throw new Exception('Intento de manipulación de usuario');
        }

        // 🧼 Sanitización
        $cedula            = trim($_POST['Cedula'] ?? '');
        $nombre            = trim($_POST['Nombre'] ?? '');
        $apellido          = trim($_POST['Apellido'] ?? '');
        $apellidoDos       = trim($_POST['ApellidoDos'] ?? '');
        $correoElectronico = trim($_POST['CorreoElectronico'] ?? '');
        $telefono          = trim($_POST['Telefono'] ?? '');
        $direccion         = trim($_POST['Direccion'] ?? '');
        $fechaNacimiento   = $_POST['FechaNacimiento'] ?? null;

        // ⚠️ Validaciones mínimas
        if (empty($nombre) || empty($apellido) || empty($correoElectronico)) {
            throw new Exception('Campos obligatorios incompletos');
        }

        if (!filter_var($correoElectronico, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Correo electrónico inválido');
        }

        // 🔥 FIX CRÍTICO → FORMATO DE FECHA
        if (!empty($fechaNacimiento)) {
            $fechaNacimiento = date('Y-m-d', strtotime($fechaNacimiento));
        } else {
            $fechaNacimiento = null;
        }

        // 🚀 Ejecutar modelo
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

        // 🔍 Mostrar mensaje REAL del modelo
        $_SESSION['txtMensaje'] = $resultadoEdit['mensaje'] ?? 'Operación realizada';

        if (($resultadoEdit['resultado'] ?? 0) == 1) {
            $_SESSION['CambioExitoso'] = true;
        } else {
            // 🔥 IMPORTANTE: log del error real
            error_log('SP EditarPerfil FALLÓ: ' . json_encode($resultadoEdit));
        }

    } catch (Throwable $e) {

        // 🧠 Log técnico
        error_log('EditarPerfil ERROR: ' . $e->getMessage());

        // 👀 Mensaje para UI (puedes cambiarlo si quieres)
        $_SESSION['txtMensaje'] = $e->getMessage();
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