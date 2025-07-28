<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    public function run()
    {
        // Deshabilitar llaves foráneas temporalmente
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        
        // Limpiar la tabla primero para evitar duplicados
        $this->db->table('categorias')->truncate();
        
        // Habilitar llaves foráneas nuevamente
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        $categorias = [
            ['descripcion' => 'Electrónicos', 'activo' => 'SI', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['descripcion' => 'Ropa y Accesorios', 'activo' => 'SI', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['descripcion' => 'Hogar y Jardín', 'activo' => 'SI', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['descripcion' => 'Deportes', 'activo' => 'SI', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['descripcion' => 'Libros y Revistas', 'activo' => 'SI', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['descripcion' => 'Juguetes', 'activo' => 'NO', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]
        ];

        $this->db->table('categorias')->insertBatch($categorias);
        echo "✅ " . count($categorias) . " categorías insertadas correctamente\n";
    }
}
