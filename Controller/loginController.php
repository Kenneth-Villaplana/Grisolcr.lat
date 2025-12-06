<?php

include_once __DIR__ . '/../Model/loginModel.php';
//include_once __DIR__ . '/../Model/usuarioModel.php';


if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_GET["cerrarSesion"])) {
    $_SESSION = array();
    session_destroy();
    header('Location: /OptiGestion/View/iniciarSesion.php');
    exit();
}


// Registro de paciente
if(isset($_POST["btnRegistrarPaciente"])) {

    $cedula = $_POST["Cedula"];
    $nombre = $_POST["Nombre"];
    $apellido = $_POST["Apellido"];
    $apellidoDos = $_POST["ApellidoDos"];
    $correoElectronico = $_POST["CorreoElectronico"];
    $contrasenna = $_POST["Contrasenna"];
    $confirmarContrasenna = $_POST["ConfirmarContrasenna"];
    $telefono = $_POST["Telefono"];
    $direccion = $_POST["Direccion"];
    $fechaNacimiento = $_POST["FechaNacimiento"];  

    if($contrasenna != $confirmarContrasenna) {

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

        
        if($resultadoReg['resultado'] == 1) {

            $_SESSION["txtMensaje"] = "Paciente registrado con éxito";
            $_SESSION["registroExitoso"] = true;

            //cuando viene desde pos → redirige al Punto de Venta 
            if (isset($_POST["origen"]) && $_POST["origen"] === "POS") {
                header("Location: /OptiGestion/View/puntoVenta.php?cedula=" . urlencode($cedula));
                exit;
            }

        } 
        
        else {
            $_SESSION["txtMensaje"] = $resultadoReg['mensaje'] ?? "Error en el registro.";
        }
    }
}

// Registro de empleado
if(isset($_POST["btnRegistrarPersonal"])) {
    $cedula = $_POST["Cedula"];
    $nombre = $_POST["Nombre"];
    $apellido = $_POST["Apellido"];
    $apellidoDos = $_POST["ApellidoDos"];
    $correoElectronico = $_POST["CorreoElectronico"];
    $contrasenna = $_POST["Contrasenna"];
    $confirmarContrasenna = $_POST["ConfirmarContrasenna"];
    $telefono = $_POST["Telefono"];
    $direccion = $_POST["Direccion"];
    $rolId = $_POST["RolId"]; 
    $fechaNacimiento = $_POST["FechaNacimiento"]; 
 
    if($contrasenna != $confirmarContrasenna) {
        $_SESSION["txtMensaje"] = "Las contraseñas no coinciden.";
    } else {
         $hash = password_hash($contrasenna, PASSWORD_DEFAULT);

        $resultadoReg = RegistrarPersonalModel( $cedula, $nombre, $apellido, $apellidoDos, $correoElectronico, $hash, $telefono, $direccion, $rolId,$fechaNacimiento);
       
         if($resultadoReg['resultado'] == 1) {
            $_SESSION["txtMensaje"] = $resultadoReg['mensaje'];
            $_SESSION["registroExitoso"] = true;
        } else {
            $_SESSION["txtMensaje"] = $resultadoReg['mensaje'] ?? "Error en el registro.";
        }
    }
}


// para el inicio de sesión
if(isset($_POST["btnIniciarSesion"])) {
    $correo = $_POST["CorreoElectronico"] ?? '';
    $contrasenna = $_POST["Contrasenna"] ?? '';

    if(empty($correo) || empty($contrasenna)) {
        $_SESSION["txtMensaje"] = "Debe ingresar correo y contraseña.";
    } else {
        $usuario = IniciarSesionModel($correo);
        if($usuario && password_verify($contrasenna, $usuario["Contrasenna"])) {
            $_SESSION["UsuarioID"] = $usuario["IdUsuario"];
            $_SESSION["Cedula"] = $usuario["Cedula"];
            $_SESSION["Nombre"] = $usuario["Nombre"];
            $_SESSION["Apellido"] = $usuario["Apellido"];
            $_SESSION["ApellidoDos"] = $usuario["ApellidoDos"];
            $_SESSION["CorreoElectronico"] = $usuario["CorreoElectronico"];
            $_SESSION["Telefono"] = $usuario["Telefono"];
            $_SESSION["Direccion"] = $usuario["Direccion"];
            $_SESSION["RolID"] = $usuario["RolUsuario"];
            $_SESSION['EmpleadoRol'] = $usuario['RolEmpleado'] ?? null;

            header('Location: /OptiGestion/index.php');
            exit();
        } else {
            $_SESSION["txtMensaje"] = "Correo electrónico o contraseña incorrectos.";
        }
    }

}

?>