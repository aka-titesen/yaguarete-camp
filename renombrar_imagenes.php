<?php
// Script para limpiar nombres de archivos de imagen en la tabla productos y en el disco
function limpiar_nombre($nombre) {
    // Reemplaza ñ, acentos y caracteres especiales por equivalentes simples
    $nombre = iconv('UTF-8', 'ASCII//TRANSLIT', $nombre);
    $nombre = preg_replace('/[^A-Za-z0-9_.-]/', '', $nombre);
    return $nombre;
}

$db = \Config\Database::connect();
$builder = $db->table('productos');
$productos = $builder->select('id, imagen')->get()->getResultArray();

foreach ($productos as $prod) {
    $id = $prod['id'];
    $imagen = $prod['imagen'];
    if ($imagen && preg_match('/[^A-Za-z0-9_.-]/', $imagen)) {
        $nuevo = limpiar_nombre($imagen);
        // Renombrar archivo en disco si existe
        $ruta = FCPATH . 'assets/uploads/' . $imagen;
        $nueva_ruta = FCPATH . 'assets/uploads/' . $nuevo;
        if (file_exists($ruta)) {
            rename($ruta, $nueva_ruta);
        }
        // Actualizar en la base de datos
        $db->table('productos')->where('id', $id)->update(['imagen' => $nuevo]);
    }
}
echo 'Nombres de imágenes limpiados.';
?>
