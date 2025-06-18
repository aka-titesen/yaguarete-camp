<?php
// Script para verificar productos de una compra usando el modelo y buenas prácticas de CodeIgniter
use App\Models\VentasDetalleModel;

require_once 'app/Config/Database.php';
require_once 'vendor/autoload.php';

$venta_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($venta_id <= 0) {
    echo "<h2>Debes pasar un id de compra (?id=)</h2>";
    exit;
}

// Inicializar el modelo y obtener los detalles
$detalleModel = new VentasDetalleModel();
$detalles = $detalleModel->getDetalles($venta_id);

if (empty($detalles)) {
    echo "<h2>No hay detalles para la compra #$venta_id</h2>";
    exit;
}

echo "<h2>Detalles de la compra #$venta_id</h2>";
echo "<table border='1' cellpadding='5'><tr><th>ID Detalle</th><th>ID Producto</th><th>Cantidad</th><th>Precio</th><th>Producto existe</th><th>Nombre</th><th>Imagen</th></tr>";

foreach ($detalles as $detalle) {
    $existe = $detalle['nombre_prod'] ? 'SI' : 'NO';
    $nombre = $detalle['nombre_prod'] ?? '-';
    $imagen = $detalle['imagen'] ?? '-';
    // Mostrar imagen si existe
    $img_html = ($imagen && $imagen != '-') ? "<img src='assets/uploads/$imagen' width='60'>" : '-';
    echo "<tr>";
    echo "<td>{$detalle['id']}</td>";
    echo "<td>{$detalle['producto_id']}</td>";
    echo "<td>{$detalle['cantidad']}</td>";
    echo "<td>{$detalle['precio']}</td>";
    echo "<td>$existe</td>";
    echo "<td>$nombre</td>";
    echo "<td>$img_html</td>";
    echo "</tr>";
}
echo "</table>";
?>
