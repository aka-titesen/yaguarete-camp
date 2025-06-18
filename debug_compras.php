<?php
// Script para depurar los datos de ventas
require_once 'app/Config/Constants.php';  // Para tener acceso a ENVIRONMENT

// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Función de ayuda para mostrar datos formateados
function mostrar($titulo, $datos) {
    echo "<h3>$titulo</h3>";
    echo "<pre>";
    print_r($datos);
    echo "</pre>";
    echo "<hr>";
}

// Obtener el ID de la compra de la URL
$id = $_GET['id'] ?? '';
if (empty($id)) {
    echo "<h2>Debe especificar un ID de compra</h2>";
    echo "<p>Ejemplo: debug_compras.php?id=1</p>";
    exit;
}

// Inicializar la conexión a la base de datos
require_once 'app/Config/Database.php';
$db = new \Config\Database();
$conn = $db->connect();

// 1. Verificar la existencia de la compra
$cabecera = $conn->query("SELECT * FROM ventas_cabecera WHERE id = ?", [$id])->getRowArray();
if (!$cabecera) {
    echo "<h2>No existe una compra con el ID: $id</h2>";
    exit;
}

mostrar("Datos de la cabecera", $cabecera);

// 2. Obtener los detalles de la compra
$sql = "SELECT 
    vd.*, 
    vc.id as cabecera_id, vc.fecha, vc.total_venta, vc.usuario_id,
    p.nombre_prod, p.descripcion, p.imagen, p.precio_vta,
    u.nombre, u.apellido
FROM 
    ventas_detalle vd
INNER JOIN 
    ventas_cabecera vc ON vc.id = vd.venta_id
INNER JOIN 
    productos p ON p.id = vd.producto_id
INNER JOIN 
    usuarios u ON u.id = vc.usuario_id
WHERE 
    vd.venta_id = ?";

$detalles = $conn->query($sql, [$id])->getResultArray();
mostrar("Detalles de la compra (" . count($detalles) . " productos)", $detalles);

// 3. Verificar las imágenes de los productos
echo "<h3>Verificación de imágenes</h3>";
foreach ($detalles as $i => $detalle) {
    $imagen = $detalle['imagen'] ?? 'No disponible';
    $nombre_prod = $detalle['nombre_prod'] ?? 'Producto sin nombre';
    
    echo "<h4>#$i - $nombre_prod</h4>";
    echo "Ruta de imagen en la BD: $imagen<br>";
    
    $ruta_completa = $imagen;
    if (!empty($imagen) && $imagen !== 'null' && $imagen !== 'undefined') {
        if (strpos($ruta_completa, 'assets/') !== 0) {
            $ruta_completa = 'assets/' . $ruta_completa;
        }
    } else {
        $ruta_completa = 'assets/img/producto-sin-imagen.jpg';
    }
    
    echo "Ruta completa: $ruta_completa<br>";
    echo "¿Archivo existe? " . (file_exists($ruta_completa) ? "SÍ" : "NO") . "<br>";
    
    echo "<img src='$ruta_completa' style='max-width:200px; max-height:150px; border:1px solid #ccc' />";
    echo "<hr>";
}
