<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


function obtenerPermisosVista($vista) {

    return [

        // públicas
        'index.php' => ['publico' => true],
        'about.php' => ['publico' => true],
        'anteojos.php' => ['publico' => true],
        'iniciarSesion.php' => ['publico' => true],

        // requieren login 
        'agendarCita.php' => ['login' => true],
        'editarCita.php' => ['login' => true],
        'misRecetas.php' => ['login' => true],
        'verReceta.php' => ['login' => true],

        'reportes.php' => ['login' => true],
        'inventario.php' => ['login' => true],
        'facturacion.php' => ['login' => true],
        'historialCierreCaja.php' => ['login' => true],
        'puntoVenta.php' => ['login' => true],
        'cierreCaja.php' => ['login' => true],

        'editarProducto.php' => ['login' => true],
        'agregarProducto.php' => ['login' => true],

        'registrarClientePOS.php' => ['login' => true],

        'historialExpedientes.php' => ['login' => true],
        'historialExpedientePaciente.php' => ['login' => true],
        'expedienteDigital.php' => ['login' => true],
        'verExpediente.php' => ['login' => true],
        'recetaParaDoctor.php' => ['login' => true],

        'personal.php' => ['login' => true],
        'editarPersonal.php' => ['login' => true],
        'agregarPersonal.php' => ['login' => true],

        'editarPerfil.php' => ['login' => true],
    ];
}


// VALIDACIÓN SIMPLE (SIN ROLES)
function validarAccesoAutomatico() {

    $archivoActual = basename($_SERVER['PHP_SELF']);
    $permisos = obtenerPermisosVista($archivoActual);

    // ❌ No existe → bloquear
    if (!isset($permisos[$archivoActual])) {
        mostrarAccesoDenegado();
    }

    $config = $permisos[$archivoActual];

    // Público → dejar pasar
    if (!empty($config['publico'])) {
        return;
    }

    // Requiere login
    if (!empty($config['login'])) {

        if (!isset($_SESSION['RolID'])) {
            mostrarAccesoDenegado();
        }

        return;
    }
}


// UI 
function mostrarAccesoDenegado() {

    http_response_code(403);

    echo '
    <div style="
        display:flex;
        justify-content:center;
        align-items:center;
        height:100vh;
        background:#f1f5f9;
        font-family:system-ui;
    ">
        <div style="
            background:white;
            padding:40px;
            border-radius:18px;
            text-align:center;
            box-shadow:0 10px 30px rgba(0,0,0,0.1);
        ">
            <h2 style="color:#dc2626;">Acceso Denegado</h2>
            <p>No tienes permisos para acceder a esta página.</p>

            <a href="/index.php" style="
                display:inline-block;
                margin-top:15px;
                padding:10px 20px;
                background:#0ea5e9;
                color:white;
                border-radius:8px;
                text-decoration:none;
            ">
                Ir al inicio
            </a>
        </div>
    </div>
    ';

    exit;
}


// 🔥 EJECUTAR
validarAccesoAutomatico();

?>