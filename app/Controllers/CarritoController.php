<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\Carritos_model;

class CarritoController extends BaseController{
    protected $response;

    public function __construct()
    {
        helper(['form', 'url', 'cart' ]);
        $cart = \Config\Services::cart();
        $session = session();
        $this->response = \Config\Services::response();
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
        $producto = $productoModel->find($producto_id);
        if (is_object($producto)) {
            $producto = (array)$producto;
        }
        if (!$producto || (isset($producto['eliminado']) && $producto['eliminado'] === 'SI')) {
            return $this->response->setJSON(['status'=>'error','msg'=>'Producto no disponible.']);
        }
        if ($cantidad < 1) $cantidad = 1;
        $stock = (int)$producto['stock'];

        // Revisar si ya está en el carrito y sumar cantidades
        $yaEnCarrito = false;
        $cantidadEnCarrito = 0;
        $rowid = null;
        foreach ($cart->contents() as $item) {
            if ($item['id'] == $producto_id) {
                $cantidadEnCarrito = (int)$item['qty'];
                $rowid = $item['rowid'];
                $yaEnCarrito = true;
                break;
            }
        }
        $puedeAgregar = $stock - $cantidadEnCarrito;
        if ($puedeAgregar <= 0) {
            // Ya está el máximo en el carrito
            return $this->response->setJSON(['status'=>'info','msg'=>'Ya tienes la máxima cantidad permitida de este producto en el carrito.']);
        }
        if ($cantidad > $puedeAgregar) {
            // Solo se pueden agregar las unidades que faltan
            if ($yaEnCarrito) {
                $cart->update([
                    'rowid' => $rowid,
                    'qty' => $stock,
                    'stock' => $stock,
                ]);
            } else {
                $cart->insert([
                    'id'      => $producto_id,
                    'qty'     => $puedeAgregar,
                    'name'    => $nombre,
                    'price'   => $precio,
                    'imagen'  => $imagen,
                    'stock'   => $stock,
                ]);
            }
            $fuera = $cantidad - $puedeAgregar;
            return $this->response->setJSON(['status'=>'warning','msg'=>'Solo se agregaron '.$puedeAgregar.' unidades. '.$fuera.' unidad(es) quedaron fuera por falta de stock.']);
        } else {
            // Se pueden agregar todas las unidades solicitadas
            if ($yaEnCarrito) {
                $cart->update([
                    'rowid' => $rowid,
                    'qty' => $cantidadEnCarrito + $cantidad,
                    'stock' => $stock,
                ]);
            } else {
                $cart->insert([
                    'id'      => $producto_id,
                    'qty'     => $cantidad,
                    'name'    => $nombre,
                    'price'   => $precio,
                    'imagen'  => $imagen,
                    'stock'   => $stock,
                ]);
            }
            return $this->response->setJSON(['status'=>'success','msg'=>'Producto agregado al carrito.']);
        }
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
    }    public function devolver_carrito($returnArray = false)
    {
        $cart = \Config\Services::cart();
        $contents = $cart->contents();
        $total_items = 0;
        
        // Registrar para depuración
        log_message('debug', 'Contenido del carrito: ' . json_encode($contents));

        foreach ($contents as $item) {
            if (isset($item['qty'])) {
                $total_items += (int)$item['qty'];
            }
        }
        
        // Si se solicita el array directamente (desde otros controladores)
        if ($returnArray) {
            log_message('debug', 'Devolviendo contenido del carrito como array: ' . count($contents) . ' productos');
            return $contents;
        }
        
        // De lo contrario, devuelve la respuesta JSON (para AJAX)
        return $this->response->setJSON([
            'total_items' => $total_items,
            'items' => $contents
        ]);
    }    // Método para mostrar la página del carrito completo
    public function muestro()
    {
        $cart = \Config\Services::cart();
        $contents = $cart->contents();
        
        // Registrar para depuración
        log_message('debug', 'Mostrando página de carrito. Contenido: ' . json_encode($contents));
        
        $data = [
            'titulo' => 'Mi carrito de compras',
            'cart_contents' => $contents,
            'total' => $cart->total()
        ];
        
        // Simplemente cargaremos las vistas con el contenido del carrito
        echo view('front/layouts/header', $data);
        echo view('front/layouts/navbar');
        echo view('front/muestroCarrito', $data);
        echo view('front/layouts/footer');
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
    public function debug_cart()
    {
        $cart = \Config\Services::cart();
        $contents = $cart->contents();
        
        echo '<pre>';
        echo "Contenido del carrito:\n";
        print_r($contents);
        echo '</pre>';
        
        if (empty($contents)) {
            echo "El carrito está vacío.";
        } else {
            echo "El carrito tiene " . count($contents) . " productos.";
        }
        
        exit;
    }
    
}

