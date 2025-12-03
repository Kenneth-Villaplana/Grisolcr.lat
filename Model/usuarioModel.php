<?php
include_once __DIR__ . '/../Model/baseDatos.php';

function ObtenerPerfil($idUsuario){
    try{
        $enlace = AbrirBD();
        $sentencia = $enlace->prepare("CALL ObtenerPerfilUsuario(?)");
        
        $sentencia->bind_param("i", $idUsuario);
        $sentencia->execute();
        
        $resultado = $sentencia->get_result();
        $usuario = $resultado->fetch_assoc();
        
        $sentencia->close();
        CerrarBD($enlace);
        return $usuario;

    }catch(Exception $ex){
        return [];
    }
}

function EditarPerfil($idUsuario, $cedula, $nombre, $apellido, $apellidoDos, $correoElectronico, $telefono, $direccion, $fechaNacimiento = null){
    try{
        $enlace = AbrirBD();
        $sentencia = $enlace->prepare("CALL EditarPerfil(?, ?, ?, ?, ?, ?, ?, ?, ?)");
       
        $sentencia->bind_param("issssssss",
        $idUsuario,
        $cedula,
        $nombre,
        $apellido,
        $apellidoDos,
        $correoElectronico,
        $telefono,
        $direccion,
        $fechaNacimiento);

    $sentencia->execute();
    $sentencia->close();
    CerrarBD($enlace);

    return['resultado'=>1, 'mensaje'=>'Perfil actualizado con éxito'];
    } catch(Exception $ex){
        return ['resultado'=>0, 'mensaje'=>'Error en el servidor: '.$ex->getMessage()];
    }
}

// =============================================
// NUEVAS FUNCIONES PARA GOOGLE CALENDAR
// =============================================

/**
 * Verificar si un doctor tiene Google Calendar conectado
 */
function hasGoogleCalendar($doctorId) {
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
        
        return $userData && $userData['google_calendar_enabled'] && !empty($userData['google_access_token']);
        
    } catch(Exception $ex) {
        error_log("Error checking Google Calendar: " . $ex->getMessage());
        return false;
    }
}

/**
 * Obtener token de Google Calendar de un doctor
 */
function getGoogleToken($doctorId) {
    try {
        $enlace = AbrirBD();
        $sql = "SELECT google_access_token, google_refresh_token 
                FROM usuario 
                WHERE IdUsuario = ? AND google_calendar_enabled = TRUE";
        
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
        
    } catch(Exception $ex) {
        error_log("Error getting Google token: " . $ex->getMessage());
        return null;
    }
}

/**
 * Obtener todos los doctores disponibles
 */
function getAllDoctors() {
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
        
    } catch(Exception $ex) {
        error_log("Error getting all doctors: " . $ex->getMessage());
        return [];
    }
}

/**
 * Guardar token de Google Calendar para un doctor
 */
function saveGoogleToken($userId, $accessToken, $refreshToken = null) {
    try {
        $enlace = AbrirBD();
        $sql = "UPDATE usuario 
                SET google_access_token = ?, 
                    google_refresh_token = ?,
                    google_calendar_enabled = TRUE,
                    google_connected_at = NOW()
                WHERE IdUsuario = ?";
        
        $accessTokenJson = json_encode($accessToken);
        
        $sentencia = $enlace->prepare($sql);
        $sentencia->bind_param("ssi", 
            $accessTokenJson,
            $refreshToken,
            $userId
        );
        
        $resultado = $sentencia->execute();
        $sentencia->close();
        CerrarBD($enlace);
        
        return $resultado;
        
    } catch(Exception $ex) {
        error_log("Error saving Google token: " . $ex->getMessage());
        return false;
    }
}

/**
 * Desconectar Google Calendar
 */
function disconnectGoogleCalendar($userId) {
    try {
        $enlace = AbrirBD();
        $sql = "UPDATE usuario 
                SET google_access_token = NULL,
                    google_refresh_token = NULL,
                    google_calendar_enabled = FALSE
                WHERE IdUsuario = ?";
        
        $sentencia = $enlace->prepare($sql);
        $sentencia->bind_param("i", $userId);
        
        $resultado = $sentencia->execute();
        $sentencia->close();
        CerrarBD($enlace);
        
        return $resultado;
        
    } catch(Exception $ex) {
        error_log("Error disconnecting Google Calendar: " . $ex->getMessage());
        return false;
    }
}

/**
 * Obtener doctores con Google Calendar conectado
 */
function getDoctorsWithGoogleCalendar() {
    try {
        $enlace = AbrirBD();
        $sql = "SELECT u.*, r.NombreRol 
                FROM usuario u
                INNER JOIN personal p ON u.IdUsuario = p.UsuarioId
                INNER JOIN rol r ON p.Id_rol = r.RolId
                WHERE u.google_calendar_enabled = TRUE 
                AND r.NombreRol IN ('Doctor/a', 'Administrador/a')";
        
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
        
    } catch(Exception $ex) {
        error_log("Error getting doctors with Google Calendar: " . $ex->getMessage());
        return [];
    }
}

/**
 * Obtener información completa del usuario incluyendo datos de Google Calendar
 */
function getUserWithGoogleInfo($userId) {
    try {
        $enlace = AbrirBD();
        $sql = "SELECT u.*, 
                       r.NombreRol,
                       p.Id_rol,
                       CASE 
                           WHEN u.google_calendar_enabled = TRUE THEN 'Conectado'
                           ELSE 'No conectado'
                       END as google_calendar_status
                FROM usuario u
                LEFT JOIN personal p ON u.IdUsuario = p.UsuarioId
                LEFT JOIN rol r ON p.Id_rol = r.RolId
                WHERE u.IdUsuario = ?";
        
        $sentencia = $enlace->prepare($sql);
        $sentencia->bind_param("i", $userId);
        $sentencia->execute();
        
        $resultado = $sentencia->get_result();
        $userData = $resultado->fetch_assoc();
        
        $sentencia->close();
        CerrarBD($enlace);
        
        return $userData;
        
    } catch(Exception $ex) {
        error_log("Error getting user with Google info: " . $ex->getMessage());
        return null;
    }
}

/**
 * Verificar si un usuario es doctor
 */
function isDoctor($userId) {
    try {
        $enlace = AbrirBD();
        $sql = "SELECT COUNT(*) as es_doctor
                FROM usuario u
                INNER JOIN personal p ON u.IdUsuario = p.UsuarioId
                INNER JOIN rol r ON p.Id_rol = r.RolId
                WHERE u.IdUsuario = ? 
                AND r.NombreRol IN ('Doctor/a', 'Administrador/a')";
        
        $sentencia = $enlace->prepare($sql);
        $sentencia->bind_param("i", $userId);
        $sentencia->execute();
        
        $resultado = $sentencia->get_result();
        $data = $resultado->fetch_assoc();
        
        $sentencia->close();
        CerrarBD($enlace);
        
        return $data && $data['es_doctor'] > 0;
        
    } catch(Exception $ex) {
        error_log("Error checking if user is doctor: " . $ex->getMessage());
        return false;
    }
}

/**
 * Obtener información básica del usuario
 */
function getUserBasicInfo($userId) {
    try {
        $enlace = AbrirBD();
        $sql = "SELECT IdUsuario, Nombre, Apellido, ApellidoDos, CorreoElectronico, Telefono 
                FROM usuario 
                WHERE IdUsuario = ?";
        
        $sentencia = $enlace->prepare($sql);
        $sentencia->bind_param("i", $userId);
        $sentencia->execute();
        
        $resultado = $sentencia->get_result();
        $userData = $resultado->fetch_assoc();
        
        $sentencia->close();
        CerrarBD($enlace);
        
        return $userData;
        
    } catch(Exception $ex) {
        error_log("Error getting user basic info: " . $ex->getMessage());
        return null;
    }
}

/**
 * Actualizar estado de Google Calendar
 */
function updateGoogleCalendarStatus($userId, $enabled) {
    try {
        $enlace = AbrirBD();
        $sql = "UPDATE usuario 
                SET google_calendar_enabled = ?
                WHERE IdUsuario = ?";
        
        $sentencia = $enlace->prepare($sql);
        $sentencia->bind_param("ii", $enabled, $userId);
        
        $resultado = $sentencia->execute();
        $sentencia->close();
        CerrarBD($enlace);
        
        return $resultado;
        
    } catch(Exception $ex) {
        error_log("Error updating Google Calendar status: " . $ex->getMessage());
        return false;
    }
}
/**
 * Obtener horarios configurados de un doctor
 */
function getDoctorSchedule($doctorId) {
    try {
        $enlace = AbrirBD();
        
        $sql = "SELECT dia_semana, hora_inicio, hora_fin 
                FROM doctor_horarios 
                WHERE doctor_id = ? AND activo = 1";
        
        $sentencia = $enlace->prepare($sql);
        $sentencia->bind_param("i", $doctorId);
        $sentencia->execute();
        
        $resultado = $sentencia->get_result();
        $horarios = [];
        
        while ($fila = $resultado->fetch_assoc()) {
            $horarios[] = $fila;
        }
        
        $sentencia->close();
        CerrarBD($enlace);
        
        // Si no hay horarios configurados, usar horarios por defecto
        if (empty($horarios)) {
            error_log("⚠️ No hay horarios configurados para doctor $doctorId, usando horarios por defecto");
            $horarios = [
                ['dia_semana' => 'lunes', 'hora_inicio' => '09:00:00', 'hora_fin' => '18:00:00'],
                ['dia_semana' => 'martes', 'hora_inicio' => '09:00:00', 'hora_fin' => '18:00:00'],
                ['dia_semana' => 'miercoles', 'hora_inicio' => '09:00:00', 'hora_fin' => '18:00:00'],
                ['dia_semana' => 'jueves', 'hora_inicio' => '09:00:00', 'hora_fin' => '18:00:00'],
                ['dia_semana' => 'viernes', 'hora_inicio' => '09:00:00', 'hora_fin' => '18:00:00']
            ];
        }
        
        return $horarios;
        
    } catch(Exception $ex) {
        error_log("Error obteniendo horarios del doctor: " . $ex->getMessage());
        return [];
    }
}
?>