<?php
class GoogleCalendarModel {
    private $client;
    private $service;
    private $calendarId = 'primary';
    private $isGoogleClientAvailable = false;
    private $logFile;

    public function __construct() {
        // Configurar log local
        $this->logFile = __DIR__ . '/google_calendar_debug.log';
        $this->log("🎬 ===== CONSTRUCTOR INICIADO =====");
        $this->log("🔍 Directorio actual: " . __DIR__);
        
        $this->checkGoogleClientAvailability();
        
        if ($this->isGoogleClientAvailable) {
            $this->initializeGoogleClient();
        }
        
        $this->log("🎬 ===== CONSTRUCTOR FINALIZADO - Disponible: " . ($this->isGoogleClientAvailable ? 'SÍ' : 'NO') . " =====");
    }

    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }

    private function checkGoogleClientAvailability() {
        $this->log("🔍 checkGoogleClientAvailability() llamado");
        
        try {
            // RUTA CORREGIDA - usar ruta absoluta desde la raíz del proyecto
            $autoloadPath = realpath(__DIR__ . '/../../vendor/autoload.php');
            $this->log("🔍 Ruta autoload (realpath): $autoloadPath");
            
            // Alternativa: ruta directa desde raíz
            $rootPath = realpath(__DIR__ . '/../../');
            $directPath = $rootPath . '/vendor/autoload.php';
            $this->log("🔍 Ruta directa: $directPath");
            
            // Probar múltiples rutas
            $possiblePaths = [
                $autoloadPath,
                $directPath,
                'C:/xampp/htdocs/OptiGestion/vendor/autoload.php',
                __DIR__ . '/../../vendor/autoload.php'
            ];
            
            $autoloadFound = false;
            $foundPath = '';
            
            foreach ($possiblePaths as $path) {
                if ($path && file_exists($path)) {
                    $autoloadFound = true;
                    $foundPath = $path;
                    $this->log("✅ AUTOLOAD ENCONTRADO en: $path");
                    break;
                }
            }
            
            if (!$autoloadFound) {
                $this->log("❌ AUTOLOAD NO ENCONTRADO en ninguna ruta");
                $this->log("🔍 Rutas probadas:");
                foreach ($possiblePaths as $path) {
                    $this->log("  - $path: " . (file_exists($path) ? 'EXISTE' : 'NO EXISTE'));
                }
                $this->isGoogleClientAvailable = false;
                return false;
            }
            
            // Cargar autoload
            require_once $foundPath;
            $this->log("✅ Autoload cargado desde: $foundPath");
            
            // Verificar clases
            $classesToCheck = ['Google_Client', 'Google_Service_Calendar'];
            foreach ($classesToCheck as $className) {
                $exists = class_exists($className);
                $this->log("🔍 $className: " . ($exists ? '✅ EXISTE' : '❌ NO EXISTE'));
                if (!$exists) {
                    $this->isGoogleClientAvailable = false;
                    return false;
                }
            }
            
            $this->isGoogleClientAvailable = true;
            $this->log("🎯 ✅ Google Client disponible");
            return true;
            
        } catch (Exception $e) {
            $this->log("💥 ERROR en check: " . $e->getMessage());
            $this->isGoogleClientAvailable = false;
            return false;
        }
    }

    private function initializeGoogleClient() {
        $this->log("🔧 initializeGoogleClient() llamado");
        
        try {
            $this->client = new Google_Client();
            $this->log("✅ Google_Client instanciado");
            
            $this->client->setApplicationName('OptiGestion - Sistema de Citas');
            $this->log("✅ ApplicationName configurado");
            
            $this->client->setScopes(Google_Service_Calendar::CALENDAR);
            $this->log("✅ Scopes configurados");
            
            $this->client->setAccessType('offline');
            $this->client->setPrompt('select_account consent');
            $this->log("✅ AccessType configurado");
            
            // Configurar credenciales
            $credsPath = realpath(__DIR__ . '/../../config/google-credentials.json');
            $this->log("🔍 Ruta credenciales: $credsPath");
            
            if ($credsPath && file_exists($credsPath)) {
                $this->client->setAuthConfig($credsPath);
                $this->log("✅ AuthConfig desde archivo EXITOSO");
            } else {
                $this->log("⚠️ Usando credenciales manuales");
                $this->client->setClientId('3214735701-bnnvh8efvug2sd9j2urqiejlhc3gmeco.apps.googleusercontent.com');
                $this->client->setClientSecret('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');
                $this->log("✅ Credenciales manuales configuradas");
            }
            
            $redirectUri = 'http://localhost/OptiGestion/google_calendar_callback.php';
            $this->client->setRedirectUri($redirectUri);
            $this->log("✅ Redirect URI: $redirectUri");
            
            $this->log("🎯 ✅ Google Client INICIALIZADO EXITOSAMENTE");
            return true;
            
        } catch (Exception $e) {
            $this->log("💥 ERROR CRÍTICO en initialize: " . $e->getMessage());
            $this->isGoogleClientAvailable = false;
            return false;
        }
    }

    /**
     * Manejar el callback de OAuth y guardar tokens en la base de datos
     */
    public function handleOAuthCallback($code, $doctorId) {
        $this->log("🔄 handleOAuthCallback() llamado para doctor: $doctorId");
        
        if (!$this->isGoogleClientAvailable) {
            $this->log("❌ Google Client no disponible para OAuth");
            return false;
        }

        try {
            // Intercambiar código por tokens
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($accessToken['error'])) {
                throw new Exception('Error OAuth: ' . $accessToken['error']);
            }
            
            $this->client->setAccessToken($accessToken);
            
            // Guardar tokens en la base de datos para el doctor
            $this->saveTokensToDatabase($doctorId, $accessToken);
            
            $this->log("✅ OAuth completado exitosamente para doctor: $doctorId");
            return true;
            
        } catch (Exception $e) {
            $this->log("💥 ERROR en handleOAuthCallback: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Guardar tokens en la base de datos
     */
    private function saveTokensToDatabase($doctorId, $tokens) {
        $conn = $this->getDatabaseConnection();
        
        $accessToken = json_encode($tokens);
        $refreshToken = isset($tokens['refresh_token']) ? $tokens['refresh_token'] : null;
        $expiresAt = date('Y-m-d H:i:s', time() + $tokens['expires_in']);
        
        $query = "UPDATE usuario u 
                  INNER JOIN personal p ON u.IdUsuario = p.UsuarioId 
                  SET u.google_calendar_enabled = 1,
                      u.google_access_token = ?,
                      u.google_refresh_token = ?,
                      u.google_token_expires_at = ?,
                      u.google_connected_at = NOW()
                  WHERE p.EmpleadoId = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "sssi", $accessToken, $refreshToken, $expiresAt, $doctorId);
        $result = mysqli_stmt_execute($stmt);
        
        if (!$result) {
            throw new Exception("Error ejecutando consulta: " . mysqli_stmt_error($stmt));
        }
        
        mysqli_stmt_close($stmt);
        $this->closeDatabaseConnection($conn);
        
        $this->log("💾 Tokens guardados en BD para doctor: $doctorId");
    }

    /**
     * Cargar tokens desde la base de datos para un doctor específico
     */
    public function loadTokensForDoctor($doctorId) {
        $this->log("🔍 loadTokensForDoctor() llamado para doctor: $doctorId");
        
        if (!$this->isGoogleClientAvailable) {
            $this->log("❌ Google Client no disponible");
            return false;
        }

        $conn = $this->getDatabaseConnection();
        
        $query = "SELECT u.google_access_token, u.google_refresh_token, u.google_token_expires_at
                  FROM usuario u 
                  INNER JOIN personal p ON u.IdUsuario = p.UsuarioId 
                  WHERE p.EmpleadoId = ? AND u.google_calendar_enabled = 1";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $doctorId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $tokenData = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        $this->closeDatabaseConnection($conn);
        
        if ($tokenData && $tokenData['google_access_token']) {
            $accessToken = json_decode($tokenData['google_access_token'], true);
            
            // Verificar si el token necesita refresh
            if ($this->isTokenExpired($tokenData['google_token_expires_at'])) {
                $this->log("🔄 Token expirado, refrescando...");
                $accessToken = $this->refreshAccessToken($tokenData['google_refresh_token'], $doctorId);
            }
            
            if ($accessToken) {
                $this->client->setAccessToken($accessToken);
                $this->log("✅ Tokens cargados para doctor: $doctorId");
                return true;
            }
        }
        
        $this->log("❌ No hay tokens válidos para doctor: $doctorId");
        return false;
    }

    /**
     * Verificar si el token está expirado
     */
    private function isTokenExpired($expiresAt) {
        return strtotime($expiresAt) <= time();
    }

    /**
     * Refrescar access token
     */
    private function refreshAccessToken($refreshToken, $doctorId) {
        $this->log("🔄 refreshAccessToken() para doctor: $doctorId");
        
        if (!$refreshToken) {
            $this->log("❌ No hay refresh token disponible");
            return false;
        }

        try {
            $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            $newToken = $this->client->getAccessToken();
            
            // Actualizar en base de datos
            $this->saveTokensToDatabase($doctorId, $newToken);
            
            $this->log("✅ Token refrescado para doctor: $doctorId");
            return $newToken;
            
        } catch (Exception $e) {
            $this->log("💥 Error refrescando token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener conexión a la base de datos
     */
    private function getDatabaseConnection() {
        // Incluir el archivo de base de datos
        $baseDatosPath = __DIR__ . '/../../Model/baseDatos.php';
        if (file_exists($baseDatosPath)) {
            include_once $baseDatosPath;
        }
        
        // Asumiendo que tienes la función AbrirBD()
        if (function_exists('AbrirBD')) {
            return AbrirBD();
        } else {
            throw new Exception("Función AbrirBD no encontrada");
        }
    }

    /**
     * Cerrar conexión a la base de datos
     */
    private function closeDatabaseConnection($conn) {
        if (function_exists('CerrarBD')) {
            CerrarBD($conn);
        }
    }

    public function isGoogleClientAvailable() {
        return $this->isGoogleClientAvailable;
    }

    public function getDebugLogPath() {
        return $this->logFile;
    }

    // Método para verificar disponibilidad en el calendario
    public function checkAvailability($startTime, $endTime, $calendarId = 'primary') {
        $this->log("🔍 checkAvailability() llamado - Inicio: $startTime, Fin: $endTime");
        
        if (!$this->isGoogleClientAvailable) {
            $this->log("❌ Google Client no disponible para checkAvailability");
            return ['available' => false, 'error' => 'Google Client no disponible'];
        }

        try {
            // Inicializar el servicio si no existe
            if (!$this->service) {
                $this->service = new Google_Service_Calendar($this->client);
                $this->log("✅ Google_Service_Calendar inicializado en checkAvailability");
            }

            // Verificar si tenemos token de acceso válido
            $accessToken = $this->client->getAccessToken();
            if (!$accessToken || $this->client->isAccessTokenExpired()) {
                $this->log("❌ Token de acceso no válido o expirado");
                return ['available' => false, 'error' => 'Token de acceso no válido o expirado'];
            }

            // Configurar los parámetros para la consulta de eventos
            $optParams = [
                'timeMin' => $startTime,
                'timeMax' => $endTime,
                'singleEvents' => true,
                'orderBy' => 'startTime'
            ];

            $this->log("🔍 Consultando eventos en calendario: $calendarId");
            $events = $this->service->events->listEvents($calendarId, $optParams);
            $conflictingEvents = $events->getItems();
            $conflictCount = count($conflictingEvents);

            $this->log("🔍 Eventos conflictivos encontrados: $conflictCount");

            // Si hay eventos conflictivos, loggear detalles
            if ($conflictCount > 0) {
                foreach ($conflictingEvents as $event) {
                    $eventStart = $event->getStart()->getDateTime();
                    $eventEnd = $event->getEnd()->getDateTime();
                    $eventSummary = $event->getSummary() ?: 'Sin título';
                    $this->log("❌ Evento conflictivo: $eventSummary ($eventStart - $eventEnd)");
                }
                return [
                    'available' => false,
                    'conflicts' => $conflictCount,
                    'conflicting_events' => $conflictingEvents
                ];
            }

            $this->log("✅ Horario disponible: $startTime - $endTime");
            return [
                'available' => true,
                'message' => 'Horario disponible'
            ];

        } catch (Exception $e) {
            $errorMsg = "💥 ERROR en checkAvailability: " . $e->getMessage();
            $this->log($errorMsg);
            return [
                'available' => false,
                'error' => $errorMsg
            ];
        }
    }

    // Método para crear un evento en el calendario
    public function createEvent($eventData, $calendarId = 'primary') {
        $this->log("🎯 createEvent() llamado");
        
        if (!$this->isGoogleClientAvailable) {
            $this->log("❌ Google Client no disponible para createEvent");
            return ['success' => false, 'error' => 'Google Client no disponible'];
        }

        try {
            // Inicializar el servicio si no existe
            if (!$this->service) {
                $this->service = new Google_Service_Calendar($this->client);
                $this->log("✅ Google_Service_Calendar inicializado en createEvent");
            }

            // Verificar si tenemos token de acceso válido
            $accessToken = $this->client->getAccessToken();
            if (!$accessToken || $this->client->isAccessTokenExpired()) {
                $this->log("❌ Token de acceso no válido o expirado");
                return ['success' => false, 'error' => 'Token de acceso no válido o expirado'];
            }

            // Primero verificar disponibilidad
            $availability = $this->checkAvailability(
                $eventData['start_time'], 
                $eventData['end_time'], 
                $calendarId
            );

            if (!$availability['available']) {
                $this->log("❌ No se puede crear evento - Horario no disponible");
                return [
                    'success' => false, 
                    'error' => 'Horario no disponible',
                    'conflicts' => $availability['conflicts'] ?? 0
                ];
            }

            // Crear el objeto evento
            $event = new Google_Service_Calendar_Event([
                'summary' => $eventData['summary'] ?? 'Cita OptiGestion',
                'description' => $eventData['description'] ?? 'Cita creada desde el sistema OptiGestion',
                'start' => [
                    'dateTime' => $eventData['start_time'],
                    'timeZone' => $eventData['timezone'] ?? 'America/Costa_Rica',
                ],
                'end' => [
                    'dateTime' => $eventData['end_time'],
                    'timeZone' => $eventData['timezone'] ?? 'America/Costa_Rica',
                ],
                'attendees' => $eventData['attendees'] ?? [],
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'email', 'minutes' => 24 * 60],
                        ['method' => 'popup', 'minutes' => 30],
                    ],
                ],
            ]);

            $this->log("✅ Objeto evento creado - Resumen: " . ($eventData['summary'] ?? 'Sin título'));

            // Insertar el evento en el calendario
            $createdEvent = $this->service->events->insert($calendarId, $event);
            
            $eventId = $createdEvent->getId();
            $eventLink = $createdEvent->getHtmlLink();

            $this->log("🎉 EVENTO CREADO EXITOSAMENTE - ID: $eventId");
            $this->log("🔗 Enlace del evento: $eventLink");

            return [
                'success' => true,
                'event_id' => $eventId,
                'event_link' => $eventLink,
                'event' => $createdEvent,
                'message' => 'Evento creado exitosamente'
            ];

        } catch (Exception $e) {
            $errorMsg = "💥 ERROR CRÍTICO en createEvent: " . $e->getMessage();
            $this->log($errorMsg);
            return [
                'success' => false,
                'error' => $errorMsg
            ];
        }
    }

    // Método para obtener la URL de autenticación
    public function getAuthUrl() {
        if (!$this->isGoogleClientAvailable) {
            $this->log("❌ Google Client no disponible para getAuthUrl");
            return null;
        }
        
        try {
            $authUrl = $this->client->createAuthUrl();
            $this->log("🔗 URL de autenticación generada");
            return $authUrl;
        } catch (Exception $e) {
            $this->log("💥 Error en getAuthUrl: " . $e->getMessage());
            return null;
        }
    }

    // Método para establecer el token de acceso
    public function setAccessToken($token) {
        if (!$this->isGoogleClientAvailable) {
            $this->log("❌ Google Client no disponible para setAccessToken");
            return;
        }
        
        try {
            $this->client->setAccessToken($token);
            $this->log("✅ Token de acceso establecido");
            
            // Verificar si el token está expirado y refrescar si es necesario
            if ($this->client->isAccessTokenExpired()) {
                $this->log("🔄 Token expirado, intentando refrescar...");
                $this->refreshToken();
            }
        } catch (Exception $e) {
            $this->log("💥 Error en setAccessToken: " . $e->getMessage());
        }
    }

    // Método para refrescar el token (método público para uso externo)
    public function refreshToken() {
        if (!$this->isGoogleClientAvailable) {
            return false;
        }
        
        try {
            $refreshToken = $this->client->getRefreshToken();
            if ($refreshToken) {
                $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                $newToken = $this->client->getAccessToken();
                $this->log("✅ Token refrescado exitosamente");
                return $newToken;
            } else {
                $this->log("❌ No hay refresh token disponible");
                return false;
            }
        } catch (Exception $e) {
            $this->log("💥 Error refrescando token: " . $e->getMessage());
            return false;
        }
    }

    // Método para verificar si un doctor tiene Google Calendar conectado
    public function isDoctorConnected($doctorId) {
        $conn = $this->getDatabaseConnection();
        
        $query = "SELECT u.google_calendar_enabled 
                  FROM usuario u 
                  INNER JOIN personal p ON u.IdUsuario = p.UsuarioId 
                  WHERE p.EmpleadoId = ? AND u.google_calendar_enabled = 1";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $doctorId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        $isConnected = mysqli_stmt_num_rows($stmt) > 0;
        
        mysqli_stmt_close($stmt);
        $this->closeDatabaseConnection($conn);
        
        $this->log("🔍 Doctor $doctorId conectado: " . ($isConnected ? 'SÍ' : 'NO'));
        return $isConnected;
    }

    // Método para obtener información del calendario
    public function getCalendarInfo($calendarId = 'primary') {
        if (!$this->isGoogleClientAvailable) {
            return ['success' => false, 'error' => 'Google Client no disponible'];
        }
        
        try {
            if (!$this->service) {
                $this->service = new Google_Service_Calendar($this->client);
            }
            
            $calendar = $this->service->calendars->get($calendarId);
            
            return [
                'success' => true,
                'id' => $calendar->getId(),
                'summary' => $calendar->getSummary(),
                'description' => $calendar->getDescription(),
                'timezone' => $calendar->getTimeZone()
            ];
        } catch (Exception $e) {
            $this->log("💥 Error obteniendo info del calendario: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
?>