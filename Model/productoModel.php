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

        // DEBUG 1: Check what columns are returned from database
        error_log("=== DEBUG ObtenerProductos START ===");
        
        if ($resultado->num_rows > 0) {
            $fields = $resultado->fetch_fields();
            $fieldNames = [];
            foreach ($fields as $field) {
                $fieldNames[] = $field->name;
            }
            error_log("Database columns returned: " . implode(', ', $fieldNames));
            $resultado->data_seek(0); // Reset pointer
        } else {
            error_log("No rows returned from database");
        }

        $productos = [];
        $rowCount = 0;
        
        while ($row = $resultado->fetch_assoc()) {
            $rowCount++;
            
            // DEBUG 2: Log each row structure
            error_log("--- Row {$rowCount} ---");
            error_log("Row keys: " . implode(', ', array_keys($row)));
            error_log("Row data: " . print_r($row, true));
            
            // Check what key actually exists for cantidad
            $cantidadKey = null;
            $cantidadValue = 0;
            
            if (isset($row['Cantidad'])) {
                $cantidadKey = 'Cantidad';
                $cantidadValue = (int)$row['Cantidad'];
                error_log("Found 'Cantidad' key with value: {$cantidadValue}");
            } elseif (isset($row['cantidad'])) {
                $cantidadKey = 'cantidad';
                $cantidadValue = (int)$row['cantidad'];
                error_log("Found 'cantidad' key with value: {$cantidadValue}");
            } elseif (isset($row['CANTIDAD'])) {
                $cantidadKey = 'CANTIDAD';
                $cantidadValue = (int)$row['CANTIDAD'];
                error_log("Found 'CANTIDAD' key with value: {$cantidadValue}");
            } else {
                error_log("No cantidad key found in row. Available keys: " . implode(', ', array_keys($row)));
                // Try to find any key that might contain quantity
                foreach ($row as $key => $value) {
                    if (stripos($key, 'cant') !== false || stripos($key, 'stock') !== false || stripos($key, 'quantity') !== false) {
                        $cantidadKey = $key;
                        $cantidadValue = (int)$value;
                        error_log("Found potential quantity key '{$key}' with value: {$cantidadValue}");
                        break;
                    }
                }
            }
            
            // Store with consistent key name
            $row['Cantidad'] = $cantidadValue;

            // Color según cantidad
            if ($cantidadValue < 20) {
                $colorBarra = 'bg-danger';
            } elseif ($cantidadValue <= 50) {
                $colorBarra = 'bg-warning';
            } else {
                $colorBarra = 'bg-success';
            }

            $anchoBarra = ($cantidadValue > 100) ? 100 : $cantidadValue;

            $row['ColorBarra'] = $colorBarra;
            $row['AnchoBarra'] = $anchoBarra;
            
            // DEBUG 3: Log final row structure
            error_log("Final row structure keys: " . implode(', ', array_keys($row)));
            
            $productos[] = $row;
        }
        
        error_log("Total rows processed: {$rowCount}");
        error_log("=== DEBUG ObtenerProductos END ===");

        $sentencia->close();
        CerrarBD($enlace);
        
        // DEBUG 4: Check final productos array
        error_log("Returning " . count($productos) . " products");
        if (!empty($productos)) {
            error_log("First product keys: " . implode(', ', array_keys($productos[0])));
        }
        
        return $productos;

    } catch (Exception $ex) {
        error_log("Error en ObtenerProductos: " . $ex->getMessage());
        error_log("Stack trace: " . $ex->getTraceAsString());
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