# Documentación de Funciones Complejas - Proyecto Martínez González

## Funciones de Validación y Seguridad

### Sistema de Validación de Contraseñas

```php
/**
 * Validación compleja de contraseña en UsuarioCrudController::store()
 * 
 * Regex: /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,32}/
 * 
 * Explicación del patrón:
 * - (?=.*[a-z])        : Al menos una letra minúscula
 * - (?=.*[A-Z])        : Al menos una letra mayúscula  
 * - (?=.*\d)           : Al menos un dígito
 * - (?=.*[^a-zA-Z\d])  : Al menos un carácter especial
 * - .{8,32}            : Longitud total entre 8 y 32 caracteres
 */
'pass' => [
    'label' => 'Contraseña',
    'rules' => 'required|min_length[8]|max_length[32]|regex_match[/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,32}/]'
]
```

### Función de Verificación de Unicidad

```php
/**
 * Verificación manual de unicidad en UsuarioCrudController::store()
 * 
 * Esta función verifica manualmente que el email y usuario sean únicos
 * porque CodeIgniter 4 no tiene regla 'is_unique' por defecto.
 * 
 * @param string $email Email a verificar
 * @param string $usuario Username a verificar
 * @return bool True si existe, False si es único
 */

// Verificar email único
if ($this->userModel->where('email', $email)->first()) {
    // Ya existe, mostrar error
}

// Verificar usuario único (excluyendo el usuario actual en updates)
if ($this->userModel->where('usuario', $usuario)->where('id !=', $id)->first()) {
    // Ya existe, mostrar error  
}
```

## Sistema de Autenticación

### Proceso de Login Seguro

```php
/**
 * LoginController::auth() - Proceso completo de autenticación
 * 
 * Pasos del proceso:
 * 1. Validación de formato de entrada
 * 2. Búsqueda de usuario en BD
 * 3. Verificación de estado del usuario
 * 4. Verificación de contraseña con password_verify()
 * 5. Regeneración de sesión (prevención de fijación)
 * 6. Creación de variables de sesión
 */

// Paso 1: Validaciones de entrada
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    return 'Email inválido';
}

// Paso 4: Verificación segura de contraseña
if(password_verify($pass, $data['pass'])) {
    // Paso 5: Regenerar sesión por seguridad
    $session->regenerate();
    
    // Paso 6: Crear variables de sesión
    $ses_data = [
        'id' => $data['id'],
        'perfil_id' => $data['perfil_id'],
        'isLoggedIn' => TRUE
    ];
}
```

### Filtros de Autorización

```php
/**
 * Auth.php - Filtro para administradores
 * 
 * Verifica dos condiciones en orden:
 * 1. Usuario autenticado (isLoggedIn = true)
 * 2. Perfil de administrador (perfil_id = 2)
 * 
 * Si falla cualquiera, redirige con mensaje específico
 */

public function before(RequestInterface $request, $arguments = null)
{
    $session = session();
    
    // Condición 1: Verificar autenticación
    if (!$session->get('isLoggedIn')) {
        return redirect()->to('/')->with('showLogin', true);
    }
    
    // Condición 2: Verificar permisos de administrador
    if ($session->get('perfil_id') != 2) {
        return redirect()->to('/')->with('msg', 'Sin permisos de admin');
    }
}
```

## Protecciones Especiales

### Prevención de Auto-eliminación

```php
/**
 * UsuarioCrudController::deleteLogico() - Protección de auto-eliminación
 * 
 * Evita que un administrador se elimine a sí mismo, lo que podría
 * dejar el sistema sin administradores.
 * 
 * @param int $id ID del usuario a eliminar
 * @return RedirectResponse Con mensaje de éxito o error
 */

public function deleteLogico($id = null)
{
    $session = session();
    $currentUserId = $session->get('id');
    
    // Verificación de seguridad
    if ($id == $currentUserId) {
        session()->setFlashdata('msg', 'No puedes desactivar tu propio usuario.');
        return redirect()->to('admin_usuarios');
    }
    
    // Proceder con eliminación lógica
    $this->userModel->update($id, ['baja' => 'SI']);
}
```

## Sistema de Carrito

### Manejo de Sesiones del Carrito

```php
/**
 * CarritoController - Gestión de carrito por sesiones
 * 
 * El carrito se maneja completamente por sesiones, permitiendo
 * que usuarios no autenticados también puedan usarlo.
 * 
 * Estructura de datos en sesión:
 * $_SESSION['carrito'] = [
 *     'producto_id_1' => [
 *         'id' => 1,
 *         'nombre' => 'Producto A',
 *         'precio' => 100.00,
 *         'cantidad' => 2,
 *         'subtotal' => 200.00
 *     ],
 *     'producto_id_2' => [...]
 * ];
 */

// Agregar producto
public function add() {
    $session = session();
    $carrito = $session->get('carrito') ?: [];
    
    // Verificar si ya existe
    if (isset($carrito[$productoId])) {
        $carrito[$productoId]['cantidad']++;
    } else {
        $carrito[$productoId] = $datosProducto;
    }
    
    $session->set('carrito', $carrito);
}
```

## Validaciones Complejas

### Validación de Formularios con Verificación Manual

```php
/**
 * UsuarioCrudController::update() - Validación con exclusión
 * 
 * Al actualizar un usuario, se debe verificar que email y username
 * sean únicos, pero excluyendo el registro actual.
 */

// Validación estándar de CodeIgniter
$input = $this->validate([
    'email' => 'required|valid_email',
    'usuario' => 'required|min_length[3]'
]);

// Validación manual con exclusión
$usuarioExistente = $this->userModel
    ->where('email', $email)
    ->where('id !=', $id)  // Excluir el usuario actual
    ->first();

if ($usuarioExistente) {
    return 'Email ya registrado por otro usuario';
}
```

## Funciones de Vista Condicional

### Renderizado Condicional en Vistas

```php
/**
 * admin_usuarios.php - Lógica condicional compleja
 * 
 * Muestra diferentes botones según el estado del usuario y
 * si es el usuario actual logueado.
 */

<!-- Condiciones anidadas para mostrar botones correctos -->
<?php if (isset($user['baja']) && $user['baja'] === 'SI'): ?>
    <!-- Usuario desactivado: mostrar botón reactivar -->
    <a href="<?= base_url('activar/'.$user['id']);?>" class="btn btn-success">
        <i class="fas fa-undo"></i>
    </a>
<?php else: ?>
    <!-- Usuario activo: mostrar botones editar y eliminar -->
    <?php if ($user['id'] != session()->get('id')): ?>
        <!-- NO es el usuario actual: permitir eliminación -->
        <a href="<?= base_url('deletelogico/'.$user['id']);?>" class="btn btn-danger">
            <i class="fas fa-trash-alt"></i>
        </a>
    <?php else: ?>
        <!-- ES el usuario actual: botón deshabilitado -->
        <button class="btn btn-secondary" disabled>
            <i class="fas fa-ban"></i>
        </button>
    <?php endif; ?>
<?php endif; ?>
```

## Funciones de Seguridad Avanzada

### Hash y Verificación de Contraseñas

```php
/**
 * Uso de password_hash() y password_verify() para seguridad máxima
 */

// Al crear usuario - en UsuarioCrudController::store()
$newUser = [
    'pass' => password_hash($this->request->getVar('pass'), PASSWORD_DEFAULT)
];

// Al verificar login - en LoginController::auth()
if(password_verify($pass, $data['pass'])) {
    // Contraseña correcta
}

/**
 * PASSWORD_DEFAULT usa bcrypt con cost automático, considerado
 * el estándar de seguridad actual para hash de contraseñas.
 */
```

### Regeneración de Sesiones

```php
/**
 * LoginController::auth() - Prevención de fijación de sesión
 * 
 * La regeneración de sesión previene ataques de fijación donde
 * un atacante podría predeterminar el ID de sesión.
 */

if(password_verify($pass, $data['pass'])) {
    // IMPORTANTE: Regenerar antes de establecer datos sensibles
    $session->regenerate();
    
    // Ahora es seguro establecer datos de autenticación
    $session->set($ses_data);
}
```

## Patrones de Diseño Utilizados

### MVC (Model-View-Controller)
- **Models**: Lógica de datos y validaciones
- **Views**: Presentación y interfaz de usuario
- **Controllers**: Lógica de negocio y flujo de aplicación

### Repository Pattern (Implícito)
- Los modelos actúan como repositorios para cada entidad
- Abstracción de la lógica de base de datos

### Filter Pattern
- Filtros de autenticación y autorización
- Interceptan requests antes de llegar a controladores

## Convenciones de Nomenclatura

### Base de Datos
- Tablas: snake_case plural (`ventas_cabecera`, `usuarios`)
- Campos: snake_case (`perfil_id`, `fecha_creacion`)
- Claves primarias: `id` (estandarizado)

### PHP
- Clases: PascalCase (`UsuarioCrudController`)
- Métodos: camelCase (`deleteLogico()`)
- Variables: camelCase (`$currentUserId`)
- Constantes: UPPER_SNAKE_CASE (`PASSWORD_DEFAULT`)

### Frontend
- CSS Classes: kebab-case (`btn-verde-selva`)
- IDs: camelCase (`modalAgregarUsuario`)
- Archivos: snake_case (`admin_usuarios.php`)
