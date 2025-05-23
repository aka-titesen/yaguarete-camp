<?php
namespace App\Models;
use CodeIgniter\Model;

class Productos_model extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre', 'imagen', 'categoria_id', 'costo', 'precio', 'stock', 'stock_min', 'eliminado'];
}
?>