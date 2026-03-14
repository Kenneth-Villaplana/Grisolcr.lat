<?php

include_once __DIR__ . '/../Model/baseDatos.php';

/* =============================
   REGISTRAR PERSONAL
============================= */

function RegistrarPersonalModel($cedula,$nombre,$apellido,$apellidoDos,$correoElectronico,$contrasenna,$telefono,$direccion,$rolId,$fechaNacimiento)
{
    try {

        $enlace = AbrirBD();

        $sentencia = $enlace->prepare("CALL RegistrarPersonal(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $sentencia->bind_param(
            "ssssssssss",
            $cedula,
            $nombre,
            $apellido,
            $apellidoDos,
            $correoElectronico,
            $contrasenna,
            $telefono,
            $direccion,
            $rolId,
            $fechaNacimiento
        );

        $sentencia->execute();

        $sentencia->close();
        $enlace->next_result();
        CerrarBD($enlace);

        return ['resultado'=>1,'mensaje'=>'Registro realizado con éxito'];

    } catch(Exception $ex){

        return ['resultado'=>0,'mensaje'=>$ex->getMessage()];
    }
}

/* =============================
   REGISTRAR PACIENTE
============================= */

function RegistrarPacienteModel($cedula,$nombre,$apellido,$apellidoDos,$correoElectronico,$contrasenna,$telefono,$direccion,$fechaNacimiento)
{
    try {

        $enlace = AbrirBD();

        $sentencia = $enlace->prepare("CALL RegistrarPaciente(?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $sentencia->bind_param(
            "sssssssss",
            $cedula,
            $nombre,
            $apellido,
            $apellidoDos,
            $correoElectronico,
            $contrasenna,
            $telefono,
            $direccion,
            $fechaNacimiento
        );

        $sentencia->execute();

        $sentencia->close();
        $enlace->next_result();
        CerrarBD($enlace);

        return ['resultado'=>1,'mensaje'=>'Registro realizado con éxito'];

    } catch(Exception $ex){

        return ['resultado'=>0,'mensaje'=>$ex->getMessage()];
    }
}

/* =============================
   INICIAR SESIÓN
============================= */

function IniciarSesionModel($correo)
{
    try {

        $enlace = AbrirBD();

        $sentencia = $enlace->prepare("CALL IniciarSesion(?)");

        $sentencia->bind_param("s",$correo);

        $sentencia->execute();

        $resultado = $sentencia->get_result();

        $usuario = $resultado->fetch_assoc();

        $sentencia->close();
        $enlace->next_result();
        CerrarBD($enlace);

        return $usuario ?: null;

    } catch(Exception $ex){

        return null;
    }
}

/* =============================
   CAMBIAR CONTRASEÑA
============================= */

function CambiarContrasennaModel($token,$nuevaContrasenna)
{
    try {

        $enlace = AbrirBD();

        $hash = password_hash($nuevaContrasenna,PASSWORD_DEFAULT);

        $sentencia = $enlace->prepare("CALL CambiarContrasenna(?, ?)");

        $sentencia->bind_param("ss",$token,$hash);

        $sentencia->execute();

        $resultado = $sentencia->get_result()->fetch_assoc();

        $sentencia->close();
        $enlace->next_result();
        CerrarBD($enlace);

        return $resultado;

    } catch(Exception $ex){

        return ['resultado'=>0,'mensaje'=>$ex->getMessage()];
    }
}

?>