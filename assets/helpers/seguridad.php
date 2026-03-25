<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ===============================
// MAPEO DE PERMISOS
// ===============================
function obtenerPermisosVista($vista) {

    return [

        // ===============================
        // PÚBLICO
        // ===============================
        'index.php' => ['publico' => true],
        'about.php' => ['publico' => true],
        'anteojos.php' => ['publico' => true],
        'iniciarSesion.php' => ['publico' => true],

        // ===============================
        // PACIENTE
        // ===============================
        'agendarCita.php' => ['roles' => ['Paciente']],
        'misRecetas.php' => ['roles' => ['Paciente']],
        'verReceta.php' => ['roles' => ['Paciente']],

        
        'editarCita.php' => [
            'roles' => ['Paciente', 'Empleado'],
            'empleadoRoles' => [1,2,3,4]
        ],

        // ===============================
        // EMPLEADOS
        // ===============================
        'reportes.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1,2,4]],
        'inventario.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1,2,4]],
        'facturacion.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1,2,4]],
        'historialCierreCaja.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1,2,4]],
        'puntoVenta.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1,2,4]],
        'cierreCaja.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1,2,4]],

        // ===============================
        // INVENTARIO (HIJOS)
        // ===============================
        'editarProducto.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1,2,4]],
        'agregarProducto.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1,2,4]],

        // ===============================
        // FACTURACIÓN / CLIENTES
        // ===============================
        'registrarClientePOS.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1,2,3,4]],

        // ===============================
        // DOCTOR
        // ===============================
        'historialExpedientes.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1,2,3,4]],
        'historialExpedientePaciente.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1,2,3,4]],
        'expedienteDigital.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [3]],
        'verExpediente.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [3]],
        'recetaParaDoctor.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [3]],

        // ===============================
        // ADMIN
        // ===============================
        'personal.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1]],
        'editarPersonal.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1]],
        'agregarPersonal.php' => ['roles' => ['Empleado'], 'empleadoRoles' => [1]],

        // ===============================
        // PERFIL
        // ===============================
        'editarPerfil.php' => ['login' => true],

    ];
}

// ===============================
// VALIDACIÓN DE ACCESO
// ===============================
function validarAccesoAutomatico() {

    $archivoActual = basename($_SERVER['PHP_SELF']);
    $permisos = obtenerPermisosVista($archivoActual);

    // Si no existe en el mapa → bloquear
    if (!isset($permisos[$archivoActual])) {
        mostrarAccesoDenegado();
    }

    $config = $permisos[$archivoActual];

    // Público
    if (!empty($config['publico'])) {
        return;
    }

    $rol = $_SESSION['RolID'] ?? null;
    $empleadoRol = $_SESSION['EmpleadoRol'] ?? null;

    // Solo login requerido
    if (isset($config['login'])) {
        if (!$rol) {
            mostrarAccesoDenegado();
        }
        return;
    }

    // ===============================
    // VALIDAR ROLES (MULTIROL)
    // ===============================
    if (isset($config['roles'])) {

        if (!in_array($rol, $config['roles'])) {
            mostrarAccesoDenegado();
        }

    } else {
        mostrarAccesoDenegado();
    }

    // ===============================
    // VALIDAR ROL INTERNO EMPLEADO
    // ===============================
    if ($rol === 'Empleado' && isset($config['empleadoRoles'])) {

        if (!in_array($empleadoRol, $config['empleadoRoles'])) {
            mostrarAccesoDenegado();
        }
    }
}

// ===============================
// UI ACCESO DENEGADO
// ===============================
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


validarAccesoAutomatico();