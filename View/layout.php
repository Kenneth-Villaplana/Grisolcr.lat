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
            <a class="navbar-brand" href="/index.php">Óptica Grisol</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="/view/about.php">Sobre Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="/view/anteojos.php">Anteojos</a></li>';

    if (!$rol) {
        echo '<li class="nav-item ms-lg-3"><a class="nav-link" href="/view/iniciarSesion.php">Iniciar Sesión</a></li>';
    } else if ($rol === 'Paciente') {
        echo '
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownCitas" role="button" data-bs-toggle="dropdown">
                Citas
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="/view/appointmentForm.php">Agendar Cita</a></li>
                <li><a class="dropdown-item" href="/view/editarcita.php">Mis Citas</a></li>
                <li><a class="dropdown-item" href="/view/misRecetas.php">Historial Médico</a></li>
            </ul>
        </li>';
    } else if ($rol === 'Empleado') {

        if ($EmpleadoRol == 1) {
            echo '
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    Personal
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/view/personal.php">Ver Personal</a></li>
                </ul>
            </li>';
        }

        echo '
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                Administración
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="/view/reportes.php">Reportes</a></li>
                <li><a class="dropdown-item" href="/view/inventario.php">Inventario</a></li>
                <li><a class="dropdown-item" href="/view/facturacion.php">Facturación</a></li>
                <li><a class="dropdown-item" href="/view/historialCierreCaja.php">Historial Cierre de Caja</a></li>
                <li><a class="dropdown-item" href="/view/historialExpedientes.php">Historial de Expedientes</a></li>
                <li><a class="dropdown-item" href="/view/editarcita.php">Manipular Citas</a></li>
            </ul>
        </li>';
    }

    if ($rol) {
        echo '
        <li class="nav-item dropdown ms-lg-3">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                Perfil
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="/view/editarPerfil.php">Editar Perfil</a></li>
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
<footer class="bg-dark text-light pt-3 pb-2 mt-auto">
    <div class="container text-center">
        <p class="mb-0 small">
            &copy; <script>document.write(new Date().getFullYear());</script> Óptica Grisol. Todos los derechos reservados.
        </p>
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

    <link rel="stylesheet" href="/assets/css/styles.css?v=9">
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
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
