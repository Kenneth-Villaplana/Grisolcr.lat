<?php
 include('view/layout.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Óptica Grisol</title>
  <?php IncluirCSS();?>
</head>
<body>
  <?php MostrarMenu();?>

  <main class="home-wrapper">

    <!-- HERO PRINCIPAL -->
    <header class="hero-home position-relative overflow-hidden">
      <div class="hero-bg-gradient"></div>
      <div class="hero-overlay"></div>
      <div class="container position-relative hero-inner">
        <div class="row align-items-center">
          <div class="col-lg-7 text-center text-lg-start mb-5 mb-lg-0">
            <span class="hero-badge d-inline-flex align-items-center mb-3">
              <span class="badge-dot me-2"></span>
              Cuidamos su salud visual con tecnología de punta
            </span>

            <h1 class="hero-title fw-bold mb-3">
              Bienvenido a <span class="hero-title-highlight">Óptica Grisol</span>
            </h1>

            <p class="hero-subtitle mb-4">
              Combine su estilo con una visión nítida. Exámenes visuales,
              lentes de alta calidad y asesoría profesional en un solo lugar.
            </p>

            <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
              <a href="/OptiGestion/View/RegistrarPaciente.php" class="btn-primary-modern">
                Agendar cita
              </a>
              <a href="#servicios" class="btn-secondary-modern">
                Ver servicios
              </a>
            </div>

            <div class="hero-meta d-flex flex-column flex-md-row gap-3 mt-4 justify-content-center justify-content-lg-start">
              
              <div class="hero-meta-item">
                <span class="meta-label">Años de experiencia</span>
                <span class="meta-value">+20</span>
              </div>
            </div>
          </div>

          <div class="col-lg-5">
            <div class="hero-card-glass">
              <h5 class="mb-3">Su visión, nuestra prioridad</h5>
              <p class="small mb-4">
                Agende su valoración visual y reciba recomendaciones personalizadas
                para lentes, armazones y tratamientos según su estilo de vida.
              </p>

              <ul class="list-unstyled hero-list">
                <li><i data-lucide="glasses" class="me-2"></i> Lentes oftálmicos y de sol de marcas reconocidas</li>
                <li><i data-lucide="scan-eye" class="me-2"></i> Exámenes visuales completos</li>
                <li><i data-lucide="user-check" class="me-2"></i> Asesoría personalizada en cada visita</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- SECCIÓN SERVICIOS / BENEFICIOS -->
   <section id="servicios" class="section-beneficios">
    <div class="container px-4">
        
        <div class="text-center mb-5">
            <h2 class="section-title fw-bold mb-3">
                La mejor forma de cuidar su vista
            </h2>
            <p class="section-subtitle">
                Servicios integrales para que veas y te veas mejor, siempre.
            </p>
        </div>

        <div class="row g-4">

            <!-- Servicio 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="beneficio-card">
                    <div class="icono-beneficio">
                        <i data-lucide="sparkles"></i>
                    </div>
                    <h5>Servicios innovadores</h5>
                    <p>Soluciones personalizadas y tecnología avanzada para su salud visual.</p>
                </div>
            </div>

            <!-- Servicio 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="beneficio-card">
                    <div class="icono-beneficio">
                        <i data-lucide="stethoscope"></i>
                    </div>
                    <h5>Diagnóstico profesional</h5>
                    <p>Exámenes oculares completos y asesoría experta.</p>
                </div>
            </div>

            <!-- Servicio 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="beneficio-card">
                    <div class="icono-beneficio">
                        <i data-lucide="glasses"></i>
                    </div>
                    <h5>Lentes de alta calidad</h5>
                         <p>
            Marcas premium con materiales resistentes y tratamientos especializados.
    
        </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="beneficio-card">
                    <div class="icono-beneficio">
                        <i data-lucide="circle-dot"></i>  
                    </div>
                    <h5>Lentes de contacto</h5>
                     <p>
            Opciones cómodas y seguras diseñadas para adaptarse a su estilo de vida.
        </p>
                </div>
            </div>

        </div>

    </div>
</section>

<section class="section-carousel-modern">
   <div class="container px-4">
    
    <div class="text-center mb-5">
            <h2 class="section-title fw-bold mb-3">
                Su vista merece lo mejor.
            </h2>
            <p class="section-subtitle">
         Descubrí nuestras ofertas especiales en lentes, armazones y servicios visuales.
        
    </p>
</div>
    <div class="container d-flex justify-content-center">

        <div class="carousel-modern-wrapper">

            <div id="carouselModern" class="carousel slide" data-bs-ride="carousel">
                
                <div class="carousel-inner">

                    <div class="carousel-item active">
                        <div class="carousel-card">
                            <div class="glow-bg"></div>
                            <img src="/OptiGestion/assets/img/carrusel1.jpg" class="carousel-card-img">
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="carousel-card">
                            <div class="glow-bg"></div>
                            <img src="/OptiGestion/assets/img/carrusel2.jpg" class="carousel-card-img">
                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="carousel-card">
                            <div class="glow-bg"></div>
                            <img src="/OptiGestion/assets/img/carrusel3.jpg" class="carousel-card-img">
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-card">
                            <div class="glow-bg"></div>
                            <img src="/OptiGestion/assets/img/carrusel4.jpg" class="carousel-card-img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

  </main>

  <?php MostrarFooter(); ?>
  <?php IncluirScripts(); ?>
  <script> if (window.lucide) lucide.createIcons(); </script>
</body>
</html>