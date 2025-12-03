<?php
echo "<h1>🔍 Buscando Archivos de Logs</h1>";

// Posibles ubicaciones de logs
$possibleLogPaths = [
    'C:\xampp\php\logs\php_error_log',
    'C:\xampp\apache\logs\error.log', 
    'C:\xampp\php\error_log',
    'C:\xampp\htdocs\OptiGestion\php_errors.log',
    __DIR__ . '\php_errors.log',
    ini_get('error_log')
];

echo "<h3>📁 Ubicaciones verificadas:</h3>";
foreach ($possibleLogPaths as $path) {
    $exists = file_exists($path);
    echo "<strong>$path</strong>: " . ($exists ? '✅ EXISTE' : '❌ NO EXISTE');
    if ($exists) {
        echo " - Tamaño: " . filesize($path) . " bytes";
        echo " - Modificado: " . date('Y-m-d H:i:s', filemtime($path));
    }
    echo "<br>";
}

// Crear un log local
$localLogPath = __DIR__ . '/php_errors.log';
ini_set('error_log', $localLogPath);
error_log("=== TEST LOG MESSAGE ===");

echo "<h3>📝 Log local creado en:</h3>";
echo $localLogPath . " - " . (file_exists($localLogPath) ? '✅ CREADO' : '❌ NO CREADO');

// Forzar un error para ver dónde se guarda
echo "<h3>🧪 Forzando errores de prueba:</h3>";
trigger_error("Este es un error de prueba PHP", E_USER_WARNING);

// Información de configuración PHP
echo "<h3>⚙️ Configuración PHP:</h3>";
echo "error_log: " . ini_get('error_log') . "<br>";
echo "log_errors: " . ini_get('log_errors') . "<br>";
echo "display_errors: " . ini_get('display_errors') . "<br>";
echo "error_reporting: " . ini_get('error_reporting') . "<br>";

echo "<hr>";
echo "<p><a href='check-logs.php'>🔧 Continuar con Debug</a></p>";
?>