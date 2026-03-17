<?php
include_once __DIR__ . '/../Model/baseDatos.php';

/*
|--------------------------------------------------------------------------
| HELPER CRÍTICO PARA SP (EVITA ERRORES 500)
|--------------------------------------------------------------------------
*/
function limpiarResultados($conn)
{
    while ($conn->more_results() && $conn->next_result()) {;}
}

/*
|--------------------------------------------------------------------------
| OBTENER PERFIL
|--------------------------------------------------------------------------
*/
function ObtenerPerfil($idUsuario)
{
    try {
        $enlace = AbrirBD();

        $sql = "SELECT 
                    u.IdUsuario,
                    u.Cedula,
                    u.Nombre,
                    u.Apellido,
                    u.ApellidoDos,
                    u.CorreoElectronico, 
                    u.Telefono,
                    u.Direccion,
                    p.FechaNacimiento
                FROM usuario u
                LEFT JOIN paciente p ON u.IdUsuario = p.usuarioId
                WHERE u.IdUsuario = ?";

        $sentencia = $enlace->prepare($sql);

        if (!$sentencia) {
            throw new Exception("Error en prepare: " . $enlace->error);
        }

        $sentencia->bind_param("i", $idUsuario);
        $sentencia->execute();

        $resultado = $sentencia->get_result();

        $usuario = [];

        if ($resultado && $resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
        }

        $sentencia->close();
        CerrarBD($enlace);

        return $usuario;

    } catch (Throwable $ex) {

        error_log("ObtenerPerfil ERROR: " . $ex->getMessage());
        return [];
    }
}

/*
|--------------------------------------------------------------------------
| EDITAR PERFIL
|--------------------------------------------------------------------------
*/
function EditarPerfil(
    $idUsuario,
    $cedula,
    $nombre,
    $apellido,
    $apellidoDos,
    $correoElectronico,
    $telefono,
    $direccion,
    $fechaNacimiento = null
) {
    try {
        $enlace = AbrirBD();

        $sentencia = $enlace->prepare("CALL EditarPerfil(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$sentencia) {
            throw new Exception("Error en prepare: " . $enlace->error);
        }

        $sentencia->bind_param(
            "issssssss",
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

        if (!$sentencia->execute()) {
            throw new Exception("Error en execute: " . $sentencia->error);
        }

        limpiarResultados($enlace); // 🔥 CRÍTICO PARA SP

        $sentencia->close();
        CerrarBD($enlace);

        return [
            'resultado' => 1,
            'mensaje' => 'Perfil actualizado con éxito'
        ];

    } catch (Throwable $ex) {
        error_log("EditarPerfil ERROR: " . $ex->getMessage());

        return [
            'resultado' => 0,
            'mensaje' => 'Error en el servidor: ' . $ex->getMessage()
        ];
    }
}

/*
|--------------------------------------------------------------------------
| GOOGLE CALENDAR
|--------------------------------------------------------------------------
*/

function hasGoogleCalendar($doctorId)
{
    try {
        $enlace = AbrirBD();

        $sql = "SELECT google_calendar_enabled, google_access_token 
                FROM usuario 
                WHERE IdUsuario = ?";

        $sentencia = $enlace->prepare($sql);
        $sentencia->bind_param("i", $doctorId);
        $sentencia->execute();

        $resultado = $sentencia->get_result();
        $userData = $resultado->fetch_assoc();

        $sentencia->close();
        CerrarBD($enlace);

        return $userData &&
            (int)$userData['google_calendar_enabled'] === 1 &&
            !empty($userData['google_access_token']);

    } catch (Throwable $ex) {
        error_log("hasGoogleCalendar ERROR: " . $ex->getMessage());
        return false;
    }
}

function getGoogleToken($doctorId)
{
    try {
        $enlace = AbrirBD();

        $sql = "SELECT google_access_token, google_refresh_token 
                FROM usuario 
                WHERE IdUsuario = ? AND google_calendar_enabled = 1";

        $sentencia = $enlace->prepare($sql);
        $sentencia->bind_param("i", $doctorId);
        $sentencia->execute();

        $resultado = $sentencia->get_result();
        $tokenData = $resultado->fetch_assoc();

        $sentencia->close();
        CerrarBD($enlace);

        if ($tokenData && $tokenData['google_access_token']) {
            return [
                'access_token' => json_decode($tokenData['google_access_token'], true),
                'refresh_token' => $tokenData['google_refresh_token']
            ];
        }

        return null;

    } catch (Throwable $ex) {
        error_log("getGoogleToken ERROR: " . $ex->getMessage());
        return null;
    }
}

function saveGoogleToken($userId, $accessToken, $refreshToken = null)
{
    try {
        $enlace = AbrirBD();

        $sql = "UPDATE usuario 
                SET google_access_token = ?, 
                    google_refresh_token = ?,
                    google_calendar_enabled = 1,
                    google_connected_at = NOW()
                WHERE IdUsuario = ?";

        $accessTokenJson = json_encode($accessToken);

        $sentencia = $enlace->prepare($sql);
        $sentencia->bind_param("ssi", $accessTokenJson, $refreshToken, $userId);

        $resultado = $sentencia->execute();

        $sentencia->close();
        CerrarBD($enlace);

        return $resultado;

    } catch (Throwable $ex) {
        error_log("saveGoogleToken ERROR: " . $ex->getMessage());
        return false;
    }
}

function disconnectGoogleCalendar($userId)
{
    try {
        $enlace = AbrirBD();

        $sql = "UPDATE usuario 
                SET google_access_token = NULL,
                    google_refresh_token = NULL,
                    google_calendar_enabled = 0
                WHERE IdUsuario = ?";

        $sentencia = $enlace->prepare($sql);
        $sentencia->bind_param("i", $userId);

        $resultado = $sentencia->execute();

        $sentencia->close();
        CerrarBD($enlace);

        return $resultado;

    } catch (Throwable $ex) {
        error_log("disconnectGoogleCalendar ERROR: " . $ex->getMessage());
        return false;
    }
}

/*
|--------------------------------------------------------------------------
| DOCTORES
|--------------------------------------------------------------------------
*/
function getAllDoctors()
{
    try {
        $enlace = AbrirBD();

        $sql = "SELECT u.IdUsuario, u.Nombre, u.Apellido, u.ApellidoDos, u.CorreoElectronico,
                       r.NombreRol, r.RolId,
                       u.google_calendar_enabled
                FROM usuario u
                INNER JOIN personal p ON u.IdUsuario = p.UsuarioId
                INNER JOIN rol r ON p.Id_rol = r.RolId
                WHERE r.NombreRol IN ('Doctor/a', 'Administrador/a')
                AND u.Estado = 1
                ORDER BY u.Nombre, u.Apellido";

        $sentencia = $enlace->prepare($sql);
        $sentencia->execute();

        $resultado = $sentencia->get_result();
        $doctors = [];

        while ($fila = $resultado->fetch_assoc()) {
            $doctors[] = $fila;
        }

        $sentencia->close();
        CerrarBD($enlace);

        return $doctors;

    } catch (Throwable $ex) {
        error_log("getAllDoctors ERROR: " . $ex->getMessage());
        return [];
    }
}
?>