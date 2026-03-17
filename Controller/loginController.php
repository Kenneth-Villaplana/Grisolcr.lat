<?php

include_once __DIR__ . '/../Model/loginModel.php';

if (session_status() === PHP_SESSION_NONE) {

    // 🔥 CONFIG COOKIE (IMPORTANTE EN VPS / DO)
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => false, // true si usas https
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

/* =============================
   CERRAR SESIÓN
============================= */

if (isset($_GET["cerrarSesion"])) {

    $_SESSION = [];
    session_destroy();

    header('Location: /View/iniciarSesion.php');
    exit();
}

/* =============================
   REGISTRO PACIENTE
============================= */

if (isset($_POST["btnRegistrarPaciente"])) {

    $cedula = $_POST["Cedula"] ?? '';
    $nombre = $_POST["Nombre"] ?? '';
    $apellido = $_POST["Apellido"] ?? '';
    $apellidoDos = $_POST["ApellidoDos"] ?? '';
    $correoElectronico = $_POST["CorreoElectronico"] ?? '';
    $contrasenna = $_POST["Contrasenna"] ?? '';
    $confirmarContrasenna = $_POST["ConfirmarContrasenna"] ?? '';
    $telefono = $_POST["Telefono"] ?? '';
    $direccion = $_POST["Direccion"] ?? '';
    $fechaNacimiento = $_POST["FechaNacimiento"] ?? null;

    if (strlen($contrasenna) < 8) {

        $_SESSION["txtMensaje"] = "La contraseña debe tener mínimo 8 caracteres.";

    } elseif ($contrasenna != $confirmarContrasenna) {

        $_SESSION["txtMensaje"] = "Las contraseñas no coinciden.";

    } else {

        $hash = password_hash($contrasenna, PASSWORD_DEFAULT);

        $resultadoReg = RegistrarPacienteModel(
            $cedula,
            $nombre,
            $apellido,
            $apellidoDos,
            $correoElectronico,
            $hash,
            $telefono,
            $direccion,
            $fechaNacimiento
        );

        if (($resultadoReg['resultado'] ?? 0) == 1) {

            $_SESSION["txtMensaje"] = "Paciente registrado con éxito";
            $_SESSION["registroExitoso"] = true;

            if (isset($_POST["origen"]) && $_POST["origen"] === "POS") {
                header("Location: /View/puntoVenta.php?cedula=" . urlencode($cedula));
                exit;
            }

        } else {

            $_SESSION["txtMensaje"] = $resultadoReg['mensaje'] ?? "Error en el registro.";
        }
    }
}

/* =============================
   REGISTRO EMPLEADO
============================= */

if (isset($_POST["btnRegistrarPersonal"])) {

    $cedula = $_POST["Cedula"] ?? '';
    $nombre = $_POST["Nombre"] ?? '';
    $apellido = $_POST["Apellido"] ?? '';
    $apellidoDos = $_POST["ApellidoDos"] ?? '';
    $correoElectronico = $_POST["CorreoElectronico"] ?? '';
    $contrasenna = $_POST["Contrasenna"] ?? '';
    $confirmarContrasenna = $_POST["ConfirmarContrasenna"] ?? '';
    $telefono = $_POST["Telefono"] ?? '';
    $direccion = $_POST["Direccion"] ?? '';
    $rolId = $_POST["RolId"] ?? null;
    $fechaNacimiento = $_POST["FechaNacimiento"] ?? null;

    if (strlen($contrasenna) < 8) {

        $_SESSION["txtMensaje"] = "La contraseña debe tener mínimo 8 caracteres.";

    } elseif ($contrasenna != $confirmarContrasenna) {

        $_SESSION["txtMensaje"] = "Las contraseñas no coinciden.";

    } else {

        $hash = password_hash($contrasenna, PASSWORD_DEFAULT);

        $resultadoReg = RegistrarPersonalModel(
            $cedula,
            $nombre,
            $apellido,
            $apellidoDos,
            $correoElectronico,
            $hash,
            $telefono,
            $direccion,
            $rolId,
            $fechaNacimiento
        );

        if (($resultadoReg['resultado'] ?? 0) == 1) {

            $_SESSION["txtMensaje"] = $resultadoReg['mensaje'] ?? "Registro exitoso";
            $_SESSION["registroExitoso"] = true;

        } else {

            $_SESSION["txtMensaje"] = $resultadoReg['mensaje'] ?? "Error en el registro.";
        }
    }
}

/* =============================
   LOGIN
============================= */

if (isset($_POST["btnIniciarSesion"])) {

    $correo = $_POST["CorreoElectronico"] ?? '';
    $contrasenna = $_POST["Contrasenna"] ?? '';

    if (empty($correo) || empty($contrasenna)) {

        $_SESSION["txtMensaje"] = "Debe ingresar correo y contraseña.";

    } else {

        $usuario = IniciarSesionModel($correo);

        if ($usuario && password_verify($contrasenna, $usuario["Contrasenna"])) {

            // 🔥 SESSION UNIFICADA
            $_SESSION["IdUsuario"] = $usuario["IdUsuario"];
            $_SESSION["UsuarioID"] = $usuario["IdUsuario"]; // compatibilidad

            $_SESSION["Cedula"] = $usuario["Cedula"];
            $_SESSION["Nombre"] = $usuario["Nombre"];
            $_SESSION["Apellido"] = $usuario["Apellido"];
            $_SESSION["ApellidoDos"] = $usuario["ApellidoDos"];
            $_SESSION["CorreoElectronico"] = $usuario["CorreoElectronico"];
            $_SESSION["Telefono"] = $usuario["Telefono"];
            $_SESSION["Direccion"] = $usuario["Direccion"];
            $_SESSION["RolID"] = $usuario["RolUsuario"];
            $_SESSION["EmpleadoRol"] = $usuario["RolEmpleado"] ?? null;

            // 🔥 REGENERAR SESIÓN (SEGURIDAD + BUG FIX)
            session_regenerate_id(true);

            header('Location: /index.php');
            exit();

        } else {

            $_SESSION["txtMensaje"] = "Correo electrónico o contraseña incorrectos.";
        }
    }
}

/* =============================
   CAMBIAR CONTRASEÑA
============================= */

if (isset($_POST["btnCambiarContrasenna"])) {

    $token = $_POST["Token"] ?? '';
    $nueva = $_POST["NuevaContrasenna"] ?? '';
    $confirmar = $_POST["ConfirmarContrasenna"] ?? '';

    if (strlen($nueva) < 8) {

        $_SESSION["txtMensaje"] = "La contraseña debe tener mínimo 8 caracteres.";
        header("Location: /View/restablecerContrasenna.php?token=" . $token);
        exit;
    }

    if ($nueva != $confirmar) {

        $_SESSION["txtMensaje"] = "Las contraseñas no coinciden.";
        header("Location: /View/restablecerContrasenna.php?token=" . $token);
        exit;
    }

    $resultado = CambiarContrasennaModel($token, $nueva);

    if (($resultado['resultado'] ?? 0) == 1) {

        $_SESSION["txtMensaje"] = "Contraseña actualizada correctamente.";
        header("Location: /View/iniciarSesion.php");
        exit;

    } else {

        $_SESSION["txtMensaje"] = $resultado['mensaje'] ?? "Error al cambiar contraseña.";
        header("Location: /View/restablecerContrasenna.php?token=" . $token);
        exit;
    }
}
?>