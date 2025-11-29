<?php

include_once __DIR__ . '/../Model/loginModel.php';
include_once __DIR__ . '/../Model/personalModel.php';

if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_POST["btnEditarPersonal"])) {

    $idUsuario = $_POST["IdUsuario"] ?? null;
    $cedula = $_POST["Cedula"];
    $nombre = $_POST["Nombre"];
    $apellido = $_POST["Apellido"];
    $apellidoDos = $_POST["ApellidoDos"];
    $correoElectronico = $_POST["CorreoElectronico"];
    $telefono = $_POST["Telefono"];
    $direccion = $_POST["Direccion"];
    $rolId = $_POST["RolId"]; 

    $fechaNacimiento = $_POST["FechaNacimiento"];


    $estado = isset($_POST["Estado"]) ? 0 : 1;


    $resultadoEdit = EditarPersonalModel(
        $idUsuario,
        $cedula,
        $nombre,
        $apellido,
        $apellidoDos,
        $correoElectronico,
        $telefono,
        $direccion,
        $rolId,
        $fechaNacimiento,
        $estado
    );
    
    $_SESSION["txtMensaje"] = $resultadoEdit['mensaje'];

    if($resultadoEdit['resultado'] == 1) {
        $_SESSION["CambioExitoso"] = true;
    }

    header("Location: editarPersonal.php?id=".$idUsuario);
    exit;
}

$idUsuario = $_GET['id'] ?? null;

if(!$idUsuario){
    die("Usuario no encontrado");
}

$usuario = ObtenerPersonalPorId($idUsuario);

if (!$usuario) {
    die ("Usuario no encontrado");
}

?>