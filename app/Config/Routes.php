<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('sobreNosotros', 'Home::sobreNosotros');
$routes->get('termYCondiciones','Home::termYCondiciones');
$routes->get('comercializacion','Home::aComercializacion');
$routes->get('contacto','Home::aContacto');
$routes->get('producto','Home::aProducto');
$routes->get('producto/(:num)', 'Home::aDetalleProducto/$1');
$routes->get('catalogo', 'Home::aCatalogoProductos');
$routes->post('/enviar-form', 'UsuarioController::formValidation');
$routes->get('/dashboard', 'Home::aDashboard', ['filter' => 'auth']);
$routes->get('login', 'LoginController::index');
$routes->post('login/auth', 'LoginController::auth');
$routes->get('logout', 'LoginController::logout');
$routes->post('/crear-producto', 'ProductoController::store', ['filter' => 'auth']);
$routes->get('administrar_productos', 'ProductoController::index', ['filter' => 'auth']);
$routes->get('admin_usuarios', 'UsuarioCrudController::index', ['filter' => 'auth']);
$routes->post('/store', 'UsuarioCrudController::store', ['filter' => 'auth']);
$routes->get('deletelogico/(:num)', 'UsuarioCrudController::deleteLogico/$1', ['filter' => 'auth']);
$routes->get('activar/(:num)', 'UsuarioCrudController::activar/$1', ['filter' => 'auth']);
$routes->post('editar_user', 'UsuarioCrudController::update', ['filter' => 'auth']);

$routes->post('ProductoController/modifica/(:num)', 'ProductoController::modifica/$1', ['filter' => 'auth']);
$routes->get('deleteproducto/(:num)', 'ProductoController::deleteProducto/$1', ['filter' => 'auth']);
$routes->post('deleteproducto/(:num)', 'ProductoController::deleteProducto/$1', ['filter' => 'auth']);
$routes->post('activarproducto/(:num)', 'ProductoController::activarProducto/$1', ['filter' => 'auth']);

/*CARRITO RUTAS*/
$routes->post('carrito/add', 'CarritoController::add');
$routes->get('carrito_elimina/(:any)', 'CarritoController::remove/$1');
$routes->get('/borrar','CarritoController::borrarCarrito');
$routes->get('/carrito_actualiza','CarritoController::actualizaCarrito');
$routes->get('/carrito-comprar', 'Ventascontroller::registrarVenta',['filter' => 'cliente']);
$routes->get('carrito_suma/(:any)', 'CarritoController::suma/$1');
$routes->get('carrito_resta/(:any)', 'CarritoController::resta/$1');
$routes->get('carrito/ajax', 'CarritoController::ajax');
$routes->get('carrito/devolver_carrito', 'CarritoController::devolverCarrito');
$routes->get('muestro', 'CarritoController::muestro');


// Rutas del cliente para ver sus compras y detalle
$routes->get('vista_compras/(:num)', 'Ventascontroller::verFactura/$1', ['filter' => 'cliente']);
$routes->get('ver_factura_usuario/(:num)', 'Ventascontroller::verFacturasUsuario/$1', ['filter' => 'cliente']);

// Nuevas rutas para compras de cliente y administración de ventas
$routes->get('mis-compras', 'Ventascontroller::misCompras', ['filter' => 'cliente']);
$routes->get('detalle-compra/(:num)', 'Ventascontroller::verFactura/$1', ['filter' => 'cliente']); // Usar el método que funcionaba correctamente
$routes->get('admin-ventas', 'Ventascontroller::administrarVentas', ['filter' => 'auth']);
$routes->get('detalle-venta/(:num)', 'Ventascontroller::detalleVenta/$1', ['filter' => 'auth']);

// Rutas para consultas
$routes->post('enviar-consulta', 'ConsultasController::enviarConsulta');
$routes->get('admin-consultas', 'ConsultasController::administrarConsultas', ['filter' => 'auth']);
$routes->post('responder-consulta', 'ConsultasController::responderConsulta');

$routes->get('/ventas', 'Ventascontroller::ventas');