<?php

// Abre una conexión a la base de datos
function AbrirBD(): mysqli
{
    $host = "localhost";
    $user = "admin";
    $pass = "AdminOptica2026!";
    $db   = "optigestion";
    $port = 3306;

    $conn = new mysqli($host, $user, $pass, $db, $port);

    if ($conn->connect_error) {
        die("Error de conexión a MySQL: " . $conn->connect_error);
    }

    // Configuración de charset
    $conn->set_charset("utf8mb4");

    // Zona horaria
    $conn->query("SET time_zone = '-06:00'");
    date_default_timezone_set('America/Costa_Rica');

    return $conn;
}


// Cierra la conexión a la base de datos
function CerrarBD($enlace)
{
    if ($enlace instanceof mysqli) {
        $enlace->close();
    }
}

?>