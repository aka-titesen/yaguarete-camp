<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class VentasSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Verificar que existen usuarios clientes y productos
        $usuarios = $db->table('usuarios')->where('perfil_id', 2)->get()->getResultArray();
        $productos = $db->table('productos')->where('eliminado', 'NO')->get()->getResultArray();
        
        if (empty($usuarios)) {
            echo "No hay usuarios clientes disponibles. Creando usuarios de prueba...\n";
            // Crear algunos usuarios de prueba
            $usuariosData = [
                [
                    'nombre' => 'Carlos',
                    'apellido' => 'Rodriguez',
                    'usuario' => 'carlos.rodriguez',
                    'email' => 'carlos@example.com',
                    'pass' => password_hash('123456', PASSWORD_DEFAULT),
                    'perfil_id' => 2,
                    'baja' => 'NO'
                ],
                [
                    'nombre' => 'Maria',
                    'apellido' => 'Lopez',
                    'usuario' => 'maria.lopez',
                    'email' => 'maria@example.com',
                    'pass' => password_hash('123456', PASSWORD_DEFAULT),
                    'perfil_id' => 2,
                    'baja' => 'NO'
                ],
                [
                    'nombre' => 'Juan',
                    'apellido' => 'Perez',
                    'usuario' => 'juan.perez',
                    'email' => 'juan@example.com',
                    'pass' => password_hash('123456', PASSWORD_DEFAULT),
                    'perfil_id' => 2,
                    'baja' => 'NO'
                ]
            ];
            
            foreach ($usuariosData as $usuario) {
                $db->table('usuarios')->insert($usuario);
            }
            
            // Actualizar la lista de usuarios clientes
            $usuarios = $db->table('usuarios')->where('perfil_id', 2)->get()->getResultArray();
        }
        
        if (empty($productos)) {
            echo "No hay productos disponibles para crear ventas.\n";
            return;
        }
        
        // Limpiar ventas existentes
        $db->table('ventas_detalle')->where('id >', 0)->delete();
        $db->table('ventas_cabecera')->where('id >', 0)->delete();
        
        // Crear ventas de ejemplo
        $ventasData = [
            [
                'fecha' => '2025-07-20 14:30:00',
                'usuario_id' => $usuarios[0]['id'],
                'productos' => [
                    ['producto_id' => $productos[0]['id'], 'cantidad' => 2],
                    ['producto_id' => $productos[1]['id'], 'cantidad' => 1]
                ]
            ],
            [
                'fecha' => '2025-07-21 16:45:00',
                'usuario_id' => $usuarios[1]['id'],
                'productos' => [
                    ['producto_id' => $productos[2]['id'], 'cantidad' => 1],
                    ['producto_id' => $productos[3]['id'], 'cantidad' => 3]
                ]
            ],
            [
                'fecha' => '2025-07-22 10:15:00',
                'usuario_id' => $usuarios[2]['id'],
                'productos' => [
                    ['producto_id' => $productos[4]['id'], 'cantidad' => 1]
                ]
            ],
            [
                'fecha' => '2025-07-23 18:20:00',
                'usuario_id' => $usuarios[0]['id'],
                'productos' => [
                    ['producto_id' => $productos[5]['id'], 'cantidad' => 2],
                    ['producto_id' => $productos[6]['id'], 'cantidad' => 1],
                    ['producto_id' => $productos[7]['id'], 'cantidad' => 1]
                ]
            ],
            [
                'fecha' => '2025-07-24 12:00:00',
                'usuario_id' => $usuarios[1]['id'],
                'productos' => [
                    ['producto_id' => $productos[8]['id'], 'cantidad' => 1],
                    ['producto_id' => $productos[9]['id'], 'cantidad' => 2]
                ]
            ]
        ];
        
        foreach ($ventasData as $venta) {
            // Calcular total de la venta
            $total_venta = 0;
            foreach ($venta['productos'] as $item) {
                $producto = array_filter($productos, function($p) use ($item) {
                    return $p['id'] == $item['producto_id'];
                });
                $producto = reset($producto);
                if ($producto) {
                    $total_venta += floatval($producto['precio_vta']) * $item['cantidad'];
                }
            }
            
            // Insertar cabecera de venta
            $cabeceraData = [
                'fecha' => $venta['fecha'],
                'usuario_id' => $venta['usuario_id'],
                'total_venta' => $total_venta
            ];
            
            $db->table('ventas_cabecera')->insert($cabeceraData);
            $venta_id = $db->insertID();
            
            // Insertar detalles de venta
            foreach ($venta['productos'] as $item) {
                $producto = array_filter($productos, function($p) use ($item) {
                    return $p['id'] == $item['producto_id'];
                });
                $producto = reset($producto);
                
                if ($producto) {
                    $detalleData = [
                        'venta_id' => $venta_id,
                        'producto_id' => $item['producto_id'],
                        'cantidad' => $item['cantidad'],
                        'precio' => floatval($producto['precio_vta'])
                    ];
                    
                    $db->table('ventas_detalle')->insert($detalleData);
                }
            }
        }
        
        echo "Seeder VentasSeeder ejecutado: " . count($ventasData) . " ventas creadas.\n";
    }
}
