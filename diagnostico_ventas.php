<?php
/**
 * Script de diagnóstico para revisar detalles de compras
 * Colocar en la raíz del proyecto y acceder desde el navegador
 */

// Inicializar el autoloader de CodeIgniter
require 'vendor/autoload.php';
$app = Config\Services::codeigniter();
$app->initialize();
$app->boot();

// Obtener la configuración de la base de datos
$config = new \Config\Database();
$db = \Config\Database::connect();

// Obtener el ID de la venta desde la URL
$venta_id = isset($_GET['id']) ? intval($_GET['id']) : null;

echo "<h1>Diagnóstico de Ventas</h1>";

if (!$venta_id) {
    echo "<div style='color:red'>Por favor ingrese un ID de venta usando ?id=X en la URL</div>";
    
    // Mostrar las últimas 10 ventas para seleccionar
    echo "<h2>Últimas ventas registradas:</h2>";
    $query = $db->query("SELECT vc.id, vc.fecha, vc.total_venta, u.nombre, u.apellido, u.usuario 
                        FROM ventas_cabecera vc
                        JOIN usuarios u ON u.id = vc.usuario_id
                        ORDER BY vc.fecha DESC LIMIT 10");
    
    if ($query->getNumRows() > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse'>";
        echo "<tr><th>ID Venta</th><th>Fecha</th><th>Total</th><th>Cliente</th><th>Acción</th></tr>";
        
        foreach ($query->getResultArray() as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['fecha'] . "</td>";
            echo "<td>$" . number_format($row['total_venta'], 2) . "</td>";
            echo "<td>" . $row['nombre'] . " " . $row['apellido'] . " (" . $row['usuario'] . ")</td>";
            echo "<td><a href='?id=" . $row['id'] . "'>Ver detalles</a></td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No hay ventas registradas.</p>";
    }
    
    exit;
}

// Verificar si la venta existe
$query_cabecera = $db->query("SELECT vc.*, u.nombre, u.apellido, u.usuario, u.email 
                            FROM ventas_cabecera vc
                            JOIN usuarios u ON u.id = vc.usuario_id
                            WHERE vc.id = ?", [$venta_id]);

if ($query_cabecera->getNumRows() == 0) {
    echo "<div style='color:red'>No existe venta con ID: $venta_id</div>";
    exit;
}

$cabecera = $query_cabecera->getRowArray();

echo "<h2>Información de la Compra #$venta_id</h2>";
echo "<table border='0' cellpadding='3'>";
echo "<tr><td><strong>Fecha:</strong></td><td>" . $cabecera['fecha'] . "</td></tr>";
echo "<tr><td><strong>Cliente:</strong></td><td>" . $cabecera['nombre'] . " " . $cabecera['apellido'] . " (" . $cabecera['usuario'] . ")</td></tr>";
echo "<tr><td><strong>Email:</strong></td><td>" . $cabecera['email'] . "</td></tr>";
echo "<tr><td><strong>Total:</strong></td><td>$" . number_format($cabecera['total_venta'], 2) . "</td></tr>";
echo "</table>";

// Consulta SQL para los detalles (es la misma que en el controlador)
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

// Ejecutar la consulta
try {
    $query_detalle = $db->query($sql_detalle, [$venta_id]);
    $detalles = $query_detalle->getResultArray();

    echo "<h2>Detalles de la Compra</h2>";
    
    if (count($detalles) == 0) {
        echo "<div style='color:orange'>No hay productos en esta venta.</div>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse'>";
        echo "<tr><th>ID</th><th>Producto</th><th>Imagen</th><th>Precio Unitario</th><th>Cantidad</th><th>Subtotal</th></tr>";
        
        $total = 0;
        foreach ($detalles as $detalle) {
            $subtotal = $detalle['precio'];
            $total += $subtotal;
            
            echo "<tr>";
            echo "<td>" . $detalle['producto_id'] . "</td>";
            echo "<td>" . $detalle['nombre_prod'] . "</td>";
            
            // Mostrar la imagen o un placeholder
            $ruta_imagen = $detalle['imagen'];
            if (!empty($ruta_imagen) && $ruta_imagen != 'null' && $ruta_imagen != 'undefined') {
                // Si la ruta no comienza con 'assets/', agregarla
                if (strpos($ruta_imagen, 'assets/') !== 0) {
                    $ruta_imagen = 'assets/' . $ruta_imagen;
                }
                
                // Si no existe la imagen, usar la imagen por defecto
                if (!file_exists($ruta_imagen)) {
                    $ruta_imagen = 'assets/img/producto-sin-imagen.jpg';
                }
            } else {
                $ruta_imagen = 'assets/img/producto-sin-imagen.jpg';
            }
            
            echo "<td><img src='$ruta_imagen' style='width:80px; height:60px; object-fit:cover'></td>";
            
            echo "<td>$" . number_format($detalle['precio_vta'], 2) . "</td>";
            echo "<td>" . $detalle['cantidad'] . "</td>";
            echo "<td>$" . number_format($subtotal, 2) . "</td>";
            echo "</tr>";
        }
        
        echo "<tr style='font-weight:bold'>";
        echo "<td colspan='5' align='right'>Total:</td>";
        echo "<td>$" . number_format($total, 2) . "</td>";
        echo "</tr>";
        echo "</table>";

        // Mostrar el dump completo del primer detalle para debug
        echo "<h3>Dump de datos del primer producto (para debug)</h3>";
        echo "<pre>";
        print_r($detalles[0]);
        echo "</pre>";
    }
} catch (\Exception $e) {
    echo "<div style='color:red; background:#fee; padding:10px; margin:10px 0; border:1px solid #f99'>";
    echo "<strong>ERROR en la consulta SQL:</strong><br>";
    echo $e->getMessage();
    echo "</div>";
    
    // Mostrar la consulta que se intentó ejecutar
    echo "<h3>Consulta SQL intentada:</h3>";
    echo "<pre>" . str_replace('?', $venta_id, $sql_detalle) . "</pre>";
    
    // Verificar estructura de las tablas
    echo "<h3>Verificando estructura de las tablas:</h3>";
    
    echo "<h4>Tabla: ventas_cabecera</h4>";
    $fields_vc = $db->getFieldData('ventas_cabecera');
    echo "<ul>";
    foreach ($fields_vc as $field) {
        echo "<li><strong>{$field->name}</strong> - Tipo: {$field->type}";
        if (isset($field->max_length)) echo ", Longitud máx: {$field->max_length}";
        echo "</li>";
    }
    echo "</ul>";
    
    echo "<h4>Tabla: ventas_detalle</h4>";
    $fields_vd = $db->getFieldData('ventas_detalle');
    echo "<ul>";
    foreach ($fields_vd as $field) {
        echo "<li><strong>{$field->name}</strong> - Tipo: {$field->type}";
        if (isset($field->max_length)) echo ", Longitud máx: {$field->max_length}";
        echo "</li>";
    }
    echo "</ul>";
    
    echo "<h4>Tabla: productos</h4>";
    $fields_p = $db->getFieldData('productos');
    echo "<ul>";
    foreach ($fields_p as $field) {
        echo "<li><strong>{$field->name}</strong> - Tipo: {$field->type}";
        if (isset($field->max_length)) echo ", Longitud máx: {$field->max_length}";
        echo "</li>";
    }
    echo "</ul>";
    
    echo "<h4>Tabla: usuarios</h4>";
    $fields_u = $db->getFieldData('usuarios');
    echo "<ul>";
    foreach ($fields_u as $field) {
        echo "<li><strong>{$field->name}</strong> - Tipo: {$field->type}";
        if (isset($field->max_length)) echo ", Longitud máx: {$field->max_length}";
        echo "</li>";
    }
    echo "</ul>";
}

// Vínculo de regreso
echo "<p><a href='javascript:history.back()'>Regresar</a> | <a href='diagnostico_ventas.php'>Ver otra venta</a></p>";
?>
