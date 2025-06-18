<?php
/**
 * Script para verificar la estructura de las tablas en la base de datos
 * Este script debe colocarse en la raíz del proyecto para su ejecución directa
 */

// Cargar autoloader de CodeIgniter para poder usar sus funciones
require 'vendor/autoload.php';
$app = Config\Services::codeigniter();
$app->initialize();
$app->boot();

// Conectarse a la base de datos
$db = \Config\Database::connect();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Verificación de Base de Datos</title>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1, h2, h3 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .error { color: red; background: #fee; padding: 10px; border: 1px solid #f99; }
        .success { color: green; background: #efe; padding: 10px; border: 1px solid #9f9; }
        .code { background: #f5f5f5; padding: 10px; font-family: monospace; }
    </style>
</head>
<body>
    <h1>Verificación de Estructura de Base de Datos</h1>";

// Función para mostrar la estructura de una tabla
function mostrarEstructuraTabla($db, $tabla) {
    echo "<h2>Tabla: $tabla</h2>";
    
    try {
        $query = $db->query("DESCRIBE $tabla");
        
        if ($query === false) {
            echo "<div class='error'>Error al consultar la estructura de la tabla $tabla: " . $db->error()['message'] . "</div>";
            return;
        }
        
        echo "<table>
                <tr>
                    <th>Campo</th>
                    <th>Tipo</th>
                    <th>Nulo</th>
                    <th>Clave</th>
                    <th>Default</th>
                    <th>Extra</th>
                </tr>";
        
        foreach ($query->getResultArray() as $row) {
            echo "<tr>
                    <td>{$row['Field']}</td>
                    <td>{$row['Type']}</td>
                    <td>{$row['Null']}</td>
                    <td>{$row['Key']}</td>
                    <td>" . ($row['Default'] !== null ? $row['Default'] : '<i>NULL</i>') . "</td>
                    <td>{$row['Extra']}</td>
                  </tr>";
        }
        
        echo "</table>";
        
        // Mostrar algunos datos de ejemplo
        $query = $db->query("SELECT * FROM $tabla LIMIT 5");
        
        if ($query === false) {
            echo "<div class='error'>Error al consultar datos de la tabla $tabla: " . $db->error()['message'] . "</div>";
            return;
        }
        
        echo "<h3>Datos de ejemplo</h3>";
        
        if ($query->getNumRows() == 0) {
            echo "<p>No hay datos en esta tabla.</p>";
            return;
        }
        
        $first_row = $query->getResultArray()[0];
        
        echo "<table><tr>";
        foreach (array_keys($first_row) as $column) {
            echo "<th>$column</th>";
        }
        echo "</tr>";
        
        foreach ($query->getResultArray() as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                if (is_null($value)) {
                    echo "<td><i>NULL</i></td>";
                } elseif (strlen($value) > 100) {
                    echo "<td>" . htmlspecialchars(substr($value, 0, 100) . '...') . "</td>";
                } else {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
            }
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Contar total de registros
        $query = $db->query("SELECT COUNT(*) as total FROM $tabla");
        $total = $query->getRowArray()['total'];
        echo "<p>Total de registros en la tabla: <strong>$total</strong></p>";
        
    } catch (\Exception $e) {
        echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
    }
}

// Verificar las tablas relevantes para el problema actual
$tablas = ['ventas_cabecera', 'ventas_detalle', 'productos', 'usuarios'];

foreach ($tablas as $tabla) {
    mostrarEstructuraTabla($db, $tabla);
}

// Verificar si hay problemas específicos en la relación de las tablas
echo "<h2>Verificación de relaciones entre tablas</h2>";

// 1. Verificar ventas sin detalles
$query_sin_detalles = $db->query("
    SELECT vc.id, vc.fecha, vc.total_venta, vc.usuario_id
    FROM ventas_cabecera vc
    LEFT JOIN ventas_detalle vd ON vc.id = vd.venta_id
    WHERE vd.id IS NULL
");

if ($query_sin_detalles === false) {
    echo "<div class='error'>Error al verificar ventas sin detalles: " . $db->error()['message'] . "</div>";
} else {
    $ventas_sin_detalles = $query_sin_detalles->getResultArray();
    
    if (count($ventas_sin_detalles) > 0) {
        echo "<div class='error'><strong>Se encontraron " . count($ventas_sin_detalles) . " ventas sin detalles:</strong></div>";
        
        echo "<table>
                <tr>
                    <th>ID Venta</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Usuario ID</th>
                </tr>";
        
        foreach ($ventas_sin_detalles as $venta) {
            echo "<tr>
                    <td>{$venta['id']}</td>
                    <td>{$venta['fecha']}</td>
                    <td>{$venta['total_venta']}</td>
                    <td>{$venta['usuario_id']}</td>
                  </tr>";
        }
        
        echo "</table>";
    } else {
        echo "<div class='success'>Todas las ventas tienen detalles asociados.</div>";
    }
}

// 2. Verificar detalles con productos inexistentes
$query_prod_inexistentes = $db->query("
    SELECT vd.id, vd.venta_id, vd.producto_id
    FROM ventas_detalle vd
    LEFT JOIN productos p ON vd.producto_id = p.id
    WHERE p.id IS NULL
");

if ($query_prod_inexistentes === false) {
    echo "<div class='error'>Error al verificar detalles con productos inexistentes: " . $db->error()['message'] . "</div>";
} else {
    $detalles_prod_inexistentes = $query_prod_inexistentes->getResultArray();
    
    if (count($detalles_prod_inexistentes) > 0) {
        echo "<div class='error'><strong>Se encontraron " . count($detalles_prod_inexistentes) . " detalles con productos inexistentes:</strong></div>";
        
        echo "<table>
                <tr>
                    <th>ID Detalle</th>
                    <th>ID Venta</th>
                    <th>ID Producto (Inexistente)</th>
                </tr>";
        
        foreach ($detalles_prod_inexistentes as $detalle) {
            echo "<tr>
                    <td>{$detalle['id']}</td>
                    <td>{$detalle['venta_id']}</td>
                    <td>{$detalle['producto_id']}</td>
                  </tr>";
        }
        
        echo "</table>";
    } else {
        echo "<div class='success'>Todos los detalles tienen productos válidos.</div>";
    }
}

// 3. Verificar que los campos necesarios estén presentes
echo "<h2>Verificación de la consulta SQL</h2>";

$sql_detalle = "SELECT 
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
    vd.venta_id = 1
  ORDER BY 
    p.nombre_prod ASC";

echo "<div class='code'>" . htmlspecialchars($sql_detalle) . "</div>";

// Probar la consulta
try {
    $query_test = $db->query($sql_detalle);
    
    if ($query_test === false) {
        echo "<div class='error'>La consulta SQL falló: " . $db->error()['message'] . "</div>";
    } else {
        echo "<div class='success'>La consulta SQL se ejecutó correctamente.</div>";
        
        $resultados = $query_test->getResultArray();
        if (count($resultados) > 0) {
            echo "<p>Número de resultados: " . count($resultados) . "</p>";
            echo "<h3>Muestra del primer resultado:</h3>";
            echo "<pre>" . print_r($resultados[0], true) . "</pre>";
        } else {
            echo "<p>No se encontraron resultados para la venta con ID 1.</p>";
        }
    }
} catch (\Exception $e) {
    echo "<div class='error'>Error al ejecutar la consulta: " . $e->getMessage() . "</div>";
}

// Verificar problemas específicos de columnas
echo "<h3>Verificando que existan todas las columnas utilizadas en la consulta</h3>";

$tablas_columnas = [
    'ventas_detalle' => ['id', 'venta_id', 'producto_id', 'cantidad', 'precio'],
    'ventas_cabecera' => ['id', 'fecha', 'total_venta', 'usuario_id'],
    'productos' => ['id', 'nombre_prod', 'descripcion', 'imagen', 'precio_vta'],
    'usuarios' => ['id', 'nombre', 'apellido', 'email', 'usuario']
];

$problemas_encontrados = false;

foreach ($tablas_columnas as $tabla => $columnas) {
    try {
        $campos_tabla = [];
        $query_campos = $db->query("DESCRIBE $tabla");
        
        if ($query_campos !== false) {
            foreach ($query_campos->getResultArray() as $campo) {
                $campos_tabla[] = $campo['Field'];
            }
            
            $columnas_faltantes = array_diff($columnas, $campos_tabla);
            
            if (!empty($columnas_faltantes)) {
                $problemas_encontrados = true;
                echo "<div class='error'>La tabla <strong>$tabla</strong> no tiene las siguientes columnas utilizadas en la consulta: <strong>" . implode(", ", $columnas_faltantes) . "</strong></div>";
            }
        } else {
            $problemas_encontrados = true;
            echo "<div class='error'>Error al consultar los campos de la tabla $tabla: " . $db->error()['message'] . "</div>";
        }
    } catch (\Exception $e) {
        $problemas_encontrados = true;
        echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
    }
}

if (!$problemas_encontrados) {
    echo "<div class='success'>Todas las columnas utilizadas en la consulta existen en sus respectivas tablas.</div>";
}

echo "<p><a href='index.php'>Volver al inicio</a></p>";
echo "</body></html>";
?>
