<?php

// 🔥 Iniciar sesión SIEMPRE
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔥 Models
include_once __DIR__ . '/../Model/LoginModel.php';
include_once __DIR__ . '/../Model/UsuarioModel.php';


/*
|--------------------------------------------------------------------------
| VALIDACIÓN DE SESIÓN
|--------------------------------------------------------------------------
*/
$idUsuario = $_SESSION['IdUsuario'] ?? $_SESSION['UsuarioID'] ?? 0;
$idUsuario = (int)$idUsuario;

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

        // 🔒 Seguridad: validar que no manipulen el ID
        $idUsuarioPost = $_POST["IdUsuario"] ?? 0;

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
        $_SESSION["txtMensaje"] = "Error al actualizar el perfil";
    }

    header("Location: editarPerfil.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| OBTENER PERFIL
|--------------------------------------------------------------------------
*/
try {

    $usuario = ObtenerPerfil($idUsuario);

    if (empty($usuario)) {

        error_log("Perfil vacío para ID: " . $idUsuario);

        // Validar si el usuario aún existe en DB
        require_once __DIR__ . '/../Model/baseDatos.php';
        $conn = AbrirBD();

        $check = $conn->prepare("SELECT IdUsuario FROM usuario WHERE IdUsuario = ?");
        $check->bind_param("i", $idUsuario);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows === 0) {
            // 💣 sesión inválida → logout
            session_destroy();
            header("Location: /View/iniciarSesion.php");
            exit();
        }

        // Usuario existe pero SP falló
        die("Error al cargar el perfil");
    }

} catch (Throwable $e) {

    error_log("ERROR PERFIL: " . $e->getMessage());
    die("Error al cargar el perfil");
}