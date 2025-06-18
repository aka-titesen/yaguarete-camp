<?php
/**
 * Script de diagnóstico para el problema con los detalles de compra
 * Este script proporciona un análisis completo del problema y posibles soluciones
 */

// Inicializar el autoloader de CodeIgniter
require 'vendor/autoload.php';
$app = Config\Services::codeigniter();
$app->initialize();
$app->boot();

// Obtener la configuración de la base de datos
$db = \Config\Database::connect();

// Establecer estilo para la página
echo "<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico Compras - Solución de Problemas</title>
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
        .solution { background: #e7f7e7; padding: 15px; border-left: 5px solid #4CAF50; margin: 10px 0; }
        .step { background: #f9f9f9; padding: 10px; border-left: 3px solid #2196F3; margin: 5px 0; }
    </style>
</head>
<body>
    <h1>Diagnóstico y Soluciones para el Error en Detalle de Compra</h1>";

// 1. Verificar las estructuras de tablas involucradas
echo "<h2>1. Verificación de tablas en la base de datos</h2>";

$tablas_requeridas = ['ventas_cabecera', 'ventas_detalle', 'productos', 'usuarios'];
$tablas_faltantes = [];

foreach ($tablas_requeridas as $tabla) {
    $query = $db->query("SHOW TABLES LIKE '$tabla'");
    if ($query->getNumRows() == 0) {
        $tablas_faltantes[] = $tabla;
    }
}

if (!empty($tablas_faltantes)) {
    echo "<div class='error'>No se encontraron las siguientes tablas: " . implode(', ', $tablas_faltantes) . "</div>";
    echo "<div class='solution'>
            <h3>Solución:</h3>
            <p>Es necesario crear las tablas faltantes. Revise su script de migración o estructura de base de datos para restaurar estas tablas esenciales.</p>
          </div>";
} else {
    echo "<div class='success'>Todas las tablas necesarias existen en la base de datos.</div>";
}

// 2. Verificar las columnas de las tablas
echo "<h2>2. Verificación de columnas en tablas</h2>";

$columnas_requeridas = [
    'ventas_cabecera' => ['id', 'fecha', 'usuario_id', 'total_venta'],
    'ventas_detalle' => ['id', 'venta_id', 'producto_id', 'cantidad', 'precio'],
    'productos' => ['id', 'nombre_prod', 'descripcion', 'imagen', 'precio_vta'],
    'usuarios' => ['id', 'nombre', 'apellido', 'email', 'usuario']
];

$problemas_columnas = [];

foreach ($columnas_requeridas as $tabla => $columnas) {
    if (in_array($tabla, $tablas_faltantes)) {
        continue; // Saltamos la tabla si no existe
    }
    
    $query = $db->query("DESCRIBE $tabla");
    if ($query === false) {
        $problemas_columnas[$tabla] = "Error al obtener estructura";
        continue;
    }
    
    $columnas_existentes = [];
    foreach ($query->getResultArray() as $fila) {
        $columnas_existentes[] = $fila['Field'];
    }
    
    $columnas_faltantes = array_diff($columnas, $columnas_existentes);
    if (!empty($columnas_faltantes)) {
        $problemas_columnas[$tabla] = implode(', ', $columnas_faltantes);
    }
}

if (!empty($problemas_columnas)) {
    echo "<div class='error'>Se encontraron problemas en las columnas de las siguientes tablas:</div>";
    echo "<ul>";
    foreach ($problemas_columnas as $tabla => $problema) {
        echo "<li><strong>$tabla</strong>: Columnas faltantes o problema: $problema</li>";
    }
    echo "</ul>";
    
    echo "<div class='solution'>
            <h3>Solución:</h3>
            <p>Es necesario añadir las columnas faltantes a las tablas correspondientes. Utilice comandos ALTER TABLE para añadir estas columnas.</p>
            <div class='code'>
                ALTER TABLE [tabla] ADD COLUMN [nombre_columna] [tipo_dato];<br>
                -- Ejemplo: ALTER TABLE ventas_detalle ADD COLUMN cantidad INT NOT NULL DEFAULT 1;
            </div>
          </div>";
} else {
    echo "<div class='success'>Todas las columnas necesarias existen en sus respectivas tablas.</div>";
}

// 3. Verificar ventas y sus detalles
echo "<h2>3. Verificación de datos de ventas y detalles</h2>";

$compras_recientes = $db->query("SELECT * FROM ventas_cabecera ORDER BY id DESC LIMIT 5");
if ($compras_recientes === false) {
    echo "<div class='error'>Error al consultar las compras recientes: " . $db->error()['message'] . "</div>";
} else {
    if ($compras_recientes->getNumRows() == 0) {
        echo "<div class='error'>No hay compras registradas en la base de datos.</div>";
        echo "<div class='solution'>
                <h3>Solución:</h3>
                <p>Debe realizar algunas compras de prueba para poder diagnosticar correctamente el sistema.</p>
              </div>";
    } else {
        echo "<div class='success'>Se encontraron " . $compras_recientes->getNumRows() . " compras recientes.</div>";
        
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Total</th>
                    <th>Detalles</th>
                </tr>";
        
        $ventas_sin_detalles = [];
        foreach ($compras_recientes->getResultArray() as $compra) {
            $venta_id = $compra['id'];
            $detalles = $db->query("SELECT COUNT(*) as total FROM ventas_detalle WHERE venta_id = ?", [$venta_id]);
            $num_detalles = $detalles->getRowArray()['total'];
            
            echo "<tr>
                    <td>{$compra['id']}</td>
                    <td>{$compra['fecha']}</td>
                    <td>{$compra['usuario_id']}</td>
                    <td>\${$compra['total_venta']}</td>
                    <td>" . ($num_detalles > 0 ? "$num_detalles productos" : "<span style='color:red'>Sin detalles</span>") . "</td>
                  </tr>";
            
            if ($num_detalles == 0) {
                $ventas_sin_detalles[] = $venta_id;
            }
        }
        
        echo "</table>";
        
        if (!empty($ventas_sin_detalles)) {
            echo "<div class='error'>Se encontraron compras sin detalles: " . implode(', ', $ventas_sin_detalles) . "</div>";
            echo "<div class='solution'>
                    <h3>Solución:</h3>
                    <p>El problema principal es que hay compras que no tienen detalles asociados en la tabla ventas_detalle.</p>
                    <ol>
                        <li>Revise la función registrar_venta() para asegurar que se están insertando correctamente los registros en ventas_detalle.</li>
                        <li>Verifique que los valores de venta_id en ventas_detalle corresponden con los ids en ventas_cabecera.</li>
                    </ol>
                  </div>";
        } else {
            echo "<div class='success'>Todas las compras recientes tienen detalles asociados.</div>";
        }
    }
}

// 4. Verificar el código del controlador
echo "<h2>4. Diagnóstico del código del controlador</h2>";

echo "<h3>Problemas comunes y sus soluciones:</h3>";

echo "<div class='step'>
        <h4>Problema 1: La consulta SQL falla por errores en nombres de tablas o columnas</h4>
        <p>Si la consulta falla con un error como \"Unknown column\" o \"Table doesn't exist\", revise los nombres de tablas y columnas en la consulta.</p>
        <div class='solution'>
            <h5>Solución:</h5>
            <p>Modifique la consulta en VentasController.php para asegurarse de que todos los nombres de tablas y columnas son correctos.</p>
            <p>Además, reemplace los INNER JOIN por LEFT JOIN para mayor tolerancia a errores:</p>
            <div class='code'>
                \$sql_detalle = \"SELECT 
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
                    p.nombre_prod ASC\";
            </div>
        </div>
      </div>";

echo "<div class='step'>
        <h4>Problema 2: Error \"Call to a member function getResultArray() on bool\"</h4>
        <p>Este error ocurre cuando \$query_detalle es FALSE porque la consulta SQL falló, pero el código intenta llamar al método getResultArray() sobre el valor FALSE.</p>
        <div class='solution'>
            <h5>Solución:</h5>
            <p>Añada una verificación antes de llamar a getResultArray():</p>
            <div class='code'>
                // Verificar si la consulta falló
                if (\$query_detalle === false) {
                    // Registrar el error en los logs
                    log_message('error', \"Error en consulta SQL: \" . \$db->error()['message']);
                    \$session->setFlashdata('mensaje', 'Error al recuperar los detalles de la compra.');
                    return redirect()->to(base_url('mis-compras'));
                }
                
                // Obtener los resultados solo si la consulta fue exitosa
                \$data['venta'] = \$query_detalle->getResultArray();
            </div>
        </div>
      </div>";

echo "<div class='step'>
        <h4>Problema 3: Datos faltantes o incorrectos en las tablas</h4>
        <p>Es posible que existan registros en ventas_cabecera pero no en ventas_detalle, o que haya productos eliminados.</p>
        <div class='solution'>
            <h5>Solución:</h5>
            <p>Utilice consultas LEFT JOIN para ser más tolerante a datos faltantes, y maneje los valores NULL en la vista:</p>
            <div class='code'>
                // En la vista, manejar valores que podrían ser NULL
                \$imagen = isset(\$row['imagen']) && \$row['imagen'] !== null ? \$row['imagen'] : 'assets/img/producto-sin-imagen.jpg';
                \$nombre = isset(\$row['nombre_prod']) && \$row['nombre_prod'] !== null ? \$row['nombre_prod'] : 'Producto sin nombre';
                \$precio = isset(\$row['precio']) && is_numeric(\$row['precio']) ? floatval(\$row['precio']) : 0;
                \$cantidad = isset(\$row['cantidad']) && is_numeric(\$row['cantidad']) ? intval(\$row['cantidad']) : 1;
            </div>
        </div>
      </div>";

echo "<h2>5. Herramientas de Diagnóstico</h2>";

echo "<p>Para ayudar a diagnosticar y solucionar el problema, se han creado las siguientes herramientas:</p>";

echo "<ul>
        <li><a href='diagnostico_detalle_compra.php'>diagnostico_detalle_compra.php</a> - Para revisar los detalles de una compra específica</li>
        <li><a href='verificar_db.php'>verificar_db.php</a> - Para verificar la estructura de las tablas relacionadas con ventas</li>
        <li><a href='test_compra.php'>test_compra.php</a> - Para probar diferentes consultas SQL en una compra específica</li>
      </ul>";

echo "<h2>6. Pasos para resolver el problema</h2>";

echo "<ol>
        <li>Ejecute las herramientas de diagnóstico para identificar el problema específico.</li>
        <li>Verifique que todas las tablas y columnas necesarias existen en la base de datos.</li>
        <li>Asegúrese de que las ventas tengan detalles asociados en la tabla ventas_detalle.</li>
        <li>Modifique el código del controlador para manejar mejor los errores y usar LEFT JOIN en lugar de INNER JOIN.</li>
        <li>Mejore la vista para manejar correctamente valores NULL o faltantes.</li>
      </ol>";

echo "</body></html>";
?>
