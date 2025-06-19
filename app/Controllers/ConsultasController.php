<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\ConsultaModel;

class ConsultasController extends Controller
{
    protected $consultaModel;

    public function __construct()
    {
        $this->consultaModel = new ConsultaModel();
    }

    /**
     * Muestra el formulario de contacto (opcional, ya está en el footer)
     */
    public function index()
    {
        $data = [
            'titulo' => 'Contacto'
        ];
        
        echo view('front/layouts/header', $data);
        echo view('front/layouts/navbar');
        echo view('front/contacto');
        echo view('front/layouts/footer');
    }
    
    /**
     * Recibe y procesa el formulario de contacto
     */
    public function enviarConsulta()
    {
        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'email' => $this->request->getPost('email'),
            'telefono' => $this->request->getPost('telefono'),
            'mensaje' => $this->request->getPost('mensaje'),
            'fecha_consulta' => date('Y-m-d H:i:s')
        ];
        
        if ($this->consultaModel->save($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Tu consulta ha sido enviada correctamente. Nos pondremos en contacto contigo pronto.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al enviar tu consulta. Por favor, intenta nuevamente.',
                'errors' => $this->consultaModel->errors()
            ]);
        }
    }
    
    /**
     * Panel de administración de consultas - Solo para administradores
     */
    public function administrarConsultas()
    {
        $session = session();
        
        // Verificar si el usuario es administrador
        if (!$session->get('isLoggedIn') || $session->get('perfil_id') != 2) {
            $session->setFlashdata('mensaje', 'Acceso no autorizado - Se requiere permiso de administrador');
            return redirect()->to(base_url('/'));
        }
        
        // Obtener el filtro (todas, respondidas, sin responder)
        $filtro = $this->request->getGet('filtro') ?? 'todas';
        
        if ($filtro == 'respondidas') {
            $consultas = $this->consultaModel->getConsultasRespondidas();
        } elseif ($filtro == 'sin_responder') {
            $consultas = $this->consultaModel->getConsultasSinResponder();
        } else {
            $consultas = $this->consultaModel->getConsultas();
        }
        
        $data = [
            'titulo' => 'Administración de Consultas',
            'consultas' => $consultas,
            'filtro' => $filtro
        ];
        
        echo view('front/layouts/header', $data);
        echo view('front/layouts/navbar');
        echo view('front/admin_consultas', $data);
        echo view('front/layouts/footer');
    }
    
    /**
     * Responder a una consulta - Solo para administradores
     */
    public function responderConsulta()
    {
        $session = session();
        
        // Verificar si el usuario es administrador
        if (!$session->get('isLoggedIn') || $session->get('perfil_id') != 2) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Acceso no autorizado'
            ]);
        }
        
        $id = $this->request->getPost('id');
        $respuesta = $this->request->getPost('respuesta');
        
        if (empty($respuesta)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'La respuesta no puede estar vacía'
            ]);
        }
        
        if ($this->consultaModel->responderConsulta($id, $respuesta)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Respuesta enviada correctamente'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al responder la consulta'
            ]);
        }
    }
}
