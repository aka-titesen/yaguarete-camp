<?php
namespace App\Models;
use CodeIgniter\Model;

class VentasCabeceraModel extends Model
{
    protected $table = 'ventas_cabecera';
    protected $primaryKey = 'id';
    protected $allowedFields = ['fecha', 'usuario_id', 'total_venta'];    public function getBuilderVentas_cabecera(){
        $db = \Config\Database::connect();
        $builder = $db->table('ventas_cabecera');
        $builder->select('
            ventas_cabecera.id,
            ventas_cabecera.fecha,
            ventas_cabecera.total_venta,
            ventas_cabecera.usuario_id,
            usuarios.nombre,
            usuarios.apellido,
            usuarios.email,
            usuarios.usuario
        ');
        $builder->join('usuarios', 'usuarios.id = ventas_cabecera.usuario_id');
        $builder->orderBy('ventas_cabecera.fecha', 'DESC'); // Más reciente primero
        $query = $builder->get();
        
        // Manejo de errores para evitar Call to a member function getResultArray() on bool
        if ($query === false) {
            log_message('error', 'Error en consulta getBuilderVentas_cabecera: ' . $db->error()['message']);
            return [];
        }
        
        return $query->getResultArray();    }public function getVentas($id_usuario = null){
        if($id_usuario === null) {
            return $this->getBuilderVentas_cabecera();
        } else {
            $db = \Config\Database::connect();
            $builder = $db->table('ventas_cabecera');
            $builder->select('
                ventas_cabecera.id,
                ventas_cabecera.fecha,
                ventas_cabecera.total_venta,
                ventas_cabecera.usuario_id,
                usuarios.nombre,
                usuarios.apellido,
                usuarios.email,
                usuarios.usuario
            ');
            $builder->join('usuarios', 'usuarios.id = ventas_cabecera.usuario_id');
            $builder->where('ventas_cabecera.usuario_id', $id_usuario);
            $builder->orderBy('ventas_cabecera.fecha', 'DESC'); // Más reciente primero
            $query = $builder->get();
            
            // Manejo de errores para evitar Call to a member function getResultArray() on bool
            if ($query === false) {
                log_message('error', 'Error en consulta getVentas: ' . $db->error()['message'] . ' - ID Usuario: ' . $id_usuario);
                return [];
            }
            
            return $query->getResultArray();
        }
    }

    /**
     * Obtiene ventas con filtros aplicados
     */
    public function getVentasFiltradas($fecha_desde = null, $fecha_hasta = null, $cliente = null, $monto_min = null, $monto_max = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('ventas_cabecera');        $builder->select('
            ventas_cabecera.id,
            ventas_cabecera.fecha,
            ventas_cabecera.total_venta,
            ventas_cabecera.usuario_id,
            usuarios.nombre,
            usuarios.apellido,
            usuarios.email,
            usuarios.usuario
        ');
        $builder->join('usuarios', 'usuarios.id = ventas_cabecera.usuario_id');
        
        // Aplicar filtros si están definidos
        if (!empty($fecha_desde)) {
            $builder->where('ventas_cabecera.fecha >=', $fecha_desde);
        }
        
        if (!empty($fecha_hasta)) {
            $builder->where('ventas_cabecera.fecha <=', $fecha_hasta);
        }
        
        if (!empty($cliente)) {
            $builder->where('ventas_cabecera.usuario_id', $cliente);
        }
        
        if (!empty($monto_min)) {
            $builder->where('ventas_cabecera.total_venta >=', $monto_min);
        }
        
        if (!empty($monto_max)) {
            $builder->where('ventas_cabecera.total_venta <=', $monto_max);
        }
        
        // Ordenar por fecha descendente (más reciente primero)
        $builder->orderBy('ventas_cabecera.fecha', 'DESC');
        
        $query = $builder->get();
        
        // Manejo de errores
        if ($query === false) {
            log_message('error', 'Error en consulta getVentasFiltradas: ' . $db->error()['message']);
            return [];
        }
        
        return $query->getResultArray();
    }
}
?>