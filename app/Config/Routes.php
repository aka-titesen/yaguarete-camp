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
$routes->get('/carrito-comprar', 'Ventascontroller::registrarVenta',['filter' => 'cliente']);

// ================================
// VENTAS Y FACTURAS
// ================================

/**
 * Área del cliente - Ver sus compras
 */
$routes->get('vista_compras/(:num)', 'Ventascontroller::verFactura/$1', ['filter' => 'cliente']);
$routes->get('ver_factura_usuario/(:num)', 'Ventascontroller::verFacturasUsuario/$1', ['filter' => 'cliente']);
$routes->get('mis-compras', 'Ventascontroller::misCompras', ['filter' => 'cliente']);
$routes->get('detalle-compra/(:num)', 'Ventascontroller::verFactura/$1', ['filter' => 'cliente']);

/**
 * Administración de ventas (Admin/Vendedor)
 */
$routes->get('admin-ventas', 'Ventascontroller::administrarVentas', ['filter' => 'auth']);
$routes->get('detalle-venta/(:num)', 'Ventascontroller::detalleVenta/$1', ['filter' => 'auth']);
$routes->get('/ventas', 'Ventascontroller::ventas');

// ================================
// SISTEMA DE CONSULTAS
// ================================

/**
 * Consultas de clientes
 */
$routes->post('enviar-consulta', 'ConsultasController::enviarConsulta');        // Enviar consulta (público)
$routes->get('admin-consultas', 'ConsultasController::administrarConsultas', ['filter' => 'auth']); // Ver consultas (admin)
$routes->post('responder-consulta', 'ConsultasController::responderConsulta'); // Responder consulta (admin)