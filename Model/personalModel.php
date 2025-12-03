<?php
include_once __DIR__ . '/../Model/baseDatos.php';

function ObtenerPersonalPorId($idUsuario){
    try{
        $enlace = AbrirBD();

        $sentencia = $enlace->prepare("CALL FiltroPorIdUsuario(?)");
        $sentencia->bind_param("i", $idUsuario);

        $sentencia->execute();
        $resultado = $sentencia->get_result();
        $usuario = $resultado->fetch_assoc();


        if($resultado) { $resultado->close(); }

        $sentencia->close();
        $enlace->next_result(); 
        CerrarBD($enlace);

        return $usuario;

    } catch(Exception $ex){
        return [];
    }
}

function ObtenerPersonal($cedula = null){
    try{
        $enlace = AbrirBD();

        if($cedula) {
            $sentencia = $enlace->prepare("CALL FiltroPorCedula(?)");
            $sentencia->bind_param("s", $cedula);
        } else {
            $sentencia = $enlace->prepare("CALL MostrarPersonal()");
        }

        $sentencia->execute();
        $resultado = $sentencia->get_result();

        $personal = [];
        while($row = $resultado->fetch_assoc()) {
            $personal[] = $row;
        }

        if($resultado) { $resultado->close(); }

        $sentencia->close();
        $enlace->next_result();
        CerrarBD($enlace);

        return $personal;

    } catch(Exception $ex){
        return [];
    }
}

function EditarPersonalModel(
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
){
    try {
        $enlace = AbrirBD();

 
        $sentencia = $enlace->prepare(
            "CALL EditarPersonal(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if(!$sentencia) {
            throw new Exception($enlace->error);
        }

        $sentencia->bind_param(
            "issssssissi",
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

        $sentencia->execute();
        $sentencia->close();
        $enlace->next_result();
        CerrarBD($enlace);

        return [
            'resultado' => 1,
            'mensaje'   => 'Cambio realizado con éxito'
        ];

    } catch(Exception $ex) {
        return [
            'resultado' => 0,
            'mensaje'   => 'Error en el servidor: '.$ex->getMessage()
        ];
    }
}
?>