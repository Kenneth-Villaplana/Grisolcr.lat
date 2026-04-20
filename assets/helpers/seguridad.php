<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*  Roles del sistema:
- $_SESSION['RolID'] = 'Paciente' o 'Empleado'
- $_SESSION['EmpleadoRol']:
    1 = Administrador
    2 = Asistente
    3 = Doctor
    4 = Cajero
*/
function obtenerPermisosVista() {
    return array(

        /*publicas*/
        'index.php'            => array('publico' => true),
        'about.php'            => array('publico' => true),
        'anteojos.php'         => array('publico' => true),
        'iniciarSesion.php'    => array('publico' => true),
        'registrarPaciente.php'=> array('publico' => true),
        'recuperarCuenta.php'  => array('publico' => true),

        /*cualquier usuario logeado*/
        'editarPerfil.php' => array('auth' => true),

        /*
        Paciente
         Menúl:
         - agendarCita.php
         - editarCita.php
         - misRecetas.php
        
         Internas:
         - verReceta.php (desde misRecetas)
        */
        'agendarCita.php' => array(
            'roles' => array(
                array('RolID' => 'Paciente'),
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 3, 4))
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

        /*
        Personal
         Menú:
        - personal.php (solo admin)
        
         Internas:
         - registrarPersonal.php
         - editarPersonal.php
        */
        'personal.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1))
            )
        ),
        'registrarPersonal.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1))
            )
        ),
        'editarPersonal.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1))
            )
        ),

        /*
        inventario
         Menú:
         - inventario.php (admin, asistente, cajero)
        
         Internas:
         - agregarProducto.php
         - editarProducto.php
        */
        'inventario.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),
        'agregarProducto.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),
        'editarProducto.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),

        /*
        Facturacion
         Menú:
         - facturacion.php (admin, asistente, cajero)
         - historialCierreCaja.php (admin, asistente, cajero)
        
         Internas:
         - puntoVenta.php
        - cierreCaja.php
        - registrarClientePOS.php
        */
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

        /*
        Historial de expedientes
         Menú:
         - historialExpedientes.php
           Doctor: aparece en menú
           Admin/Asistente/Cajero: también aparece en menú
        
         Internas:
        - expedienteDigital.php
        - historialExpedientePaciente.php
        - registrarClientePOS.php
         - recetaParaDoctor.php
        
        
        registrarClientePOS.php aparece también desde facturación.
         unión de permisos necesarios:
         admin, asistente, doctor, cajero.
        */
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
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 3, 4))
            )
        ),
        'recetaParaDoctor.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 3, 4))
            )
        ),
        'registrarClientePOS.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 3, 4))
            )
        ),

        /*
        Reportes
        - reportes.php (admin, asistente, cajero)
        */
        'reportes.php' => array(
            'roles' => array(
                array('RolID' => 'Empleado', 'EmpleadoRol' => array(1, 2, 4))
            )
        ),
    );
}

/* obtiene el nombre del archivo actual que se está ejecutando
   usa la url (request_uri) y elimina cualquier parámetro
   ejemplo: /view/facturacion.php?id=1 -> facturacion.php */
function obtenerVistaActual() {
    return basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
}


function usuarioAutenticado() {
    return isset($_SESSION['RolID']) && !empty($_SESSION['RolID']);
}

/* valida si el usuario cumple con alguna regla de acceso por rol
   recibe un arreglo de reglas definidas en seguridad.php */
function usuarioTienePermisoPorRol($reglas) {

    /* obtiene el rol general (paciente o empleado) */
    $rol = isset($_SESSION['RolID']) ? $_SESSION['RolID'] : null;

    /* obtiene el rol específico de empleado (1,2,3,4) */
    $empleadoRol = isset($_SESSION['EmpleadoRol']) ? (int) $_SESSION['EmpleadoRol'] : null;

    /* recorre todas las reglas permitidas */
    foreach ($reglas as $regla) {

        /* si la regla no tiene rol definido, la ignora */
        if (!isset($regla['RolID'])) {
            continue;
        }

        /* si el rol no coincide, pasa a la siguiente regla */
        if ($regla['RolID'] !== $rol) {
            continue;
        }

        /* si la regla solo pide rol entonces permite acceso */
        if (!isset($regla['EmpleadoRol'])) {
            return true;
        }

        /* si es empleado, valida si su rol específico está dentro de los permitidos */
        if ($rol === 'Empleado' && in_array($empleadoRol, $regla['EmpleadoRol'], true)) {
            return true;
        }
    }

    /* si ninguna regla coincide, no tiene permiso */
    return false;
}


function usuarioPuedeVerVista($vista) {

    /* obtiene todas las reglas de acceso */
    $permisos = obtenerPermisosVista();

    /* si la vista no está registrada, se bloquea */
    if (!isset($permisos[$vista])) {
        return false;
    }

    $config = $permisos[$vista];

    /* si es pública, cualquiera puede entrar */
    if (!empty($config['publico'])) {
        return true;
    }

    /* si requiere solo login, valida que tenga sesión */
    if (!empty($config['auth'])) {
        return usuarioAutenticado();
    }

    /* si requiere roles específicos */
    if (!empty($config['roles'])) {

        /* si no hay sesión, no puede entrar */
        if (!usuarioAutenticado()) {
            return false;
        }

        /* valida si cumple con los roles definidos */
        return usuarioTienePermisoPorRol($config['roles']);
    }

    /* si no cumple ninguna condición se bloquea */
    return false;
}

function validarAccesoAutomatico() {

    /* obtiene el archivo actual */
    $vistaActual = obtenerVistaActual();

    /* si el usuario no tiene permiso, muestra error 403 */
    if (!usuarioPuedeVerVista($vistaActual)) {
        mostrarAccesoDenegado();
    }
}

function mostrarAccesoDenegado() {
    http_response_code(403);

    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>403 - Acceso denegado</title>
        <style>
            *{box-sizing:border-box}
            body{
                margin:0;
                font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
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
                font-weight:700;
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
            <a href="/index.php">Ir al inicio</a>
        </div>
    </body>
    </html>
    ';
    exit;
}
?>