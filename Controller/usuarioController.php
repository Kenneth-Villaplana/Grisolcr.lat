<?php

// 🔥 SIEMPRE iniciar sesión primero
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../Model/LoginModel.php';
include_once __DIR__ . '/../Model/UsuarioModel.php';


/*
|--------------------------------------------------------------------------
| VALIDACIÓN DE SESIÓN (ROBUSTA)
|--------------------------------------------------------------------------
*/
$idUsuario = $_SESSION['IdUsuario'] ?? $_SESSION['UsuarioID'] ?? 0;
$idUsuario = (int)$idUsuario;

if ($idUsuario <= 0) {
    header('Location: /View/iniciarSesion.php');
    exit();
}

if ($idUsuario <= 0) {
    error_log("Sesión inválida en usuarioController");
    header('Location: /View/iniciarSesion.php');
    exit();
}


/*
|--------------------------------------------------------------------------
| ACTUALIZAR PERFIL
|--------------------------------------------------------------------------
*/
if (isset($_POST["btnEditarPerfil"])) {

    try {

        // 🔥 NUNCA confiar en POST para el ID
        $idUsuarioPost = $_POST["IdUsuario"] ?? null;

        // Validación de seguridad
        if ((int)$idUsuarioPost !== $idUsuario) {
            throw new Exception("Intento de manipulación de usuario");
        }

        $cedula = $_POST["Cedula"] ?? '';
        $nombre = $_POST["Nombre"] ?? '';
        $apellido = $_POST["Apellido"] ?? '';
        $apellidoDos = $_POST["ApellidoDos"] ?? '';
        $correoElectronico = $_POST["CorreoElectronico"] ?? '';
        $telefono = $_POST["Telefono"] ?? '';
        $direccion = $_POST["Direccion"] ?? '';
        $fechaNacimiento = $_POST["FechaNacimiento"] ?? null;

        // Normalizar fecha
        if ($fechaNacimiento === '') {
            $fechaNacimiento = null;
        }

        // Llamada al modelo
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

        $_SESSION["txtMensaje"] = $resultadoEdit['mensaje'] ?? "Operación realizada";

        if (($resultadoEdit['resultado'] ?? 0) == 1) {
            $_SESSION["CambioExitoso"] = true;
        }

    } catch (Throwable $e) {

        error_log("EditarPerfil ERROR: " . $e->getMessage());
        $_SESSION["txtMensaje"] = "Error: " . $e->getMessage();
    }

    header("Location: editarPerfil.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| OBTENER PERFIL PARA LA VISTA
|--------------------------------------------------------------------------
*/
try {

    $usuario = ObtenerPerfil($idUsuario);

    // 🔥 VALIDACIÓN CORRECTA
    if (empty($usuario)) {
        error_log("Perfil vacío para ID: " . $idUsuario);
        die("Error al cargar el perfil");
    }

} catch (Throwable $e) {

    error_log("ObtenerPerfil ERROR: " . $e->getMessage());
    die("Error al cargar el perfil");
}
?>