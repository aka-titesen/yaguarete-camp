<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * DatabaseSeeder - Seeder principal del sistema
 * 
 * Ejecuta todos los seeders esenciales en el orden correcto:
 * 1. CategoriasSeeder - Categorías base del sistema
 * 2. UsuariosSeeder - Administradores y clientes
 * 3. ProductosSeeder - Catálogo de productos temáticos
 * 4. VentasSeeder - Ventas de ejemplo completas
 * 
 * Uso: php spark db:seed DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "🌱 INICIANDO POBLACIÓN COMPLETA DE LA BASE DE DATOS\n";
        echo "=" . str_repeat("=", 60) . "\n\n";
        
        try {
            // 1. Categorías (requeridas para productos)
            echo "1️⃣ Ejecutando CategoriasSeeder...\n";
            $this->call('CategoriasSeeder');
            echo "   ✅ Categorías creadas correctamente\n\n";
            
            // 2. Usuarios (administradores y clientes)
            echo "2️⃣ Ejecutando UsuariosSeeder...\n";
            $this->call('UsuariosSeeder');
            echo "   ✅ Usuarios creados correctamente\n\n";
            
            // 3. Productos outdoor (requiere categorías)
            echo "3️⃣ Ejecutando ProductosSeeder...\n";
            $this->call('ProductosSeeder');
            echo "   ✅ Productos creados correctamente\n\n";
            
            // 4. Ventas de ejemplo (requiere usuarios y productos)
            echo "4️⃣ Ejecutando VentasSeeder...\n";
            $this->call('VentasSeeder');
            echo "   ✅ Ventas creadas correctamente\n\n";
            
            echo "🎉 POBLACIÓN DE BASE DE DATOS COMPLETADA EXITOSAMENTE\n";
            echo "=" . str_repeat("=", 60) . "\n";
            echo "📊 Resumen:\n";
            echo "   - Categorías: 4 categorías base\n";
            echo "   - Usuarios: 2 administradores + 7 clientes\n";
            echo "   - Productos: 23 productos outdoor temáticos\n";
            echo "   - Ventas: 5 ventas de ejemplo con detalles\n\n";
            echo "🔑 Credenciales de administrador:\n";
            echo "   Email: admin@yagaruete.com\n";
            echo "   Contraseña: Admin123!\n\n";
            
        } catch (\Exception $e) {
            echo "❌ ERROR durante la población de la base de datos:\n";
            echo "   {$e->getMessage()}\n";
            throw $e;
        }
    }
}
