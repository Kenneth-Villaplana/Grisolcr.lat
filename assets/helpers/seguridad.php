<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//mapeo de accesos

function obtenerPermisosVista($vista) {

    return [

        //vstas publicas
        'index.php' => ['publico' => true],
        'about.php' => ['publico' => true],
        'anteojos.php' => ['publico' => true],
        'iniciarSesion.php' => ['publico' => true],

       //vistas de pacientes
        'agendarCita.php' => ['rol' => 'Paciente'],
        'editarCita.php' => ['rol' => 'Paciente'],
        'misRecetas.php' => ['rol' => 'Paciente'],
        'verReceta.php' => ['rol' => 'Paciente'],

        
        //vistas de empleados
        'reportes.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1,2,4]],
        'inventario.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1,2,4]],
        'facturacion.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1,2,4]],
        'historialCierreCaja.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1,2,4]],
        'puntoVenta.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1,2,4]],
        'cierreCaja.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1,2,4]],

        //hijos de inventario
        'editarProducto.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1,2,4]],
        'agregarProducto.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1,2,4]],

        //hijo de facturacion y expediente
        'registrarClientePOS.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1,2,4,3]], // también doctor

       //vistas de doctor
        'historialExpedientes.php' => ['rol' => 'Empleado', 'empleadoRoles' => [3,1,2,4]],
        'historialExpedientePaciente.php' => ['rol' => 'Empleado', 'empleadoRoles' => [3,1,2,4]],
        'expedienteDigital.php' => ['rol' => 'Empleado', 'empleadoRoles' => [3]],
        'verExpediente.php' => ['rol' => 'Empleado', 'empleadoRoles' => [3]],
        'recetaParaDoctor.php' => ['rol' => 'Empleado', 'empleadoRoles' => [3]],

       
        //citas de empleado
        'editarCita.php' => ['rol' => 'Empleado', 'empleadoRoles' => [3,1,2,4]],

        //vistas exclusivas de admi
        'personal.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1]],
        'editarPersonal.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1]],
        'agregarPersonal.php' => ['rol' => 'Empleado', 'empleadoRoles' => [1]],

        
        //vista de perfil logeado
        'editarPerfil.php' => ['login' => true],

    ];
}

//validar acceso automatico

function validarAccesoAutomatico() {

    $archivoActual = basename($_SERVER['PHP_SELF']);

    $permisos = obtenerPermisosVista($archivoActual);

    // Si no está definido → bloquear
    if (!isset($permisos[$archivoActual])) {
        mostrarAccesoDenegado();
    }

    $config = $permisos[$archivoActual];

    // Público
    if (isset($config['publico']) && $config['publico']) {
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

    // Validar rol base
    if (!isset($config['rol']) || $rol !== $config['rol']) {
        mostrarAccesoDenegado();
    }

    // Validar rol interno empleado
    if ($rol === 'Empleado' && isset($config['empleadoRoles'])) {
        if (!in_array($empleadoRol, $config['empleadoRoles'])) {
            mostrarAccesoDenegado();
        }
    }
}

//ui 

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

?>