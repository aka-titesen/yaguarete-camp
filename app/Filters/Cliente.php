<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface; 
use CodeIgniter\Filters\FilterInterface;

/**
 * Filtro de autenticación para usuarios (clientes)
 * 
 * Estructura de perfiles en BD:
 * - perfil_id = 1: Administrador (acceso completo incluido dashboard)
 * - perfil_id = 2: Cliente (acceso al sitio principal)
 */
class Cliente implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            // Redirigir a la página principal y mostrar el modal de login
            return redirect()->to('/')->with('showLogin', true)->with('msg', [
                'type' => 'warning',
                'body' => 'Debe iniciar sesión para ver sus compras'
            ]);
        }
        // No necesitamos verificar el perfil, cualquier usuario autenticado puede acceder
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
