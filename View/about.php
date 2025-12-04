<?php include('layout.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Óptica Grisol</title>
    <?php IncluirCSS(); ?>
</head>

<body>
<?php MostrarMenu(); ?>


<header class="about-hero d-flex align-items-center">
    <div class="container text-center">
        <h1 class="fw-bold about-title">Cuidamos su visión, realzamos su estilo</h1>
        <p class="lead about-subtitle">
            En Óptica Grisol combinamos tecnología, salud visual y asesoría personalizada
            para brindarte una experiencia única.
        </p>
    </div>
</header>


<section class="about-section">
    <div class="container px-5">
        <div class="row gx-5 align-items-center">
            <div class="col-lg-6 mb-4">
                <div class="about-img-wrapper">
                    <img class="img-fluid rounded-4" src="/OptiGestion/assets/img/AboutUS.jpg" alt="Sobre nosotros">
                </div>
            </div>

            <div class="col-lg-6">
                <h2 class="fw-bold about-section-title">Crecimiento e innovación</h2>
                <p class="about-text">
                    Gracias a la confianza de nuestros clientes y al compromiso de nuestro equipo,
                    hemos crecido de forma constante.
                </p>
            </div>
        </div>
    </div>
</section>


<section class="about-section alt">
    <div class="container px-5">
        <div class="row gx-5 align-items-center">
            
            <div class="col-lg-6 order-last order-lg-first">
                <h2 class="fw-bold about-section-title">Nuestros productos</h2>
                <p class="about-text">
                    En Óptica Grisol ofrecemos lentes y servicios de alta calidad. 
                    Nos enfocamos en mejorar su visión y confianza.
                </p>
            </div>

            <div class="col-lg-6 mb-4 order-first order-lg-last">
                <div class="about-img-wrapper">
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