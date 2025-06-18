<?php
// Script para depurar rutas de imagen en los detalles de compra
require 'vendor/autoload.php';
$app = Config\Services::codeigniter();
$app->initialize();
$app->boot();

$db = \Config\Database::connect();
$venta_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

$sql = "SELECT vd.id, vd.venta_id, vd.producto_id, p.imagen, p.nombre_prod
        FROM ventas_detalle vd
        LEFT JOIN productos p ON p.id = vd.producto_id
        WHERE vd.venta_id = ?";
$res = $db->query($sql, [$venta_id]);

if ($res === false) {
    echo "<b>Error en la consulta SQL:</b> " . $db->error()['message'];
    exit;
}

$detalles = $res->getResultArray();
echo "<h2>Debug de imágenes para venta #$venta_id</h2>";
if (empty($detalles)) {
    echo "<p>No hay detalles para esta venta.</p>";
    exit;
}

foreach ($detalles as $row) {
    $imagen = $row['imagen'];
    $nombre = $row['nombre_prod'];
    $ruta = '';
    if (!empty($imagen) && $imagen !== 'null' && $imagen !== 'undefined') {
        if (strpos($imagen, 'assets/') === 0 || strpos($imagen, '/assets/') === 0) {
            $ruta = ltrim($imagen, '/');
        } else if (strpos($imagen, 'img/') === 0 || strpos($imagen, 'productos_ejemplo/') === 0) {
            $ruta = 'assets/' . ltrim($imagen, '/');
        } else {
            $ruta = 'assets/img/' . ltrim($imagen, '/');
        }
    } else {
        $ruta = 'assets/img/producto-sin-imagen.jpg';
    }
    $existe = file_exists(__DIR__ . '/' . $ruta) ? 'SI' : 'NO';
    echo "<div style='margin-bottom:10px;padding:10px;border:1px solid #ccc'>";
    echo "<b>Producto:</b> $nombre<br>";
    echo "<b>Imagen en BD:</b> $imagen<br>";
    echo "<b>Ruta final:</b> $ruta<br>";
    echo "<b>¿Existe físicamente?:</b> $existe<br>";
    echo "<img src='$ruta' style='width:120px;height:90px;object-fit:cover;border:1px solid #999'><br>";
    echo "</div>";
}
?>
