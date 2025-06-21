<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * MODELO DE USUARIOS
 * 
 * Maneja las operaciones de base de datos para la tabla 'usuarios'.
 * Este modelo gestiona toda la información de los usuarios del sistema,
 * incluyendo datos personales, credenciales y perfiles de acceso.
 * 
 * Estructura de la tabla usuarios:
 * - id: Clave primaria auto-incremental
 * - nombre: Nombre del usuario (3-25 caracteres)
 * - apellido: Apellido del usuario (3-25 caracteres)
 * - usuario: Nombre de usuario único (3-10 caracteres)
 * - email: Correo electrónico único
 * - pass: Contraseña encriptada con password_hash()
 * - perfil_id: Referencia al tipo de perfil (1=Cliente, 2=Admin, 3=Vendedor)
 * - baja: Estado del usuario ('SI'=Desactivado, 'NO'=Activo)
 * 
 * @author Proyecto Martínez González
 * @version 1.0
 */
class UsuariosModel extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     * @var string
     */
    protected $table = 'usuarios';
    
    /**
     * Clave primaria de la tabla
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Campos que pueden ser insertados/actualizados de forma masiva
     * 
     * Campos permitidos:
     * - nombre: Nombre del usuario
     * - apellido: Apellido del usuario
     * - usuario: Username único
     * - email: Correo electrónico único
     * - pass: Contraseña (se debe encriptar antes de insertar)
     * - perfil_id: ID del perfil de usuario
     * - baja: Estado del usuario (SI/NO)
     * 
     * @var array
     */
    protected $allowedFields = ['nombre', 'apellido', 'usuario', 'email', 'pass', 'perfil_id', 'baja'];
    
    /**
     * Habilitar timestamps automáticos
     * @var bool
     */
    protected $useTimestamps = false;
    
    /**
     * Tipo de retorno para las consultas
     * @var string
     */
    protected $returnType = 'array';
}
?>