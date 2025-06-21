<?php

namespace App\Models;

use CodeIgniter\Model;


class UsuariosModel extends Model
{
    /**
    *Nombre de la tabla en la base de datos
    */
    protected $table = 'usuarios';
    
    /**
     * Clave primaria de la tabla
     */
    protected $primaryKey = 'id';
    
    protected $allowedFields = ['nombre', 'apellido', 'usuario', 'email', 'pass', 'perfil_id', 'baja'];
    
    /**
     * Habilitar timestamps automáticos
     */
    protected $useTimestamps = false;
    
    /**
     * Tipo de retorno para las consultas
     */
    protected $returnType = 'array';
}
?>