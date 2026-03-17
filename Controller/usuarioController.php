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

    // 🔥 DEBUG PROFESIONAL
    if ($usuario === null) {
        error_log("SP devolvió NULL - ID: " . $idUsuario);
        die("Error crítico al obtener perfil");
    }

    if (!is_array($usuario)) {
        error_log("SP devolvió formato inválido - ID: " . $idUsuario);
        die("Error interno de datos");
    }

    if (empty($usuario)) {
        error_log("SP devolvió array vacío - ID: " . $idUsuario);

        // 🔥 VALIDACIÓN EXTRA (CLAVE)
        // Verificar si el usuario existe en tabla base
        require_once __DIR__ . '/../Model/baseDatos.php';
        $conn = AbrirBD();

        $check = $conn->prepare("SELECT IdUsuario FROM usuario WHERE IdUsuario = ?");
        $check->bind_param("i", $idUsuario);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows === 0) {
            // 💣 usuario no existe → sesión corrupta
            session_destroy();
            header("Location: /View/iniciarSesion.php");
            exit();
        }

        // Si existe pero SP no devolvió → problema de JOIN
        die("Perfil existe pero no se pudo cargar (JOIN issue)");
    }

} catch (Throwable $e) {

    error_log("ERROR PERFIL: " . $e->getMessage());
    die("Error al cargar el perfil");
}"Error al cargar el perfil";

?>