<?php
namespace App\Models;
use CodeIgniter\Model;


class VentasDetalleModel extends Model
{
    protected $table = 'ventas_detalle';
    protected $primaryKey = 'id';
    protected $allowedFields = ['venta_id', 'producto_id', 'cantidad', 'precio'];    public function getDetalles($id = null) {
        $db = \Config\Database::connect();
        $builder = $db->table('ventas_detalle');
        $builder->select('*');
        $builder->join('ventas_cabecera', 'ventas_cabecera.id = ventas_detalle.venta_id');
        $builder->join('productos', 'productos.id = ventas_detalle.producto_id');
        $builder->join('usuarios', 'usuarios.id = ventas_cabecera.usuario_id'); 
        if ($id != null) {
            $builder->where('ventas_cabecera.id', $id);
        }
        $query = $builder->get();
        
        // Manejo de errores para evitar Call to a member function getResultArray() on bool
        if ($query === false) {
            log_message('error', 'Error en consulta getDetalles: ' . $db->error()['message'] . ' - ID Venta: ' . $id);
            return [];
        }
        
        return $query->getResultArray();
    }
}
?>