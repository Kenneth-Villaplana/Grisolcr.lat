<?php

// Abre una conexion a la base de datos
function AbrirBD()
{
    $conn = mysqli_connect("127.0.0.1:3307", "root", "", "optigestion");

    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    mysqli_query($conn, "SET time_zone = '-06:00'");
    date_default_timezone_set('America/Costa_Rica');

    return $conn;
}

// Cierra la conexion a la base de datos.
function CerrarBD($enlace)
{
    mysqli_close($enlace);
}


?>
