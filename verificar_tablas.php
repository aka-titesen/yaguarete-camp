<?php
// Script para verificar la estructura de ventas_detalle
require_once 'app/Config/Database.php';

try {
    // Inicializar la base de datos
    $db = new \Config\Database();
    $conn = $db->connect();
    
    // Verificar la estructura de la tabla
    echo "=== ESTRUCTURA DE TABLAS ===\n";
    
    // Tablas usadas en la consulta
    $tablas = ['ventas_detalle', 'ventas_cabecera', 'productos', 'usuarios'];
    
    foreach ($tablas as $tabla) {
        $result = $conn->query("DESCRIBE $tabla");
        
        if ($result === false) {
            echo "Error al obtener la estructura de $tabla: " . $conn->error()['message'] . "\n";
            continue;
        }
        
        echo "\n--- ESTRUCTURA DE $tabla ---\n";
        while ($row = $result->getResultArray()) {
            echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Key'] . "\n";
        }
    }
    
    // Intentar una consulta simple para obtener un registro de ventas_detalle
    echo "\n=== MUESTRA DE DATOS ===\n";
    
    $result = $conn->query("SELECT * FROM ventas_detalle LIMIT 1");
    if ($result === false) {
        echo "Error al obtener datos de ventas_detalle: " . $conn->error()['message'] . "\n";
    } else {
        $row = $result->getRowArray();
        if ($row) {
            echo "Ejemplo de registro de ventas_detalle:\n";
            print_r($row);
        } else {
            echo "No hay registros en ventas_detalle\n";
        }
    }
    
    echo "\nScript completado.";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
