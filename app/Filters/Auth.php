<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface; 
use CodeIgniter\Filters\FilterInterface;

/**
 * FILTRO DE AUTENTICACIÓN PARA ADMINISTRADORES
 * 
 * Este filtro protege las rutas administrativas del sistema,
 * permitiendo acceso únicamente a usuarios autenticados con
 * perfil de administrador (perfil_id = 2).
 * 
 * Estructura de perfiles en la base de datos:
 * - perfil_id = 1: Cliente (acceso limitado al sitio público)
 * - perfil_id = 2: Administrador (acceso completo al sistema)
 * - perfil_id = 3: Vendedor (acceso limitado al sitio público)
 * 
 * Rutas protegidas por este filtro:
 * - /admin_usuarios (gestión de usuarios)
 * - /administrar_productos (gestión de productos)
 * - /admin-ventas (administración de ventas)
 * - /admin-consultas (administración de consultas)
 * 
 * @author Proyecto Martínez González
 * @version 1.0
 */
class Auth implements FilterInterface
{
    /**
     * Verificar autenticación antes de acceder a rutas protegidas
     * 
     * Valida dos condiciones:
     * 1. Usuario debe estar autenticado (isLoggedIn = true)
     * 2. Usuario debe tener perfil de administrador (perfil_id = 2)
     * 
     * @param RequestInterface $request Objeto de solicitud HTTP
     * @param mixed $arguments Argumentos adicionales del filtro
     * @return \CodeIgniter\HTTP\RedirectResponse|void Redirección si no tiene acceso
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Verificar si el usuario está autenticado
        if (!$session->get('isLoggedIn')) {
            // Redirigir a la página principal y mostrar el modal de login
            return redirect()->to('/')->with('showLogin', true)->with('msg', [
                'type' => 'warning',
                'body' => 'No tienes acceso a esta página'
            ]);
        }
        
        // Solo permitir acceso a administradores (perfil_id == 2 según la BD)
        if ($session->get('perfil_id') != 2) {
            return redirect()->to('/')->with('msg', [
                'type' => 'warning',
                'body' => 'No tienes permisos de administrador'
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}