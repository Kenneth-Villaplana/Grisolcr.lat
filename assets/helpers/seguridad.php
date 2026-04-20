<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function obtenerPermisosVista() {
    return array(

        // Públicas
        'index.php' => array('publico' => true),
        'about.php' => array('publico' => true),
        'anteojos.php' => array('publico' => true),
        'iniciarSesion.php' => array('publico' => true),

        // Paciente
        'agendarCita.php' => array(
            'roles' => array(
                array('RolID' => 'Paciente')
            )
        ),
        'editarCita.php' => array(
            'roles' => array(
                array('RolID' => 'Paciente'),
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 3, 4))
            )
        ),
        'misRecetas.php' => array(
            'roles' => array(
                array('RolID' => 'Paciente')
            )
        ),
        'verReceta.php' => array(
            'roles' => array(
                array('RolID' => 'Paciente')
            )
        ),

        // Cualquier usuario logueado
        'editarPerfil.php' => array('auth' => true),

        // Empleados caja / administración
        'reportes.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),
        'inventario.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),
        'facturacion.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),
        'historialCierreCaja.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),
        'puntoVenta.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),
        'cierreCaja.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),
        'registrarClientePOS.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),
        'editarProducto.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),
        'agregarProducto.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),

        // Expedientes
        'historialExpedientes.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 3, 4))
            )
        ),
        'historialExpedientePaciente.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 3, 4))
            )
        ),
        'expedienteDigital.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(3))
            )
        ),
        'verExpediente.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 3, 4))
            )
        ),
        'recetaParaDoctor.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(3))
            )
        ),

        // Personal
        'personal.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1))
            )
        ),
        'editarPersonal.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1))
            )
        ),
        'agregarPersonal.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1))
            )
        ),
    );
}

function obtenerVistaActual() {
    return basename($_SERVER['PHP_SELF']);
}

function usuarioAutenticado() {
    return !empty($_SESSION['RolID']);
}

function usuarioTienePermisoPorRol($reglas) {
    $rolID = isset($_SESSION['RolID']) ? $_SESSION['RolID'] : null;
    $empleadoRol = isset($_SESSION['EmpleadoRol']) ? (int)$_SESSION['EmpleadoRol'] : null;

    foreach ($reglas as $regla) {
        if (!isset($regla['RolID'])) {
            continue;
        }

        if ($regla['RolID'] !== $rolID) {
            continue;
        }

        if (!isset($regla['EmpleadoRol'])) {
            return true;
        }

        $rolesPermitidos = (array)$regla['EmpleadoRol'];

        if ($rolID === 'Empleado' && in_array($empleadoRol, $rolesPermitidos, true)) {
            return true;
        }
    }

    return false;
}

function validarAccesoAutomatico() {
    $vistaActual = obtenerVistaActual();
    $permisos = obtenerPermisosVista();

    if (!array_key_exists($vistaActual, $permisos)) {
        mostrarAccesoDenegado('Vista no registrada en la política de seguridad.');
    }

    $config = $permisos[$vistaActual];

    if (!empty($config['publico'])) {
        return;
    }

    if (!empty($config['auth'])) {
        if (!usuarioAutenticado()) {
            mostrarAccesoDenegado('Debes iniciar sesión para acceder a esta página.');
        }
        return;
    }

    if (!empty($config['roles'])) {
        if (!usuarioAutenticado()) {
            mostrarAccesoDenegado('Debes iniciar sesión para acceder a esta página.');
        }

        if (!usuarioTienePermisoPorRol($config['roles'])) {
            mostrarAccesoDenegado('No tienes permisos para acceder a esta página.');
        }

        return;
    }

    mostrarAccesoDenegado('Configuración de seguridad inválida.');
}

function mostrarAccesoDenegado($motivo = 'Acceso no autorizado.') {
    http_response_code(403);

    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>403 - Acceso denegado</title>
        <style>
            body{
                margin:0;
                font-family:Arial,sans-serif;
                background:#f1f5f9;
                min-height:100vh;
                display:flex;
                justify-content:center;
                align-items:center;
                padding:24px;
            }
            .card{
                width:100%;
                max-width:520px;
                background:#fff;
                border-radius:18px;
                padding:36px;
                text-align:center;
                box-shadow:0 10px 30px rgba(0,0,0,.10);
            }
            .code{
                font-size:56px;
                font-weight:bold;
                color:#dc2626;
                margin-bottom:10px;
            }
            h1{
                margin:0 0 12px;
                color:#0f172a;
            }
            p{
                margin:0 0 8px;
                color:#475569;
            }
            a{
                display:inline-block;
                margin-top:18px;
                padding:10px 20px;
                background:#0ea5e9;
                color:#fff;
                text-decoration:none;
                border-radius:8px;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="code">403</div>
            <h1>Acceso denegado</h1>
            <p>No tienes permisos para acceder a esta página.</p>
            <p>' . htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8') . '</p>
            <a href="/index.php">Ir al inicio</a>
        </div>
    </body>
    </html>
    ';
    exit;
}

validarAccesoAutomatico();
?>