<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface; 
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            // Redirigir a la página principal y mostrar el modal de login
            return redirect()->to('/')->with('showLogin', true)->with('msg', [
                'type' => 'warning',
                'body' => 'No tienes acceso a esta página'
            ]);
        }
        // Solo permitir acceso a administradores (perfil_id == 1)
        if ($session->get('perfil_id') != 1) {
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