<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface; 
use CodeIgniter\Filters\FilterInterface;

class AuthUser implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            // Redirigir a la página principal si no está logueado
            return redirect()->to(base_url('login'))->with('mensaje', 'Debes iniciar sesión para acceder a esta página.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se necesita nada después de la solicitud
    }
}
