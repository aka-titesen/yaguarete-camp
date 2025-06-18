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
        // Ofertas relámpago: productos de Camping (id=1)
        $ofertasCamping = $productoModel->where('categoria_id', 1)
            ->where('eliminado !=', 'SI')
            ->orderBy('id', 'DESC')
            ->limit(4)
            ->findAll();
        $data = [
        'destacadosPesca' => $destacadosPesca,
        'ofertasCamping' => $ofertasCamping
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

    // Método para depuración de compras - Solo en entorno de desarrollo
    public function verCompra($id = null)
    {
        // Verificar que estamos en entorno de desarrollo
        if (ENVIRONMENT !== 'development') {
            return redirect()->to(base_url());
        }
        
        // Asegurarse de que el ID sea numérico
        if (!is_numeric($id)) {
            echo "El ID debe ser numérico";
            return;
        }
        
        $db = \Config\Database::connect();
        
        // Verificar la existencia de la compra
        $cabecera = $db->query("SELECT * FROM ventas_cabecera WHERE id = ?", [$id])->getRowArray();
        if (!$cabecera) {
            echo "<h2>No existe una compra con el ID: $id</h2>";
            return;
        }
        
        // Obtener los detalles de la compra
        $sql = "SELECT 
            vd.*, 
            vc.id as cabecera_id, vc.fecha, vc.total_venta, vc.usuario_id,
            p.nombre_prod, p.descripcion, p.imagen, p.precio_vta,
            u.nombre, u.apellido
        FROM 
            ventas_detalle vd
        INNER JOIN 
            ventas_cabecera vc ON vc.id = vd.venta_id
        INNER JOIN 
            productos p ON p.id = vd.producto_id
        INNER JOIN 
            usuarios u ON u.id = vc.usuario_id
        WHERE 
            vd.venta_id = ?";
        
        $detalles = $db->query($sql, [$id])->getResultArray();
        
        $data = [
            'cabecera' => $cabecera,
            'detalles' => $detalles
        ];
        
        // Mostrar la vista de depuración
        echo view('debug_compra', $data);
    }

}