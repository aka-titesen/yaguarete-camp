<?php

namespace App\Controllers;
use App\Models\ProductoModel;

class Home extends BaseController
{
    protected $productoModel;
    public function __construct()
    {
        $this->productoModel = new ProductoModel();
    }
    public function index(): void
    {
        // Traer productos de la categoría Pesca (id=7), solo activos
        $destacadosPesca = $this->productoModel->where('categoria_id', 7)
            ->where('eliminado !=', 'SI')
            ->orderBy('id', 'DESC')
            ->limit(6)
            ->findAll();
        $data = [
            'destacadosPesca' => $destacadosPesca
        ];
        // Ofertas relámpago: productos de Camping (id=8)
        $ofertasCamping = $this->productoModel->where('categoria_id', 8)
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

    public function sobreNosotros(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/sobre_nosotros");
        echo view("front/layouts/footer");
    }

    public function termYCondiciones(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/term_y_condiciones");
        echo view("front/layouts/footer");
    }

    public function aComercializacion(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/comercializacion");
        echo view("front/layouts/footer");
    }

    public function aContacto(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/contacto");
        echo view("front/layouts/footer");
    }    public function aProducto(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/producto");
        echo view("front/layouts/footer");
    }

    public function aDetalleProducto($id): void
    {
        $producto = $this->productoModel->where('id', $id)->first();
        
        if (!$producto || $producto['eliminado'] === 'SI') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        // Obtener productos relacionados (misma categoría, excluyendo el actual)
        $relacionados = $this->productoModel->getRelacionados($producto['categoria_id'], $producto['id']);
        $data = [
            'producto' => $producto,
            'relacionados' => $relacionados
        ];
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/detalle_producto", $data);
        echo view("front/layouts/footer");
    }

    
    public function aDashboard(): void
    {
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/dashboard");
        echo view("front/layouts/footer");
    }

    public function aAdministrarProductos(): void
    {
        $session = session();
        if ($session->get('perfil_id') != 1) {
            // Si no es admin (perfil_id = 1), no puede ver la administración
            redirect()->to(base_url())->with('mensaje', 'Acceso restringido solo para administradores.');
            exit();
        }
        echo view("front/layouts/header");
        echo view("front/layouts/navbar");
        echo view("front/administrarProductos");
        echo view("front/layouts/footer");
    }

    public function aCatalogoProductos(): void
    {
        $session = session();
        if ($session->get('perfil_id') == 1) {
            // Si es admin (perfil_id = 1), mostrar mensaje descriptivo
            echo view('front/layouts/header');
            echo view('front/layouts/navbar');
            echo '<div class="container mt-5 pt-5"><div class="alert alert-warning text-center mt-5"><h3>Acceso restringido</h3><p>El administrador no puede acceder al catálogo de productos.</p></div></div>';
            echo view('front/layouts/footer');
            exit();
        }
        $productos = $this->productoModel->getProductoAll();
        $data = ['productos' => $productos];
        echo view('front/layouts/header');
        echo view('front/layouts/navbar');
        echo view('front/catalogo_productos', $data);
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
        
        // Mostrar la información de debug
        echo "<h2>Debug de Compra - ID: $id</h2>";
        echo "<h3>Cabecera:</h3>";
        echo "<pre>" . print_r($cabecera, true) . "</pre>";
        echo "<h3>Detalles:</h3>";
        echo "<pre>" . print_r($detalles, true) . "</pre>";
    }

}