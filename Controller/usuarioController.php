<?php
include_once __DIR__ . '/../Model/LoginModel.php';
include_once __DIR__ . '/../Model/UsuarioModel.php';

if(session_status() == PHP_SESSION_NONE) {
    session_start();
}


if(isset($_POST["btnEditarPerfil"])) {
    $idUsuario = $_POST["IdUsuario"];
    $cedula = $_POST["Cedula"];
    $nombre = $_POST["Nombre"];
    $apellido = $_POST["Apellido"];
    $apellidoDos = $_POST["ApellidoDos"];
    $correoElectronico = $_POST["CorreoElectronico"];
    $telefono = $_POST["Telefono"];
    $direccion = $_POST["Direccion"];
    $fechaNacimiento = $_POST["FechaNacimiento"] ?? null;

     if ($fechaNacimiento === '') {
        $fechaNacimiento = null;
     }

        $resultadoEdit = EditarPerfil($idUsuario, $cedula, $nombre, $apellido, $apellidoDos, $correoElectronico, $telefono, $direccion, $fechaNacimiento);
       
        $_SESSION["txtMensaje"] = $resultadoEdit['mensaje'];
         if($resultadoEdit['resultado'] == 1) {
            $_SESSION["CambioExitoso"] = true;
        }
        header ("Location: editarPerfil.php?id=".$idUsuario);
        exit;
    }
$idUsuario = $_SESSION['UsuarioID'] ?? null;
    if(!$idUsuario){
            header('Location: iniciarSesion.php');
    exit();
}

    $usuario = ObtenerPerfil($idUsuario);
    if (!$usuario) {
        die ("Perfil no encontrado");
    }
?>