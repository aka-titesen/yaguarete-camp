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
$routes->post('/enviar-form', 'Usuario_controller::formValidation');
$routes->get('/dashboard', 'Home::a_dashboard', ['filter' => 'auth']);
$routes->get('login', 'login_controller::index');
$routes->post('login/auth', 'login_controller::auth');
$routes->get('logout', 'login_controller::logout');
$routes->post('/crear-producto', 'Producto_controller::formValidation', ['filter' => 'auth']);
//$routes->get(from:'administrarProductos', 'Home::a_administrarProductos', ['filter' => 'auth']);



$routes->get('administrarProductos', function(){
    echo view("front/layouts/header");
    echo view("front/layouts/navbar");
    echo view("front/administrarProductos");
    echo view("front/layouts/footer");
}, ['filter' => 'auth']);

/*
$routes->get('dashboard', function() {
    echo view('front/dashboard');
}, ['filter' => 'auth']);
*/