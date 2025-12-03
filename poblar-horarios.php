<?php
require_once 'model/baseDatos.php';

echo "<h1>⏰ Poblando Horarios de Doctores</h1>";

try {
    $enlace = AbrirBD();
    
    // 1. Verificar doctores existentes
    echo "<h3>1. Buscando doctores...</h3>";
    $sqlDoctores = "SELECT u.IdUsuario, u.Nombre, u.Apellido 
                   FROM usuario u 
                   INNER JOIN personal p ON u.IdUsuario = p.UsuarioId 
                   WHERE p.Id_rol = 3 AND u.Estado = 1";
    $result = mysqli_query($enlace, $sqlDoctores);
    
    $doctores = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $doctores[] = $row;
    }
    
    echo "Doctores encontrados: " . count($doctores) . "<br>";
    foreach ($doctores as $doc) {
        echo "▪ Dr. {$doc['Nombre']} {$doc['Apellido']} (ID: {$doc['IdUsuario']})<br>";
    }
    
    // 2. Poblar horarios
    echo "<h3>2. Insertando horarios...</h3>";
    
    $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];
    $horariosInsertados = 0;
    
    foreach ($doctores as $doctor) {
        foreach ($diasSemana as $dia) {
            $sqlInsert = "INSERT INTO doctor_horarios (doctor_id, dia_semana, hora_inicio, hora_fin, activo) 
                         VALUES (?, ?, '09:00:00', '18:00:00', 1)";
            $stmt = $enlace->prepare($sqlInsert);
            $stmt->bind_param("is", $doctor['IdUsuario'], $dia);
            
            if ($stmt->execute()) {
                $horariosInsertados++;
                echo "✅ Horario para Dr. {$doctor['Nombre']} - $dia<br>";
            } else {
                echo "❌ Error insertando horario para Dr. {$doctor['Nombre']} - $dia<br>";
            }
            $stmt->close();
        }
    }
    
    // 3. Verificar resultado
    echo "<h3>3. Resultado final:</h3>";
    $sqlCount = "SELECT COUNT(*) as total FROM doctor_horarios";
    $result = mysqli_query($enlace, $sqlCount);
    $row = mysqli_fetch_assoc($result);
    
    echo "Total horarios en BD: {$row['total']}<br>";
    echo "Horarios insertados en esta ejecución: $horariosInsertados<br>";
    
    CerrarBD($enlace);
    
    echo "<hr>";
    echo "<h3>🎯 Estado del Sistema:</h3>";
    if ($row['total'] > 0) {
        echo "✅ <strong>Horarios configurados correctamente</strong><br>";
        echo "El sistema ahora usará los horarios reales de la BD en lugar del fallback.<br>";
    } else {
        echo "⚠️ <strong>No se pudieron insertar horarios</strong><br>";
        echo "El sistema continuará usando el fallback de 9 AM - 6 PM.<br>";
    }
    
    echo "<p><a href='test-system.php'>🧪 Probar sistema completo</a></p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>