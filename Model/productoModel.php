<?php
include_once __DIR__ . '/../Model/baseDatos.php';



function AgregarProductoModel($nombre, $descripcion, $precio, $cantidad)
{
    try {
        $enlace = AbrirBD();
        $sentencia = $enlace->prepare("CALL AgregarProducto(?, ?, ?, ?)");
        if(!$sentencia) {
            throw new Exception($enlace->error);
        }

        $sentencia->bind_param("ssii", $nombre, $descripcion, $precio, $cantidad);
        $sentencia->execute();
        $sentencia->close();
        CerrarBD($enlace);

        return ['resultado' => 1, 'mensaje' => 'Producto agregado con éxito'];

    } catch(Exception $ex) {
        return ['resultado' => 0, 'mensaje' => 'Error en el servidor: '.$ex->getMessage()];
    }
}


function ObtenerProductos($ProductoId = null)
{
    try {
        $enlace = AbrirBD();

        if ($ProductoId) {
            $sentencia = $enlace->prepare("CALL FiltroPorId(?)");
            $sentencia->bind_param("i", $ProductoId);
        } else {
            $sentencia = $enlace->prepare("CALL MostrarProductos()");
        }

        $sentencia->execute();
        $resultado = $sentencia->get_result();

        $productos = [];
        while ($row = $resultado->fetch_assoc()) {
            $cantidad = (int)$row['Cantidad'];

            // Color según cantidad
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
        CerrarBD($enlace);
        return $productos;

    } catch (Exception $ex) {
        return [];
    }
}

function EditarProductoModel($productoId, $nombre, $descripcion, $precio, $cantidad)
{
    try {
        $enlace = AbrirBD();
        $sentencia = $enlace->prepare("CALL EditarProducto(?, ?, ?, ?, ?)");
        if(!$sentencia) {
            throw new Exception($enlace->error);
        }

       
        $sentencia->bind_param("issii", 
             $productoId,
             $nombre, 
             $descripcion,
             $precio, 
             $cantidad
        );

        $sentencia->execute();
        $sentencia->close();
        CerrarBD($enlace);

        return ['resultado' => 1, 'mensaje' => 'Cambio realizado con exito'];

    } catch(Exception $ex) {
        return ['resultado' => 0, 'mensaje' => 'Error en el servidor: '.$ex->getMessage()];
    }
}

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

        return ['resultado' => 1, 'mensaje' => 'Producto eliminado con éxito'];
    } catch (Exception $ex) {
        return ['resultado' => 0, 'mensaje' => 'Error: ' . $ex->getMessage()];
    }
}
?>