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
