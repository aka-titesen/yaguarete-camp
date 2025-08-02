<?php

namespace App\Controllers\API;

use App\Controllers\API\BaseApiController;
use App\Models\UsuarioModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Controlador de autenticación para API
 * Maneja login, registro, refresh tokens y logout
 */
class AuthController extends BaseApiController
{
    /**
     * Modelo de usuario
     * @var UsuarioModel
     */
    protected $usuarioModel;

    /**
     * Configuración JWT
     * @var array
     */
    protected $jwtConfig;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        
        // Configuración JWT
        $this->jwtConfig = [
            'secret_key' => getenv('JWT_SECRET_KEY') ?: 'yaguarete_camp_secret_key_2024',
            'expire_time' => 3600, // 1 hora
            'refresh_expire_time' => 2592000, // 30 días
            'algorithm' => 'HS256'
        ];
    }

    /**
     * POST /api/auth/login
     * Autenticar usuario y generar tokens JWT
     */
    public function login()
    {
        try {
            // Verificar rate limiting
            $ip = $this->request->getIPAddress();
            $rateLimitKey = 'login_attempts_' . $ip;
            
            if (!$this->checkRateLimit($rateLimitKey, 10, 300)) { // 10 intentos en 5 minutos
                return $this->respondError('Demasiados intentos de login. Intente más tarde.', 429);
            }

            // Obtener datos del request
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Validar datos requeridos
            if (empty($email) || empty($password)) {
                return $this->respondError('Email y contraseña son requeridos', 400);
            }

            // Buscar usuario por email
            $usuario = $this->usuarioModel->where('email', $email)->first();

            if (!$usuario) {
                $this->logApiActivity('login_failed', ['email' => $email, 'reason' => 'user_not_found']);
                return $this->respondError('Credenciales inválidas', 401);
            }

            // Verificar estado del usuario
            if ($usuario['baja'] !== 'NO') {
                $this->logApiActivity('login_failed', ['email' => $email, 'reason' => 'user_inactive']);
                return $this->respondError('Usuario inactivo', 401);
            }

            // Verificar contraseña
            if (!password_verify($password, $usuario['pass'])) {
                $this->logApiActivity('login_failed', ['email' => $email, 'reason' => 'invalid_password']);
                return $this->respondError('Credenciales inválidas', 401);
            }

            // Generar tokens
            $accessToken = $this->generateAccessToken($usuario);
            $refreshToken = $this->generateRefreshToken($usuario);

            // Actualizar último login
            $this->usuarioModel->update($usuario['id'], [
                'ultimo_login' => date('Y-m-d H:i:s')
            ]);

            // Log de login exitoso
            $this->logApiActivity('login_success', ['user_id' => $usuario['id'], 'email' => $email]);

            // Preparar respuesta
            $userData = [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'apellido' => $usuario['apellido'],
                'email' => $usuario['email'],
                'perfil_id' => $usuario['perfil_id'],
                'ultimo_login' => $usuario['ultimo_login']
            ];

            return $this->respondSuccess([
                'user' => $userData,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => $this->jwtConfig['expire_time']
            ], 'Login exitoso');

        } catch (\Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }

    /**
     * POST /api/auth/registro
     * Registrar nuevo usuario
     */
    public function registro()
    {
        try {
            // Verificar rate limiting
            $ip = $this->request->getIPAddress();
            $rateLimitKey = 'register_attempts_' . $ip;
            
            if (!$this->checkRateLimit($rateLimitKey, 5, 300)) { // 5 intentos en 5 minutos
                return $this->respondError('Demasiados intentos de registro. Intente más tarde.', 429);
            }

            // Obtener datos del request
            $data = [
                'nombre' => $this->request->getPost('nombre'),
                'apellido' => $this->request->getPost('apellido'),
                'email' => $this->request->getPost('email'),
                'pass' => $this->request->getPost('password'),
                'perfil_id' => 2, // Cliente por defecto
                'baja' => 'NO'
            ];

            // Reglas de validación
            $validationRules = [
                'nombre' => 'required|min_length[2]|max_length[50]',
                'apellido' => 'required|min_length[2]|max_length[50]',
                'email' => 'required|valid_email|is_unique[usuarios.email]',
                'pass' => 'required|min_length[6]'
            ];

            // Validar datos
            if (!$this->validateData($data, $validationRules)) {
                return $this->respondError('Datos de validación incorrectos', 400, $this->getValidationErrors());
            }

            // Hashear contraseña
            $data['pass'] = password_hash($data['pass'], PASSWORD_DEFAULT);

            // Crear usuario
            $userId = $this->usuarioModel->insert($data);

            if (!$userId) {
                return $this->respondError('Error al crear usuario', 500);
            }

            // Obtener usuario creado
            $usuario = $this->usuarioModel->find($userId);

            // Generar tokens
            $accessToken = $this->generateAccessToken($usuario);
            $refreshToken = $this->generateRefreshToken($usuario);

            // Log de registro exitoso
            $this->logApiActivity('register_success', ['user_id' => $userId, 'email' => $data['email']]);

            // Preparar respuesta
            $userData = [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'apellido' => $usuario['apellido'],
                'email' => $usuario['email'],
                'perfil_id' => $usuario['perfil_id']
            ];

            return $this->respondSuccess([
                'user' => $userData,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => $this->jwtConfig['expire_time']
            ], 'Usuario registrado exitosamente', 201);

        } catch (\Exception $e) {
            log_message('error', 'Register error: ' . $e->getMessage());
            return $this->respondError('Error interno del servidor', 500);
        }
    }

    /**
     * POST /api/auth/refresh
     * Refrescar token de acceso usando refresh token
     */
    public function refresh()
    {
        try {
            $refreshToken = $this->request->getPost('refresh_token');

            if (empty($refreshToken)) {
                return $this->respondError('Refresh token requerido', 400);
            }

            // Verificar si el token está en blacklist
            if ($this->isTokenBlacklisted($refreshToken)) {
                return $this->respondError('Token revocado', 401);
            }

            // Decodificar refresh token
            $decoded = JWT::decode($refreshToken, new Key($this->jwtConfig['secret_key'], $this->jwtConfig['algorithm']));

            // Verificar que es un refresh token
            if ($decoded->token_type !== 'refresh') {
                return $this->respondError('Token inválido', 401);
            }

            // Obtener usuario
            $usuario = $this->usuarioModel->find($decoded->user_id);

            if (!$usuario || $usuario['baja'] !== 'NO') {
                return $this->respondError('Usuario inválido o inactivo', 401);
            }

            // Generar nuevo access token
            $newAccessToken = $this->generateAccessToken($usuario);

            // Log de refresh exitoso
            $this->logApiActivity('token_refresh', ['user_id' => $usuario['id']]);

            return $this->respondSuccess([
                'access_token' => $newAccessToken,
                'token_type' => 'Bearer',
                'expires_in' => $this->jwtConfig['expire_time']
            ], 'Token renovado exitosamente');

        } catch (\Exception $e) {
            log_message('error', 'Token refresh error: ' . $e->getMessage());
            return $this->respondError('Token inválido o expirado', 401);
        }
    }

    /**
     * POST /api/auth/logout
     * Cerrar sesión y blacklist tokens
     */
    public function logout()
    {
        try {
            $accessToken = $this->request->getHeaderLine('Authorization');
            $refreshToken = $this->request->getPost('refresh_token');

            // Extraer token del header Authorization
            if (preg_match('/Bearer\s+(.*)$/i', $accessToken, $matches)) {
                $accessToken = $matches[1];
            }

            // Blacklist access token
            if (!empty($accessToken)) {
                $this->blacklistToken($accessToken);
            }

            // Blacklist refresh token
            if (!empty($refreshToken)) {
                $this->blacklistToken($refreshToken);
            }

            // Log de logout
            $userId = $this->request->user_id ?? null;
            $this->logApiActivity('logout', ['user_id' => $userId]);

            return $this->respondSuccess(null, 'Logout exitoso');

        } catch (\Exception $e) {
            log_message('error', 'Logout error: ' . $e->getMessage());
            return $this->respondError('Error en logout', 500);
        }
    }

    /**
     * Generar access token JWT
     * 
     * @param array $usuario
     * @return string
     */
    private function generateAccessToken(array $usuario): string
    {
        $now = time();
        
        $payload = [
            'iss' => base_url(), // Issuer
            'aud' => base_url(), // Audience
            'iat' => $now,       // Issued at
            'exp' => $now + $this->jwtConfig['expire_time'], // Expiration
            'user_id' => $usuario['id'],
            'email' => $usuario['email'],
            'perfil_id' => $usuario['perfil_id'],
            'token_type' => 'access'
        ];

        return JWT::encode($payload, $this->jwtConfig['secret_key'], $this->jwtConfig['algorithm']);
    }

    /**
     * Generar refresh token JWT
     * 
     * @param array $usuario
     * @return string
     */
    private function generateRefreshToken(array $usuario): string
    {
        $now = time();
        
        $payload = [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => $now,
            'exp' => $now + $this->jwtConfig['refresh_expire_time'],
            'user_id' => $usuario['id'],
            'token_type' => 'refresh'
        ];

        return JWT::encode($payload, $this->jwtConfig['secret_key'], $this->jwtConfig['algorithm']);
    }

    /**
     * Agregar token a blacklist
     * 
     * @param string $token
     */
    private function blacklistToken(string $token): void
    {
        $cache = \Config\Services::cache();
        $tokenHash = hash('sha256', $token);
        
        // Guardar en cache por el tiempo de expiración máximo
        $cache->save(
            'blacklisted_token_' . $tokenHash, 
            true, 
            $this->jwtConfig['refresh_expire_time']
        );
    }

    /**
     * Verificar si token está en blacklist
     * 
     * @param string $token
     * @return bool
     */
    private function isTokenBlacklisted(string $token): bool
    {
        $cache = \Config\Services::cache();
        $tokenHash = hash('sha256', $token);
        
        return $cache->get('blacklisted_token_' . $tokenHash) !== null;
    }
}
