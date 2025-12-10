<?php
include_once __DIR__ . '/../Model/baseDatos.php';

// Buscar usuario por correo
function BuscarUsuarioPorCorreo($correo) {
    $enlace = AbrirBD();
    $sentencia = $enlace->prepare("SELECT * FROM usuario WHERE CorreoElectronico = ?");
    $sentencia->bind_param("s", $correo);
    $sentencia->execute();
    $resultado = $sentencia->get_result()->fetch_assoc();
    $sentencia->close();
    CerrarBD($enlace);

    return $resultado;
}

// Guardar token
function GuardarTokenRecuperacion($correo, $token) {
    $enlace = AbrirBD();
    $sentencia = $enlace->prepare("UPDATE usuario SET TokenRecuperacion = ?, TokenExpira = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE CorreoElectronico = ?");
    $sentencia->bind_param("ss", $token, $correo);
    $sentencia->execute();
    $sentencia->close();
    CerrarBD($enlace);
}

// Validar token
function ObtenerUsuarioPorToken($token) {
    $enlace = AbrirBD();
    $sentencia = $enlace->prepare("SELECT * FROM usuario WHERE TokenRecuperacion = ? AND TokenExpira > NOW()");
    $sentencia->bind_param("s", $token);
    $sentencia->execute();
    $resultado = $sentencia->get_result()->fetch_assoc();
    $sentencia->close();
    CerrarBD($enlace);

    return $resultado;
}

// Actualizar contraseña
function ActualizarContrasenna($idUsuario, $hash) {
    $enlace = AbrirBD();
    $sentencia = $enlace->prepare("UPDATE usuario SET Contrasenna = ?, TokenRecuperacion = NULL, TokenExpira = NULL WHERE IdUsuario = ?");
    $sentencia->bind_param("si", $hash, $idUsuario);
    $sentencia->execute();
    $sentencia->close();
    CerrarBD($enlace);
}
