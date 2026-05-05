<?php
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/../Model/productoModel.php';

$baseUrl = getenv('BASE_URL') 
    ?: ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http')
       . '://' . $_SERVER['HTTP_HOST'] . '/Grisolcr.lat';

$productos = ObtenerProductos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Óptica Grisol - Catálogo</title>

    <?php IncluirCSS(); ?>
</head>

<body>

<?php MostrarMenu(); ?>

<header class="catalogo-hero">

    
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

      <div class="container text-center">
        <h1 class="catalogo-title">Encuentre sus lentes ideales</h1>
        <p class="catalogo-subtitle">
            Tecnología avanzada, salud visual y asesoría personalizada
            para brindarte una experiencia única.
        </p>

    <!-- badges -->
    <div class="hero-badges">
      <span>✔ Protección UV</span>
      <span>✔ Diseños modernos</span>
      <span>✔ Alta calidad</span>
    </div>

  </div>
</header>

<section class="catalogo-section">
    <div class="container">

        <div class="catalogo-toolbar">
            <div class="catalogo-toolbar-top">
                <div>
                    <h2 class="catalogo-toolbar-title text-center">Catálogo de anteojos</h2>
                    <p class="catalogo-toolbar-subtitle text-center">Filtra por rango de precio y explora nuestros modelos disponibles.</p>
                </div>

                <div class="catalogo-resultados">
                    <span id="contadorResultados"><?= $totalProductos ?></span> 
                </div>
            </div>

            <div class="catalogo-filtros">
                <label>
                    <input type="radio" name="precio" value="todos" checked>
                    <span class="filtro-op">Todos</span>
                </label>

                <label>
                    <input type="radio" name="precio" value="1">
                    <span class="filtro-op">₡5.000 – ₡30.000</span>
                </label>

                <label>
                    <input type="radio" name="precio" value="2">
                    <span class="filtro-op">₡30.000 – ₡80.000</span>
                </label>

                <label>
                    <input type="radio" name="precio" value="3">
                    <span class="filtro-op">₡80.000 o más</span>
                </label>
            </div>
        </div>

        <div class="catalogo-grid-wrap">
            <div id="contenedorProductos" class="row">

                <?php foreach ($productos as $p): ?>
                    <?php 
                    $img = (!empty($p['Imagen'])) ? $p['Imagen'] : 'no-image.jpg';
                    $imgSrc = '/assets/img/' . rawurlencode($img);
                    ?>

                   <div class="col-6 col-md-6 col-lg-4 producto-item" data-precio="<?= $p['Precio'] ?>">
                    <div class="product-card">
                        <div class="product-img-wrapper">
                            <img src="<?= $imgSrc ?>" 
                                class="product-image"
                                alt="<?= htmlspecialchars($p['Nombre']) ?>">
                        </div>

                        <div class="product-body">
                            <h5 class="product-title"><?= htmlspecialchars($p['Nombre']) ?></h5>

                            <p class="product-price">
                                ₡<?= number_format($p['Precio'], 0, ',', '.') ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-producto-dialog">
                    <div class="modal-content modal-producto-content">
                        <div class="modal-header modal-producto-header">
                            <h5 class="modal-title" id="modalProductoLabel">Producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body modal-producto-body">
                            <img id="modalProductoImg" src="" alt="Imagen del producto" class="modal-producto-img">
                        </div>
                    </div>
                </div>
            </div>
                <?php endforeach; ?>

            </div>
        </div>

    </div>
</section>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

<script src="../assets/js/producto.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalProducto = document.getElementById('modalProducto');
    const modalImg = document.getElementById('modalProductoImg');
    const modalTitle = document.getElementById('modalProductoLabel');

    modalProducto.addEventListener('show.bs.modal', function (event) {
        const card = event.relatedTarget;

        const img = card.getAttribute('data-img');
        const nombre = card.getAttribute('data-nombre');

        modalImg.src = img;
        modalImg.alt = nombre;
        modalTitle.textContent = nombre;
    });
});
</script>
</body>
</html>
