<?php
include_once __DIR__ . '/../Model/baseDatos.php';


/* ========================= */
/* AGREGAR PRODUCTO */
/* ========================= */

function AgregarProductoModel($nombre, $descripcion, $precio, $cantidad, $nombreImagen)
{
    try {

        $enlace = AbrirBD();

        $sentencia = $enlace->prepare("CALL AgregarProducto(?, ?, ?, ?, ?)");

        if(!$sentencia){
            throw new Exception($enlace->error);
        }

        $sentencia->bind_param("ssdis", $nombre, $descripcion, $precio, $cantidad, $nombreImagen);

        $sentencia->execute();

        $sentencia->close();

        while ($enlace->more_results() && $enlace->next_result()) {;}

        CerrarBD($enlace);

        return [
            'resultado' => 1,
            'mensaje' => 'Producto agregado con éxito'
        ];

    } catch(Exception $ex){

        return [
            'resultado' => 0,
            'mensaje' => 'Error en el servidor: '.$ex->getMessage()
        ];
    }
}


/* ========================= */
/* OBTENER PRODUCTOS */
/* ========================= */

function ObtenerProductos($ProductoId = null)
{
    try {

        $enlace = AbrirBD();

        if ($ProductoId !== null) {

            $sentencia = $enlace->prepare("CALL FiltroPorId(?)");

            if(!$sentencia){
                throw new Exception($enlace->error);
            }

            $sentencia->bind_param("i", $ProductoId);

        } else {

            $sentencia = $enlace->prepare("CALL MostrarProductos()");

            if(!$sentencia){
                throw new Exception($enlace->error);
            }
        }

        $sentencia->execute();

        $resultado = $sentencia->get_result();

        $productos = [];

        while ($row = $resultado->fetch_assoc()) {

            $cantidad = isset($row['Cantidad']) ? (int)$row['Cantidad'] : 0;

            /* color barra inventario */

            if ($cantidad < 20) {
                $colorBarra = 'bg-danger';
            } elseif ($cantidad <= 50) {
                $colorBarra = 'bg-warning';
            } else {
                $colorBarra = 'bg-success';
            }

            $anchoBarra = ($cantidad > 100) ? 100 : $cantidad;

            $row['ColorBarra'] = $colorBarra;
            $row['AnchoBarra'] = $anchoBarra;

            $productos[] = $row;
        }

        $sentencia->close();

        /* limpiar resultados pendientes */

        while ($enlace->more_results() && $enlace->next_result()) {;}

        CerrarBD($enlace);

        return $productos;

    } catch (Exception $ex) {

        error_log("Error en ObtenerProductos: ".$ex->getMessage());

        return [];
    }
}


/* ========================= */
/* OBTENER PRODUCTO POR ID */
/* ========================= */

function ObtenerProductoPorId($productoId)
{
    try {

        $enlace = AbrirBD();

        $sentencia = $enlace->prepare("CALL FiltroPorId(?)");

        if(!$sentencia){
            throw new Exception($enlace->error);
        }

        $sentencia->bind_param("i", $productoId);

        $sentencia->execute();

        $resultado = $sentencia->get_result();

        $producto = $resultado->fetch_assoc();

        $sentencia->close();

        while ($enlace->more_results() && $enlace->next_result()) {;}

        CerrarBD($enlace);

        return $producto;

    } catch (Exception $ex) {

        error_log("Error ObtenerProductoPorId: ".$ex->getMessage());

        return null;
    }
}


/* ========================= */
/* EDITAR PRODUCTO */
/* ========================= */

function EditarProductoModel($productoId, $nombre, $descripcion, $precio, $cantidad, $nombreImagen)
{
    try {

        $enlace = AbrirBD();

        $sentencia = $enlace->prepare("CALL EditarProducto(?, ?, ?, ?, ?)");

        if(!$sentencia){
            throw new Exception($enlace->error);
        }

        $sentencia->bind_param(
            "issii",
            $productoId,
            $nombre,
            $descripcion,
            $precio,
            $cantidad,
            $nombreImagen
        );

        $sentencia->execute();

        $sentencia->close();

        while ($enlace->more_results() && $enlace->next_result()) {;}

        CerrarBD($enlace);

        return [
            'resultado' => 1,
            'mensaje' => 'Cambio realizado con éxito'
        ];

    } catch(Exception $ex){

        return [
            'resultado' => 0,
            'mensaje' => 'Error en el servidor: '.$ex->getMessage()
        ];
    }
}


/* ========================= */
/* ELIMINAR PRODUCTO */
/* ========================= */

function EliminarProductoModel($productoId)
{
    try {

        $enlace = AbrirBD();

        $sentencia = $enlace->prepare("CALL EliminarProducto(?)");

        if (!$sentencia) {
            throw new Exception("Error al preparar la sentencia: " . $enlace->error);
        }

        $sentencia->bind_param("i", $productoId);

        $sentencia->execute();

        if ($sentencia->errno) {
            throw new Exception("Error al ejecutar: " . $sentencia->error);
        }

        $sentencia->close();

        while ($enlace->more_results() && $enlace->next_result()) {;}

        CerrarBD($enlace);

        return [
            'resultado' => 1,
            'mensaje' => 'Producto eliminado con éxito'
        ];

    } catch (Exception $ex) {

        return [
            'resultado' => 0,
            'mensaje' => 'Error: ' . $ex->getMessage()
        ];
    }
}

?>