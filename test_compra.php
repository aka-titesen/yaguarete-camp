<?php
/**
 * Script para testear directamente el detalle de una compra
 * Uso: http://localhost/proyecto_Martinez_Gonzalez/test_compra.php?id=X 
 * (donde X es el ID de la compra a probar)
 */

// Cargar autoloader de CodeIgniter
require 'vendor/autoload.php';
$app = Config\Services::codeigniter();
$app->initialize();
$app->boot();

// Obtener ID de compra de la URL
$venta_id = isset($_GET['id']) ? intval($_GET['id']) : 1; // Default a 1 si no se especifica

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test de Detalle de Compra</title>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Test de Detalle de Compra #$venta_id</h1>";

// Conectar a la base de datos
$db = \Config\Database::connect();

// Verificar que la compra existe
$query_cabecera = $db->query("SELECT id, fecha, total_venta, usuario_id FROM ventas_cabecera WHERE id = ?", [$venta_id]);

if ($query_cabecera === false) {
    echo "<div class='error'>Error al consultar la cabecera: " . $db->error()['message'] . "</div>";
    exit("</body></html>");
}

if ($query_cabecera->getNumRows() == 0) {
    echo "<div class='error'>No existe una compra con ID $venta_id</div>";
    exit("</body></html>");
}

$cabecera = $query_cabecera->getRowArray();
echo "<div class='success'>Cabecera encontrada: " . print_r($cabecera, true) . "</div>";

// 1. Consulta INNER JOIN (la original que fallaba)
echo "<h2>Test 1: Consulta con INNER JOIN</h2>";

$sql_inner = "SELECT 
    vd.id, 
    vd.venta_id, 
    vd.producto_id, 
    vd.cantidad, 
    vd.precio,
    vc.fecha, 
    vc.total_venta, 
    vc.usuario_id,
    p.nombre_prod, 
    p.descripcion, 
    p.imagen, 
    p.precio_vta,
    u.nombre, 
    u.apellido, 
    u.email, 
    u.usuario
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

try {
    echo "<pre>SQL: " . str_replace('?', $venta_id, $sql_inner) . "</pre>";
    
    $query_inner = $db->query($sql_inner, [$venta_id]);
    
    if ($query_inner === false) {
        echo "<div class='error'>La consulta con INNER JOIN falló: " . $db->error()['message'] . "</div>";
    } else {
        echo "<div class='success'>La consulta con INNER JOIN fue exitosa.</div>";
        
        $resultados = $query_inner->getResultArray();
        echo "<p>Cantidad de resultados: " . count($resultados) . "</p>";
        
        if (count($resultados) > 0) {
            echo "<h3>Primer resultado:</h3>";
            echo "<pre>" . print_r($resultados[0], true) . "</pre>";
        }
    }
} catch (\Exception $e) {
    echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
}

// 2. Consulta LEFT JOIN (la versión más permisiva)
echo "<h2>Test 2: Consulta con LEFT JOIN</h2>";

$sql_left = "SELECT 
    vd.id, 
    vd.venta_id, 
    vd.producto_id, 
    vd.cantidad, 
    vd.precio,
    vc.fecha, 
    vc.total_venta, 
    vc.usuario_id,
    p.nombre_prod, 
    p.descripcion, 
    p.imagen, 
    p.precio_vta,
    u.nombre, 
    u.apellido, 
    u.email, 
    u.usuario
  FROM 
    ventas_detalle vd
  LEFT JOIN 
    ventas_cabecera vc ON vc.id = vd.venta_id
  LEFT JOIN 
    productos p ON p.id = vd.producto_id
  LEFT JOIN 
    usuarios u ON u.id = vc.usuario_id
  WHERE 
    vd.venta_id = ?
  ORDER BY 
    p.nombre_prod ASC";

try {
    echo "<pre>SQL: " . str_replace('?', $venta_id, $sql_left) . "</pre>";
    
    $query_left = $db->query($sql_left, [$venta_id]);
    
    if ($query_left === false) {
        echo "<div class='error'>La consulta con LEFT JOIN falló: " . $db->error()['message'] . "</div>";
    } else {
        echo "<div class='success'>La consulta con LEFT JOIN fue exitosa.</div>";
        
        $resultados = $query_left->getResultArray();
        echo "<p>Cantidad de resultados: " . count($resultados) . "</p>";
        
        if (count($resultados) > 0) {
            echo "<h3>Primer resultado:</h3>";
            echo "<pre>" . print_r($resultados[0], true) . "</pre>";
        }
    }
} catch (\Exception $e) {
    echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
}

// 3. Consulta simplificada (solo los campos necesarios)
echo "<h2>Test 3: Consulta simplificada</h2>";

$sql_simple = "SELECT 
    vd.id, 
    vd.venta_id, 
    vd.producto_id, 
    vd.cantidad, 
    vd.precio,
    p.nombre_prod, 
    p.imagen
  FROM 
    ventas_detalle vd
  LEFT JOIN 
    productos p ON p.id = vd.producto_id
  WHERE 
    vd.venta_id = ?";

try {
    echo "<pre>SQL: " . str_replace('?', $venta_id, $sql_simple) . "</pre>";
    
    $query_simple = $db->query($sql_simple, [$venta_id]);
    
    if ($query_simple === false) {
        echo "<div class='error'>La consulta simplificada falló: " . $db->error()['message'] . "</div>";
    } else {
        echo "<div class='success'>La consulta simplificada fue exitosa.</div>";
        
        $resultados = $query_simple->getResultArray();
        echo "<p>Cantidad de resultados: " . count($resultados) . "</p>";
        
        if (count($resultados) > 0) {
            echo "<h3>Primeros resultados:</h3>";
            echo "<pre>" . print_r(array_slice($resultados, 0, 3), true) . "</pre>";
        }
    }
} catch (\Exception $e) {
    echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
}

// 4. Verificar los detalles directamente (sin JOINs)
echo "<h2>Test 4: Consulta directa de detalles</h2>";

$sql_directo = "SELECT * FROM ventas_detalle WHERE venta_id = ?";

try {
    echo "<pre>SQL: " . str_replace('?', $venta_id, $sql_directo) . "</pre>";
    
    $query_directo = $db->query($sql_directo, [$venta_id]);
    
    if ($query_directo === false) {
        echo "<div class='error'>La consulta directa falló: " . $db->error()['message'] . "</div>";
    } else {
        echo "<div class='success'>La consulta directa fue exitosa.</div>";
        
        $resultados = $query_directo->getResultArray();
        echo "<p>Cantidad de resultados directos: " . count($resultados) . "</p>";
        
        if (count($resultados) > 0) {
            echo "<h3>Primeros resultados directos:</h3>";
            echo "<pre>" . print_r(array_slice($resultados, 0, 3), true) . "</pre>";
        } else {
            echo "<div class='error'>No hay detalles para esta venta. Este es el problema principal.</div>";
        }
    }
} catch (\Exception $e) {
    echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
}

echo "<p><a href='test_compra.php?id=" . ($venta_id + 1) . "'>Probar siguiente compra</a> | <a href='test_compra.php?id=" . ($venta_id - 1) . "'>Probar compra anterior</a></p>";
echo "</body></html>";
?>
