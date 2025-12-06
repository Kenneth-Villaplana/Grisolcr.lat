<?php
require_once 'model/baseDatos.php';

echo "<h1>👨‍⚕️ Crear Doctores Completos + Horarios</h1>";

try {
    $enlace = AbrirBD();
    
    // 1. PRIMERO: Crear doctores si no existen
    echo "<h3>1. Creando doctores...</h3>";
    
    $doctores = [
        [
            'cedula' => '123456789',
            'nombre' => 'Ana', 
            'apellido' => 'Gómez',
            'apellido2' => 'Hernández',
            'email' => 'dra.ana@opticagrisol.com',
            'telefono' => '8888-0001'
        ],
        [
            'cedula' => '987654321', 
            'nombre' => 'Carlos',
            'apellido' => 'Medina',
            'apellido2' => 'López',
            'email' => 'dr.carlos@opticagrisol.com',
            'telefono' => '8888-0002'
        ]
    ];
    
    $doctoresCreados = [];
    
    foreach ($doctores as $doctor) {
        // Verificar si el usuario ya existe
        $sqlCheck = "SELECT IdUsuario FROM usuario WHERE CorreoElectronico = ?";
        $stmt = $enlace->prepare($sqlCheck);
        $stmt->bind_param("s", $doctor['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Usuario ya existe
            $row = $result->fetch_assoc();
            $doctoresCreados[] = $row['IdUsuario'];
            echo "✅ Doctor {$doctor['nombre']} {$doctor['apellido']} ya existe (ID: {$row['IdUsuario']})<br>";
        } else {
            // Crear nuevo usuario
            $sqlUsuario = "INSERT INTO usuario (Cedula, Nombre, Apellido, ApellidoDos, CorreoElectronico, Contrasenna, Telefono, Direccion, RolUsuario, Estado, google_calendar_enabled) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, 'Clínica Central', 'Empleado', 1, 1)";
            
            $contrasennaHash = password_hash('password123', PASSWORD_DEFAULT);
            
            $stmt = $enlace->prepare($sqlUsuario);
            $stmt->bind_param("issssss", 
                $doctor['cedula'], 
                $doctor['nombre'], 
                $doctor['apellido'],
                $doctor['apellido2'],
                $doctor['email'],
                $contrasennaHash,
                $doctor['telefono']
            );
            
            if ($stmt->execute()) {
                $usuarioId = $enlace->insert_id;
                $doctoresCreados[] = $usuarioId;
                echo "✅ Doctor {$doctor['nombre']} {$doctor['apellido']} creado (ID: $usuarioId)<br>";
                
                // Asignar rol de doctor
                $sqlPersonal = "INSERT INTO personal (UsuarioId, Id_rol) VALUES (?, 3)";
                $stmtPersonal = $enlace->prepare($sqlPersonal);
                $stmtPersonal->bind_param("i", $usuarioId);
                $stmtPersonal->execute();
                $stmtPersonal->close();
                
                echo "&nbsp;&nbsp;✅ Rol de doctor asignado<br>";
            } else {
                echo "❌ Error creando doctor {$doctor['nombre']}<br>";
            }
            $stmt->close();
        }
    }
    
    // 2. SEGUNDO: Crear horarios para los doctores
    echo "<h3>2. Creando horarios para doctores...</h3>";
    
    $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];
    $horariosInsertados = 0;
    
    foreach ($doctoresCreados as $doctorId) {
        foreach ($diasSemana as $dia) {
            // Verificar si el horario ya existe
            $sqlCheckHorario = "SELECT id FROM doctor_horarios WHERE doctor_id = ? AND dia_semana = ?";
            $stmt = $enlace->prepare($sqlCheckHorario);
            $stmt->bind_param("is", $doctorId, $dia);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) {
                // Insertar horario
                $sqlInsert = "INSERT INTO doctor_horarios (doctor_id, dia_semana, hora_inicio, hora_fin, activo) 
                             VALUES (?, ?, '09:00:00', '18:00:00', 1)";
                $stmtInsert = $enlace->prepare($sqlInsert);
                $stmtInsert->bind_param("is", $doctorId, $dia);
                
                if ($stmtInsert->execute()) {
                    $horariosInsertados++;
                    echo "✅ Horario creado para doctor ID $doctorId - $dia<br>";
                } else {
                    echo "❌ Error creando horario para doctor ID $doctorId - $dia<br>";
                }
                $stmtInsert->close();
            } else {
                echo "⚠️ Horario ya existe para doctor ID $doctorId - $dia<br>";
            }
            $stmt->close();
        }
    }
    
    // 3. Verificar estado final
    echo "<h3>3. Estado final:</h3>";
    
    // Contar doctores
    $sqlCountDoctores = "SELECT COUNT(*) as total FROM usuario u 
                        INNER JOIN personal p ON u.IdUsuario = p.UsuarioId 
                        WHERE p.Id_rol = 3 AND u.Estado = 1";
    $result = mysqli_query($enlace, $sqlCountDoctores);
    $row = mysqli_fetch_assoc($result);
    echo "Doctores en sistema: {$row['total']}<br>";
    
    // Contar horarios
    $sqlCountHorarios = "SELECT COUNT(*) as total FROM doctor_horarios";
    $result = mysqli_query($enlace, $sqlCountHorarios);
    $row = mysqli_fetch_assoc($result);
    echo "Horarios configurados: {$row['total']}<br>";
    
    CerrarBD($enlace);
    
    echo "<hr>";
    echo "<h3>🎯 Resumen:</h3>";
    if ($row['total'] > 0) {
        echo "✅ <strong>Sistema configurado correctamente</strong><br>";
        echo "Ahora tienes doctores reales con horarios configurados.<br>";
    } else {
        echo "⚠️ <strong>El sistema usará el modo fallback</strong><br>";
        echo "Pero seguirá funcionando con horarios por defecto.<br>";
    }
    
    echo "<p><a href='test-system.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🧪 Probar Sistema Completo</a></p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>