<?php


// Abre una conexion a la base de datos con SSL
function AbrirBD()
{
    $host = "op-server.mysql.database.azure.com";
    $username = "wmlcrcnljk";
    $password = "6QnST41bR3b";
    $dbname = "op-database";
    $port = 3306;
    
    // Inicializar conexión mysqli (PHP 8.2)
    $conn = mysqli_init();
    
    // Configuración SSL para Azure MySQL Flexible Server
    // No requiere descargar archivos de certificado
    mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
    
    // Usar certificados del sistema Windows
    mysqli_ssl_set(
        $conn,
        NULL,   // ssl_key - no necesario
        NULL,   // ssl_cert - no necesario  
        NULL,   // ssl_ca - NULL usa certificados del sistema Windows
        NULL,   // ssl_capath
        NULL    // ssl_cipher - auto-detecta
    );
    
    // Conectar con SSL obligatorio
    if (!mysqli_real_connect(
        $conn, 
        $host, 
        $username, 
        $password, 
        $dbname, 
        $port,
        NULL,  // socket
        MYSQLI_CLIENT_SSL  // Forzar conexión SSL
    )) {
        // Error más detallado
        $error_msg = "Error de conexión SSL a Azure MySQL: " . mysqli_connect_error();
        $error_msg .= " (Código: " . mysqli_connect_errno() . ")";
        error_log($error_msg);
        die($error_msg);
    }
    
    // Configuraciones adicionales
    mysqli_query($conn, "SET time_zone = '-06:00'");
    mysqli_set_charset($conn, "utf8mb4"); // Mejor soporte Unicode
    date_default_timezone_set('America/Costa_Rica');
    
    // Opcional: Verificar que SSL está activo (para debug)
    $result = mysqli_query($conn, "SHOW STATUS LIKE 'Ssl_cipher'");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if (empty($row['Value'])) {
            error_log("ADVERTENCIA: Conexión a Azure MySQL podría no estar cifrada");
        }
    }
    
    return $conn;
}

// Cierra la conexion a la base de datos.
function CerrarBD($enlace)
{
    mysqli_close($enlace);
}

?>
