<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\Usuarios_model;

class login_controller extends Controller{
    public function index(){
        // Redirigir siempre a la página principal para mantener el layout y estilos
        return redirect()->to('/');
    }
    public function auth(){
        $session = session();
        $model = new Usuarios_model();

        $email = $this->request->getVar('email');
        $pass = $this->request->getVar('pass');

        $data = $model->where('email', $email)->first();
        if($data){
            if(isset($data['baja']) && $data['baja'] == 'SI'){
                $session->setFlashdata('msg', 'Usuario dado de baja');
                return redirect()->to('/');
            }
            if(password_verify($pass, $data['pass'])){
                $ses_data = [
                    'id' => $data['id'],
                    'nombre' => $data['nombre'],
                    'apellido' => $data['apellido'],
                    'email' => $data['email'],
                    'usuario' => $data['usuario'],
                    'perfil_id' => $data['perfil_id'],
                    'isLoggedIn' => TRUE
                ];
                $session->set($ses_data);
                if($data['perfil_id'] == 1){
                    return redirect()->to('/dashboard');
                } else {
                    return redirect()->to('/');
                }
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