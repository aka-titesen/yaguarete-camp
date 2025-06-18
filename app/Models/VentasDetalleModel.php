<?php
namespace App\Models;
use CodeIgniter\Model;


class VentasDetalleModel extends Model
{
    protected $table = 'ventas_detalle';
    protected $primaryKey = 'id';    protected $allowedFields = ['venta_id', 'producto_id', 'cantidad', 'precio'];
      public function getDetalles($id = null) {
        $db = \Config\Database::connect();
        $builder = $db->table('ventas_detalle');
        
        // Seleccionar campos específicos para mejor claridad - usar alias para evitar ambigüedad
        $builder->select('
            ventas_detalle.id, 
            ventas_detalle.venta_id,
            ventas_detalle.producto_id,
            ventas_detalle.cantidad,
            ventas_detalle.precio,
            ventas_cabecera.id as cabecera_id, 
            ventas_cabecera.fecha, 
            ventas_cabecera.total_venta, 
            ventas_cabecera.usuario_id, 
            productos.nombre_prod,
            productos.descripcion,
            productos.imagen,
            productos.precio_vta,
            usuarios.nombre, 
            usuarios.apellido, 
            usuarios.email, 
            usuarios.usuario
        ');
        
        $builder->join('ventas_cabecera', 'ventas_cabecera.id = ventas_detalle.venta_id', 'left');
        $builder->join('productos', 'productos.id = ventas_detalle.producto_id', 'left');
        $builder->join('usuarios', 'usuarios.id = ventas_cabecera.usuario_id', 'left');
        
        if ($id != null) {
            $builder->where('ventas_detalle.venta_id', $id);
        }
        
        // Ordenar por producto id
        $builder->orderBy('ventas_detalle.id', 'ASC');
        
        // Debug: Log the SQL query being executed
        log_message('debug', 'SQL Query for getDetalles: ' . $builder->getCompiledSelect());
        
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