<?php
// Script para diagnosticar problemas con ventas_detalle

// Habilitar mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Permitir tiempo de ejecución extendido
set_time_limit(30);

// Obtener el ID de venta a verificar
$venta_id = $_GET['id'] ?? null;

// Mensajes HTML
$html_header = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .section { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .error { color: red; font-weight: bold; }
        .success { color: green; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Diagnóstico de Ventas</h1>';

$html_footer = '</body></html>';

echo $html_header;

if (!$venta_id) {
    echo '<div class="section">
        <h2>Instrucciones</h2>
        <p>Este script diagnostica problemas con los detalles de ventas.</p>
        <p>Por favor ingrese un ID de venta en la URL: <code>diagnostico.php?id=X</code> donde X es el ID de la venta.</p>
    </div>';
    echo $html_footer;
    exit;
}

// Iniciar el diagnóstico
echo '<div class="section">
    <h2>Diagnóstico para Venta ID: ' . htmlspecialchars($venta_id) . '</h2>
</div>';

try {
    // Cargar la configuración de la base de datos
    require_once 'app/Config/Database.php';
    $db = new \Config\Database();
    $conn = $db->connect();
    
    echo '<div class="section">
        <h3>1. Verificando existencia de la venta</h3>';
        
    $query = $conn->query("SELECT * FROM ventas_cabecera WHERE id = ?", [$venta_id]);
    
    if ($query === false) {
        echo '<p class="error">Error al consultar la cabecera: ' . $conn->error()['message'] . '</p>';
    } else {
        $cabecera = $query->getRowArray();
        
        if ($cabecera) {
            echo '<p class="success">✓ Venta encontrada</p>';
            echo '<pre>' . print_r($cabecera, true) . '</pre>';
        } else {
            echo '<p class="error">✗ No existe una venta con ID ' . htmlspecialchars($venta_id) . '</p>';
        }
    }
    
    echo '</div>';
    
    // Solo continuamos si existe la cabecera
    if (isset($cabecera) && $cabecera) {
        echo '<div class="section">
            <h3>2. Verificando detalles de la venta</h3>';
            
        $query = $conn->query("SELECT * FROM ventas_detalle WHERE venta_id = ?", [$venta_id]);
        
        if ($query === false) {
            echo '<p class="error">Error al consultar detalles: ' . $conn->error()['message'] . '</p>';
        } else {
            $detalles = $query->getResultArray();
            
            if (empty($detalles)) {
                echo '<p class="error">✗ No hay detalles para esta venta</p>';
            } else {
                echo '<p class="success">✓ Se encontraron ' . count($detalles) . ' detalles</p>';
                echo '<table>
                    <tr>
                        <th>ID</th>
                        <th>Venta ID</th>
                        <th>Producto ID</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                    </tr>';
                    
                foreach ($detalles as $detalle) {
                    echo '<tr>
                        <td>' . $detalle['id'] . '</td>
                        <td>' . $detalle['venta_id'] . '</td>
                        <td>' . $detalle['producto_id'] . '</td>
                        <td>' . $detalle['cantidad'] . '</td>
                        <td>' . $detalle['precio'] . '</td>
                    </tr>';
                }
                
                echo '</table>';
            }
        }
        
        echo '</div>';
        
        echo '<div class="section">
            <h3>3. Probando la consulta completa</h3>';
            
        $sql = "SELECT 
            vd.*,
            vc.fecha, vc.total_venta, 
            p.nombre_prod, p.descripcion, p.imagen,
            u.nombre, u.apellido
          FROM 
            ventas_detalle vd,
            ventas_cabecera vc,
            productos p,
            usuarios u
          WHERE 
            vc.id = vd.venta_id AND
            p.id = vd.producto_id AND
            u.id = vc.usuario_id AND
            vd.venta_id = ?";
            
        $query = $conn->query($sql, [$venta_id]);
        
        echo '<p>SQL ejecutado:</p>';
        echo '<pre>' . str_replace('?', $venta_id, $sql) . '</pre>';
        
        if ($query === false) {
            echo '<p class="error">✗ Error en la consulta: ' . $conn->error()['message'] . '</p>';
            
            // Intentemos consultar cada tabla individualmente para verificar problemas
            echo '<h4>Diagnosticando tablas individuales:</h4>';
            
            $tablas = [
                'ventas_detalle' => "SELECT * FROM ventas_detalle WHERE venta_id = $venta_id",
                'ventas_cabecera' => "SELECT * FROM ventas_cabecera WHERE id = $venta_id",
                'productos' => "SELECT * FROM productos WHERE id IN (SELECT producto_id FROM ventas_detalle WHERE venta_id = $venta_id)",
                'usuarios' => "SELECT * FROM usuarios WHERE id = (SELECT usuario_id FROM ventas_cabecera WHERE id = $venta_id)"
            ];
            
            foreach ($tablas as $tabla => $consulta) {
                echo "<p>Verificando tabla $tabla:</p>";
                $result = $conn->query($consulta);
                
                if ($result === false) {
                    echo '<p class="error">Error en ' . $tabla . ': ' . $conn->error()['message'] . '</p>';
                } else {
                    $rows = $result->getResultArray();
                    echo '<p>' . ($rows ? count($rows) . ' registros encontrados' : 'No se encontraron registros') . '</p>';
                }
            }
            
        } else {
            $resultados = $query->getResultArray();
            
            if (empty($resultados)) {
                echo '<p class="error">✗ La consulta no devolvió resultados</p>';
            } else {
                echo '<p class="success">✓ La consulta devolvió ' . count($resultados) . ' resultados</p>';
                echo '<pre>' . print_r($resultados[0], true) . '</pre>';
            }
        }
        
        echo '</div>';
    }
    
} catch (Exception $e) {
    echo '<div class="section">
        <h3>Error</h3>
        <p class="error">' . $e->getMessage() . '</p>
    </div>';
}

echo $html_footer;
