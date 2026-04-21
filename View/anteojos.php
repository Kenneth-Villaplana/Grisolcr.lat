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

<header class="catalogo-hero d-flex align-items-center text-center">
    <div class="container position-relative">
        <h1 class="catalogo-title">Encuentre sus lentes ideales</h1>
        <p class="catalogo-subtitle">
            Descubra nuestra colección de lentes y armazones con diseños modernos, cómodos y elegantes para cada estilo.
        </p>
    </div>
</header>

<section class="catalogo-section">
    <div class="container">

        <div class="catalogo-toolbar">
            <div class="catalogo-toolbar-top">
                <div>
                    <h2 class="catalogo-toolbar-title">Catálogo de anteojos</h2>
                    <p class="catalogo-toolbar-subtitle">Filtra por rango de precio y explora nuestros modelos disponibles.</p>
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

                    <div class="col-lg-4 col-md-6 producto-item" data-precio="<?= $p['Precio'] ?>">
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
                <?php endforeach; ?>

            </div>
        </div>

    </div>
</section>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

<script src="../assets/js/producto.js"></script>

</body>
</html>
