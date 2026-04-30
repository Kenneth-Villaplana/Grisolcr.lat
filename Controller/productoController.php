<?php

include_once __DIR__ . '/../Model/productoModel.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$tamanoMaximoImagen = 1 * 1024 * 1024; // 
$extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];


/* ============================= */
/* ELIMINAR PRODUCTO */
/* ============================= */
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


/* ============================= */
/* AGREGAR PRODUCTO */
/* ============================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST["btnEditarProducto"])) {

    $nombre = trim($_POST['Nombre'] ?? '');
    $descripcion = trim($_POST['Descripcion'] ?? '');
    $precio = floatval($_POST['Precio'] ?? 0);
    $cantidad = intval($_POST['Cantidad'] ?? 0);

    $nombreImagen = "no-image.jpg";

    if (isset($_FILES['Imagen']) && $_FILES['Imagen']['error'] === 0) {

        if ($_FILES['Imagen']['size'] > $tamanoMaximoImagen) {
            header("Location: ../View/agregarProducto.php?error=" . urlencode("La imagen es muy pesada. Máximo permitido: 1 MB."));
            exit;
        }

        $carpetaDestino = __DIR__ . '/../assets/img/';

        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        $archivoTmp = $_FILES['Imagen']['tmp_name'];
        $archivoNombre = basename($_FILES['Imagen']['name']);
        $extension = strtolower(pathinfo($archivoNombre, PATHINFO_EXTENSION));

        if (in_array($extension, $extPermitidas)) {

            $nombreImagen = uniqid() . "." . $extension;
            $movido = move_uploaded_file($archivoTmp, $carpetaDestino . $nombreImagen);

            if (!$movido) {
                header("Location: ../View/agregarProducto.php?error=" . urlencode("No se pudo guardar la imagen."));
                exit;
            }

        } else {
            header("Location: ../View/agregarProducto.php?error=" . urlencode("Formato de imagen no permitido."));
            exit;
        }
    }

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


/* ============================= */
/* EDITAR PRODUCTO */
/* ============================= */
if (isset($_POST["btnEditarProducto"])) {

    $productoId = intval($_POST["ProductoId"] ?? 0);
    $nombre = trim($_POST["Nombre"] ?? '');
    $descripcion = trim($_POST["Descripcion"] ?? '');
    $precio = floatval($_POST["Precio"] ?? 0);
    $cantidad = intval($_POST["Cantidad"] ?? 0);

    if ($productoId <= 0) {
        $_SESSION["txtMensaje"] = "Producto inválido";
        header("Location: ../View/inventario.php");
        exit;
    }

    $productoActual = ObtenerProductoPorId($productoId);
    $nombreImagen = $productoActual['Imagen'] ?? 'no-image.jpg';

    if (isset($_FILES['Imagen']) && $_FILES['Imagen']['error'] === 0) {

        if ($_FILES['Imagen']['size'] > $tamanoMaximoImagen) {
            $_SESSION["txtMensaje"] = "La imagen es muy pesada. Máximo permitido: 1 MB.";
            header("Location: ../View/editarProducto.php?id=" . $productoId);
            exit;
        }

        $carpetaDestino = __DIR__ . '/../assets/img/';

        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        $archivoTmp = $_FILES['Imagen']['tmp_name'];
        $archivoNombre = basename($_FILES['Imagen']['name']);
        $extension = strtolower(pathinfo($archivoNombre, PATHINFO_EXTENSION));

        if (in_array($extension, $extPermitidas)) {

            $nombreImagen = uniqid() . "." . $extension;
            $movido = move_uploaded_file($archivoTmp, $carpetaDestino . $nombreImagen);

            if (!$movido) {
                $_SESSION["txtMensaje"] = "No se pudo guardar la nueva imagen";
                header("Location: ../View/editarProducto.php?id=" . $productoId);
                exit;
            }

        } else {
            $_SESSION["txtMensaje"] = "Formato de imagen no permitido";
            header("Location: ../View/editarProducto.php?id=" . $productoId);
            exit;
        }
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

    $resultadoEdit = EditarProductoModel($productoId, $nombre, $descripcion, $precio, $cantidad, $nombreImagen);

    $_SESSION["txtMensaje"] = $resultadoEdit['mensaje'];

    if ($resultadoEdit['resultado'] == 1) {
        $_SESSION["CambioExitoso"] = true;
    }

    header("Location: ../View/editarProducto.php?id=" . $productoId);
    exit;
}

?>