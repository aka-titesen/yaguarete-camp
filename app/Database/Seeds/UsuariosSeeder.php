<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Deshabilitar verificaciones de claves foráneas temporalmente
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        
        // Limpiar datos relacionados primero
        $db->table('ventas_detalle')->truncate();
        $db->table('ventas_cabecera')->truncate();
        $db->table('usuarios')->truncate();
        
        // Rehabilitar verificaciones de claves foráneas
        $db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        // Crear administradores (perfil_id = 1) - máximo 2
        $administradores = [
            [
                'nombre' => 'Admin',
                'apellido' => 'Principal',
                'usuario' => 'admin',
                'email' => 'admin@yagaruete.com',
                'pass' => password_hash('Admin123!', PASSWORD_DEFAULT),
                'perfil_id' => 1,
                'baja' => 'NO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nombre' => 'María',
                'apellido' => 'Rodríguez',
                'usuario' => 'mrodriguez',
                'email' => 'maria.rodriguez@yagaruete.com',
                'pass' => password_hash('Maria123!', PASSWORD_DEFAULT),
                'perfil_id' => 1,
                'baja' => 'NO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($administradores as $admin) {
            $db->table('usuarios')->insert($admin);
        }
        
        // Crear clientes (perfil_id = 2) con datos para asociar a las ventas
        $clientes = [
            [
                'nombre' => 'Carlos',
                'apellido' => 'Rodriguez',
                'usuario' => 'carlos.rodriguez',
                'email' => 'carlos@example.com',
                'pass' => password_hash('Carlos123!', PASSWORD_DEFAULT),
                'perfil_id' => 2,
                'baja' => 'NO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nombre' => 'María',
                'apellido' => 'Lopez',
                'usuario' => 'maria.lopez',
                'email' => 'maria.lopez@example.com',
                'pass' => password_hash('Maria456!', PASSWORD_DEFAULT),
                'perfil_id' => 2,
                'baja' => 'NO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'usuario' => 'juan.perez',
                'email' => 'juan.perez@example.com',
                'pass' => password_hash('Juan789!', PASSWORD_DEFAULT),
                'perfil_id' => 2,
                'baja' => 'NO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'García',
                'usuario' => 'ana.garcia',
                'email' => 'ana.garcia@example.com',
                'pass' => password_hash('Ana123#', PASSWORD_DEFAULT),
                'perfil_id' => 2,
                'baja' => 'NO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nombre' => 'Luis',
                'apellido' => 'Martínez',
                'usuario' => 'luis.martinez',
                'email' => 'luis.martinez@example.com',
                'pass' => password_hash('Luis456#', PASSWORD_DEFAULT),
                'perfil_id' => 2,
                'baja' => 'NO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nombre' => 'Carmen',
                'apellido' => 'Fernández',
                'usuario' => 'carmen.fernandez',
                'email' => 'carmen.fernandez@example.com',
                'pass' => password_hash('Carmen789#', PASSWORD_DEFAULT),
                'perfil_id' => 2,
                'baja' => 'NO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nombre' => 'Diego',
                'apellido' => 'Gómez',
                'usuario' => 'diego.gomez',
                'email' => 'diego.gomez@example.com',
                'pass' => password_hash('Diego123$', PASSWORD_DEFAULT),
                'perfil_id' => 2,
                'baja' => 'NO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($clientes as $cliente) {
            $db->table('usuarios')->insert($cliente);
        }
        
        echo "UsuariosSeeder: Se crearon 2 administradores (perfil_id = 1) y 7 clientes (perfil_id = 2)\n";
    }
}
