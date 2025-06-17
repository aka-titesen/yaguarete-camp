<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConsultasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'apellido' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'telefono' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'mensaje' => [
                'type' => 'TEXT',
            ],
            'respuesta' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => null,
            ],
            'fecha_consulta' => [
                'type' => 'DATETIME',
            ],
            'fecha_respuesta' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('consultas');
    }

    public function down()
    {
        $this->forge->dropTable('consultas');
    }
}
