<?php
namespace App\Models;
use CodeIgniter\Model;

class CategoriasModel extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id';
    protected $allowedFields = ['descripcion', 'activo'];
}
?>