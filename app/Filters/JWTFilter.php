<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

/**
 * Filtro JWT para autenticación de API
 * Valida tokens JWT en las rutas protegidas de la API
 */
class JWTFilter implements FilterInterface
{
    /**
     * Ejecutar antes de procesar la petición
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');
        
        // Configurar headers CORS para respuestas de error
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        
        // Obtener el token del header Authorization
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            return $this->respondUnauthorized('Token de autenticación requerido');
        }
        
        // Verificar formato "Bearer TOKEN"
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $this->respondUnauthorized('Formato de token inválido. Use: Bearer <token>');
        }
        
        $token = $matches[1];
        
        try {
            // Validar y decodificar el token
            $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET_KEY'), 'HS256'));
            
            // Verificar que el token no esté en blacklist
            if ($this->isTokenBlacklisted($token)) {
                return $this->respondUnauthorized('Token revocado');
            }
            
            // Agregar datos del usuario al request para uso posterior
            $request->user_id = $decoded->user_id;
            $request->user_email = $decoded->email;
            $request->user_perfil = $decoded->perfil_id;
            $request->token_exp = $decoded->exp;
            $request->token_iat = $decoded->iat;
            
            // Log de acceso exitoso
            log_message('info', "JWT Auth Success: User {$decoded->user_id} accessed " . $request->getUri()->getPath());
            
        } catch (ExpiredException $e) {
            return $this->respondUnauthorized('Token expirado');
        } catch (SignatureInvalidException $e) {
            return $this->respondUnauthorized('Token inválido');
        } catch (\Exception $e) {
            log_message('error', 'JWT Validation Error: ' . $e->getMessage());
            return $this->respondUnauthorized('Error de autenticación');
        }
        
        return $request;
    }

    /**
     * Ejecutar después de procesar la petición
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se requiere procesamiento posterior
        return $response;
    }

    /**
     * Responder con error 401 Unauthorized
     *
     * @param string $message
     * @return ResponseInterface
     */
    private function respondUnauthorized(string $message): ResponseInterface
    {
        $response = service('response');
        
        $data = [
            'status' => 'error',
            'message' => $message,
            'code' => 401,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        return $response
            ->setStatusCode(401)
            ->setContentType('application/json')
            ->setBody(json_encode($data));
    }

    /**
     * Verificar si el token está en blacklist
     *
     * @param string $token
     * @return bool
     */
    private function isTokenBlacklisted(string $token): bool
    {
        $cache = \Config\Services::cache();
        $blacklisted = $cache->get('blacklisted_token_' . hash('sha256', $token));
        
        return $blacklisted !== null;
    }
}
