<?php

include_once __DIR__ . '/../Model/productoModel.php';

if(session_status() == PHP_SESSION_NONE) {
    session_start();
}



if (isset($_GET['eliminarProducto'])) {
    $productoId = intval($_GET['eliminarProducto']);
    $resultado = EliminarProductoModel($productoId);

    if ($resultado['resultado'] == 1) {
        header("Location: ../View/inventario.php?msg=eliminado");
        exit;
    } else {
        header("Location: ../View/inventario.php?error=" . urlencode($resultado['mensaje']));
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST["btnEditarProducto"])) {
    $nombre = trim($_POST['Nombre'] ?? '');
    $descripcion = trim($_POST['Descripcion'] ?? '');
    $precio = floatval($_POST['Precio'] ?? 0);
    $cantidad = intval($_POST['Cantidad'] ?? 0);

    $nombreImagen = "no-image.jpg"; // por defecto

   if (isset($_FILES['Imagen']) && $_FILES['Imagen']['error'] === 0) {

    $carpetaDestino = __DIR__ . '/../assets/img/productos/';

    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }

    $archivoTmp = $_FILES['Imagen']['tmp_name'];
    $archivoNombre = basename($_FILES['Imagen']['name']);
    $extension = strtolower(pathinfo($archivoNombre, PATHINFO_EXTENSION));

    $extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($extension, $extPermitidas)) {

        $nombreImagen = uniqid() . "." . $extension;

        move_uploaded_file($archivoTmp, $carpetaDestino . $nombreImagen);
    }
    
    }

      //validaciones
    if ($nombre === '' || $descripcion === '') {
        header("Location: ../View/agregarProducto.php?error=Datos incompletos");
        exit;
    }

    if ($precio <= 0) {
        header("Location: ../View/agregarProducto.php?error=El precio debe ser mayor a 0");
        exit;
    }

    if ($cantidad <= 0) {
        header("Location: ../View/agregarProducto.php?error=La cantidad debe ser mayor a 0");
        exit;
    }

    $resultado = AgregarProductoModel($nombre, $descripcion, $precio, $cantidad, $nombreImagen);

    if ($resultado['resultado'] == 1) {
        header("Location: ../View/inventario.php?msg=agregado");
        exit;
    } else {
        header("Location: ../View/agregarProducto.php?error=" . urlencode($resultado['mensaje']));
        exit;
    }
}


if (isset($_POST["btnEditarProducto"])) {
    $productoId = intval($_POST["ProductoId"] ?? 0);
    $nombre = trim($_POST["Nombre"] ?? '');
    $descripcion = trim($_POST["Descripcion"] ?? '');
    $precio = floatval($_POST["Precio"] ?? 0);
    $cantidad = intval($_POST["Cantidad"] ?? 0);

    //validaciones

    if ($productoId <= 0) {
        $_SESSION["txtMensaje"] = "Producto inválido";
        header("Location: ../View/inventario.php");
        exit;
    }

    if ($nombre === '' || $descripcion === '') {
        $_SESSION["txtMensaje"] = "Datos incompletos";
        header("Location: ../View/editarProducto.php?id=" . $productoId);
        exit;
    }

    if ($precio <= 0) {
    $_SESSION["txtMensaje"] = "El precio debe ser mayor a 0";
    header("Location: ../View/editarProducto.php?id=" . $productoId);
    exit;
    }

    if ($cantidad <= 0) {
        $_SESSION["txtMensaje"] = "La cantidad debe ser mayor a 0";
        header("Location: ../View/editarProducto.php?id=" . $productoId);
        exit;
    }

    $resultadoEdit = EditarProductoModel($productoId, $nombre, $descripcion, $precio, $cantidad);

    $_SESSION["txtMensaje"] = $resultadoEdit['mensaje'];
    if ($resultadoEdit['resultado'] == 1) {
        $_SESSION["CambioExitoso"] = true;
    }

    header("Location: ../View/editarProducto.php?id=" . $productoId);
    exit;
}


if (isset($_GET['id'])) {
    $productoId = $_GET['id'];
    $productos = ObtenerProductos($productoId);
    $producto = $productos[0] ?? null;

    if (!$producto) {
        die("Producto no encontrado");
    }
}
?>