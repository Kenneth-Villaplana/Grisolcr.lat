<?php
session_start();

// Cargar autoload de Composer
require_once __DIR__ . '/vendor/autoload.php';

// Cargar modelos con rutas absolutas
require_once __DIR__ . '/model/GoogleCalendarModel.php';
require_once __DIR__ . '/model/UsuarioModel.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>🧪 Test del Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .debug { background: #f5f5f5; padding: 10px; margin: 10px 0; border-left: 4px solid #ccc; }
    </style>
</head>
<body>";

echo "<h1>🧪 Prueba del Sistema de Citas</h1>";

// Primero verificar que los archivos se cargaron
echo "<h2>🔍 Verificando archivos cargados:</h2>";
echo "GoogleCalendarModel: " . (class_exists('GoogleCalendarModel') ? '<span class="success">✅ CARGADO</span>' : '<span class="error">❌ NO CARGADO</span>') . "<br>";
echo "UserModel functions: " . (function_exists('getAllDoctors') ? '<span class="success">✅ CARGADO</span>' : '<span class="error">❌ NO CARGADO</span>') . "<br>";

// Probar Google Calendar Model
echo "<h2>1. Probando GoogleCalendarModel</h2>";
$googleModel = new GoogleCalendarModel();

if ($googleModel->isGoogleClientAvailable()) {
    echo '<span class="success">✅ Google API Client está disponible!</span><br>';
    echo '<span class="success">✅ Las clases de Google se cargaron correctamente</span><br>';
    
    // Probar URL de autenticación
    $authUrl = $googleModel->getAuthUrl();
    if ($authUrl) {
        echo '<span class="success">✅ URL de auth generada correctamente</span><br>';
        echo "URL: <a href='$authUrl' target='_blank'>$authUrl</a><br>";
    } else {
        echo '<span class="error">❌ No se pudo generar URL de auth</span><br>';
    }
} else {
    echo '<span class="error">❌ Google API Client NO está disponible</span><br>';
    echo "<div class='debug'>";
    echo "<strong>Posibles causas:</strong><br>";
    echo "• El archivo google-credentials.json no se carga correctamente<br>";
    echo "• Problema con las rutas en el modelo<br>";
    echo "• Error de configuración en GoogleCalendarModel<br>";
    echo "</div>";
}

// Probar UserModel
echo "<h2>2. Probando UserModel</h2>";
try {
    $doctores = getAllDoctors();
    if (!empty($doctores)) {
        echo '<span class="success">✅ UserModel funciona correctamente</span><br>';
        echo '<span class="success">✅ Se encontraron ' . count($doctores) . ' doctores</span><br>';
        
        echo "<div style='margin-left: 20px;'>";
        foreach ($doctores as $doctor) {
            echo "▪ <strong>Dr. " . $doctor['Nombre'] . " " . $doctor['Apellido'] . "</strong>";
            if (isset($doctor['google_calendar_enabled']) && $doctor['google_calendar_enabled']) {
                echo " <span class='success'>(Google Calendar ✅)</span>";
            } else {
                echo " <span class='warning'>(Sin Google Calendar)</span>";
            }
            echo " - " . ($doctor['CorreoElectronico'] ?? 'Sin email') . "<br>";
        }
        echo "</div>";
    } else {
        echo '<span class="warning">⚠️ No se encontraron doctores en la base de datos</span><br>';
        echo "<div class='debug'>";
        echo "<strong>Solución:</strong> La función getAllDoctors() está devolviendo un array vacío.<br>";
        echo "Esto puede ser porque:<br>";
        echo "• No hay usuarios con rol de doctor en la BD<br>";
        echo "• La consulta SQL no está encontrando resultados<br>";
        echo "• Hay un problema con la estructura de las tablas<br>";
        echo "</div>";
    }
} catch (Exception $e) {
    echo '<span class="error">❌ Error en UserModel: ' . $e->getMessage() . '</span><br>';
}

// Probar horarios disponibles
echo "<h2>3. Probando horarios disponibles</h2>";
$fechaPrueba = date('Y-m-d', strtotime('+1 day'));
$slots = $googleModel->getAvailableSlots($fechaPrueba);

if (!empty($slots)) {
    echo '<span class="success">✅ Sistema de horarios funcionando</span><br>';
    echo '<span class="success">✅ ' . count($slots) . ' horarios disponibles para ' . $fechaPrueba . '</span><br>';
    
    echo "<div style='margin-left: 20px;'>";
    echo "<strong>Horarios:</strong> ";
    $displaySlots = array_slice($slots, 0, 5); // Mostrar solo 5
    foreach ($displaySlots as $slot) {
        echo $slot['display'] . " ";
    }
    if (count($slots) > 5) {
        echo "... <span class='warning'>(+" . (count($slots) - 5) . " más)</span>";
    }
    echo "<br>";
    echo "</div>";
} else {
    echo '<span class="error">❌ No se generaron horarios</span><br>';
}

// Resumen del sistema
echo "<hr>";
echo "<h2>🎯 Resumen del Sistema:</h2>";

$googleStatus = $googleModel->isGoogleClientAvailable() ? '<span class="success">✅ LISTO</span>' : '<span class="error">❌ FALTA CONFIGURAR</span>';
$dbStatus = !empty($doctores) ? '<span class="success">✅ CONECTADA</span>' : '<span class="error">❌ ERROR</span>';
$appointmentStatus = !empty($slots) ? '<span class="success">✅ FUNCIONAL</span>' : '<span class="error">❌ PROBLEMAS</span>';

echo "<strong>Google Calendar:</strong> " . $googleStatus . "<br>";
echo "<strong>Base de Datos:</strong> " . $dbStatus . "<br>";
echo "<strong>Sistema de Citas:</strong> " . $appointmentStatus . "<br>";

// Información de debug
echo "<hr>";
echo "<h2>🔍 Información de Debug:</h2>";

echo "<strong>Rutas verificadas:</strong><br>";
echo "Vendor path: " . __DIR__ . '/vendor/autoload.php' . "<br>";
echo "File exists: " . (file_exists(__DIR__ . '/vendor/autoload.php') ? '<span class="success">Yes</span>' : '<span class="error">No</span>') . "<br>";

echo "<strong>Configuración Google:</strong><br>";
$credsFile = __DIR__ . '/config/google-credentials.json';
echo "Credenciales path: " . $credsFile . "<br>";
echo "Credenciales existen: " . (file_exists($credsFile) ? '<span class="success">Yes</span>' : '<span class="error">No</span>') . "<br>";

if (file_exists($credsFile)) {
    $content = file_get_contents($credsFile);
    $data = json_decode($content, true);
    echo "JSON válido: " . (json_last_error() === JSON_ERROR_NONE ? '<span class="success">Yes</span>' : '<span class="error">No - ' . json_last_error_msg() . '</span>') . "<br>";
    
    if (isset($data['web']['redirect_uris'])) {
        echo "Redirect URIs: " . count($data['web']['redirect_uris']) . " configuradas<br>";
        foreach ($data['web']['redirect_uris'] as $uri) {
            echo "&nbsp;&nbsp;▪ " . $uri . "<br>";
        }
    }
}

// Enlace al sistema principal
echo "<hr>";
echo "<h2>🚀 Próximos Pasos:</h2>";

if ($googleModel->isGoogleClientAvailable() && !empty($doctores)) {
    echo '<p><a href="app/views/citas/agendar.php" style="background: #4CAF50; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-size: 16px; display: inline-block;">🚀 Ir al Sistema de Citas Principal</a></p>';
    echo "<p><strong>¡El sistema está listo para usar!</strong> Puedes proceder con la interfaz principal de citas.</p>";
} else {
    echo "<div class='debug'>";
    echo "<strong>Problemas detectados que necesitan solución:</strong><br>";
    
    if (!$googleModel->isGoogleClientAvailable()) {
        echo "• <span class='error'>Google Calendar no está configurado correctamente</span><br>";
    }
    
    if (empty($doctores)) {
        echo "• <span class='error'>No se encontraron doctores en la base de datos</span><br>";
    }
    
    echo "<br><strong>Soluciones:</strong><br>";
    echo "1. Revisa los logs de error de PHP<br>";
    echo "2. Verifica la configuración de Google Calendar<br>";
    echo "3. Asegúrate de que hay doctores en la base de datos<br>";
    echo "</div>";
}

echo "</body>
</html>";
?>