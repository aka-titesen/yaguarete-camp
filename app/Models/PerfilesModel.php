<?php
namespace App\Models;
use CodeIgniter\Model;

class PerfilesModel extends Model
{
    protected $table = 'perfiles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_perfil', 'descripcion', 'baja'];
}
?>