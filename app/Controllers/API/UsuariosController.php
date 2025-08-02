<?php

namespace App\Controllers\API;

use App\Models\UsuarioModel;
use App\Models\VentaModel;
use App\Models\VentaDetalleModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controlador API para Usuarios
 * Maneja operaciones del perfil de usuario autenticado
 */
class UsuariosController extends BaseApiController
{
    protected $modelName = 'App\Models\UsuarioModel';
    protected $validationRules = [
        'nombre' => 'required|max_length[255]',
        'apellido' => 'required|max_length[255]',
        'email' => 'required|valid_email|max_length[255]',
        'telefono' => 'max_length[20]',
        'direccion' => 'max_length[500]'
    ];

    /**
     * Obtener perfil del usuario autenticado
     * GET /api/usuario/perfil
     */
    public function perfil(): ResponseInterface
    {
        try {
            $usuarioModel = new UsuarioModel();
            $userId = $this->request->user_id;
            
            $usuario = $usuarioModel->select('
                id,
                nombre,
                apellido,
                email,
                telefono,
                direccion,
                fecha_alta,
                perfil_id
            ')
            ->where('id', $userId)
            ->where('baja', 'NO')
            ->first();
            
            if (!$usuario) {
                return $this->respondError('Usuario no encontrado', 404);
            }
            
            // Obtener estadísticas del usuario
            $ventaModel = new VentaModel();
            $estadisticas = [
                'total_compras' => $ventaModel->where('usuario_id', $userId)->countAllResults(),
                'total_gastado' => $ventaModel->selectSum('total')
                    ->where('usuario_id', $userId)
                    ->get()
                    ->getRowArray()['total'] ?? 0
            ];
            
            $usuario['estadisticas'] = $estadisticas;
            $usuario['estadisticas']['total_gastado_formateado'] = '$' . number_format($estadisticas['total_gastado'], 0, ',', '.');
            
            $this->logApiActivity('user_profile_view', ['user_id' => $userId]);
            
            return $this->respondSuccess($usuario, 'Perfil obtenido exitosamente');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API usuarios/perfil: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Actualizar perfil del usuario autenticado
     * PUT /api/usuario/perfil
     */
    public function actualizarPerfil(): ResponseInterface
    {
        $data = $this->request->getJSON(true) ?: $this->request->getPost();
        $userId = $this->request->user_id;
        
        // Validaciones personalizadas
        $validationRules = $this->validationRules;
        
        // Si se está actualizando el email, verificar que no esté en uso por otro usuario
        if (isset($data['email'])) {
            $usuarioModel = new UsuarioModel();
            $existingUser = $usuarioModel->where('email', $data['email'])
                ->where('id !=', $userId)
                ->where('baja', 'NO')
                ->first();
                
            if ($existingUser) {
                return $this->respondError('El email ya está en uso por otro usuario', 400);
            }
        }
        
        if (!$this->validateData($data, $validationRules)) {
            return $this->respondError(
                'Datos de entrada inválidos',
                400,
                $this->getValidationErrors()
            );
        }
        
        try {
            $usuarioModel = new UsuarioModel();
            
            // Verificar que el usuario existe
            $usuario = $usuarioModel->where('id', $userId)->where('baja', 'NO')->first();
            if (!$usuario) {
                return $this->respondError('Usuario no encontrado', 404);
            }
            
            $updateData = [
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'email' => $data['email'],
                'telefono' => $data['telefono'] ?? $usuario['telefono'],
                'direccion' => $data['direccion'] ?? $usuario['direccion'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $updated = $usuarioModel->update($userId, $updateData);
            
            if (!$updated) {
                return $this->respondError('No se pudo actualizar el perfil', 500);
            }
            
            // Obtener datos actualizados
            $usuarioActualizado = $usuarioModel->select('
                id,
                nombre,
                apellido,
                email,
                telefono,
                direccion,
                fecha_alta
            ')
            ->where('id', $userId)
            ->first();
            
            $this->logApiActivity('user_profile_updated', ['user_id' => $userId]);
            
            return $this->respondSuccess(
                $usuarioActualizado,
                'Perfil actualizado exitosamente'
            );
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API usuarios/actualizarPerfil: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Obtener historial de pedidos del usuario
     * GET /api/usuario/pedidos
     */
    public function pedidos(): ResponseInterface
    {
        try {
            $userId = $this->request->user_id;
            $ventaModel = new VentaModel();
            $ventaDetalleModel = new VentaDetalleModel();
            
            // Parámetros de paginación
            $paginacion = $this->getPaginationParams();
            
            // Obtener ventas del usuario
            $builder = $ventaModel->select('
                ventas.id,
                ventas.fecha,
                ventas.total,
                ventas.estado,
                ventas.created_at
            ')
            ->where('ventas.usuario_id', $userId)
            ->orderBy('ventas.created_at', 'DESC');
            
            // Contar total para paginación
            $totalRegistros = $builder->countAllResults(false);
            
            // Obtener datos paginados
            $ventas = $builder->limit($paginacion['limit'], $paginacion['offset'])->get()->getResultArray();
            
            // Obtener detalles de cada venta
            foreach ($ventas as &$venta) {
                $detalles = $ventaDetalleModel->select('
                    venta_detalle.cantidad,
                    venta_detalle.precio,
                    productos.nombre as producto_nombre,
                    productos.imagen as producto_imagen
                ')
                ->join('productos', 'productos.id = venta_detalle.producto_id', 'left')
                ->where('venta_detalle.venta_id', $venta['id'])
                ->get()
                ->getResultArray();
                
                // Formatear detalles
                foreach ($detalles as &$detalle) {
                    $detalle['subtotal'] = $detalle['cantidad'] * $detalle['precio'];
                    $detalle['subtotal_formateado'] = '$' . number_format($detalle['subtotal'], 0, ',', '.');
                    $detalle['precio_formateado'] = '$' . number_format($detalle['precio'], 0, ',', '.');
                    $detalle['producto_imagen_url'] = $detalle['producto_imagen'] 
                        ? base_url('assets/uploads/' . $detalle['producto_imagen'])
                        : base_url('assets/images/producto-default.jpg');
                }
                
                $venta['detalles'] = $detalles;
                $venta['total_formateado'] = '$' . number_format($venta['total'], 0, ',', '.');
                $venta['fecha_formateada'] = date('d/m/Y', strtotime($venta['fecha']));
                $venta['total_items'] = array_sum(array_column($detalles, 'cantidad'));
            }
            
            // Información de paginación
            $paginacionInfo = [
                'page' => $paginacion['page'],
                'limit' => $paginacion['limit'],
                'total_items' => $totalRegistros,
                'total_pages' => ceil($totalRegistros / $paginacion['limit']),
                'has_next' => ($paginacion['page'] * $paginacion['limit']) < $totalRegistros,
                'has_prev' => $paginacion['page'] > 1
            ];
            
            $this->logApiActivity('user_orders_view', [
                'user_id' => $userId,
                'total_orders' => $totalRegistros
            ]);
            
            return $this->respondWithPagination(
                $ventas,
                $paginacionInfo,
                'Historial de pedidos obtenido exitosamente'
            );
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API usuarios/pedidos: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Agregar dirección del usuario
     * POST /api/usuario/direccion
     */
    public function agregarDireccion(): ResponseInterface
    {
        $data = $this->request->getJSON(true) ?: $this->request->getPost();
        $userId = $this->request->user_id;
        
        $validationRules = [
            'direccion' => 'required|max_length[500]',
            'ciudad' => 'required|max_length[100]',
            'codigo_postal' => 'max_length[10]',
            'provincia' => 'max_length[100]',
            'es_principal' => 'in_list[0,1]'
        ];
        
        if (!$this->validateData($data, $validationRules)) {
            return $this->respondError(
                'Datos de entrada inválidos',
                400,
                $this->getValidationErrors()
            );
        }
        
        try {
            $usuarioModel = new UsuarioModel();
            
            // Verificar que el usuario existe
            $usuario = $usuarioModel->where('id', $userId)->where('baja', 'NO')->first();
            if (!$usuario) {
                return $this->respondError('Usuario no encontrado', 404);
            }
            
            // Por ahora, actualizar la dirección principal del usuario
            // En el futuro se puede crear una tabla separada para múltiples direcciones
            $direccionCompleta = $data['direccion'];
            if (isset($data['ciudad'])) {
                $direccionCompleta .= ', ' . $data['ciudad'];
            }
            if (isset($data['provincia'])) {
                $direccionCompleta .= ', ' . $data['provincia'];
            }
            if (isset($data['codigo_postal'])) {
                $direccionCompleta .= ' (' . $data['codigo_postal'] . ')';
            }
            
            $updated = $usuarioModel->update($userId, [
                'direccion' => $direccionCompleta,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if (!$updated) {
                return $this->respondError('No se pudo agregar la dirección', 500);
            }
            
            $this->logApiActivity('user_address_added', [
                'user_id' => $userId,
                'address_data' => $data
            ]);
            
            return $this->respondSuccess(
                ['direccion' => $direccionCompleta],
                'Dirección agregada exitosamente'
            );
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API usuarios/agregarDireccion: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Cambiar contraseña del usuario
     * PUT /api/usuario/cambiar-password
     */
    public function cambiarPassword(): ResponseInterface
    {
        $data = $this->request->getJSON(true) ?: $this->request->getPost();
        $userId = $this->request->user_id;
        
        $validationRules = [
            'password_actual' => 'required|min_length[6]',
            'password_nuevo' => 'required|min_length[6]',
            'password_confirmacion' => 'required|matches[password_nuevo]'
        ];
        
        if (!$this->validateData($data, $validationRules)) {
            return $this->respondError(
                'Datos de entrada inválidos',
                400,
                $this->getValidationErrors()
            );
        }
        
        try {
            $usuarioModel = new UsuarioModel();
            
            // Obtener usuario actual
            $usuario = $usuarioModel->where('id', $userId)->where('baja', 'NO')->first();
            if (!$usuario) {
                return $this->respondError('Usuario no encontrado', 404);
            }
            
            // Verificar contraseña actual
            if (!password_verify($data['password_actual'], $usuario['pass'])) {
                return $this->respondError('La contraseña actual es incorrecta', 400);
            }
            
            // Actualizar contraseña
            $passwordHash = password_hash($data['password_nuevo'], PASSWORD_DEFAULT);
            
            $updated = $usuarioModel->update($userId, [
                'pass' => $passwordHash,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if (!$updated) {
                return $this->respondError('No se pudo cambiar la contraseña', 500);
            }
            
            $this->logApiActivity('user_password_changed', ['user_id' => $userId]);
            
            return $this->respondSuccess(null, 'Contraseña cambiada exitosamente');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API usuarios/cambiarPassword: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
}
