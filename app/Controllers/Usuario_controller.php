<?php
namespace App\Controllers;
use App\Models\Usuarios_model;
use CodeIgniter\Controller;

class Usuario_controller extends Controller
{
    public function __construct()
    {
        helper(['form', 'url']);
    }

    public function formValidation()
    {

        $input = $this->validate(
            [
                'nombre' => 'required|min_length[3]',
                'apellido' => 'required|min_length[3]|max_length[25]',
                'email' => 'required|min_length[4]|max_length[100]|valid_email|is_unique[usuarios.email]',
                'usuario' => 'required|min_length[3]|is_unique[usuarios.usuario]',
                'pass' => 'required|min_length[3]|max_length[10]'
            ],
        );
        $formModel = new Usuarios_model();
        if (!$input) {
            $data['titulo'] = 'Registro';
            echo view('front/head_view', $data);
            echo view('back/usuario/registro', ['validation' => $this->validator]);
            echo view('front/footer_view');
            echo view('front/nav_view');
        } else {

            $perfil_id = $this->request->getVar('perfil_id') ?? 2; // 1=admin, 2=usuario normal
            $formModel->save([
                'nombre' => $this->request->getVar('nombre'),
                'apellido' => $this->request->getVar('apellido'),
                'usuario' => $this->request->getVar('usuario'),
                'email' => $this->request->getVar('email'),
                'pass' => password_hash($this->request->getVar('pass'), PASSWORD_DEFAULT),
                'perfil_id' => $perfil_id
            ]);
            session()->setFlashdata('success', 'Usuario registrado con exito');
            if ($perfil_id == 1) {
                // Si es administrador, iniciar sesión y redirigir al dashboard
                $session = session();
                $usuario = $formModel->where('email', $this->request->getVar('email'))->first();
                $session->set([
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'apellido' => $usuario['apellido'],
                    'email' => $usuario['email'],
                    'usuario' => $usuario['usuario'],
                    'perfil_id' => $usuario['perfil_id'],
                    'isLoggedIn' => TRUE
                ]);
                return $this->response->redirect(site_url('dashboard'));
            } else {
                return $this->response->redirect(site_url('/'));
            }

        }
    }
}
