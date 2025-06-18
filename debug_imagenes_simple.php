<?php
// Configuración de conexión (ajusta según tu entorno)
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'bd_martinez_gonzalez'; // Cambiado al nombre real de la base de datos

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

$venta_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$sql = "SELECT vd.id, vd.venta_id, vd.producto_id, p.imagen, p.nombre_prod
        FROM ventas_detalle vd
        LEFT JOIN productos p ON p.id = vd.producto_id
        WHERE vd.venta_id = $venta_id";
$res = $conn->query($sql);

if (!$res) {
    die('<b>Error en la consulta SQL:</b> ' . $conn->error);
}

echo "<h2>Debug de imágenes para venta #$venta_id</h2>";
if ($res->num_rows === 0) {
    echo "<p>No hay detalles para esta venta.</p>";
    exit;
}

while ($row = $res->fetch_assoc()) {
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
$conn->close();
?>
