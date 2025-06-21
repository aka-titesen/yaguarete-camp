<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UsuariosModel;

/**
 * CONTROLADOR DE AUTENTICACIÓN
 * 
 * Maneja el proceso de login y logout de usuarios en el sistema.
 * Incluye validaciones de seguridad y manejo de sesiones.
 * 
 * Funcionalidades principales:
 * - Validación de credenciales de usuario
 * - Creación y destrucción de sesiones
 * - Verificación de estado de usuarios (activos/dados de baja)
 * - Protección contra ataques de fijación de sesión
 * 
 * Perfiles de usuario soportados:
 * - 1: Cliente
 * - 2: Administrador  
 * - 3: Vendedor
 * 
 * @author Proyecto Martínez González
 * @version 1.0
 */
class LoginController extends Controller
{    
    /**
     * Modelo de usuarios para consultas de autenticación
     * @var UsuariosModel
     */
    protected $model;
    
    /**
     * Constructor del controlador
     * 
     * Inicializa el modelo de usuarios para las operaciones de autenticación
     */
    public function __construct()
    {
        $this->model = new UsuariosModel();
    }
    
    /**
     * Mostrar formulario de login
     * 
     * Redirige a la página principal donde se encuentra el modal de login.
     * Esta redirección mantiene el layout y estilos consistentes.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a página principal
     */
    public function index(){
        // Redirigir siempre a la página principal para mantener el layout y estilos
        return redirect()->to('/');
    }
    
    /**
     * Procesar autenticación de usuario
     * 
     * Valida las credenciales del usuario y crea la sesión si es exitoso.
     * 
     * Proceso de autenticación:
     * 1. Valida formato de email y longitud de contraseña
     * 2. Busca el usuario por email en la base de datos
     * 3. Verifica que el usuario no esté dado de baja
     * 4. Compara la contraseña usando password_verify()
     * 5. Crea la sesión con datos del usuario
     * 6. Regenera ID de sesión por seguridad
     * 
     * Validaciones de seguridad:
     * - Email debe tener formato válido y máximo 100 caracteres
     * - Contraseña debe tener entre 3-32 caracteres
     * - Usuario no debe estar dado de baja (campo 'baja' != 'SI')
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección con resultado
     */
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

        // Buscar usuario por email
        $data = $this->model->where('email', $email)->first();
        
        if($data){
            // Verificar que el usuario no esté dado de baja
            if(isset($data['baja']) && $data['baja'] == 'SI'){
                $session->setFlashdata('msg', 'Usuario dado de baja');
                return redirect()->to('/');
            }
            
            // Verificar contraseña
            if(password_verify($pass, $data['pass'])){                
                // Regenerar la sesión para evitar fijación de sesión
                $session->regenerate();
                
                // Crear array de datos de sesión
                $ses_data = [
                    'id' => $data['id'],               // ID único del usuario
                    'nombre' => $data['nombre'],       // Nombre del usuario
                    'apellido' => $data['apellido'],   // Apellido del usuario
                    'email' => $data['email'],         // Email del usuario
                    'usuario' => $data['usuario'],     // Username del usuario
                    'perfil_id' => $data['perfil_id'], // Tipo de perfil (1=Cliente, 2=Admin, 3=Vendedor)
                    'isLoggedIn' => TRUE               // Flag de autenticación
                ];
                
                // Establecer datos en la sesión
                $session->set($ses_data);
                
                // Redirigir a página principal (todos los perfiles van al mismo lugar)
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
    
    /**
     * Cerrar sesión de usuario
     * 
     * Destruye completamente la sesión del usuario y redirige
     * a la página principal.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a página principal
     */
    public function logout(){
        $session = session();
        $session->destroy(); // Destruir completamente la sesión
        return redirect()->to('/'); // Redirigir a página principal
    }
}