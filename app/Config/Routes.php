<?php

use CodeIgniter\Router\RouteCollection;

/**
 * CONFIGURACIÓN DE RUTAS DEL SISTEMA
 * 
 * Este archivo define todas las rutas URL del sistema y su mapeo a controladores.
 * 
 * Filtros disponibles:
 * - 'auth': Requiere usuario logueado (cualquier perfil)
 * - 'cliente': Requiere perfil de cliente específicamente
 * 
 * @var RouteCollection $routes
 */

// ================================
// RUTAS PÚBLICAS (Sin autenticación)
// ================================

/**
 * Página principal del sitio
 */
$routes->get('/', 'Home::index');

/**
 * Páginas informativas estáticas
 */
$routes->get('sobreNosotros', 'Home::sobreNosotros');
$routes->get('termYCondiciones','Home::termYCondiciones');
$routes->get('comercializacion','Home::aComercializacion');
$routes->get('contacto','Home::aContacto');

/**
 * Rutas relacionadas con productos (públicas)
 */
$routes->get('producto','Home::aProducto');
$routes->get('producto/(:num)', 'Home::aDetalleProducto/$1'); // (:num) = ID del producto
$routes->get('catalogo', 'Home::aCatalogoProductos');

/**
 * Sistema de registro y contacto
 */
$routes->post('/enviar-form', 'UsuarioController::formValidation'); // Formulario de registro

// ================================
// AUTENTICACIÓN
// ================================

/**
 * Login del sistema
 */
$routes->get('login', 'LoginController::index');           // Mostrar formulario
$routes->post('login/auth', 'LoginController::auth');      // Procesar login
$routes->get('logout', 'LoginController::logout');         // Cerrar sesión

// ================================
// RUTAS ADMINISTRATIVAS (Requieren auth)
// ================================

/**
 * Dashboard administrativo
 */
$routes->get('/dashboard', 'Home::aDashboard', ['filter' => 'auth']);

/**
 * Gestión de productos (Admin/Vendedor)
 */
$routes->post('/crear-producto', 'ProductoController::store', ['filter' => 'auth']);
$routes->get('administrar_productos', 'ProductoController::index', ['filter' => 'auth']);
$routes->post('ProductoController/modifica/(:num)', 'ProductoController::modifica/$1', ['filter' => 'auth']);
$routes->get('deleteproducto/(:num)', 'ProductoController::deleteProducto/$1', ['filter' => 'auth']);
$routes->post('deleteproducto/(:num)', 'ProductoController::deleteProducto/$1', ['filter' => 'auth']);
$routes->post('activarproducto/(:num)', 'ProductoController::activarProducto/$1', ['filter' => 'auth']);

/**
 * Gestión de usuarios (Solo Admin)
 */
$routes->get('admin_usuarios', 'UsuarioCrudController::index', ['filter' => 'auth']);
$routes->post('/store', 'UsuarioCrudController::store', ['filter' => 'auth']);
$routes->get('deletelogico/(:num)', 'UsuarioCrudController::deleteLogico/$1', ['filter' => 'auth']); // (:num) = ID usuario
$routes->get('activar/(:num)', 'UsuarioCrudController::activar/$1', ['filter' => 'auth']);
$routes->post('editar_user', 'UsuarioCrudController::update', ['filter' => 'auth']);

// ================================
// CARRITO DE COMPRAS
// ================================

/**
 * Operaciones del carrito (disponibles para todos)
 */
$routes->post('carrito/add', 'CarritoController::add');                    // Agregar producto
$routes->get('carrito_elimina/(:any)', 'CarritoController::remove/$1');    // Eliminar producto
$routes->get('/borrar','CarritoController::borrarCarrito');                // Vaciar carrito
$routes->get('/carrito_actualiza','CarritoController::actualizaCarrito');  // Actualizar carrito
$routes->get('carrito_suma/(:any)', 'CarritoController::suma/$1');         // Incrementar cantidad
$routes->get('carrito_resta/(:any)', 'CarritoController::resta/$1');       // Decrementar cantidad

/**
 * AJAX y utilidades del carrito
 */
$routes->get('carrito/ajax', 'CarritoController::ajax');
$routes->get('carrito/devolver_carrito', 'CarritoController::devolverCarrito');
$routes->get('muestro', 'CarritoController::muestro');

/**
 * Procesamiento de compra (requiere ser cliente)
 */
$routes->get('/carrito-comprar', 'VentasController::registrarVenta',['filter' => 'cliente']);

/**
 * Gestión de compras del cliente
 */
$routes->get('vista_compras/(:num)', 'VentasController::verFactura/$1', ['filter' => 'cliente']);
$routes->get('ver_factura_usuario/(:num)', 'VentasController::verFacturasUsuario/$1', ['filter' => 'cliente']);
$routes->get('mis-compras', 'VentasController::misCompras', ['filter' => 'cliente']);
$routes->get('detalle-compra/(:num)', 'VentasController::verFactura/$1', ['filter' => 'cliente']);

/**
 * Panel de administración de ventas
 */
$routes->get('admin-ventas', 'VentasController::administrarVentas', ['filter' => 'auth']);
$routes->get('detalle-venta/(:num)', 'VentasController::detalleVenta/$1', ['filter' => 'auth']);
$routes->get('/ventas', 'VentasController::ventas');

// ================================
// SISTEMA DE CONSULTAS
// ================================

/**
 * Consultas de clientes
 */
$routes->post('enviar-consulta', 'ConsultasController::enviarConsulta');        // Enviar consulta (público)
$routes->get('admin-consultas', 'ConsultasController::administrarConsultas', ['filter' => 'auth']); // Ver consultas (admin)
$routes->post('responder-consulta', 'ConsultasController::responderConsulta'); // Responder consulta (admin)

// ================================
// API REST ENDPOINTS
// ================================

/**
 * Grupo de rutas API con prefijo /api
 */
$routes->group('api', ['namespace' => 'App\Controllers\API'], function($routes) {
    
    // ================================
    // AUTENTICACIÓN API
    // ================================
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/registro', 'AuthController::registro');
    $routes->post('auth/refresh', 'AuthController::refresh', ['filter' => 'jwt']);
    $routes->post('auth/logout', 'AuthController::logout', ['filter' => 'jwt']);
    
    // ================================
    // PRODUCTOS API
    // ================================
    $routes->get('productos', 'ProductosController::index');                    // Listar productos con filtros
    $routes->get('productos/(:num)', 'ProductosController::show/$1');           // Detalle de producto específico
    $routes->get('productos/destacados', 'ProductosController::destacados');    // Productos destacados
    $routes->get('productos/relacionados/(:num)', 'ProductosController::relacionados/$1'); // Productos relacionados
    $routes->get('productos/buscar', 'ProductosController::buscar');            // Búsqueda de productos
    
    // Gestión de productos (requiere autenticación)
    $routes->post('productos', 'ProductosController::create', ['filter' => 'jwt']);
    $routes->put('productos/(:num)', 'ProductosController::update/$1', ['filter' => 'jwt']);
    $routes->delete('productos/(:num)', 'ProductosController::delete/$1', ['filter' => 'jwt']);
    
    // ================================
    // CATEGORÍAS API
    // ================================
    $routes->get('categorias', 'CategoriasController::index');                  // Listar todas las categorías
    $routes->get('categorias/(:num)', 'CategoriasController::show/$1');         // Detalle de categoría
    $routes->get('categorias/(:num)/productos', 'CategoriasController::productos/$1'); // Productos de una categoría
    
    // ================================
    // USUARIO API (Requieren autenticación JWT)
    // ================================
    $routes->get('usuario/perfil', 'UsuariosController::perfil', ['filter' => 'jwt']);
    $routes->put('usuario/perfil', 'UsuariosController::actualizarPerfil', ['filter' => 'jwt']);
    $routes->get('usuario/pedidos', 'UsuariosController::pedidos', ['filter' => 'jwt']);
    $routes->post('usuario/direccion', 'UsuariosController::agregarDireccion', ['filter' => 'jwt']);
    
    // ================================
    // CARRITO API
    // ================================
    $routes->get('carrito', 'CarritoController::obtener');
    $routes->post('carrito/agregar', 'CarritoController::agregar');
    $routes->put('carrito/actualizar', 'CarritoController::actualizar');
    $routes->delete('carrito/eliminar/(:any)', 'CarritoController::eliminar/$1');
    $routes->delete('carrito/vaciar', 'CarritoController::vaciar');
    
    // ================================
    // VENTAS API (Requieren autenticación JWT)
    // ================================
    $routes->post('ventas/procesar', 'VentasController::procesar', ['filter' => 'jwt']);
    $routes->get('ventas/historial', 'VentasController::historial', ['filter' => 'jwt']);
    $routes->get('ventas/(:num)', 'VentasController::detalle/$1', ['filter' => 'jwt']);
    
    // ================================
    // MANEJO DE OPCIONES CORS
    // ================================
    $routes->options('(:any)', function() {
        return service('response')->setStatusCode(200);
    });
});