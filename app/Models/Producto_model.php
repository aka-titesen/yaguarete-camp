<?php
namespace App\Models;
use CodeIgniter\Model;

class Producto_model extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre_prod', 'imagen', 'categoria_id', 'precio', 'precio_vta', 'stock', 'stock_min', 'eliminado'];

    // Obtener todos los productos
    public function getProductoAll()
    {
        return $this->findAll();
    }
    
    // Obtener productos relacionados
    public function getRelacionados($categoria_id, $actual_id, $limit = 4)
    {
        return $this->where('categoria_id', $categoria_id)
            ->where('id !=', $actual_id)
            ->where('eliminado !=', 'SI')
            ->orderBy('RAND()')
            ->limit($limit)
            ->findAll();
    }
}
?>