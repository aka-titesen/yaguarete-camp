<?php
namespace App\Controllers;
use App\Models\UsuariosModel;
use CodeIgniter\Controller;

class UsuarioController extends Controller
{
    protected $formModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->formModel = new UsuariosModel();
    }

    public function formValidation()
    {

        $input = $this->validate([
            'nombre' => 'required|min_length[3]',
            'apellido' => 'required|min_length[3]|max_length[25]',
            'email' => 'required|min_length[4]|max_length[100]|valid_email',
            'usuario' => 'required|min_length[3]',
            'pass' => [
                'label' => 'Contraseña',
                'rules' => 'required|min_length[8]|max_length[32]|regex_match[/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,32}/]'
            ]
        ], [
            'pass' => [
                'regex_match' => 'La contraseña debe tener entre 8 y 32 caracteres, al menos una mayúscula, una minúscula, un número y un símbolo.'
            ]
        ]);
        // Verificación manual de unicidad de email y usuario
        $email = $this->request->getVar('email');
        $usuario = $this->request->getVar('usuario');
        if ($this->formModel->where('email', $email)->first()) {
            session()->setFlashdata('validation', ['email' => 'El email ya está registrado.']);
            return redirect()->to('/');
        }
        if ($this->formModel->where('usuario', $usuario)->first()) {
            session()->setFlashdata('validation', ['usuario' => 'El nombre de usuario ya está registrado.']);
            return redirect()->to('/');
        }
        if (!$input) {
            // Si la validación falla, redirige a la página principal y muestra los errores en la sesión
            session()->setFlashdata('validation', $this->validator);
            return redirect()->to('/');
        } else {

            $perfil_id = $this->request->getVar('perfil_id') ?? 1; // Por defecto: 1=Cliente según BD
            $this->formModel->save([
                'nombre' => $this->request->getVar('nombre'),
                'apellido' => $this->request->getVar('apellido'),
                'usuario' => $this->request->getVar('usuario'),
                'email' => $this->request->getVar('email'),
                'pass' => password_hash($this->request->getVar('pass'), PASSWORD_DEFAULT),
                'perfil_id' => $perfil_id
            ]);            session()->setFlashdata('success', 'Usuario registrado con exito');
            if ($perfil_id == 1) { // Si es administrador según BD (perfil_id = 1)
                // Si es administrador, iniciar sesión y redirigir al dashboard
                $session = session();
                $usuario = $this->formModel->where('email', $this->request->getVar('email'))->first();                $session->set([
                    'id' => $usuario['id'], // Usar clave primaria estándar
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
