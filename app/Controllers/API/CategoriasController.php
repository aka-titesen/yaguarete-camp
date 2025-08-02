<?php

namespace App\Controllers\API;

use App\Models\CategoriaModel;
use App\Models\ProductoModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controlador API para Categorías
 * Maneja todas las operaciones relacionadas con categorías
 */
class CategoriasController extends BaseApiController
{
    protected $modelName = 'App\Models\CategoriaModel';

    /**
     * Listar todas las categorías
     * GET /api/categorias
     */
    public function index(): ResponseInterface
    {
        // Verificar rate limiting
        $rateLimitKey = 'api_categorias_list_' . $this->request->getIPAddress();
        if (!$this->checkRateLimit($rateLimitKey, 100, 60)) {
            return $this->respondError('Demasiadas peticiones. Intenta más tarde.', 429);
        }

        try {
            $categoriaModel = new CategoriaModel();
            $productoModel = new ProductoModel();
            
            // Obtener categorías activas
            $categorias = $categoriaModel->select('
                categorias.id,
                categorias.nombre,
                categorias.descripcion,
                categorias.imagen,
                categorias.created_at
            ')
            ->where('categorias.estado', 'Activo')
            ->orderBy('categorias.nombre', 'ASC')
            ->get()
            ->getResultArray();
            
            // Contar productos por categoría
            foreach ($categorias as &$categoria) {
                $categoria['total_productos'] = $productoModel
                    ->where('categoria_id', $categoria['id'])
                    ->where('estado', 'Activo')
                    ->countAllResults();
                    
                $categoria['imagen_url'] = $categoria['imagen'] 
                    ? base_url('assets/uploads/categorias/' . $categoria['imagen'])
                    : base_url('assets/images/categoria-default.jpg');
            }
            
            $this->logApiActivity('categorias_list', [
                'total_found' => count($categorias)
            ]);
            
            return $this->respondSuccess(
                $categorias,
                'Categorías obtenidas exitosamente'
            );
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API categorias/index: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Detalle de categoría específica
     * GET /api/categorias/{id}
     */
    public function show($id = null): ResponseInterface
    {
        if (!$id || !is_numeric($id)) {
            return $this->respondError('ID de categoría inválido', 400);
        }
        
        try {
            $categoriaModel = new CategoriaModel();
            $productoModel = new ProductoModel();
            
            $categoria = $categoriaModel->select('
                id,
                nombre,
                descripcion,
                imagen,
                created_at
            ')
            ->where('id', $id)
            ->where('estado', 'Activo')
            ->first();
            
            if (!$categoria) {
                return $this->respondError('Categoría no encontrada', 404);
            }
            
            // Contar productos de esta categoría
            $categoria['total_productos'] = $productoModel
                ->where('categoria_id', $categoria['id'])
                ->where('estado', 'Activo')
                ->countAllResults();
                
            // Obtener algunos productos destacados de esta categoría
            $categoria['productos_destacados'] = $productoModel->select('
                id,
                nombre,
                precio_venta,
                imagen
            ')
            ->where('categoria_id', $categoria['id'])
            ->where('estado', 'Activo')
            ->where('destacado', 1)
            ->limit(3)
            ->get()
            ->getResultArray();
            
            // Formatear URLs de imágenes
            $categoria['imagen_url'] = $categoria['imagen'] 
                ? base_url('assets/uploads/categorias/' . $categoria['imagen'])
                : base_url('assets/images/categoria-default.jpg');
                
            foreach ($categoria['productos_destacados'] as &$producto) {
                $producto['imagen_url'] = $producto['imagen'] 
                    ? base_url('assets/uploads/' . $producto['imagen'])
                    : base_url('assets/images/producto-default.jpg');
                $producto['precio_formateado'] = '$' . number_format($producto['precio_venta'], 0, ',', '.');
            }
            
            $this->logApiActivity('categoria_detail', ['categoria_id' => $id]);
            
            return $this->respondSuccess($categoria, 'Categoría obtenida exitosamente');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API categorias/show: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Productos de una categoría específica
     * GET /api/categorias/{id}/productos
     */
    public function productos($id = null): ResponseInterface
    {
        if (!$id || !is_numeric($id)) {
            return $this->respondError('ID de categoría inválido', 400);
        }
        
        try {
            $categoriaModel = new CategoriaModel();
            $productoModel = new ProductoModel();
            
            // Verificar que la categoría existe
            $categoria = $categoriaModel->where('id', $id)->where('estado', 'Activo')->first();
            if (!$categoria) {
                return $this->respondError('Categoría no encontrada', 404);
            }
            
            // Parámetros de paginación
            $paginacion = $this->getPaginationParams();
            
            // Filtros adicionales
            $filtrosPermitidos = ['precio_min', 'precio_max', 'orden'];
            $filtros = $this->getFilters($filtrosPermitidos);
            
            // Construir query de productos
            $builder = $productoModel->select('
                productos.id,
                productos.nombre,
                productos.precio_venta,
                productos.descripcion,
                productos.stock,
                productos.imagen,
                productos.destacado,
                productos.created_at
            ')
            ->where('productos.categoria_id', $id)
            ->where('productos.estado', 'Activo');
            
            // Aplicar filtros de precio
            if (isset($filtros['precio_min'])) {
                $builder->where('productos.precio_venta >=', (float)$filtros['precio_min']);
            }
            
            if (isset($filtros['precio_max'])) {
                $builder->where('productos.precio_venta <=', (float)$filtros['precio_max']);
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
                case 'destacados':
                    $builder->orderBy('productos.destacado', 'DESC')
                           ->orderBy('productos.created_at', 'DESC');
                    break;
                default:
                    $builder->orderBy('productos.created_at', 'DESC');
            }
            
            // Contar total para paginación
            $totalRegistros = $builder->countAllResults(false);
            
            // Obtener datos paginados
            $productos = $builder->limit($paginacion['limit'], $paginacion['offset'])->get()->getResultArray();
            
            // Formatear URLs de imágenes y precios
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
            
            $responseData = [
                'categoria' => [
                    'id' => $categoria['id'],
                    'nombre' => $categoria['nombre'],
                    'descripcion' => $categoria['descripcion']
                ],
                'productos' => $productos
            ];
            
            $this->logApiActivity('categoria_productos', [
                'categoria_id' => $id,
                'filters' => $filtros,
                'total_found' => $totalRegistros
            ]);
            
            return $this->respondWithPagination(
                $responseData,
                $paginacionInfo,
                'Productos de la categoría obtenidos exitosamente'
            );
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API categorias/productos: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
    
    /**
     * Estadísticas de categorías (para admin)
     * GET /api/categorias/estadisticas
     */
    public function estadisticas(): ResponseInterface
    {
        try {
            $categoriaModel = new CategoriaModel();
            $productoModel = new ProductoModel();
            
            $categorias = $categoriaModel->select('
                id,
                nombre
            ')
            ->where('estado', 'Activo')
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();
            
            foreach ($categorias as &$categoria) {
                // Total de productos
                $categoria['total_productos'] = $productoModel
                    ->where('categoria_id', $categoria['id'])
                    ->where('estado', 'Activo')
                    ->countAllResults();
                
                // Productos destacados
                $categoria['productos_destacados'] = $productoModel
                    ->where('categoria_id', $categoria['id'])
                    ->where('estado', 'Activo')
                    ->where('destacado', 1)
                    ->countAllResults();
                
                // Promedio de precios
                $precioPromedio = $productoModel->selectAvg('precio_venta')
                    ->where('categoria_id', $categoria['id'])
                    ->where('estado', 'Activo')
                    ->get()
                    ->getRowArray();
                    
                $categoria['precio_promedio'] = $precioPromedio['precio_venta'] 
                    ? round($precioPromedio['precio_venta'], 2)
                    : 0;
                    
                $categoria['precio_promedio_formateado'] = '$' . number_format($categoria['precio_promedio'], 0, ',', '.');
            }
            
            return $this->respondSuccess(
                $categorias,
                'Estadísticas de categorías obtenidas exitosamente'
            );
            
        } catch (\Exception $e) {
            log_message('error', 'Error en API categorias/estadisticas: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }
}
