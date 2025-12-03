<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function MostrarMenu() {
    $rol = $_SESSION['RolID'] ?? null;
    $EmpleadoRol = $_SESSION['EmpleadoRol'] ?? null;

    echo '
    <nav class="navbar navbar-expand-lg navbar-dark bg-blue-dark">
        <div class="container-fluid px-5 d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="/OptiGestion/index.php">Óptica Grisol</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/OptiGestion/index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="/OptiGestion/view/about.php">Sobre Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="/OptiGestion/view/anteojos.php">Anteojos</a></li>';

    if (!$rol) {
        echo '<li class="nav-item ms-lg-3"><a class="nav-link" href="/OptiGestion/view/iniciarSesion.php">Iniciar Sesión</a></li>';
    } else if ($rol === 'Paciente') {
        echo '
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownCitas" role="button" data-bs-toggle="dropdown">
                Citas
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownCitas">
                <li><a class="dropdown-item" href="/OptiGestion/view/appointmentForm.php">Agendar Cita</a></li>
                <li><a class="dropdown-item" href="/OptiGestion/view/editarcita.php">Mis Citas</a></li>
                <li><a class="dropdown-item" href="/OptiGestion/view/historialMedico.php">Historial Médico</a></li>
            </ul>
        </li>';
    } else if ($rol === 'Empleado') {
        if ($EmpleadoRol == 1) {
            echo ' 
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPersonal" role="button" data-bs-toggle="dropdown">
                    Personal
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownPersonal">
                    <li><a class="dropdown-item" href="/OptiGestion/view/personal.php">Ver Personal</a></li>
                </ul>
            </li>';
        }

        echo '
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown">
                Administración
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownAdmin">
                <li><a class="dropdown-item" href="/OptiGestion/view/reportes.php">Reportes</a></li>
                <li><a class="dropdown-item" href="/OptiGestion/view/inventario.php">Inventario</a></li>
                <li><a class="dropdown-item" href="/OptiGestion/view/facturacion.php">Facturación</a></li>
                <li><a class="dropdown-item" href="/OptiGestion/view/historialExpedientes.php">Historial de Expedientes</a></li>
            </ul>
        </li>';
    }

    if ($rol) {
        echo '
        <li class="nav-item dropdown ms-lg-3">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPerfil" role="button" data-bs-toggle="dropdown">
                Perfil
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownPerfil">
                <li><a class="dropdown-item" href="/OptiGestion/view/editarPerfil.php">Editar Perfil</a></li>
                <li><a class="dropdown-item" href="/OptiGestion/view/editarcita.php">Mis Citas</a></li>
                <li><a class="dropdown-item" href="/OptiGestion/logout.php">Cerrar Sesión</a></li>
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
<footer class="bg-dark text-light pt-3 pb-2 mt-auto">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-3 mb-2">
                    <h6 class="fw-bold text-uppercase">Sucursal</h6>
                    <a href="https://maps.app.goo.gl/8xCe7rQRBhBzRZsr7" class="text-light text-decoration-none small">Ver ubicación</a>
                </div>
                <div class="col-md-3 mb-2">
                    <h6 class="fw-bold text-uppercase">Redes Sociales</h6>
                    <p class="mb-1 small">
                        <a href="https://www.instagram.com/opticagrisol?igsh=cm5zMXprZmphczAz" class="text-light text-decoration-none">
                            <i class="bi bi-instagram me-1"></i> Instagram
                        </a>
                    </p>
                    <p class="mb-1 small">
                        <a href="https://www.facebook.com/share/19kUWTvjNF/?mibextid=wwXIfr" class="text-light text-decoration-none">
                            <i class="bi bi-facebook me-1"></i> Facebook
                        </a>
                    </p>
                    <p class="mb-1 small">
                        <a href="https://wa.me/50612345678" class="text-light text-decoration-none" target="_blank">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                    </p>
                </div>
                <div class="col-md-3 mb-2">
                    <h6 class="fw-bold text-uppercase">Oficina Central</h6>
                    <p class="mb-1 small">Avenida 1A, Cartago Province, Cartago</p>
                    <h6 class="fw-bold mt-2 small">Contacto</h6>
                    <p class="mb-1 small">8813-9883 || 2592-5460</p>
                    <p class="mb-0 small">opticagrisol@gmail.com</p>
                </div>
            </div>

            <hr class="border-secondary">

            <div class="row">
                <div class="col">
                    <p class="mb-0 small">
                        &copy; <script>document.write(new Date().getFullYear());</script> Óptica Grisol. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>';
}

function IncluirCSS() {
    echo '

    <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300,400,500,600,700,800&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> 
    <link rel="stylesheet" href="/OptiGestion/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/OptiGestion/assets/vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="/OptiGestion/assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="/OptiGestion/assets/vendor/glightbox/css/glightbox.min.css">
    <link rel="stylesheet" href="/OptiGestion/assets/vendor/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/OptiGestion/assets/css/styles.css?v=5.5">
    <link rel="icon" type="image/x-icon" href="/OptiGestion/assets/favicon.ico">
    ';
}

function IncluirScripts() {
    echo '
    <script src="/OptiGestion/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/OptiGestion/assets/vendor/aos/aos.js"></script>
    <script src="/OptiGestion/assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="/OptiGestion/assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="/OptiGestion/assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="/OptiGestion/assets/vendor/php-email-form/validate.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/OptiGestion/assets/js/registro.js"></script>
    <script src="/OptiGestion/assets/js/scripts.js"></script>
   
  
    ';
}
?>