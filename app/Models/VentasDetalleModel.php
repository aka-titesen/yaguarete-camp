<?php
namespace App\Models;
use CodeIgniter\Model;


class VentasDetalleModel extends Model
{
    protected $table = 'ventas_detalle';
    protected $primaryKey = 'id';    protected $allowedFields = ['venta_id', 'producto_id', 'cantidad', 'precio'];
    public function getDetalles($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new \InvalidArgumentException('ID de venta inválido en getDetalles');
        }
        
        $db = \Config\Database::connect();
        $builder = $db->table('ventas_detalle');
        
        // Seleccionar campos específicos
        $builder->select('
            ventas_detalle.id, 
            ventas_detalle.venta_id,
            ventas_detalle.producto_id,
            ventas_detalle.cantidad,
            ventas_detalle.precio,
            productos.nombre_prod,
            productos.imagen,
            productos.precio_vta
        ');
        
        $builder->join('productos', 'productos.id = ventas_detalle.producto_id', 'left');
        $builder->where('ventas_detalle.venta_id', (int)$id);
        $builder->orderBy('ventas_detalle.id', 'ASC');
        
        $query = $builder->get();
        
        // Manejo de errores
        if ($query === false) {
            log_message('error', 'Error en consulta getDetalles - ID Venta: ' . $id);
            return [];
        }
        
        $result = $query->getResultArray();
        
        // Filtrar solo productos de la venta solicitada
        $result = array_filter($result, function($row) use ($id) {
            return isset($row['venta_id']) && (int)$row['venta_id'] === (int)$id;
        });
        
        return array_values($result);
    }
}
?>