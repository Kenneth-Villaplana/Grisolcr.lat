<?php include('layout.php'); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Óptica Grisol</title>
    <?php IncluirCSS(); ?>
</head>

<body>
<?php MostrarMenu(); ?>

<!-- HERO SOBRE NOSOTROS -->
<header class="about-hero position-relative overflow-hidden d-flex align-items-center">
    
    <!-- Orbes -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="container text-center position-relative">
        <h1 class="fw-bold about-title">Cuidamos su visión, realzamos su estilo</h1>
        <p class="lead about-subtitle">
            Tecnología avanzada, salud visual y asesoría personalizada
            para brindarte una experiencia única.
        </p>
    </div>
</header>


<!-- SECCIÓN 1 -->
<section class="about-section py-5">
    <div class="container px-4 px-lg-5">
        <div class="row gx-5 align-items-center">

            <div class="col-lg-6 mb-4" data-aos="fade-right">
                <div class="about-img-card shadow-glass">
                    <img class="img-fluid rounded-4" src="/OptiGestion/assets/img/AboutUS.jpg" alt="Sobre nosotros">
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="fw-bold about-section-title">Crecimiento e innovación</h2>
                <p class="about-text">
                    Gracias a la confianza de nuestros pacientes y al compromiso de nuestro equipo,
                    seguimos avanzando hacia nuevas tecnologías y mejores prácticas en salud visual.
                </p>
                <p class="about-text">
                    Nuestro objetivo es brindarte una atención moderna, humana
                    y alineada con las tendencias actuales del cuidado ocular.
                </p>
            </div>

        </div>
    </div>
</section>


<!-- SECCIÓN 2 -->
<section class="about-section alt py-5">
    <div class="container px-4 px-lg-5">
        <div class="row gx-5 align-items-center">

            <div class="col-lg-6 order-last order-lg-first" data-aos="fade-right">
                <h2 class="fw-bold about-section-title">Nuestros productos</h2>
                <p class="about-text">
                    Contamos con una amplia selección de armazones, lentes y tratamientos especializados
                    diseñados para proteger su visión y complementar su estilo personal.
                </p>
                <p class="about-text">
                    Cada producto es elegido cuidadosamente para garantizarte calidad,
                    comodidad y la mejor experiencia visual posible.
                </p>
            </div>

            <div class="col-lg-6 mb-4 order-first order-lg-last" data-aos="fade-left">
                <div class="about-img-card shadow-glass">
                    <img class="img-fluid rounded-4" src="/OptiGestion/assets/img/AboutUS3.jpg" alt="Productos">
                </div>
            </div>

        </div>
    </div>
</section>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

</body>
</html>
//refresh
