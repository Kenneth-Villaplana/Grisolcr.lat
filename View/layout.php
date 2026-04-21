<?php
require_once __DIR__ . '/../assets/helpers/seguridad.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();

}

validarAccesoAutomatico();

function MostrarMenu() {
    $rol = $_SESSION['RolID'] ?? null;
    $EmpleadoRol = $_SESSION['EmpleadoRol'] ?? null;

    echo '
    <nav class="navbar navbar-expand-lg navbar-dark bg-blue-dark">
        <div class="container-fluid px-5 d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="/index.php">Óptica Grisol</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="/View/about.php">Sobre Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="/View/anteojos.php">Anteojos</a></li>';

    if (!$rol) {
        echo '<li class="nav-item ms-lg-3"><a class="nav-link" href="/View/iniciarSesion.php">Iniciar Sesión</a></li>';
    }

  
    else if ($rol === 'Paciente') {
        echo '
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                Citas
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="/View/agendarCita.php">Agendar Cita</a></li>
                <li><a class="dropdown-item" href="/View/editarCita.php">Mis Citas</a></li>
                <li><a class="dropdown-item" href="/View/misRecetas.php">Historial Médico</a></li>
            </ul>
        </li>';
    }

    //  Empleados
    else if ($rol === 'Empleado') {

        //  Administrador 
        if ($EmpleadoRol == 1) {
            echo '
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    Personal
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/View/personal.php">Ver Personal</a></li>
                </ul>
            </li>';
        }

        // Administración (según rol)
        echo '
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                Administración
            </a>
            <ul class="dropdown-menu dropdown-menu-end">';

        //  Doctor 
        if ($EmpleadoRol == 3) {
            echo '
                <li><a class="dropdown-item" href="/View/historialExpedientes.php">Historial de Expedientes</a></li>
                <li><a class="dropdown-item" href="/View/editarCita.php">Manipular Citas</a></li>';
        }

        //  Cajero y  Asistente 
        if ($EmpleadoRol == 4 || $EmpleadoRol == 2 || $EmpleadoRol == 1 ) {
            echo '
                <li><a class="dropdown-item" href="/View/reportes.php">Reportes</a></li>
                <li><a class="dropdown-item" href="/View/inventario.php">Inventario</a></li>
                <li><a class="dropdown-item" href="/View/facturacion.php">Facturación</a></li>
                <li><a class="dropdown-item" href="/View/historialCierreCaja.php">Historial Cierre de Caja</a></li>
                <li><a class="dropdown-item" href="/View/historialExpedientes.php">Historial de Expedientes</a></li>
                <li><a class="dropdown-item" href="/View/editarCita.php">Manipular Citas</a></li>';
        }

        echo '
            </ul>
        </li>';
    }

    // para cualquier usuario logueado
    if ($rol) {
        echo '
        <li class="nav-item dropdown ms-lg-3">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                Perfil
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="/View/editarPerfil.php">Editar Perfil</a></li>
                <li><a class="dropdown-item" href="/logout.php">Cerrar Sesión</a></li>
            </ul>
        </li>';
    }

    echo '
                </ul>
            </div>
        </div>
    </nav>';
}

function MostrarFooter() {
    echo '
<footer class="bg-dark text-light mt-auto py-3" style="font-size: 0.85rem;">
    <div class="container">

        <div class="row text-center align-items-center gy-2">

            <!-- Ubicación -->
            <div class="col-md-4">
                <div class="text-center">
                    <span class="fw-semibold text-uppercase small d-block mb-1">Ubicación</span>

                    <span class="d-block small text-secondary mb-1">
                        Avenida 1A, Cartago Province, Cartago
                    </span>

                    <a href="https://maps.app.goo.gl/8xCe7rQRBhBzRZsr7" 
                       class="text-light text-decoration-none">
                        <i class="bi bi-geo-alt-fill me-1 text-info"></i> Ver en mapa
                    </a>
                </div>
            </div>

            <!-- Redes -->
            <div class="col-md-4">
                <div class="text-center">
                    <span class="fw-semibold text-uppercase small d-block mb-1">Síguenos</span>

                    <a href="https://www.instagram.com/opticagrisol?igsh=cm5zMXprZmphczAz" 
                       class="text-light fs-5 me-3">
                        <i class="bi bi-instagram"></i>
                    </a>

                    <a href="https://www.facebook.com/share/19kUWTvjNF/?mibextid=wwXIfr" 
                       class="text-light fs-5 me-3">
                        <i class="bi bi-facebook"></i>
                    </a>

                    <a href="https://wa.me/50688139883" 
                       class="text-light fs-5">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                </div>
            </div>

            <!-- Contacto -->
            <div class="col-md-4">
                <div class="text-center">
                    <span class="fw-semibold text-uppercase small d-block mb-1">Contacto</span>
                    <span class="d-block">📞 8813-9883 · 2592-5460</span>
                    <span class="d-block">✉️ opticagrisol@gmail.com</span>
                </div>
            </div>

        </div>

        <hr class="border-secondary my-2">

        <div class="text-center small text-secondary">
            © '.date("Y").' Óptica Grisol
        </div>

    </div>
</footer>';
}

function IncluirCSS() {
    echo '
    <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300,400,500,600,700,800&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="/assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/vendor/glightbox/css/glightbox.min.css">
    <link rel="stylesheet" href="/assets/vendor/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css?v=24">
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
     <link rel="stylesheet" href="/assets/css/pos.css">
    ';
}

function IncluirScripts() {
    echo '
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/aos/aos.js"></script>
    <script src="/assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="/assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="/assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/assets/js/registro.js"></script>
    <script src="/assets/js/scripts.js"></script>
    ';
}

?>
