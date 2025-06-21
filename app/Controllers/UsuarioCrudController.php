<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use App\Models\ConsultaModel;
use CodeIgniter\Controller;

class UsuarioCrudController extends Controller
{
    /**
     * Modelo de usuarios para operaciones de base de datos
     */
    protected $userModel;

    /**
     * Constructor del controlador
     * Inicializa los helpers necesarios y el modelo de usuarios
     */
    public function __construct(){
        helper(['url', 'form']);
        $this->userModel = new UsuariosModel();
    }

    /**
     * Mostrar lista de usuarios en el panel de administración
     * Obtiene todos los usuarios ordenados por ID descendente y
     * los pasa a la vista de administración de usuarios.
     */
    public function index(){
        $data['users'] = $this->userModel->orderBy('id', 'DESC')->findAll();
        $data['titulo'] = 'Crud_usuarios';

        echo view('front/layouts/header');
        echo view('front/layouts/navbar');
        echo view('front/admin_usuarios', $data);
        echo view('front/layouts/footer');           
    }

    /**
     * Formulario para alta de usuario (ACTUALMENTE NO UTILIZADO)
     * Muestra el formulario para crear un nuevo usuario.
     * Esta función parece estar obsoleta ya que el formulario
     * se maneja via modal en la vista principal.
     */
    public function create(){
        $data['user_obj'] = $this->userModel->orderBy('id', 'DESC')->findAll();
        $data['titulo'] = 'Alta Usuario';

        echo view('front/head_view_crud', $data);
        echo view('front/nav_view');
        echo view('back/usuario/usuario_crud_view', $data);
        echo view('front/footer_view');
    }

    /**
     * Validar y almacenar nuevo usuario en la base de datos
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        
        // Definir reglas de validación
        $input = $this->validate([
            'nombre'  => 'required|min_length[3]|max_length[25]',
            'apellido'=> 'required|min_length[3]|max_length[25]',
            'email'   => 'required|min_length[4]|max_length[100]|valid_email',
            'usuario' => 'required|min_length[3]|max_length[10]',
            'pass'    => [
                'label' => 'Contraseña',
                'rules' => 'required|min_length[8]|max_length[32]|regex_match[/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,32}/]'
            ]        ], [
            'pass' => [
                'regex_match' => 'La contraseña debe tener entre 8 y 32 caracteres, al menos una mayúscula, una minúscula, un número y un símbolo.'
            ]
        ]);
        
        // Verificación manual de unicidad de email y usuario
        $email = $this->request->getVar('email');
        $usuario = $this->request->getVar('usuario');
        
        // Validar que el email no esté registrado
        if ($this->userModel->where('email', $email)->first()) {
            $data['validation'] = ['email' => 'El email ya está registrado.'];
            $data['open_modal'] = true;
            echo view('front/layouts/header');
            echo view('front/layouts/navbar');
            echo view('front/admin_usuarios', $data);
            echo view('front/layouts/footer');
            return;
        }
        
        // Validar que el usuario no esté registrado
        if ($this->userModel->where('usuario', $usuario)->first()) {
            $data['validation'] = ['usuario' => 'El nombre de usuario ya está registrado.'];
            $data['open_modal'] = true;
            echo view('front/layouts/header');
            echo view('front/layouts/navbar');
            echo view('front/admin_usuarios', $data);
            echo view('front/layouts/footer');
            return;
        }
        
        $data['users'] = $this->userModel->orderBy('id', 'DESC')->findAll();
        
        // Si hay errores de validación, mostrar formulario con errores
        if (!$input) {
            $data['validation'] = $validation;
            $data['open_modal'] = true;
            echo view('front/layouts/header');
            echo view('front/layouts/navbar');
            echo view('front/admin_usuarios', $data);
            echo view('front/layouts/footer');
        } else {
            // Crear array con datos del nuevo usuario
            $newUser = [
                'nombre'  => $this->request->getVar('nombre'),
                'apellido'=> $this->request->getVar('apellido'),
                'usuario' => $this->request->getVar('usuario'),
                'email'   => $this->request->getVar('email'),
                'pass'    => password_hash($this->request->getVar('pass'), PASSWORD_DEFAULT), // Encriptar contraseña
                'perfil_id' => $this->request->getVar('perfil_id'),
            ];
            
            // Insertar usuario en la base de datos
            $this->userModel->insert($newUser);
            
            // Mensaje de éxito y redirección
            session()->setFlashdata('msg', '<div class="alert alert-success">Usuario agregado correctamente.</div>');
            return redirect()->to('admin_usuarios');
        }
    }

    /**
     * Obtener un usuario específico para edición (ACTUALMENTE NO UTILIZADO)
     * Busca un usuario por su ID y lo pasa a la vista de edición.
     * Esta función parece estar obsoleta ya que la edición
     * se maneja via modal en la vista principal.
     */
    // mostrar un usuario por id para editarlo
    public function singleUser($id = null){
        $data['user_obj'] = $this->userModel->where('id', $id)->first();

        $data['titulo'] = 'Crud_usuarios';
        echo view('front/head_view_crud', $data); // Encabezado
        echo view('front/nav_view');              // Navbar
        echo view('back/usuario/edit_usuarios_view', $data); // Vista de edición de usuario
        echo view('front/footer_view');           // Footer
    }

    /**
     * Actualizar datos de un usuario existente
     */
    // editar y modificar un usuario
    public function update()
    {
        $id = $this->request->getPost('id');
        $input = $this->validate([
            'nombre'   => 'required|min_length[3]|max_length[25]',
            'apellido' => 'required|min_length[3]|max_length[25]',
            'usuario'  => 'required|min_length[3]|max_length[10]',
            'email'    => 'required|min_length[4]|max_length[100]|valid_email',
            'perfil_id'=> 'required|in_list[1,2,3]'
        ], [
            'email' => [
                'valid_email' => 'Debe ingresar un email válido.'
            ]
        ]);
        $email = $this->request->getPost('email');
        $usuario = $this->request->getPost('usuario');
        $usuarioExistente = $this->userModel->where('email', $email)->where('id !=', $id)->first();
        if ($usuarioExistente) {
            session()->setFlashdata('validation', ['email' => 'El email ya está registrado por otro usuario.']);
            session()->setFlashdata('edit_data', $this->request->getPost());
            return redirect()->to('admin_usuarios?edit_id=' . $id);
        }
        $usuarioExistente = $this->userModel->where('usuario', $usuario)->where('id !=', $id)->first();
        if ($usuarioExistente) {
            session()->setFlashdata('validation', ['usuario' => 'El nombre de usuario ya está registrado por otro usuario.']);
            session()->setFlashdata('edit_data', $this->request->getPost());
            return redirect()->to('admin_usuarios?edit_id=' . $id);
        }
        if (!$input) {
            session()->setFlashdata('validation', $this->validator);
            session()->setFlashdata('edit_data', $this->request->getPost());
            return redirect()->to('admin_usuarios?edit_id=' . $id);
        }
        $data = [
            'nombre'   => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'usuario'  => $this->request->getPost('usuario'),
            'email'    => $this->request->getPost('email'),
            'perfil_id'=> $this->request->getPost('perfil_id'),
        ];
        $this->userModel->update($id, $data);
        return redirect()->to('admin_usuarios');
    }    /**
     * Desactivar usuario (eliminación lógica)
     * Cambia el estado del campo 'baja' a 'SI' sin eliminar físicamente
     * el registro de la base de datos. Incluye protección para evitar
     * que un administrador se desactive a sí mismo.
     */
    // delete lógico (cambia el estado del campo baja)
    public function deleteLogico($id = null)
    {
        // Verificar que no sea el mismo usuario logueado
        $session = session();
        $currentUserId = $session->get('id');
        
        if ($id == $currentUserId) {
            session()->setFlashdata('msg', '<div class="alert alert-warning">No puedes desactivar tu propio usuario.</div>');
            return redirect()->to('admin_usuarios');
        }
        
        $this->userModel->update($id, ['baja' => 'SI']);
        session()->setFlashdata('msg', '<div class="alert alert-success">Usuario desactivado correctamente.</div>');
        return redirect()->to('admin_usuarios');
    }

    /**
     * Activar usuario previamente desactivado
     * Cambia el estado del campo 'baja' a 'NO', reactivando
     * el usuario en el sistema.
     */
    // activar usuario (cambia el estado del campo baja a NO)
    public function activar($id = null)
    {
        $this->userModel->update($id, ['baja' => 'NO']);
        return redirect()->to('admin_usuarios');
    }
}