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

        $producto_id = $request->getPost('id');
        $cantidad = (int) $request->getPost('qty');
        $nombre = $request->getPost('nombre_prod');
        $precio = $request->getPost('precio_vta');
        $imagen = $request->getPost('imagen');

        // Obtener stock actual del producto
        $productoModel = new \App\Models\Producto_model();
        $producto = $productoModel->asArray()->find($producto_id); // CORRECTO: devuelve array
        if (!$producto || (isset($producto['eliminado']) && $producto['eliminado'] === 'SI')) {
            return redirect()->back()->with('error', 'Producto no disponible.');
        }
        if ($cantidad < 1) $cantidad = 1;
        if ($cantidad > (int)$producto['stock']) {
            return redirect()->back()->with('error', 'No puedes agregar más unidades que el stock disponible.');
        }

        // Revisar si ya está en el carrito y sumar cantidades
        $yaEnCarrito = false;
        foreach ($cart->contents() as $item) {
            if ($item['id'] == $producto_id) {
                $nuevaCantidad = $item['qty'] + $cantidad;
                if ($nuevaCantidad > (int)$producto['stock']) {
                    return redirect()->back()->with('error', 'No puedes agregar más unidades que el stock disponible.');
                }
                $cart->update([
                    'rowid' => $item['rowid'],
                    'qty' => $nuevaCantidad,
                    'stock' => $producto['stock'], // Actualizar stock
                ]);
                $yaEnCarrito = true;
                break;
            }
        }
        if (!$yaEnCarrito) {
            $cart->insert([
                'id'      => $producto_id,
                'qty'     => $cantidad,
                'name'    => $nombre,
                'price'   => $precio,
                'imagen'  => $imagen,
                'stock'   => $producto['stock'], // Agregar stock
            ]);
        }
        return redirect()->back()->with('success', 'Producto agregado al carrito.');
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
    public function eliminar_item($rowid)
    {
        $cart = \Config\Services::Cart();
        $cart->remove($rowid);
        return redirect()->to(base_url('muestro'));
    }

    public function borrar_carrito()
    {
        $cart = \Config\Services::Cart();
        $cart->destroy();
        return $this->response->setStatusCode(200)->setBody('ok');
    }
    public function remove($rowid)
    {
        $cart = \Config\Services::cart();
        $cart->remove($rowid);
        return $this->response->setStatusCode(200)->setBody('ok');
    }
    public function devolver_carrito()
    {
        $cart = \Config\Services::cart();
        return $cart->contents();
    }

    public function suma($rowid)
    {
        $cart = \Config\Services::cart();
        $item = $cart->getItem($rowid);
        if ($item) {
            // Obtener stock actual del producto
            $productoModel = new \App\Models\Producto_model();
            $producto = $productoModel->asArray()->find($item['id']);
            $nuevoStock = $producto ? $producto['stock'] : ($item['stock'] ?? 0);
            if ($item['qty'] < $nuevoStock) {
                $cart->update([
                    'rowid' => $rowid,
                    'qty' => $item['qty'] + 1,
                    'stock' => $nuevoStock, // Actualizar stock
                ]);
            }
        }
        return $this->response->setStatusCode(200)->setBody('ok');
    }

    public function resta($rowid)
    {
        // resta 1 a la cantidad al producto
        $cart = \Config\Services::cart();
        $item = $cart->getItem($rowid);
        if ($item) {
            if ($item['qty'] > 1) {
                $cart->update([
                    'rowid' => $rowid,
                    'qty' => $item['qty'] - 1
                ]);
            } else {
                $cart->remove($rowid);
            }
        }
        return $this->response->setStatusCode(200)->setBody('ok');
    }
    public function ajax()
    {
        $cart = \Config\Services::cart();
        $items = $cart->contents();
        $total = $cart->total();
        ob_start();
        include(APPPATH.'Views/front/carritoVista.php');
        $html = ob_get_clean();
        return $this->response->setBody($html);
    }
    
}

