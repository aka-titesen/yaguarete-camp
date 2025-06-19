<?php
namespace App\Models;
use CodeIgniter\Model;

class ConsultaModel extends Model
{
    protected $table = 'consultas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['nombre', 'apellido', 'email', 'telefono', 'mensaje', 'respuesta', 'fecha_consulta', 'fecha_respuesta'];

    protected $validationRules = [
        'nombre' => 'required|min_length[2]|max_length[50]',
        'apellido' => 'required|min_length[2]|max_length[50]',
        'email' => 'required|valid_email|max_length[50]',
        'telefono' => 'permit_empty|regex_match[/^[0-9+\-()\s]{7,20}$/]',
        'mensaje' => 'required|min_length[10]'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre es obligatorio',
            'min_length' => 'El nombre debe tener al menos 2 caracteres',
            'max_length' => 'El nombre no puede exceder los 50 caracteres'
        ],
        'apellido' => [
            'required' => 'El apellido es obligatorio',
            'min_length' => 'El apellido debe tener al menos 2 caracteres',
            'max_length' => 'El apellido no puede exceder los 50 caracteres'
        ],
        'email' => [
            'required' => 'El email es obligatorio',
            'valid_email' => 'Debe ingresar un email válido',
            'max_length' => 'El email no puede exceder los 50 caracteres'
        ],
        'telefono' => [
            'regex_match' => 'El teléfono solo puede contener números, espacios y los símbolos + - ( )',
        ],
        'mensaje' => [
            'required' => 'El mensaje es obligatorio',
            'min_length' => 'El mensaje debe tener al menos 10 caracteres'
        ]
    ];
    
    /**
     * Obtiene todas las consultas para el administrador
     */
    public function getConsultas()
    {
        return $this->orderBy('fecha_consulta', 'DESC')->findAll();
    }
    
    /**
     * Obtiene las consultas sin responder
     */
    public function getConsultasSinResponder()
    {
        return $this->where('respuesta', null)->orWhere('respuesta', '')->orderBy('fecha_consulta', 'ASC')->findAll();
    }
    
    /**
     * Obtiene las consultas respondidas
     */
    public function getConsultasRespondidas()
    {
        return $this->where('respuesta !=', null)->where('respuesta !=', '')->orderBy('fecha_respuesta', 'DESC')->findAll();
    }
    
    /**
     * Actualiza la respuesta de una consulta
     */
    public function responderConsulta($id, $respuesta)
    {
        return $this->update($id, [
            'respuesta' => $respuesta,
            'fecha_respuesta' => date('Y-m-d H:i:s')
        ]);
    }
}
