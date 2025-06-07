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
$routes->get('/carrito_actualiza','CarritoController::actualiza_carrito',['filter' => 'auth']);
$routes->post('carrito/add', 'Carrito_controller::add',['filter' => 'auth']);
$routes->get('carrito_elimina/(:any)', 'carrito_controller::remove/$1',['filter' => 'auth']);
$routes->get('/borrar','carrito_controller::borrar_carrito',['filter' => 'auth']);
$routes->get('/carrito-comprar', 'Ventascontroller::registrar_venta',['filter' => 'auth']);
$routes->get('carrito_suma/(:any)', 'carrito_controller::suma/$1');
$routes->get('carrito_resta/(:any)', 'carrito_controller::resta/$1'); 
