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
        
        // Seleccionar campos específicos para mejor claridad - usar alias para evitar ambigüedad
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
        
        // Ordenar por producto id
        $builder->orderBy('ventas_detalle.id', 'ASC');
        
        // Debug: Log the SQL query being executed
        $sql = $builder->getCompiledSelect();
        log_message('error', 'SQL getDetalles: ' . $sql);
        
        $query = $builder->get();
        
        // Manejo de errores para evitar Call to a member function getResultArray() on bool
        if ($query === false) {
            log_message('error', 'Error en consulta getDetalles: ' . $db->error()['message'] . ' - ID Venta: ' . $id);
            return [];
        }
        
        $result = $query->getResultArray();
        // Volcado de resultado para depuración
        log_message('error', 'RESULTADO getDetalles: ' . print_r($result, true));
        // FILTRO URGENTE: solo productos de la venta solicitada
        $result = array_filter($result, function($row) use ($id) {
            return isset($row['venta_id']) && (int)$row['venta_id'] === (int)$id;
        });
        return array_values($result);
    }
}
?>