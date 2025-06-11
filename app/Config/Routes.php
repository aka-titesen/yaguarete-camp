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
$routes->post('ProductoController/modifica/(:num)', 'ProductoController::modifica/$1', ['filter' => 'auth']);
$routes->get('ProductoController/deleteproducto/(:num)', 'ProductoController::deleteproducto/$1', ['filter' => 'auth']);
$routes->post('ProductoController/deleteproducto/(:num)', 'ProductoController::deleteproducto/$1', ['filter' => 'auth']);
$routes->post('ProductoController/activarproducto/(:num)', 'ProductoController::activarproducto/$1', ['filter' => 'auth']);

/*CARRITO RUTAS*/
$routes->post('carrito/add', 'CarritoController::add');
$routes->get('carrito_elimina/(:any)', 'CarritoController::remove/$1');
$routes->get('/borrar','CarritoController::borrar_carrito');
$routes->get('/carrito_actualiza','CarritoController::actualiza_carrito');
$routes->get('/carrito-comprar', 'Ventascontroller::registrar_venta',['filter' => 'auth']);
$routes->get('carrito_suma/(:any)', 'CarritoController::suma/$1');
$routes->get('carrito_resta/(:any)', 'CarritoController::resta/$1');
$routes->get('carrito/ajax', 'CarritoController::ajax');
$routes->get('carrito/devolver_carrito', 'CarritoController::devolver_carrito');

// Rutas del cliente para ver sus compras y detalle
$routes->get('vista_compras/(:num)', 'Ventascontroller::ver_factura/$1', ['filter' => 'auth']);
$routes->get('ver_factura_usuario/(:num)', 'Ventascontroller::ver_facturas_usuario/$1', ['filter' => 'auth']);

$routes->get('/ventas', 'Ventas_controller::ventas');