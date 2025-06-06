<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\Carritos_model;

class CarritoController extends BaseController{
    public function __construct()
    {
        helper(['form', 'url', 'cart' ]);
        $cart = \Config\Services::cart();
        $session = session();
    }
//agrega items al carrito
    public function add() {
        $cart = \Config\Services::Cart();
        $request = \Config\Services::request();

        $cart->insert(array(
            'id'      => $request->getPost('id'),
            'qty'     => 1,
            'name'    => $request->getPost('nombre_prod'),
            'price'   => $request->getPost('precio_vta'),
            'imagen'  => $request->getPost('imagen'),
        ));

        return redirect()->back()->withInput();
    }
//Actualiza el carrito que se muestra
    public function actualiza_carrito()
    {
        $cart = \Config\Services::Cart();
        $request = \Config\Services::request();
        $cart->update(array(
            'id'     => $request->getPost('id'),
            'qty'    => 1,
            'price'  => $request->getPost('precio_vta'),
            'name'   => $request->getPost('nombre_prod'),
            'imagen' => $request->getPost('imagen'),
        ));
        return redirect()->back()->withInput();
    }
}

