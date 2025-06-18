<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('sobreNosotros', 'Home::sobre_Nosotros');
$routes->get('termYCondiciones','Home::term_Y_Condiciones');
$routes->get('comercializacion','Home::a_comercializacion');
$routes->get('contacto','Home::a_contacto');
$routes->get('producto','Home::a_producto');
$routes->get('producto/(:num)', 'Home::a_detalleProducto/$1');
$routes->get('catalogo', 'Home::a_catalogoProductos');
$routes->get('catalogoProductos','Home::a_catalogoProductos');
$routes->post('/enviar-form', 'Usuario_controller::formValidation');
$routes->get('/dashboard', 'Home::a_dashboard', ['filter' => 'auth']);
$routes->get('login', 'login_controller::index');
$routes->post('login/auth', 'login_controller::auth');
$routes->get('logout', 'login_controller::logout');
$routes->post('/crear-producto', 'ProductoController::store', ['filter' => 'auth']);
$routes->get('administrarProductos', 'ProductoController::index', ['filter' => 'auth']);
$routes->get('admin_usuarios', 'usuario_crud_controller::index', ['filter' => 'auth']);

$routes->post('/store', 'usuario_crud_controller::store', ['filter' => 'auth']);

$routes->post('ProductoController/modifica/(:num)', 'ProductoController::modifica/$1', ['filter' => 'auth']);
$routes->get('ProductoController/deleteproducto/(:num)', 'ProductoController::deleteproducto/$1', ['filter' => 'auth']);
$routes->post('ProductoController/deleteproducto/(:num)', 'ProductoController::deleteproducto/$1', ['filter' => 'auth']);
$routes->post('ProductoController/activarproducto/(:num)', 'ProductoController::activarproducto/$1', ['filter' => 'auth']);

/*CARRITO RUTAS*/
$routes->post('carrito/add', 'CarritoController::add');
$routes->get('carrito_elimina/(:any)', 'CarritoController::remove/$1');
$routes->get('/borrar','CarritoController::borrar_carrito');
$routes->get('/carrito_actualiza','CarritoController::actualiza_carrito');
$routes->get('/carrito-comprar', 'Ventascontroller::registrar_venta',['filter' => 'cliente']);
$routes->get('carrito_suma/(:any)', 'CarritoController::suma/$1');
$routes->get('carrito_resta/(:any)', 'CarritoController::resta/$1');
$routes->get('carrito/ajax', 'CarritoController::ajax');
$routes->get('carrito/devolver_carrito', 'CarritoController::devolver_carrito');
$routes->get('muestro', 'CarritoController::muestro');
$routes->get('debug-cart', 'CarritoController::debug_cart'); // Ruta para depuración
$routes->get('debug-compra/(:num)', 'CarritoController::debug_compra/$1'); // Ruta para depurar una compra específica
$routes->get('debug-compra', 'CarritoController::debug_compra'); // Ruta para depuración general
$routes->get('debug/compra/(:num)', 'Home::verCompra/$1'); // Ruta personalizada para acceder al script de depuración
$routes->get('diagnostico/(:num)', 'Ventascontroller::diagnosticar/$1'); // Ruta personalizada para el diagnóstico de compra

// Rutas del cliente para ver sus compras y detalle
$routes->get('vista_compras/(:num)', 'Ventascontroller::ver_factura/$1', ['filter' => 'cliente']);
$routes->get('ver_factura_usuario/(:num)', 'Ventascontroller::ver_facturas_usuario/$1', ['filter' => 'cliente']);

// Nuevas rutas para compras de cliente y administración de ventas
$routes->get('mis-compras', 'Ventascontroller::mis_compras', ['filter' => 'cliente']);
$routes->get('detalle-compra/(:num)', 'Ventascontroller::ver_factura/$1', ['filter' => 'cliente']); // Usar el método que funcionaba correctamente
$routes->get('admin-ventas', 'Ventascontroller::administrar_ventas', ['filter' => 'auth']);
$routes->get('detalle-venta/(:num)', 'Ventascontroller::detalle_venta/$1', ['filter' => 'auth']);

// Rutas para consultas
$routes->post('enviar-consulta', 'ConsultasController::enviarConsulta');
$routes->get('admin-consultas', 'ConsultasController::administrarConsultas', ['filter' => 'auth']);
$routes->post('responder-consulta', 'ConsultasController::responderConsulta');

$routes->get('/ventas', 'Ventascontroller::ventas');