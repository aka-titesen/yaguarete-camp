<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductosSeeder extends Seeder
{
    public function run()
    {
        // Deshabilitar llaves foráneas temporalmente
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        
        // Limpiar tabla primero
        $this->db->table('productos')->truncate();
        
        // Habilitar llaves foráneas nuevamente
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        $productos = [
            // === PRODUCTOS DE PESCA (categoria_id = 4 - Deportes) ===
            [
                'nombre_prod' => 'Caña de Pescar Telescópica 3.6m',
                'imagen' => 'cana-pescar-telescopica.jpg',
                'categoria_id' => 4,
                'precio' => 15000.00,
                'precio_vta' => 22500.00,
                'stock' => 12,
                'stock_min' => 3,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Reel Spinning Shimano 2500',
                'imagen' => 'reel-shimano-2500.jpg',
                'categoria_id' => 4,
                'precio' => 25000.00,
                'precio_vta' => 37500.00,
                'stock' => 8,
                'stock_min' => 2,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Caja de Señuelos Variados x50',
                'imagen' => 'caja-senuelos-50.jpg',
                'categoria_id' => 4,
                'precio' => 8500.00,
                'precio_vta' => 12750.00,
                'stock' => 15,
                'stock_min' => 5,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Línea de Pesca Monofilamento 0.35mm',
                'imagen' => 'linea-monofilamento.jpg',
                'categoria_id' => 4,
                'precio' => 3200.00,
                'precio_vta' => 4800.00,
                'stock' => 25,
                'stock_min' => 10,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Red de Aterrizaje Plegable',
                'imagen' => 'red-aterrizaje.jpg',
                'categoria_id' => 4,
                'precio' => 7500.00,
                'precio_vta' => 11250.00,
                'stock' => 10,
                'stock_min' => 3,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Kit Anzuelos Profesional x100',
                'imagen' => 'kit-anzuelos-100.jpg',
                'categoria_id' => 4,
                'precio' => 4500.00,
                'precio_vta' => 6750.00,
                'stock' => 20,
                'stock_min' => 8,
                'eliminado' => 'NO'
            ],
            
            // === PRODUCTOS DE CAMPING (categoria_id = 4 - Deportes) ===
            [
                'nombre_prod' => 'Carpa Familiar 6 Personas Impermeable',
                'imagen' => 'carpa-familiar-6p.jpg',
                'categoria_id' => 4,
                'precio' => 45000.00,
                'precio_vta' => 67500.00,
                'stock' => 5,
                'stock_min' => 2,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Bolsa de Dormir -10°C',
                'imagen' => 'bolsa-dormir-10c.jpg',
                'categoria_id' => 4,
                'precio' => 18000.00,
                'precio_vta' => 27000.00,
                'stock' => 12,
                'stock_min' => 4,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Linterna LED Recargable 1000 Lúmenes',
                'imagen' => 'linterna-led-1000.jpg',
                'categoria_id' => 4,
                'precio' => 6500.00,
                'precio_vta' => 9750.00,
                'stock' => 18,
                'stock_min' => 6,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Cocina Portátil a Gas con Cartucho',
                'imagen' => 'cocina-portatil-gas.jpg',
                'categoria_id' => 4,
                'precio' => 12000.00,
                'precio_vta' => 18000.00,
                'stock' => 8,
                'stock_min' => 3,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Mesa Plegable Aluminio 120x60cm',
                'imagen' => 'mesa-plegable-aluminio.jpg',
                'categoria_id' => 4,
                'precio' => 22000.00,
                'precio_vta' => 33000.00,
                'stock' => 6,
                'stock_min' => 2,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Sillas Camping Plegables x4',
                'imagen' => 'sillas-camping-4.jpg',
                'categoria_id' => 4,
                'precio' => 16000.00,
                'precio_vta' => 24000.00,
                'stock' => 10,
                'stock_min' => 3,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Cooler Termoeléctrico 40L',
                'imagen' => 'cooler-termico-40l.jpg',
                'categoria_id' => 4,
                'precio' => 35000.00,
                'precio_vta' => 52500.00,
                'stock' => 4,
                'stock_min' => 1,
                'eliminado' => 'NO'
            ],
            
            // === PRODUCTOS DE ROPA Y ACCESORIOS OUTDOOR (categoria_id = 2) ===
            [
                'nombre_prod' => 'Campera Impermeable Trekking Hombre',
                'imagen' => 'campera-impermeable-hombre.jpg',
                'categoria_id' => 2,
                'precio' => 28000.00,
                'precio_vta' => 42000.00,
                'stock' => 15,
                'stock_min' => 5,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Campera Impermeable Trekking Mujer',
                'imagen' => 'campera-impermeable-mujer.jpg',
                'categoria_id' => 2,
                'precio' => 26000.00,
                'precio_vta' => 39000.00,
                'stock' => 18,
                'stock_min' => 6,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Pantalón Trekking Desmontable',
                'imagen' => 'pantalon-trekking-desmontable.jpg',
                'categoria_id' => 2,
                'precio' => 20000.00,
                'precio_vta' => 30000.00,
                'stock' => 20,
                'stock_min' => 8,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Botas Trekking Impermeables',
                'imagen' => 'botas-trekking-impermeables.jpg',
                'categoria_id' => 2,
                'precio' => 35000.00,
                'precio_vta' => 52500.00,
                'stock' => 12,
                'stock_min' => 4,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Mochila Trekking 50L',
                'imagen' => 'mochila-trekking-50l.jpg',
                'categoria_id' => 2,
                'precio' => 25000.00,
                'precio_vta' => 37500.00,
                'stock' => 10,
                'stock_min' => 3,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Gorro Outdoor con Protección UV',
                'imagen' => 'gorro-outdoor-uv.jpg',
                'categoria_id' => 2,
                'precio' => 4500.00,
                'precio_vta' => 6750.00,
                'stock' => 25,
                'stock_min' => 10,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Riñonera Táctica Impermeable',
                'imagen' => 'rinonera-tactica.jpg',
                'categoria_id' => 2,
                'precio' => 8500.00,
                'precio_vta' => 12750.00,
                'stock' => 15,
                'stock_min' => 5,
                'eliminado' => 'NO'
            ],
            
            // === PRODUCTOS DEPORTIVOS OUTDOOR (categoria_id = 4) ===
            [
                'nombre_prod' => 'Bicicleta Mountain Bike 29"',
                'imagen' => 'mtb-29-pulgadas.jpg',
                'categoria_id' => 4,
                'precio' => 180000.00,
                'precio_vta' => 270000.00,
                'stock' => 3,
                'stock_min' => 1,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Kayak Individual con Remo',
                'imagen' => 'kayak-individual-remo.jpg',
                'categoria_id' => 4,
                'precio' => 85000.00,
                'precio_vta' => 127500.00,
                'stock' => 2,
                'stock_min' => 1,
                'eliminado' => 'NO'
            ],
            [
                'nombre_prod' => 'Set Escalada: Casco + Arnés + Cuerdas',
                'imagen' => 'set-escalada-completo.jpg',
                'categoria_id' => 4,
                'precio' => 55000.00,
                'precio_vta' => 82500.00,
                'stock' => 4,
                'stock_min' => 1,
                'eliminado' => 'NO'
            ]
        ];

        $this->db->table('productos')->insertBatch($productos);
        echo "✅ " . count($productos) . " productos insertados correctamente\n";
    }
}
