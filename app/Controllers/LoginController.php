<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\UsuariosModel;

class LoginController extends Controller{    
    protected $model;
    public function __construct()
    {
        $this->model = new UsuariosModel();
    }
    public function index(){
        // Redirigir siempre a la página principal para mantener el layout y estilos
        return redirect()->to('/');
    }
    public function auth(){
        $session = session();
        $email = $this->request->getVar('email');
        $pass = $this->request->getVar('pass');

        // Validar formato de email y longitud de contraseña antes de consultar la base de datos
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
            $session->setFlashdata('msg', 'Email inválido.');
            return redirect()->to('/');
        }
        if (strlen($pass) < 3 || strlen($pass) > 32) {
            $session->setFlashdata('msg', 'Contraseña inválida.');
            return redirect()->to('/');
        }

        $data = $this->model->where('email', $email)->first();
        if($data){
            if(isset($data['baja']) && $data['baja'] == 'SI'){
                $session->setFlashdata('msg', 'Usuario dado de baja');
                return redirect()->to('/');
            }
            if(password_verify($pass, $data['pass'])){                
                // Regenerar la sesión para evitar fijación de sesión
                $session->regenerate();
                $ses_data = [
                    'id' => $data['id'], // Usar clave primaria estándar
                    'nombre' => $data['nombre'],
                    'apellido' => $data['apellido'],
                    'email' => $data['email'],
                    'usuario' => $data['usuario'],
                    'perfil_id' => $data['perfil_id'],
                    'isLoggedIn' => TRUE];
                $session->set($ses_data);
                // Según la BD: 1=Cliente, 2=Administrador, 3=Vendedor
                // Ya no redirigimos a dashboard, todos van al sitio principal
                return redirect()->to('/');
            } else {
                $session->setFlashdata('msg', 'Contraseña incorrecta');
                return redirect()->to('/');
            }
        } else {
            $session->setFlashdata('msg', 'Email incorrecto');
            return redirect()->to('/');
        }
    }
    public function logout(){
        $session = session(); //Iniciamos el objeto session()
        $session->destroy(); //Destruimos la sesión
        return redirect()->to('/'); //Redirigimos al login
    }
}