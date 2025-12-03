<?php
require_once 'model/baseDatos.php';

echo "<h1>🔍 Estado Actual del Sistema</h1>";

try {
    $enlace = AbrirBD();
    
    echo "<h3>1. Doctores en el sistema:</h3>";
    $sql = "SELECT u.IdUsuario, u.Nombre, u.Apellido, u.CorreoElectronico, u.google_calendar_enabled, r.NombreRol
            FROM usuario u
            INNER JOIN personal p ON u.IdUsuario = p.UsuarioId
            INNER JOIN rol r ON p.Id_rol = r.RolId
            WHERE u.Estado = 1";
    
    $result = mysqli_query($enlace, $sql);
    $doctores = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $doctores[] = $row;
    }
    
    if (empty($doctores)) {
        echo "❌ <strong>No hay doctores en el sistema</strong><br>";
        echo "Ejecuta: <a href='crear-doctores-completo.php'>crear-doctores-completo.php</a><br>";
    } else {
        echo "✅ <strong>Doctores encontrados: " . count($doctores) . "</strong><br>";
        foreach ($doctores as $doc) {
            echo "▪ Dr. {$doc['Nombre']} {$doc['Apellido']}";
            echo " - {$doc['NombreRol']}";
            echo " - Google Calendar: " . ($doc['google_calendar_enabled'] ? '✅' : '❌');
            echo "<br>";
        }
    }
    
    echo "<h3>2. Horarios configurados:</h3>";
    $sql = "SELECT COUNT(*) as total FROM doctor_horarios";
    $result = mysqli_query($enlace, $sql);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['total'] == 0) {
        echo "❌ <strong>No hay horarios configurados</strong><br>";
        echo "Ejecuta: <a href='crear-doctores-completo.php'>crear-doctores-completo.php</a><br>";
    } else {
        echo "✅ <strong>Horarios configurados: {$row['total']}</strong><br>";
    }
    
    CerrarBD($enlace);
    
    echo "<hr>";
    echo "<h3>🚀 Acciones Recomendadas:</h3>";
    if (empty($doctores)) {
        echo "1. <a href='crear-doctores-completo.php'>Crear doctores y horarios</a><br>";
    } elseif ($row['total'] == 0) {
        echo "1. <a href='crear-doctores-completo.php'>Crear horarios para doctores existentes</a><br>";
    } else {
        echo "1. <a href='test-system.php'>✅ El sistema está listo - Probar ahora</a><br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>