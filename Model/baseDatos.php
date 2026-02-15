<?php

// Abre una conexion a la base de datos

function AbrirBD(): mysqli
{
    $host = "grisolcr-server.mysql.database.azure.com";
    $user = "hvehxcvtoq";
    $pass = "Y$4JjJZJIxHieE2G";
    $db   = "optigestion";
    $port = 3306;

    $conn = mysqli_init();

    mysqli_real_connect(
        $conn,
        $host,
        $user,
        $pass,
        $db,
        $port,
        NULL,
        MYSQLI_CLIENT_SSL
    );


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