<?php
// Forzar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Configurar log local
$localLog = __DIR__ . '/debug.log';
ini_set('error_log', $localLog);

echo "<h1>🐛 Debug con Logs Locales</h1>";

// Probar GoogleCalendarModel
echo "<h2>🧪 Probando GoogleCalendarModel:</h2>";

try {
    require_once __DIR__ . '/model/GoogleCalendarModel.php';
    echo "✅ GoogleCalendarModel cargado<br>";
    
    $googleModel = new GoogleCalendarModel();
    echo "✅ Instancia creada<br>";
    
    echo "Estado: " . ($googleModel->isGoogleClientAvailable() ? '✅ DISPONIBLE' : '❌ NO DISPONIBLE') . "<br>";
    
    // Mostrar ruta del log
    echo "<h3>📝 Log file:</h3>";
    $logPath = $googleModel->getDebugLogPath();
    echo "Ruta: $logPath<br>";
    echo "Existe: " . (file_exists($logPath) ? '✅ SÍ' : '❌ NO') . "<br>";
    
    if (file_exists($logPath)) {
        echo "<h3>📋 Contenido del Log:</h3>";
        echo "<pre>" . htmlspecialchars(file_get_contents($logPath)) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "💥 ERROR: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Mostrar log local PHP
echo "<h3>📋 Log PHP Local:</h3>";
echo "Ruta: $localLog<br>";
if (file_exists($localLog)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($localLog)) . "</pre>";
} else {
    echo "❌ Log no creado<br>";
}
?>