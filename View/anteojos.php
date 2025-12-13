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
        <h1 class="fw-bold catalogo-title">Productos Disponibles</h1>
        <p class="catalogo-subtitle">Explore nuestra selección de lentes y armazones de alta calidad.</p>
    </div>
</header>


<section class="catalogo-section py-5">
    <div class="container">

        <div class="row g-4">

            
            <div class="col-lg-3">
                <div class="filtro-card p-4 shadow-sm">
                    <h4 class="fw-bold mb-3">Filtrar por precio</h4>

                    <label class="filtro-op">
                        <input type="radio" name="precio" value="todos" checked>
                        Mostrar todos
                    </label>

                    <label class="filtro-op">
                        <input type="radio" name="precio" value="1">
                        ₡5.000 – ₡30.000
                    </label>

                    <label class="filtro-op">
                        <input type="radio" name="precio" value="2">
                        ₡30.000 – ₡80.000
                    </label>

                    <label class="filtro-op">
                        <input type="radio" name="precio" value="3">
                        ₡80.000 o más
                    </label>
                </div>
            </div>

          
            <div class="col-lg-9">
                <div id="contenedorProductos" class="row gy-4">

                    <?php foreach ($productos as $p): ?>

                        <?php 
                     
                        $img = (!empty($p['Imagen'])) ? $p['Imagen'] : 'no-image.jpg';
                        ?>

                        <div class="col-md-4 producto-item" data-precio="<?= $p['Precio'] ?>">

                            <div class="product-card shadow-sm">
                                <div class="product-img-wrapper">
                                   <img src="<?= $baseUrl ?>/assets/img/<?= $img ?>" 
                                        class="product-image"
                                        alt="<?= $p['Nombre'] ?>">
                                </div>

                                <h5 class="product-title mt-3"><?= $p['Nombre'] ?></h5>

                                <p class="product-price">
                                    ₡<?= number_format($p['Precio'], 0, ',', '.') ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

<script src="../assets/js/producto.js"></script>

</body>
</html>