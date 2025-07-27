<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVentasDetalleTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'venta_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'producto_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'cantidad' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 1,
            ],
            'precio' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 0.00,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('venta_id', 'ventas_cabecera', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('producto_id', 'productos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ventas_detalle');
    }

    public function down()
    {
        $this->forge->dropTable('ventas_detalle');
    }
}
