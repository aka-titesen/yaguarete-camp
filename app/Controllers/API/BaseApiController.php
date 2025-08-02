<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

/**
 * Controlador base para API REST
 * Proporciona métodos comunes para todos los endpoints de la API
 */
class BaseApiController extends ResourceController
{
    use ResponseTrait;

    /**
     * Formato de respuesta predeterminado
     * @var string
     */
    protected $format = 'json';

    /**
     * Modelo asociado al controlador
     * @var string
     */
    protected $modelName;

    /**
     * Reglas de validación
     * @var array
     */
    protected $validationRules = [];

    /**
     * Inicialización del controlador
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Configurar headers CORS
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        
        // Configurar headers de seguridad
        $this->response->setHeader('X-Content-Type-Options', 'nosniff');
        $this->response->setHeader('X-Frame-Options', 'DENY');
        $this->response->setHeader('X-XSS-Protection', '1; mode=block');
    }

    /**
     * Respuesta exitosa estándar
     * 
     * @param mixed $data Datos a retornar
     * @param string $message Mensaje opcional
     * @param int $code Código HTTP
     * @return ResponseInterface
     */
    protected function respondSuccess($data = null, string $message = 'Success', int $code = 200): ResponseInterface
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        return $this->respond($response, $code);
    }

    /**
     * Respuesta de error estándar
     * 
     * @param string $message Mensaje de error
     * @param int $code Código HTTP
     * @param array $errors Errores específicos
     * @return ResponseInterface
     */
    protected function respondError(string $message = 'Error', int $code = 400, array $errors = []): ResponseInterface
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        return $this->fail($response, $code);
    }

    /**
     * Respuesta con paginación
     * 
     * @param array $data Datos paginados
     * @param array $paginationInfo Información de paginación
     * @param string $message Mensaje opcional
     * @return ResponseInterface
     */
    protected function respondWithPagination(array $data, array $paginationInfo, string $message = 'Success'): ResponseInterface
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'pagination' => $paginationInfo,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        return $this->respond($response, 200);
    }

    /**
     * Validar datos de entrada
     * 
     * @param array $data Datos a validar
     * @param array $rules Reglas de validación
     * @return bool
     */
    protected function validateData(array $data, array $rules = []): bool
    {
        $validation = \Config\Services::validation();
        $validation->setRules($rules ?: $this->validationRules);
        
        if (!$validation->run($data)) {
            $this->validator = $validation;
            return false;
        }
        
        return true;
    }

    /**
     * Obtener errores de validación formateados
     * 
     * @return array
     */
    protected function getValidationErrors(): array
    {
        if (!$this->validator) {
            return [];
        }
        
        return $this->validator->getErrors();
    }

    /**
     * Manejar peticiones OPTIONS para CORS
     * 
     * @return ResponseInterface
     */
    protected function handleOptions(): ResponseInterface
    {
        return $this->response->setStatusCode(200);
    }

    /**
     * Obtener parámetros de paginación
     * 
     * @return array
     */
    protected function getPaginationParams(): array
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        
        // Límites de seguridad
        $page = max(1, $page);
        $limit = max(1, min(100, $limit)); // Máximo 100 elementos por página
        
        $offset = ($page - 1) * $limit;
        
        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => $offset
        ];
    }

    /**
     * Obtener filtros de la query string
     * 
     * @param array $allowedFilters Filtros permitidos
     * @return array
     */
    protected function getFilters(array $allowedFilters = []): array
    {
        $filters = [];
        
        foreach ($allowedFilters as $filter) {
            $value = $this->request->getGet($filter);
            if ($value !== null && $value !== '') {
                $filters[$filter] = $value;
            }
        }
        
        return $filters;
    }

    /**
     * Log de actividad de API
     * 
     * @param string $action Acción realizada
     * @param array $data Datos adicionales
     */
    protected function logApiActivity(string $action, array $data = []): void
    {
        $logData = [
            'action' => $action,
            'ip' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent(),
            'method' => $this->request->getMethod(),
            'uri' => $this->request->getUri()->getPath(),
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        log_message('info', 'API Activity: ' . json_encode($logData));
    }

    /**
     * Verificar límite de velocidad (rate limiting)
     * 
     * @param string $key Clave única para el rate limiting
     * @param int $maxAttempts Máximo número de intentos
     * @param int $timeWindow Ventana de tiempo en segundos
     * @return bool
     */
    protected function checkRateLimit(string $key, int $maxAttempts = 60, int $timeWindow = 60): bool
    {
        $cache = \Config\Services::cache();
        $attempts = $cache->get($key) ?? 0;
        
        if ($attempts >= $maxAttempts) {
            return false;
        }
        
        $cache->save($key, $attempts + 1, $timeWindow);
        return true;
    }
}
