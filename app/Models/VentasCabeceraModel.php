<?php
namespace App\Models;
use CodeIgniter\Model;

class VentasCabeceraModel extends Model
{
    protected $table = 'ventas_cabecera';
    protected $primaryKey = 'id';
    protected $allowedFields = ['fecha', 'usuario_id', 'total_venta'];
}
?>