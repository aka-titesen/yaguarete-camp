<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface; 
use CodeIgniter\Filters\FilterInterface;

/**
 * Filtro de autenticación para rutas protegidas
 * 
 * Estructura de perfiles en BD:
 * - perfil_id = 1: Cliente (acceso al sitio principal)
 * - perfil_id = 2: Administrador (acceso completo incluido dashboard)
 * - perfil_id = 3: Vendedor (acceso al sitio principal)
 */
class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();        if (!$session->get('isLoggedIn')) {
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