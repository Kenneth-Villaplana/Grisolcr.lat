<?php
include_once __DIR__ . '/../Model/LoginModel.php';
include_once __DIR__ . '/../Model/UsuarioModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| VALIDACIÓN DE SESIÓN (UNIFICADA)
|--------------------------------------------------------------------------
*/
$idUsuario = $_SESSION['IdUsuario'] ?? null;

if (!$idUsuario) {
    header('Location: iniciarSesion.php');
    exit();
}


/*
|--------------------------------------------------------------------------
| ACTUALIZAR PERFIL
|--------------------------------------------------------------------------
*/
if (isset($_POST["btnEditarPerfil"])) {

    try {

        $idUsuario = $_POST["IdUsuario"] ?? null;
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

        // Validación básica
        if (!$idUsuario) {
            throw new Exception("ID de usuario inválido");
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

        // Manejo seguro de respuesta
        $_SESSION["txtMensaje"] = $resultadoEdit['mensaje'] ?? "Operación realizada";
        
        if (($resultadoEdit['resultado'] ?? 0) == 1) {
            $_SESSION["CambioExitoso"] = true;
        }

    } catch (Throwable $e) {

        $_SESSION["txtMensaje"] = "Error: " . $e->getMessage();
    }

    // Redirect limpio (sin id innecesario)
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

    if (!$usuario || !is_array($usuario)) {
        throw new Exception("Perfil no encontrado");
    }

} catch (Throwable $e) {

    // Puedes loguear aquí si quieres nivel pro
    error_log($e->getMessage());

    die("Error al cargar el perfil");
}
?>