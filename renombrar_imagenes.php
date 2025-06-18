<?php
// Script para renombrar imágenes con caracteres especiales y actualizar la base de datos
// Uso: Ejecutar desde el navegador o CLI

require 'vendor/autoload.php';
$app = Config\Services::codeigniter();
$app->initialize();
$app->boot();

$db = \Config\Database::connect();

// Carpeta donde están las imágenes
$carpeta = __DIR__ . '/assets/img/';

// Buscar archivos con ñ, acentos o espacios
$archivos = glob($carpeta . '*');

$renombrados = [];
foreach ($archivos as $archivo) {
    $nombre = basename($archivo);
    $nuevo_nombre = iconv('UTF-8', 'ASCII//TRANSLIT', $nombre); // Quita acentos y ñ → n
    $nuevo_nombre = preg_replace('/[^A-Za-z0-9._-]/', '', $nuevo_nombre); // Solo letras, números, guion, punto
    if ($nombre !== $nuevo_nombre) {
        $nuevo_path = $carpeta . $nuevo_nombre;
        if (!file_exists($nuevo_path)) {
            rename($archivo, $nuevo_path);
            $renombrados[$nombre] = $nuevo_nombre;
        }
    }
}

// Actualizar referencias en la base de datos
echo "<h2>Renombrando imágenes y actualizando base de datos</h2>";
if (empty($renombrados)) {
    echo "<p>No se encontraron imágenes con caracteres especiales.</p>";
} else {
    foreach ($renombrados as $viejo => $nuevo) {
        $db->query("UPDATE productos SET imagen = ? WHERE imagen = ?", [$nuevo, $viejo]);
        echo "<p>$viejo → $nuevo (actualizado en la base de datos)</p>";
    }
}

echo "<h3>Proceso finalizado.</h3>";
?>
