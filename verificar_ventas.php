<?php
// Script para verificar las ventas en la base de datos
require_once 'app/Config/Database.php';

try {
    // Inicializar la base de datos
    $db = new \Config\Database();
    $conn = $db->connect();
    
    // 1. Contar registros en cada tabla
    $tablas = ['ventas_cabecera', 'ventas_detalle', 'productos', 'usuarios'];
    
    echo "=== CONTEO DE REGISTROS ===\n";
    foreach ($tablas as $tabla) {
        $result = $conn->query("SELECT COUNT(*) as total FROM $tabla");
        if ($result === false) {
            echo "Error al contar registros en $tabla: " . $conn->error()['message'] . "\n";
        } else {
            $row = $result->getRowArray();
            echo "$tabla: " . $row['total'] . " registros\n";
        }
    }
    
    // 2. Obtener lista de ventas existentes
    echo "\n=== VENTAS EXISTENTES ===\n";
    $result = $conn->query("SELECT vc.id, vc.fecha, vc.total_venta, vc.usuario_id, 
                           u.nombre, u.apellido,
                           (SELECT COUNT(*) FROM ventas_detalle WHERE venta_id = vc.id) as num_items
                           FROM ventas_cabecera vc 
                           INNER JOIN usuarios u ON u.id = vc.usuario_id
                           ORDER BY vc.id DESC");
    
    if ($result === false) {
        echo "Error al obtener ventas: " . $conn->error()['message'] . "\n";
    } else {
        $ventas = $result->getResultArray();
        if (empty($ventas)) {
            echo "No hay ventas registradas.\n";
        } else {
            echo "ID | Fecha | Total | Usuario | Items\n";
            echo "--------------------------------\n";
            foreach ($ventas as $venta) {
                echo $venta['id'] . " | " . 
                     $venta['fecha'] . " | " . 
                     $venta['total_venta'] . " | " . 
                     $venta['nombre'] . " " . $venta['apellido'] . " | " .
                     $venta['num_items'] . " items\n";
            }
        }
    }
    
    // 3. Si hay ventas, probemos una consulta directa
    if (!empty($ventas) && count($ventas) > 0) {
        $venta_id = $ventas[0]['id']; // Tomamos la primera venta
        
        echo "\n=== PROBANDO CONSULTA PARA VENTA #$venta_id ===\n";
        
        $sql = "SELECT vd.*, vc.fecha, p.nombre_prod, p.imagen 
                FROM ventas_detalle vd 
                INNER JOIN ventas_cabecera vc ON vc.id = vd.venta_id 
                INNER JOIN productos p ON p.id = vd.producto_id 
                WHERE vd.venta_id = ?";
        
        $query = $conn->query($sql, [$venta_id]);
        
        if ($query === false) {
            echo "Error en la consulta: " . $conn->error()['message'] . "\n";
            echo "SQL: $sql\n";
        } else {
            $detalles = $query->getResultArray();
            if (empty($detalles)) {
                echo "No hay detalles para la venta #$venta_id\n";
            } else {
                echo "Se encontraron " . count($detalles) . " items para la venta #$venta_id:\n";
                foreach ($detalles as $i => $detalle) {
                    echo ($i+1) . ". " . $detalle['nombre_prod'] . " - " . $detalle['cantidad'] . " unidad(es) a $" . $detalle['precio'] . "\n";
                }
            }
        }
    }
    
    echo "\nScript completado.";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
