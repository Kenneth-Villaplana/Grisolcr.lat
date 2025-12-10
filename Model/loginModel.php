<?php
include_once __DIR__ . '/../Model/baseDatos.php';

// Registrar Personal
function RegistrarPersonalModel($cedula, $nombre, $apellido, $apellidoDos, $correoElectronico, $contrasenna, $telefono, $direccion, $rolId, $fechaNacimiento)
{
    try {
        $enlace = AbrirBD();
        $sentencia = $enlace->prepare("CALL RegistrarPersonal(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if(!$sentencia) {
            throw new Exception($enlace->error);
        }

        
       $sentencia->bind_param("ssssssssss", 
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
        CerrarBD($enlace);

        return ['resultado' => 1, 'mensaje' => 'Registro realizado con éxito'];

    } catch(Exception $ex) {
        return ['resultado' => 0, 'mensaje' => 'Error en el servidor: '.$ex->getMessage()];
    }
}

// Registrar Paciente
function RegistrarPacienteModel($cedula, $nombre, $apellido, $apellidoDos, $correoElectronico, $contrasenna, $telefono, $direccion, $fechaNacimiento)
{
    try {
        $enlace = AbrirBD();
        $sentencia = $enlace->prepare("CALL RegistrarPaciente(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if(!$sentencia) {
            throw new Exception($enlace->error);
        }

       
        $sentencia->bind_param("sssssssss", 
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
        CerrarBD($enlace);

        return ['resultado' => 1, 'mensaje' => 'Registro realizado con éxito'];

    } catch(Exception $ex) {
        return ['resultado' => 0, 'mensaje' => 'Error en el servidor: '.$ex->getMessage()];
    }
}

// Iniciar Sesión
function IniciarSesionModel($correo)
{
    try {
        $enlace = AbrirBD();
        $sentencia = $enlace->prepare("CALL IniciarSesion(?)");
        if(!$sentencia) {
            throw new Exception($enlace->error);
        }

        $sentencia->bind_param("s", $correo);
        $sentencia->execute();

        $resultado = $sentencia->get_result();
        $usuario = $resultado->fetch_assoc();

        $sentencia->close();
        CerrarBD($enlace);

        return $usuario ?: null;

    } catch(Exception $ex) {
        return null;
    }
    function CambiarContrasennaModel($token, $nuevaContrasenna)
{
    try {
        $enlace = AbrirBD();

        $sentencia = $enlace->prepare("CALL CambiarContrasenna(?, ?)");
        if (!$sentencia) {
            throw new Exception($enlace->error);
        }

        $hash = password_hash($nuevaContrasenna, PASSWORD_DEFAULT);

        $sentencia->bind_param("ss", $token, $hash);
        $sentencia->execute();

        $resultado = $sentencia->get_result()->fetch_assoc();

        $sentencia->close();
        CerrarBD($enlace);

        return $resultado;

    } catch (Exception $ex) {
        return ['resultado' => 0, 'mensaje' => 'Error en el servidor: '.$ex->getMessage()];
    }
}
}
?>