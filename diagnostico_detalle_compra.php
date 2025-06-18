<?php
/**
 * Script de diagnóstico específico para la vista de detalle de compras
 * Coloque este script en la raíz del proyecto y acceda desde el navegador:
 * http://localhost/proyecto_Martinez_Gonzalez/diagnostico_detalle_compra.php?id=X
 * (donde X es el ID de la compra a revisar)
 */

// Configuración y autoloading
require 'vendor/autoload.php';
$app = Config\Services::codeigniter();
$app->initialize();
$app->boot();

// Obtener la configuración de la base de datos
$db = \Config\Database::connect();

// Obtener ID de compra desde la URL
$venta_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Establecer encabezados y estilo
echo "<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico Detalle Compra</title>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2, h3 { color: #333; }
        .error { color: red; background: #fee; padding: 10px; border: 1px solid #f99; }
        .success { color: green; background: #efe; padding: 10px; border: 1px solid #9f9; }
        .warning { color: #855; background: #ffe; padding: 10px; border: 1px solid #ff9; }
        .info { background: #f0f8ff; padding: 10px; border: 1px solid #add8e6; }
        .code { font-family: monospace; background: #f5f5f5; padding: 10px; overflow: auto; border: 1px solid #ddd; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        img { max-width: 100px; max-height: 100px; }
    </style>
</head>
<body>
    <h1>Diagnóstico de Detalle de Compra</h1>";

if (!$venta_id) {
    echo "<div class='error'>Por favor especifique un ID de venta usando ?id=X en la URL</div>";
    echo "<h2>Últimas 10 ventas:</h2>";
    
    $query = $db->query("SELECT vc.id, vc.fecha, vc.total_venta, u.nombre, u.apellido 
                        FROM ventas_cabecera vc
                        JOIN usuarios u ON u.id = vc.usuario_id
                        ORDER BY vc.fecha DESC LIMIT 10");
    
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Cliente</th>
                <th>Acciones</th>
            </tr>";
    
    foreach ($query->getResultArray() as $row) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['fecha']}</td>
                <td>\${$row['total_venta']}</td>
                <td>{$row['nombre']} {$row['apellido']}</td>
                <td><a href='?id={$row['id']}'>Ver diagnóstico</a></td>
              </tr>";
    }
    
    echo "</table>";
    exit("</body></html>");
}

// 1. Verificar si la cabecera de venta existe
echo "<h2>1. Verificando cabecera de venta #{$venta_id}</h2>";

$query_cabecera = $db->query("SELECT vc.*, u.nombre, u.apellido, u.email, u.usuario 
                              FROM ventas_cabecera vc
                              JOIN usuarios u ON u.id = vc.usuario_id
                              WHERE vc.id = ?", [$venta_id]);

if ($query_cabecera->getNumRows() == 0) {
    echo "<div class='error'>Error: No existe la venta con ID {$venta_id}</div>";
    exit("</body></html>");
}

$cabecera = $query_cabecera->getRowArray();
echo "<div class='success'>Cabecera de venta encontrada correctamente.</div>";

echo "<table>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Cliente</th>
            <th>Email</th>
        </tr>
        <tr>
            <td>{$cabecera['id']}</td>
            <td>{$cabecera['fecha']}</td>
            <td>\${$cabecera['total_venta']}</td>
            <td>{$cabecera['nombre']} {$cabecera['apellido']}</td>
            <td>{$cabecera['email']}</td>
        </tr>
      </table>";

// 2. Verificar los detalles de la venta
echo "<h2>2. Verificando detalles de venta</h2>";

$sql_detalle = "SELECT 
    vd.id, vd.venta_id, vd.producto_id, vd.cantidad, vd.precio,
    vc.fecha, vc.total_venta, vc.usuario_id,
    p.nombre_prod, p.descripcion, p.imagen, p.precio_vta,
    u.nombre, u.apellido, u.email, u.usuario
  FROM 
    ventas_detalle vd
  INNER JOIN 
    ventas_cabecera vc ON vc.id = vd.venta_id
  INNER JOIN 
    productos p ON p.id = vd.producto_id
  INNER JOIN 
    usuarios u ON u.id = vc.usuario_id
  WHERE 
    vd.venta_id = ?
  ORDER BY 
    p.nombre_prod ASC";

echo "<div class='code'>" . htmlspecialchars(str_replace('?', $venta_id, $sql_detalle)) . "</div>";

try {
    $query_detalle = $db->query($sql_detalle, [$venta_id]);
    $detalle = $query_detalle->getResultArray();
    
    if (count($detalle) == 0) {
        echo "<div class='warning'>No se encontraron detalles para esta venta. La venta existe pero no tiene productos asociados.</div>";
    } else {
        echo "<div class='success'>Se encontraron " . count($detalle) . " productos en esta venta.</div>";
        
        echo "<h3>Detalles de productos:</h3>";
        echo "<table>
                <tr>
                    <th>#</th>
                    <th>ID Producto</th>
                    <th>Nombre</th>
                    <th>Imagen</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                </tr>";
        
        $total = 0;
        foreach ($detalle as $i => $item) {
            $ruta_imagen = $item['imagen'];
            if (!empty($ruta_imagen) && $ruta_imagen != 'null' && $ruta_imagen != 'undefined') {
                if (strpos($ruta_imagen, 'assets/') !== 0) {
                    $ruta_imagen = 'assets/' . $ruta_imagen;
                }
                
                // Verificar si la imagen existe físicamente
                if (!file_exists($ruta_imagen)) {
                    $ruta_imagen = 'assets/img/producto-sin-imagen.jpg';
                }
            } else {
                $ruta_imagen = 'assets/img/producto-sin-imagen.jpg';
            }
            
            $precio = isset($item['precio']) && $item['precio'] > 0 ? $item['precio'] : $item['precio_vta'];
            $cantidad = $item['cantidad'];
            $subtotal = $precio * $cantidad;
            $total += $subtotal;
            
            echo "<tr>
                    <td>" . ($i + 1) . "</td>
                    <td>{$item['producto_id']}</td>
                    <td>{$item['nombre_prod']}</td>
                    <td><img src='{$ruta_imagen}' alt='{$item['nombre_prod']}'></td>
                    <td>{$cantidad}</td>
                    <td>\$" . number_format($precio, 2) . "</td>
                    <td>\$" . number_format($subtotal, 2) . "</td>
                  </tr>";
        }
        
        echo "<tr>
                <td colspan='6' style='text-align:right'><strong>Total:</strong></td>
                <td><strong>\$" . number_format($total, 2) . "</strong></td>
              </tr>";
        echo "</table>";
        
        // 3. Verificar si hay alguna discrepancia en el total calculado vs. el total almacenado
        if (abs($total - $cabecera['total_venta']) > 0.01) {
            echo "<div class='warning'>Advertencia: El total calculado (\$" . number_format($total, 2) . ") no coincide con el total almacenado en la cabecera (\$" . number_format($cabecera['total_venta'], 2) . ")</div>";
        } else {
            echo "<div class='success'>El total calculado coincide con el total almacenado en la cabecera.</div>";
        }
        
        // 4. Verificar estructura completa del primer detalle (para debugging)
        echo "<h3>Estructura completa del primer detalle:</h3>";
        echo "<div class='code'><pre>" . print_r($detalle[0], true) . "</pre></div>";
    }
} catch (\Exception $e) {
    echo "<div class='error'>Error al ejecutar la consulta SQL: " . $e->getMessage() . "</div>";
}

// 5. Verificar rutas de imágenes
echo "<h2>5. Verificando rutas de imágenes</h2>";

$rutas_invalidas = 0;
$rutas_validas = 0;

if (!empty($detalle)) {
    foreach ($detalle as $item) {
        $ruta_imagen = $item['imagen'];
        
        echo "<h3>Imagen para producto: {$item['nombre_prod']}</h3>";
        echo "<div class='info'>Ruta original: " . ($ruta_imagen ?: 'NULL') . "</div>";
        
        // Procesar la ruta como lo hace la vista
        if (!empty($ruta_imagen) && $ruta_imagen != 'null' && $ruta_imagen != 'undefined') {
            if (strpos($ruta_imagen, 'assets/') !== 0 && strpos($ruta_imagen, '/assets/') !== 0) {
                $ruta_imagen = 'assets/' . $ruta_imagen;
                echo "<div class='info'>Ruta después de agregar 'assets/': {$ruta_imagen}</div>";
            }
            
            // Eliminar cualquier doble slash
            $ruta_imagen = str_replace('//', '/', $ruta_imagen);
            echo "<div class='info'>Ruta después de eliminar doble slash: {$ruta_imagen}</div>";
            
            // Normalizar la ruta
            if (strpos($ruta_imagen, '/') === 0) {
                $ruta_imagen = ltrim($ruta_imagen, '/');
                echo "<div class='info'>Ruta después de eliminar slash inicial: {$ruta_imagen}</div>";
            }
            
            // Verificar físicamente si existe
            if (file_exists($ruta_imagen)) {
                echo "<div class='success'>La imagen existe físicamente en: {$ruta_imagen}</div>";
                $rutas_validas++;
            } else {
                echo "<div class='warning'>La imagen NO existe en: " . getcwd() . "/{$ruta_imagen}</div>";
                $rutas_invalidas++;
            }
            
            echo "<div><img src='{$ruta_imagen}' alt='Imagen del producto'></div>";
        } else {
            echo "<div class='warning'>La ruta de imagen está vacía o es inválida, se usará la imagen por defecto.</div>";
            echo "<div><img src='assets/img/producto-sin-imagen.jpg' alt='Imagen por defecto'></div>";
            $rutas_invalidas++;
        }
    }
    
    echo "<div class='info'>Resumen de imágenes: {$rutas_validas} válidas, {$rutas_invalidas} inválidas.</div>";
}

// 6. Verificar valores de fecha
echo "<h2>6. Verificando valores de fecha</h2>";

$fecha = $cabecera['fecha'];
echo "<div class='info'>Fecha original: {$fecha}</div>";

try {
    $fecha_formateada = date('d/m/Y', strtotime($fecha));
    echo "<div class='success'>Fecha formateada: {$fecha_formateada}</div>";
} catch (\Exception $e) {
    echo "<div class='error'>Error al formatear fecha: " . $e->getMessage() . "</div>";
}

echo "<p><a href='javascript:history.back()'>Regresar</a> | <a href='diagnostico_detalle_compra.php'>Ver otra venta</a></p>";

echo "</body></html>";
?>
