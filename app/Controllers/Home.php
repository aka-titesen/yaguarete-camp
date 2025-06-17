<?php

namespace App\Controllers;
use App\Models\Producto_model;

class Home extends BaseController
{
    public function index(): void
    {
        $productoModel = new Producto_model();
        // Traer productos de la categoría Pesca (id=2), solo activos
        $destacadosPesca = $productoModel->where('categoria_id', 2)
            ->where('eliminado !=', 'SI')
            ->orderBy('id', 'DESC')
            ->limit(6)
            ->findAll();
        $data = [
            'destacadosPesca' => $destacadosPesca
        ];
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/principal", $data);
        echo view("front/layouts/footer");
    }

    public function sobre_Nosotros(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/sobreNosotros");
        echo view("front/layouts/footer");
    }

    public function term_Y_Condiciones(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/termYCondiciones");
        echo view("front/layouts/footer");
    }

    public function a_comercializacion(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/comercializacion");
        echo view("front/layouts/footer");
    }

    public function a_contacto(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/contacto");
        echo view("front/layouts/footer");
    }    public function a_producto(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/producto");
        echo view("front/layouts/footer");
    }

    public function a_detalleProducto($id): void
    {
        $productoModel = new Producto_model();
        $producto = $productoModel->where('id', $id)->first();
        
        if (!$producto || $producto['eliminado'] === 'SI') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        // Obtener productos relacionados (misma categoría, excluyendo el actual)
        $relacionados = $productoModel->getRelacionados($producto['categoria_id'], $producto['id']);
        $data = [
            'producto' => $producto,
            'relacionados' => $relacionados
        ];
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/detalleProducto", $data);
        echo view("front/layouts/footer");
    }

    
    public function a_dashboard(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/dashboard");
        echo view("front/layouts/footer");
    }

    public function a_administrarProductos(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/administrarProductos");
        echo view("front/layouts/footer");
    }

    public function a_catalogoProductos(): void
    {
        $productoModel = new Producto_model();
        $productos = $productoModel->getProductoAll();
        $data = ['productos' => $productos];
        echo view('front/layouts/header');
        echo view('front/layouts/navbar');
        echo view('front/catalogoProductos', $data);
        echo view('front/layouts/footer');
    }

}