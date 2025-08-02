<?php

namespace App\Controllers\API;

use App\Models\ProductoModel;
use App\Models\CategoriaModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controlador API para Productos
 * Maneja todas las operaciones relacionadas con productos
 */
class ProductosController extends BaseApiController
{
    protected $modelName = 'App\Models\ProductoModel';
    protected $validationRules = [
        'nombre' => 'required|max_length[255]',
        'precio_venta' => 'required|decimal',
        'categoria_id' => 'required|integer',
        'descripcion' => 'max_length[1000]',
        'stock' => 'integer'
    ];

    /**
     * Lista productos con filtros y paginación
     * GET /api/productos
     */
    public function index(): ResponseInterface
    {
        // Verificar rate limiting
        $rateLimitKey = 'api_productos_list_' . $this->request->getIPAddress();
        if (!$this->checkRateLimit($rateLimitKey, 100, 60)) {
            return $this->respondError('Demasiadas peticiones. Intenta más tarde.', 429);
        }

        try {
            $productoModel = new ProductoModel();
            $categoriaModel = new CategoriaModel();
            
            // Parámetros de paginación
            $paginacion = $this->getPaginationParams();
            
            // Filtros permitidos
            $filtrosPermitidos = [
                'categoria_id', 'precio_min', 'precio_max', 'nombre', 
                'estado', 'destacado', 'orden'
            ];
            $filtros = $this->getFilters($filtrosPermitidos);
            
            // Construir query
            $builder = $productoModel->select('
                productos.id,
                productos.nombre,
                productos.precio_venta,
                productos.descripcion,
                productos.stock,
                productos.imagen,
                productos.destacado,
                productos.estado,
                productos.created_at,
                categorias.nombre as categoria_nombre,
                categorias.id as categoria_id
            ')
            ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
            ->where('productos.estado', 'Activo');
            
            // Aplicar filtros
            if (isset($filtros['categoria_id'])) {
                $builder->where('productos.categoria_id', $filtros['categoria_id']);
            }
            
            if (isset($filtros['precio_min'])) {
                $builder->where('productos.precio_venta >=', (float)$filtros['precio_min']);
            }
            
            if (isset($filtros['precio_max'])) {
                $builder->where('productos.precio_venta <=', (float)$filtros['precio_max']);
            }
            
            if (isset($filtros['nombre'])) {
                $builder->like('productos.nombre', $filtros['nombre']);
            }
            
            if (isset($filtros['destacado'])) {
                $builder->where('productos.destacado', $filtros['destacado'] === 'true' ? 1 : 0);
            }
            
            // Ordenamiento
            $orden = $filtros['orden'] ?? 'created_at';
            switch ($orden) {
                case 'precio_asc':
                    $builder->orderBy('productos.precio_venta', 'ASC');
                    break;
                case 'precio_desc':
                    $builder->orderBy('productos.precio_venta', 'DESC');
                    break;
                case 'nombre':
                    $builder->orderBy('productos.nombre', 'ASC');
                    break;
                default:
                    $builder->orderBy('productos.created_at', 'DESC');
            }
            
            // Contar total para paginación
            $totalRegistros = $builder->countAllResults(false);
            
            // Obtener datos paginados
            $productos = $builder->limit($paginacion['limit'], $paginacion['offset'])->get()->getResultArray();
            
            // Formatear URLs de imágenes
            foreach ($productos as &$producto) {
                $producto['imagen_url'] = $producto['imagen'] 
                    ? base_url('assets/uploads/' . $producto['imagen'])
                    : base_url('assets/images/producto-default.jpg');
                    
                $producto['precio_formateado'] = '$' . number_format($producto['precio_venta'], 0, ',', '.');
                $producto['en_stock'] = (int)$producto['stock'] > 0;
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
            
            $this->logApiActivity('productos_list', [
                'filters' => $filtros,
                'total_found' => $totalRegistros
            ]);
            
            return $this->respondWithPagination(
                $productos,
                $paginacionInfo,
                'Productos obtenidos exitosamente'
            );
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API productos/index: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Detalle de producto específico
     * GET /api/productos/{id}
     */
    public function show($id = null): ResponseInterface
    {
        if (!$id || !is_numeric($id)) {
            return $this->respondError('ID de producto inválido', 400);
        }
        
        try {
            $productoModel = new ProductoModel();
            
            $producto = $productoModel->select('
                productos.*,
                categorias.nombre as categoria_nombre
            ')
            ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
            ->where('productos.id', $id)
            ->where('productos.estado', 'Activo')
            ->first();
            
            if (!$producto) {
                return $this->respondError('Producto no encontrado', 404);
            }
            
            // Formatear datos
            $producto['imagen_url'] = $producto['imagen'] 
                ? base_url('assets/uploads/' . $producto['imagen'])
                : base_url('assets/images/producto-default.jpg');
                
            $producto['precio_formateado'] = '$' . number_format($producto['precio_venta'], 0, ',', '.');
            $producto['en_stock'] = (int)$producto['stock'] > 0;
            
            $this->logApiActivity('producto_detail', ['producto_id' => $id]);
            
            return $this->respondSuccess($producto, 'Producto obtenido exitosamente');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API productos/show: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Productos destacados
     * GET /api/productos/destacados
     */
    public function destacados(): ResponseInterface
    {
        try {
            $productoModel = new ProductoModel();
            
            $productos = $productoModel->select('
                productos.id,
                productos.nombre,
                productos.precio_venta,
                productos.descripcion,
                productos.imagen,
                categorias.nombre as categoria_nombre
            ')
            ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
            ->where('productos.destacado', 1)
            ->where('productos.estado', 'Activo')
            ->orderBy('productos.created_at', 'DESC')
            ->limit(8)
            ->get()
            ->getResultArray();
            
            // Formatear datos
            foreach ($productos as &$producto) {
                $producto['imagen_url'] = $producto['imagen'] 
                    ? base_url('assets/uploads/' . $producto['imagen'])
                    : base_url('assets/images/producto-default.jpg');
                    
                $producto['precio_formateado'] = '$' . number_format($producto['precio_venta'], 0, ',', '.');
            }
            
            return $this->respondSuccess($productos, 'Productos destacados obtenidos exitosamente');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API productos/destacados: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Productos relacionados
     * GET /api/productos/relacionados/{id}
     */
    public function relacionados($id = null): ResponseInterface
    {
        if (!$id || !is_numeric($id)) {
            return $this->respondError('ID de producto inválido', 400);
        }
        
        try {
            $productoModel = new ProductoModel();
            
            // Obtener producto base para conseguir su categoría
            $productoBase = $productoModel->select('categoria_id')->find($id);
            if (!$productoBase) {
                return $this->respondError('Producto no encontrado', 404);
            }
            
            // Obtener productos relacionados de la misma categoría
            $productos = $productoModel->select('
                productos.id,
                productos.nombre,
                productos.precio_venta,
                productos.imagen,
                categorias.nombre as categoria_nombre
            ')
            ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
            ->where('productos.categoria_id', $productoBase['categoria_id'])
            ->where('productos.id !=', $id)
            ->where('productos.estado', 'Activo')
            ->orderBy('RAND()')
            ->limit(4)
            ->get()
            ->getResultArray();
            
            // Formatear datos
            foreach ($productos as &$producto) {
                $producto['imagen_url'] = $producto['imagen'] 
                    ? base_url('assets/uploads/' . $producto['imagen'])
                    : base_url('assets/images/producto-default.jpg');
                    
                $producto['precio_formateado'] = '$' . number_format($producto['precio_venta'], 0, ',', '.');
            }
            
            return $this->respondSuccess($productos, 'Productos relacionados obtenidos exitosamente');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API productos/relacionados: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Búsqueda de productos
     * GET /api/productos/buscar?q=termino
     */
    public function buscar(): ResponseInterface
    {
        $query = $this->request->getGet('q');
        
        if (empty($query) || strlen($query) < 2) {
            return $this->respondError('Término de búsqueda debe tener al menos 2 caracteres', 400);
        }
        
        try {
            $productoModel = new ProductoModel();
            
            $productos = $productoModel->select('
                productos.id,
                productos.nombre,
                productos.precio_venta,
                productos.descripcion,
                productos.imagen,
                categorias.nombre as categoria_nombre
            ')
            ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
            ->groupStart()
                ->like('productos.nombre', $query)
                ->orLike('productos.descripcion', $query)
                ->orLike('categorias.nombre', $query)
            ->groupEnd()
            ->where('productos.estado', 'Activo')
            ->orderBy('productos.nombre', 'ASC')
            ->limit(20)
            ->get()
            ->getResultArray();
            
            // Formatear datos
            foreach ($productos as &$producto) {
                $producto['imagen_url'] = $producto['imagen'] 
                    ? base_url('assets/uploads/' . $producto['imagen'])
                    : base_url('assets/images/producto-default.jpg');
                    
                $producto['precio_formateado'] = '$' . number_format($producto['precio_venta'], 0, ',', '.');
            }
            
            $this->logApiActivity('productos_search', [
                'query' => $query,
                'results_count' => count($productos)
            ]);
            
            return $this->respondSuccess($productos, 'Búsqueda completada exitosamente');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API productos/buscar: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Crear producto (requiere autenticación)
     * POST /api/productos
     */
    public function create(): ResponseInterface
    {
        $data = $this->request->getJSON(true) ?: $this->request->getPost();
        
        if (!$this->validateData($data)) {
            return $this->respondError(
                'Datos de entrada inválidos',
                400,
                $this->getValidationErrors()
            );
        }
        
        try {
            $productoModel = new ProductoModel();
            
            $productoData = [
                'nombre' => $data['nombre'],
                'precio_venta' => $data['precio_venta'],
                'categoria_id' => $data['categoria_id'],
                'descripcion' => $data['descripcion'] ?? '',
                'stock' => $data['stock'] ?? 0,
                'destacado' => $data['destacado'] ?? 0,
                'estado' => 'Activo',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $productoId = $productoModel->insert($productoData);
            
            if (!$productoId) {
                return $this->respondError('No se pudo crear el producto', 500);
            }
            
            $this->logApiActivity('producto_created', [
                'producto_id' => $productoId,
                'user_id' => $this->request->user_id
            ]);
            
            return $this->respondSuccess(
                ['id' => $productoId],
                'Producto creado exitosamente',
                201
            );
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API productos/create: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Actualizar producto (requiere autenticación)
     * PUT /api/productos/{id}
     */
    public function update($id = null): ResponseInterface
    {
        if (!$id || !is_numeric($id)) {
            return $this->respondError('ID de producto inválido', 400);
        }
        
        $data = $this->request->getJSON(true) ?: $this->request->getPost();
        
        if (!$this->validateData($data)) {
            return $this->respondError(
                'Datos de entrada inválidos',
                400,
                $this->getValidationErrors()
            );
        }
        
        try {
            $productoModel = new ProductoModel();
            
            $producto = $productoModel->find($id);
            if (!$producto) {
                return $this->respondError('Producto no encontrado', 404);
            }
            
            $productoData = [
                'nombre' => $data['nombre'],
                'precio_venta' => $data['precio_venta'],
                'categoria_id' => $data['categoria_id'],
                'descripcion' => $data['descripcion'] ?? $producto['descripcion'],
                'stock' => $data['stock'] ?? $producto['stock'],
                'destacado' => $data['destacado'] ?? $producto['destacado'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $updated = $productoModel->update($id, $productoData);
            
            if (!$updated) {
                return $this->respondError('No se pudo actualizar el producto', 500);
            }
            
            $this->logApiActivity('producto_updated', [
                'producto_id' => $id,
                'user_id' => $this->request->user_id
            ]);
            
            return $this->respondSuccess(null, 'Producto actualizado exitosamente');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API productos/update: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Eliminar producto (requiere autenticación)
     * DELETE /api/productos/{id}
     */
    public function delete($id = null): ResponseInterface
    {
        if (!$id || !is_numeric($id)) {
            return $this->respondError('ID de producto inválido', 400);
        }
        
        try {
            $productoModel = new ProductoModel();
            
            $producto = $productoModel->find($id);
            if (!$producto) {
                return $this->respondError('Producto no encontrado', 404);
            }
            
            // Eliminación lógica
            $updated = $productoModel->update($id, [
                'estado' => 'Inactivo',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if (!$updated) {
                return $this->respondError('No se pudo eliminar el producto', 500);
            }
            
            $this->logApiActivity('producto_deleted', [
                'producto_id' => $id,
                'user_id' => $this->request->user_id
            ]);
            
            return $this->respondSuccess(null, 'Producto eliminado exitosamente');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API productos/delete: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
}
