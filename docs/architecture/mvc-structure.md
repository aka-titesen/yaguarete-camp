# 🏛️ Estructura MVC - Yagaruete Camp

## 📋 Descripción General

Yagaruete Camp implementa el patrón **Model-View-Controller (MVC)** siguiendo las convenciones de CodeIgniter 4. Esta arquitectura garantiza separación de responsabilidades, mantenibilidad y escalabilidad del código.

## 🏗️ Arquitectura MVC

```
┌─────────────────────────────────────────────────────────────────┐
│                        REQUEST FLOW                             │
└─────────────────┬───────────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                     ROUTER                                      │
│                 Routes.php                                      │
│               • URL Parsing                                     │
│               • Route Matching                                  │
│               • Controller Resolution                           │
└─────────────────┬───────────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                   FILTERS                                       │
│                 • Authentication                                │
│                 • Authorization                                 │
│                 • CORS                                          │
│                 • Rate Limiting                                 │
└─────────────────┬───────────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                 CONTROLLERS                                     │
│               • Request Handling                                │
│               • Business Logic Coordination                     │
│               • Response Generation                             │
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐             │
│  │    Auth     │  │ Products    │  │   Sales     │             │
│  │Controllers  │  │Controllers  │  │Controllers  │             │
│  └─────────────┘  └─────────────┘  └─────────────┘             │
└─────────────────┬─────────────────────────────────┬─────────────┘
                  │                                 │
                  ▼                                 ▼
┌─────────────────────────────────────┐ ┌─────────────────────────┐
│               MODELS                │ │         VIEWS           │
│            • Data Access            │ │      • Presentation     │
│            • Business Rules         │ │      • Templates        │
│            • Validation             │ │      • UI Logic         │
│                                     │ │                         │
│  ┌─────────────┐ ┌─────────────┐   │ │  ┌─────────────┐       │
│  │   Usuario   │ │  Producto   │   │ │  │  Frontend   │       │
│  │   Model     │ │   Model     │   │ │  │   Views     │       │
│  └─────────────┘ └─────────────┘   │ │  └─────────────┘       │
│                                     │ │                         │
│  ┌─────────────┐ ┌─────────────┐   │ │  ┌─────────────┐       │
│  │   Ventas    │ │ Categories  │   │ │  │  Backend    │       │
│  │   Model     │ │   Model     │   │ │  │   Views     │       │
│  └─────────────┘ └─────────────┘   │ │  └─────────────┘       │
└─────────────────┬───────────────────┘ └─────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DATABASE                                   │
│                    MySQL 8.0                                    │
│                 • Data Persistence                              │
│                 • ACID Transactions                             │
│                 • Referential Integrity                         │
└─────────────────────────────────────────────────────────────────┘
```

## 🎯 Controllers (Controladores)

Los controladores coordinan la lógica de la aplicación y actúan como intermediarios entre Models y Views.

### 📁 Estructura de Controladores

```
app/Controllers/
├── BaseController.php           # Controlador base común
├── Home.php                     # Página principal
├── LoginController.php          # Autenticación y sesiones
├── UsuarioCrudController.php    # CRUD de usuarios
├── ProductoController.php       # Gestión de productos
├── CarritoController.php        # Carrito de compras
├── VentasController.php         # Gestión de ventas
├── ConsultasController.php      # Formulario de contacto
└── API/                         # Endpoints API REST
    ├── AuthController.php       # API de autenticación
    ├── ProductsController.php   # API de productos
    └── SalesController.php      # API de ventas
```

### 🏛️ BaseController

**Archivo**: `app/Controllers/BaseController.php`

```php
<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     */
    protected $request;

    /**
     * Helpers que se cargan automáticamente
     */
    protected $helpers = ['url', 'form', 'security'];

    /**
     * Constructor base
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Validar sesión en todos los controladores
        $this->validateSession();

        // Cargar datos comunes para todas las vistas
        $this->loadCommonData();
    }

    /**
     * Validar sesión del usuario
     */
    protected function validateSession()
    {
        $session = session();

        // Regenerar ID de sesión por seguridad
        if (!$session->has('session_regenerated')) {
            $session->regenerate();
            $session->set('session_regenerated', true);
        }

        // Log de acceso
        log_message('info', 'Access: ' . $this->request->getPath() . ' from ' . $this->request->getIPAddress());
    }

    /**
     * Cargar datos comunes para vistas
     */
    protected function loadCommonData()
    {
        $session = session();

        $this->data = [
            'usuario_logueado' => $session->get('usuario_logueado'),
            'perfil_id' => $session->get('perfil_id'),
            'usuario_nombre' => $session->get('usuario_nombre'),
            'carrito_count' => $this->getCarritoCount(),
            'current_url' => current_url()
        ];
    }

    /**
     * Obtener cantidad de items en carrito
     */
    protected function getCarritoCount()
    {
        $session = session();
        $carrito = $session->get('carrito') ?? [];

        return array_sum(array_column($carrito, 'cantidad'));
    }

    /**
     * Verificar si usuario está autenticado
     */
    protected function isLoggedIn()
    {
        return session()->get('usuario_logueado') ?? false;
    }

    /**
     * Verificar si usuario es administrador
     */
    protected function isAdmin()
    {
        return session()->get('perfil_id') == 1;
    }

    /**
     * Redirigir con mensaje
     */
    protected function redirectWithMessage($url, $message, $type = 'success')
    {
        session()->setFlashdata($type, $message);
        return redirect()->to($url);
    }
}
```

### 🔐 LoginController

**Archivo**: `app/Controllers/LoginController.php`

**Responsabilidades**:

- Autenticación de usuarios
- Gestión de sesiones
- Logout seguro
- Validación de credenciales

```php
class LoginController extends BaseController
{
    protected $usuariosModel;

    public function __construct()
    {
        $this->usuariosModel = new UsuariosModel();
    }

    /**
     * Mostrar formulario de login
     */
    public function index()
    {
        // Si ya está logueado, redirigir
        if ($this->isLoggedIn()) {
            return $this->redirectBasedOnRole();
        }

        return view('front/login', $this->data);
    }

    /**
     * Procesar login
     */
    public function authenticate()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return view('front/login', [
                'validation' => $validation,
                'input' => $this->request->getPost()
            ]);
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->usuariosModel->validateUser($email, $password);

        if ($user) {
            $this->setUserSession($user);
            return $this->redirectBasedOnRole();
        } else {
            return $this->redirectWithMessage('login', 'Credenciales inválidas', 'error');
        }
    }

    /**
     * Establecer sesión de usuario
     */
    private function setUserSession($user)
    {
        $session = session();
        $session->set([
            'usuario_logueado' => true,
            'usuario_id' => $user->id,
            'usuario_nombre' => $user->nombre,
            'usuario_email' => $user->email,
            'perfil_id' => $user->perfil_id
        ]);

        // Log del login exitoso
        log_message('info', "User login: {$user->email} (ID: {$user->id})");
    }

    /**
     * Redirigir según rol
     */
    private function redirectBasedOnRole()
    {
        $perfilId = session()->get('perfil_id');

        return match($perfilId) {
            1 => redirect()->to('admin/dashboard'),  // Admin
            2 => redirect()->to('/'),                // Cliente
            default => redirect()->to('login')
        };
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        $session = session();

        // Log del logout
        $email = $session->get('usuario_email');
        log_message('info', "User logout: {$email}");

        // Destruir sesión
        $session->destroy();

        return $this->redirectWithMessage('/', 'Sesión cerrada correctamente');
    }
}
```

### 🛍️ ProductoController

**Archivo**: `app/Controllers/ProductoController.php`

**Responsabilidades**:

- CRUD de productos
- Búsqueda y filtrado
- Gestión de imágenes
- Validación de stock

```php
class ProductoController extends BaseController
{
    protected $productoModel;
    protected $categoriasModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->categoriasModel = new CategoriasModel();
    }

    /**
     * Listar productos (Frontend)
     */
    public function index()
    {
        $productos = $this->productoModel->getProductosActivos();
        $categorias = $this->categoriasModel->getCategoriasActivas();

        $this->data['productos'] = $productos;
        $this->data['categorias'] = $categorias;

        return view('front/productos/index', $this->data);
    }

    /**
     * Ver detalle de producto
     */
    public function detalle($id)
    {
        $producto = $this->productoModel->getProductoCompleto($id);

        if (!$producto) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Producto no encontrado');
        }

        $this->data['producto'] = $producto;
        $this->data['relacionados'] = $this->productoModel->getProductosRelacionados($producto->categoria_id, $id);

        return view('front/productos/detalle', $this->data);
    }

    /**
     * Gestión de productos (Backend)
     */
    public function admin()
    {
        // Solo administradores
        if (!$this->isAdmin()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $productos = $this->productoModel->getProductosConCategoria();
        $this->data['productos'] = $productos;

        return view('back/productos/index', $this->data);
    }

    /**
     * Crear producto
     */
    public function crear()
    {
        if (!$this->isAdmin()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->procesarFormulario();
        }

        $this->data['categorias'] = $this->categoriasModel->findAll();
        return view('back/productos/crear', $this->data);
    }

    /**
     * Procesar formulario de producto
     */
    private function procesarFormulario($id = null)
    {
        $validation = $this->getValidationRules();

        if (!$validation->withRequest($this->request)->run()) {
            $this->data['validation'] = $validation;
            $this->data['categorias'] = $this->categoriasModel->findAll();
            $view = $id ? 'back/productos/editar' : 'back/productos/crear';
            return view($view, $this->data);
        }

        $data = $this->prepareProductData();

        // Procesar imagen si se subió
        $imagen = $this->request->getFile('imagen');
        if ($imagen && $imagen->isValid()) {
            $data['imagen'] = $this->processImage($imagen);
        }

        try {
            if ($id) {
                $this->productoModel->update($id, $data);
                $message = 'Producto actualizado correctamente';
            } else {
                $this->productoModel->insert($data);
                $message = 'Producto creado correctamente';
            }

            return $this->redirectWithMessage('admin/productos', $message);
        } catch (\Exception $e) {
            log_message('error', 'Error saving product: ' . $e->getMessage());
            return $this->redirectWithMessage('admin/productos', 'Error al guardar producto', 'error');
        }
    }

    /**
     * Reglas de validación
     */
    private function getValidationRules()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre' => 'required|min_length[3]|max_length[255]',
            'descripcion' => 'required|min_length[10]',
            'precio' => 'required|decimal|greater_than[0]',
            'stock' => 'required|integer|greater_than_equal_to[0]',
            'categoria_id' => 'required|integer',
            'imagen' => 'if_exist|uploaded[imagen]|max_size[imagen,2048]|is_image[imagen]'
        ]);

        return $validation;
    }

    /**
     * Preparar datos del producto
     */
    private function prepareProductData()
    {
        return [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'precio' => $this->request->getPost('precio'),
            'stock' => $this->request->getPost('stock'),
            'categoria_id' => $this->request->getPost('categoria_id'),
            'activo' => $this->request->getPost('activo') ? 1 : 0
        ];
    }

    /**
     * Procesar imagen subida
     */
    private function processImage($imagen)
    {
        $nombreArchivo = $imagen->getRandomName();
        $imagen->move(WRITEPATH . 'uploads/productos', $nombreArchivo);

        // Redimensionar imagen si es necesario
        $this->resizeImage(WRITEPATH . 'uploads/productos/' . $nombreArchivo);

        return $nombreArchivo;
    }
}
```

## 🗄️ Models (Modelos)

Los modelos manejan la lógica de datos y las reglas de negocio.

### 📁 Estructura de Modelos

```
app/Models/
├── BaseModel.php               # Modelo base común
├── UsuariosModel.php          # Gestión de usuarios
├── ProductoModel.php          # Gestión de productos
├── CategoriasModel.php        # Gestión de categorías
├── VentasCabeceraModel.php    # Cabeceras de ventas
├── VentasDetalleModel.php     # Detalles de ventas
└── ConsultasModel.php         # Consultas de contacto
```

### 🏛️ BaseModel

**Archivo**: `app/Models/BaseModel.php`

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Validación común
     */
    protected $validationRules = [];
    protected $validationMessages = [];

    /**
     * Obtener registros activos
     */
    public function getActive()
    {
        return $this->where('activo', 1)->findAll();
    }

    /**
     * Activar/Desactivar registro
     */
    public function toggleActive($id)
    {
        $record = $this->find($id);
        if (!$record) {
            throw new \Exception('Registro no encontrado');
        }

        return $this->update($id, ['activo' => !$record->activo]);
    }

    /**
     * Búsqueda paginada
     */
    public function getPaginated($perPage = 20, $conditions = [])
    {
        $builder = $this->builder();

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $builder->whereIn($field, $value);
            } else {
                $builder->where($field, $value);
            }
        }

        return $builder->paginate($perPage);
    }

    /**
     * Logging de operaciones
     */
    protected function logOperation($operation, $id = null, $data = null)
    {
        $message = sprintf(
            'Model %s: %s %s %s',
            get_class($this),
            $operation,
            $id ? "ID: $id" : '',
            $data ? json_encode($data) : ''
        );

        log_message('info', $message);
    }

    /**
     * Eventos del modelo
     */
    protected function beforeInsert(array $data)
    {
        $this->logOperation('INSERT', null, $data['data'] ?? null);
        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        $this->logOperation('UPDATE', $data['id'][0] ?? null, $data['data'] ?? null);
        return $data;
    }

    protected function beforeDelete(array $data)
    {
        $this->logOperation('DELETE', $data['id'][0] ?? null);
        return $data;
    }
}
```

### 👥 UsuariosModel

**Archivo**: `app/Models/UsuariosModel.php`

```php
class UsuariosModel extends BaseModel
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $allowedFields = ['usuario', 'nombre', 'email', 'password', 'perfil_id', 'activo'];

    protected $validationRules = [
        'usuario' => 'required|min_length[3]|max_length[50]|is_unique[usuarios.usuario,id,{id}]',
        'nombre' => 'required|min_length[3]|max_length[255]',
        'email' => 'required|valid_email|is_unique[usuarios.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'perfil_id' => 'required|integer|in_list[1,2]'
    ];

    /**
     * Validar usuario para login
     */
    public function validateUser($email, $password)
    {
        $user = $this->where('email', $email)
                    ->where('activo', 1)
                    ->first();

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return false;
    }

    /**
     * Crear usuario con password hasheado
     */
    public function createUser($data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->insert($data);
    }

    /**
     * Obtener usuarios por perfil
     */
    public function getUsersByProfile($perfilId)
    {
        return $this->where('perfil_id', $perfilId)
                   ->where('activo', 1)
                   ->findAll();
    }

    /**
     * Verificar si usuario puede ser eliminado
     */
    public function canBeDeleted($id)
    {
        // No puede eliminar si tiene ventas asociadas
        $ventasModel = new VentasCabeceraModel();
        $ventas = $ventasModel->where('usuario_id', $id)->countAllResults();

        return $ventas === 0;
    }

    /**
     * Estadísticas de usuarios
     */
    public function getStats()
    {
        return [
            'total' => $this->countAllResults(),
            'activos' => $this->where('activo', 1)->countAllResults(),
            'administradores' => $this->where('perfil_id', 1)->countAllResults(),
            'clientes' => $this->where('perfil_id', 2)->countAllResults()
        ];
    }
}
```

### 🛍️ ProductoModel

**Archivo**: `app/Models/ProductoModel.php`

```php
class ProductoModel extends BaseModel
{
    protected $table = 'productos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre', 'descripcion', 'precio', 'stock', 'categoria_id', 'imagen', 'activo'];

    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[255]',
        'precio' => 'required|decimal|greater_than[0]',
        'stock' => 'required|integer|greater_than_equal_to[0]',
        'categoria_id' => 'permit_empty|integer'
    ];

    /**
     * Obtener productos con información de categoría
     */
    public function getProductosConCategoria()
    {
        return $this->select('productos.*, categorias.nombre as categoria_nombre')
                   ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
                   ->orderBy('productos.nombre')
                   ->findAll();
    }

    /**
     * Obtener productos activos
     */
    public function getProductosActivos($limit = null)
    {
        $builder = $this->select('productos.*, categorias.nombre as categoria_nombre')
                       ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
                       ->where('productos.activo', 1)
                       ->where('productos.stock >', 0)
                       ->orderBy('productos.nombre');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Obtener producto completo
     */
    public function getProductoCompleto($id)
    {
        return $this->select('productos.*, categorias.nombre as categoria_nombre')
                   ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
                   ->where('productos.id', $id)
                   ->first();
    }

    /**
     * Productos relacionados por categoría
     */
    public function getProductosRelacionados($categoriaId, $excludeId, $limit = 4)
    {
        return $this->where('categoria_id', $categoriaId)
                   ->where('id !=', $excludeId)
                   ->where('activo', 1)
                   ->where('stock >', 0)
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Buscar productos
     */
    public function searchProducts($term, $categoriaId = null)
    {
        $builder = $this->select('productos.*, categorias.nombre as categoria_nombre')
                       ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
                       ->where('productos.activo', 1)
                       ->groupStart()
                           ->like('productos.nombre', $term)
                           ->orLike('productos.descripcion', $term)
                       ->groupEnd();

        if ($categoriaId) {
            $builder->where('productos.categoria_id', $categoriaId);
        }

        return $builder->findAll();
    }

    /**
     * Actualizar stock
     */
    public function updateStock($id, $cantidad)
    {
        $producto = $this->find($id);
        if (!$producto) {
            throw new \Exception('Producto no encontrado');
        }

        $newStock = $producto->stock + $cantidad;
        if ($newStock < 0) {
            throw new \Exception('Stock insuficiente');
        }

        return $this->update($id, ['stock' => $newStock]);
    }

    /**
     * Productos con stock bajo
     */
    public function getProductosStockBajo($limite = 5)
    {
        return $this->select('productos.*, categorias.nombre as categoria_nombre')
                   ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
                   ->where('productos.activo', 1)
                   ->where('productos.stock <=', $limite)
                   ->orderBy('productos.stock', 'ASC')
                   ->findAll();
    }

    /**
     * Estadísticas de productos
     */
    public function getStats()
    {
        return [
            'total' => $this->countAllResults(),
            'activos' => $this->where('activo', 1)->countAllResults(),
            'sin_stock' => $this->where('stock', 0)->countAllResults(),
            'stock_bajo' => $this->where('stock <=', 5)->where('stock >', 0)->countAllResults(),
            'valor_inventario' => $this->selectSum('precio * stock', 'valor_total')->first()->valor_total ?? 0
        ];
    }
}
```

## 🎨 Views (Vistas)

Las vistas manejan la presentación e interfaz de usuario.

### 📁 Estructura de Vistas

```
app/Views/
├── layouts/                    # Layouts base
│   ├── main.php               # Layout principal del sitio
│   ├── admin.php              # Layout para panel admin
│   └── auth.php               # Layout para autenticación
├── front/                     # Frontend público
│   ├── home.php               # Página principal
│   ├── login.php              # Formulario de login
│   ├── productos/             # Módulo de productos
│   │   ├── index.php          # Lista de productos
│   │   └── detalle.php        # Detalle de producto
│   ├── carrito/               # Carrito de compras
│   │   ├── index.php          # Vista del carrito
│   │   └── checkout.php       # Proceso de compra
│   └── contacto.php           # Formulario de contacto
├── back/                      # Backend administrativo
│   ├── dashboard.php          # Dashboard principal
│   ├── usuarios/              # Gestión de usuarios
│   ├── productos/             # Gestión de productos
│   └── ventas/                # Gestión de ventas
├── components/                # Componentes reutilizables
│   ├── navbar.php             # Barra de navegación
│   ├── footer.php             # Pie de página
│   ├── alerts.php             # Alertas y mensajes
│   └── pagination.php         # Paginación
└── emails/                    # Templates de email
    ├── welcome.php            # Email de bienvenida
    └── order_confirmation.php # Confirmación de pedido
```

### 🎯 Layout Principal

**Archivo**: `app/Views/layouts/main.php`

```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Yagaruete Camp' ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <?= $this->section('css') ?>
</head>
<body>
    <!-- Navigation -->
    <?= $this->include('components/navbar') ?>

    <!-- Main Content -->
    <main class="container-fluid">
        <!-- Alerts -->
        <?= $this->include('components/alerts') ?>

        <!-- Page Content -->
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <?= $this->include('components/footer') ?>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>

    <?= $this->section('js') ?>
</body>
</html>
```

### 🧩 Componentes Reutilizables

**Archivo**: `app/Views/components/navbar.php`

```php
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">
            <i class="fas fa-mountain"></i>
            Yagaruete Camp
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url() ?>">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('productos') ?>">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('contacto') ?>">Contacto</a>
                </li>
            </ul>

            <ul class="navbar-nav">
                <?php if ($usuario_logueado ?? false): ?>
                    <!-- Usuario logueado -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                            <?= esc($usuario_nombre) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if (($perfil_id ?? 0) == 1): ?>
                                <li><a class="dropdown-item" href="<?= base_url('admin') ?>">Panel Admin</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= base_url('perfil') ?>">Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('mis-compras') ?>">Mis Compras</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Cerrar Sesión</a></li>
                        </ul>
                    </li>

                    <!-- Carrito -->
                    <?php if (($perfil_id ?? 0) == 2): ?>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="<?= base_url('carrito') ?>">
                                <i class="fas fa-shopping-cart"></i>
                                Carrito
                                <?php if (($carrito_count ?? 0) > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?= $carrito_count ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Usuario no logueado -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('login') ?>">
                            <i class="fas fa-sign-in-alt"></i>
                            Iniciar Sesión
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('registro') ?>">
                            <i class="fas fa-user-plus"></i>
                            Registrarse
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
```

## 🛡️ Filters (Filtros)

Los filtros manejan middleware y autenticación.

### 📁 Estructura de Filtros

```
app/Filters/
├── Auth.php                   # Autenticación general
├── Cliente.php               # Filtro para clientes
├── Admin.php                 # Filtro para administradores
├── CORS.php                  # Cross-Origin Resource Sharing
└── RateLimit.php            # Limitación de tasa
```

### 🔐 Auth Filter

**Archivo**: `app/Filters/Auth.php`

```php
<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Verificar si usuario está logueado
        if (!$session->get('usuario_logueado')) {
            // Guardar URL destino para redirigir después del login
            $session->set('redirect_url', current_url());

            // Redirigir al login
            return redirect()->to('login');
        }

        // Verificar rol si se especifica
        if ($arguments && count($arguments) > 0) {
            $requiredRole = $arguments[0];
            $userRole = $session->get('perfil_id');

            if ($userRole != $requiredRole) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException();
            }
        }

        // Log de acceso
        log_message('info', 'Auth filter: User ' . $session->get('usuario_email') . ' accessing ' . $request->getPath());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada que hacer después
    }
}
```

## 🔄 Routing (Enrutamiento)

**Archivo**: `app/Config/Routes.php`

```php
<?php

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');

// Frontend Routes
$routes->get('/', 'Home::index');
$routes->get('productos', 'ProductoController::index');
$routes->get('productos/(:num)', 'ProductoController::detalle/$1');
$routes->get('contacto', 'ContactoController::index');
$routes->post('contacto', 'ContactoController::enviar');

// Auth Routes
$routes->get('login', 'LoginController::index');
$routes->post('login', 'LoginController::authenticate');
$routes->get('logout', 'LoginController::logout');
$routes->get('registro', 'RegistroController::index');
$routes->post('registro', 'RegistroController::crear');

// Carrito Routes (requiere cliente)
$routes->group('carrito', ['filter' => 'auth:2'], function($routes) {
    $routes->get('/', 'CarritoController::index');
    $routes->post('agregar', 'CarritoController::agregar');
    $routes->post('actualizar', 'CarritoController::actualizar');
    $routes->post('eliminar', 'CarritoController::eliminar');
    $routes->get('checkout', 'CarritoController::checkout');
    $routes->post('confirmar', 'CarritoController::confirmar');
});

// Admin Routes (requiere administrador)
$routes->group('admin', ['filter' => 'auth:1'], function($routes) {
    $routes->get('/', 'AdminController::dashboard');
    $routes->get('dashboard', 'AdminController::dashboard');

    // Usuarios
    $routes->resource('usuarios', ['controller' => 'UsuarioCrudController']);

    // Productos
    $routes->resource('productos', ['controller' => 'ProductoController']);

    // Ventas
    $routes->get('ventas', 'VentasController::admin');
    $routes->get('ventas/(:num)', 'VentasController::detalle/$1');

    // Reportes
    $routes->get('reportes', 'ReportesController::index');
    $routes->get('reportes/ventas', 'ReportesController::ventas');
    $routes->get('reportes/productos', 'ReportesController::productos');
});

// API Routes
$routes->group('api', function($routes) {
    $routes->resource('productos', ['controller' => 'API\ProductsController']);
    $routes->resource('usuarios', ['controller' => 'API\UsersController']);

    $routes->post('auth/login', 'API\AuthController::login');
    $routes->post('auth/logout', 'API\AuthController::logout');
});
```

## 📚 Beneficios de la Arquitectura MVC

### ✅ Ventajas

1. **Separación de Responsabilidades**

   - Cada componente tiene una función específica
   - Facilita el mantenimiento y debugging

2. **Reutilización de Código**

   - Models pueden ser utilizados por múltiples Controllers
   - Views pueden ser compartidas entre diferentes actions

3. **Testabilidad**

   - Cada capa puede ser testada independientemente
   - Facilita unit testing y integration testing

4. **Escalabilidad**

   - Fácil agregar nuevas funcionalidades
   - Estructura preparada para crecimiento

5. **Mantenibilidad**
   - Código organizado y fácil de entender
   - Cambios en una capa no afectan las otras

### 🔄 Flujo de Datos

```
Request → Router → Filter → Controller → Model → Database
                                    ↓
Response ← View ← Controller ← Model ← Database
```

### 📈 Mejores Prácticas

1. **Controllers Delgados**: Mínima lógica de negocio
2. **Models Robustos**: Toda la lógica de datos y validación
3. **Views Limpias**: Solo presentación, sin lógica compleja
4. **Validación Consistente**: En Models y Controllers
5. **Logging Comprehensivo**: Para debugging y auditoría

---

**Yagaruete Camp** - Implementación profesional del patrón MVC 🏛️
