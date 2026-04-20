<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Catálogo central de accesos por vista.
 *
 * 
 * - publico => cualquiera entra
 * - auth => cualquier usuario autenticado
 * - roles => acceso por RolID + EmpleadoRol
 * 
 *Como se valida en el sistema
 * - $_SESSION['RolID'] puede ser: 'Paciente' o 'Empleado'
 * - $_SESSION['EmpleadoRol'] puede ser:
 *      1 = Administrador
 *      2 = Asistente
 *      3 = Doctor
 *      4 = Cajero
 */
function obtenerPermisosVista(): array
{
    return [

       /* Vistas publicas*/
        'index.php'         => ['publico' => true],
        'about.php'         => ['publico' => true],
        'anteojos.php'      => ['publico' => true],
        'iniciarSesion.php' => ['publico' => true],

       /* Vistas de pacientes*/
        'agendarCita.php' => [
            'roles' => [
                ['RolID' => 'Paciente']
            ]
        ],
        'editarCita.php' => [
            'roles' => [
                ['RolID' => 'Paciente'],
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 3, 4]]
            ]
        ],
        'misRecetas.php' => [
            'roles' => [
                ['RolID' => 'Paciente']
            ]
        ],
        'verReceta.php' => [
            'roles' => [
                ['RolID' => 'Paciente']
            ]
        ],

       /* Perfil (cualquier usuario logeado)*/

        'editarPerfil.php' => [
            'auth' => true
        ],

        /* Empleados - Administración / caja / Inventario / reportes */

        'reportes.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 4]]
            ]
        ],
        'inventario.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 4]]
            ]
        ],
        'facturacion.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 4]]
            ]
        ],
        'historialCierreCaja.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 4]]
            ]
        ],
        'puntoVenta.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 4]]
            ]
        ],
        'cierreCaja.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 4]]
            ]
        ],
        'registrarClientePOS.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 4]]
            ]
        ],
        'editarProducto.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 4]]
            ]
        ],
        'agregarProducto.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 4]]
            ]
        ],

        /* Expedientes  */
        'historialExpedientes.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 3, 4]]
            ]
        ],
        'historialExpedientePaciente.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 3, 4]]
            ]
        ],
        'expedienteDigital.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [3]]
            ]
        ],
        'verExpediente.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1, 2, 3, 4]]
            ]
        ],
        'recetaParaDoctor.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [3]]
            ]
        ],

        /* Personal ( solo admin) */
        'personal.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1]]
            ]
        ],
        'editarPersonal.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1]]
            ]
        ],
        'agregarPersonal.php' => [
            'roles' => [
                ['RolID' => 'Empleado', 'EmpleadoRol' => [1]]
            ]
        ],
    ];
}

/* Obtiene el nombre del archivo actual.*/
function obtenerVistaActual(): string
{
    return basename($_SERVER['PHP_SELF']);
}

/* Indica si hay sesión iniciada válida.*/
function usuarioAutenticado(): bool
{
    return !empty($_SESSION['RolID']);
}

/* Evalúa si el usuario actual cumple alguna regla de roles.*/

function usuarioTienePermisoPorRol(array $reglas): bool
{
    $rolID = $_SESSION['RolID'] ?? null;
    $empleadoRol = isset($_SESSION['EmpleadoRol']) ? (int)$_SESSION['EmpleadoRol'] : null;

    foreach ($reglas as $regla) {
        if (!isset($regla['RolID'])) {
            continue;
        }

        if ($regla['RolID'] !== $rolID) {
            continue;
        }

        // Si la regla solo exige RolID, ya cumple
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

/* Validador central de acceso.*/
function validarAccesoAutomatico(): void
{
    $vistaActual = obtenerVistaActual();
    $permisos = obtenerPermisosVista();

    // Si la vista no está registrada => 403
    if (!array_key_exists($vistaActual, $permisos)) {
        mostrarAccesoDenegado('Vista no registrada en la política de seguridad.');
    }

    $config = $permisos[$vistaActual];

    // Pública => acceso libre
    if (!empty($config['publico'])) {
        return;
    }

    // Requiere autenticación general
    if (!empty($config['auth'])) {
        if (!usuarioAutenticado()) {
            mostrarAccesoDenegado('Debes iniciar sesión para acceder a esta página.');
        }
        return;
    }

    // Requiere roles específicos
    if (!empty($config['roles'])) {
        if (!usuarioAutenticado()) {
            mostrarAccesoDenegado('Debes iniciar sesión para acceder a esta página.');
        }

        if (!usuarioTienePermisoPorRol($config['roles'])) {
            mostrarAccesoDenegado('No tienes permisos para acceder a esta página.');
        }

        return;
    }

    // Configuración inválida => bloquear por seguridad
    mostrarAccesoDenegado('La configuración de acceso de esta vista es inválida.');
}

/*
  Render UI 403 y termina ejecución.
 */
function mostrarAccesoDenegado(string $motivo = 'Acceso no autorizado.'): void
{
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
                background:linear-gradient(135deg,#e2e8f0,#f8fafc);
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
                border-radius:20px;
                padding:36px 32px;
                text-align:center;
                box-shadow:0 18px 45px rgba(15,23,42,.12);
                border:1px solid #e2e8f0;
            }
            .code{
                font-size:64px;
                font-weight:800;
                color:#dc2626;
                line-height:1;
                margin-bottom:10px;
            }
            h1{
                margin:0 0 10px;
                font-size:28px;
                color:#0f172a;
            }
            p{
                margin:0 0 10px;
                color:#475569;
                line-height:1.55;
            }
            .detail{
                font-size:14px;
                color:#94a3b8;
                margin-top:8px;
            }
            .btn{
                display:inline-block;
                margin-top:22px;
                padding:12px 22px;
                border-radius:10px;
                background:#0ea5e9;
                color:#fff;
                text-decoration:none;
                font-weight:600;
            }
            .btn:hover{
                background:#0284c7;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="code">403</div>
            <h1>Acceso denegado</h1>
            <p>No tienes permisos para ingresar a esta página.</p>
            <p class="detail">'.htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8').'</p>
            <a class="btn" href="/index.php">Ir al inicio</a>
        </div>
    </body>
    </html>
    ';
    exit;
}

// Ejecutar siempre al incluir este archivo
validarAccesoAutomatico();
?>