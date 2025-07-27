<?php

// Script temporal para verificar el estado final de la base de datos

require_once 'vendor/autoload.php';

$db = \Config\Database::connect();

echo "🔍 VERIFICACIÓN FINAL DE LA BASE DE DATOS\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Verificar categorías
$categorias = $db->query("SELECT COUNT(*) as total FROM categorias")->getRow();
echo "📂 Categorías: {$categorias->total}\n";

// Verificar usuarios por perfil
$admins = $db->query("SELECT COUNT(*) as total FROM usuarios WHERE perfil_id = 1 AND baja = 'NO'")->getRow();
$clientes = $db->query("SELECT COUNT(*) as total FROM usuarios WHERE perfil_id = 2 AND baja = 'NO'")->getRow();
echo "👥 Usuarios:\n";
echo "   - Administradores (perfil_id = 1): {$admins->total}\n";
echo "   - Clientes (perfil_id = 2): {$clientes->total}\n";

// Verificar productos
$productos = $db->query("SELECT COUNT(*) as total FROM productos WHERE baja = 'NO'")->getRow();
echo "📦 Productos activos: {$productos->total}\n";

// Verificar ventas
$ventas = $db->query("SELECT COUNT(*) as total FROM ventas_cabecera")->getRow();
$detalles = $db->query("SELECT COUNT(*) as total FROM ventas_detalle")->getRow();
echo "🛒 Ventas:\n";
echo "   - Cabeceras: {$ventas->total}\n";
echo "   - Detalles: {$detalles->total}\n";

// Verificar consultas
$consultas = $db->query("SELECT COUNT(*) as total FROM consultas")->getRow();
echo "💬 Consultas: {$consultas->total}\n";

echo "\n" . str_repeat("=", 50) . "\n";

// Verificar usuarios con ventas (solo deben ser clientes)
$usuariosConVentas = $db->query("
    SELECT u.usuario, u.perfil_id, COUNT(v.id) as total_ventas
    FROM usuarios u
    LEFT JOIN ventas_cabecera v ON u.id = v.usuario_id
    WHERE v.id IS NOT NULL
    GROUP BY u.id, u.usuario, u.perfil_id
    ORDER BY u.perfil_id, u.usuario
")->getResult();

echo "👥 USUARIOS CON VENTAS:\n";
foreach ($usuariosConVentas as $usuario) {
    $tipo = $usuario->perfil_id == 1 ? 'ADMIN' : 'CLIENTE';
    echo "   - {$usuario->usuario} ({$tipo}): {$usuario->total_ventas} ventas\n";
}

echo "\n✅ VERIFICACIÓN COMPLETADA\n";
echo "🎯 Solo los CLIENTES (perfil_id = 2) deben tener ventas asociadas.\n";
