<?php
namespace App\Controllers;

use App\Models\Productos_model;
use App\Models\Categorias_model;
use CodeIgniter\Controller;

class ProductoController extends Controller
{
    public function __construct()
    {
        helper(['url', 'form']);
        $session = session();
    }

    // Mostrar los productos en lista
    public function index()
    {
        $productoModel = new Productos_model();
        $productos = $productoModel->getProductoAll(); // función en el modelo

        $data = [
            'titulo' => 'Gestión de Productos',
            'productos' => $productos
        ];
        echo view('front/administrarProductos', $data);
    }

    public function creaproducto()
    {
        $categoriasModel = new Categorias_model();
        $data['categorias'] = $categoriasModel->getCategorias(); // traer las categorías desde la db

        $productoModel = new Productos_model();
        $data['producto'] = $productoModel->getProductoAll();

        // Aquí puedes agregar la lógica para mostrar la vista de creación de producto
        // echo view(...);
    }

    public function store()
    {
        // Construimos las reglas de validación
        $input = $this->validate([
            'nombre_prod' => 'required|min_length[3]',
            'categoria'   => 'is_not_unique[categorias.id]',
            'precio'      => 'required|numeric',
            'precio_vta'  => 'required|numeric',
            'stock'       => 'required|numeric',
            'stock_min'   => 'required|numeric',
            'imagen'      => 'uploaded[imagen]'
        ]);

        $productoModel = new Productos_model(); // Se instancia el modelo

        if ($input) {
            $categoria_model = new Categorias_model();
            $data['categorias'] = $categoria_model->getCategorias();
            $data['validation'] = $this->validator;

            $data['titulo'] = 'Alta';
            echo view('front/head_view', $data);
            echo view('front/nav_view');
            echo view('back/productos/alta_producto_view', $data);
            echo view('front/footer_view');
        } else {
            // Aquí puedes manejar el caso de validación fallida
            $categoria_model = new Categorias_model();
            $data['categorias'] = $categoria_model->getCategorias();
            $data['validation'] = $this->validator;

            $data['titulo'] = 'Alta';
            echo view('front/head_view', $data);
            echo view('front/nav_view');
            echo view('back/productos/alta_producto_view', $data);
            echo view('front/footer_view');
        }

        // Procesar imagen y guardar producto
        $img = $this->request->getFile('imagen');
        $nombre_aleatorio = $img->getRandomName();
        $img->move(ROOTPATH . 'assets/uploads/', $nombre_aleatorio);

        $data = [
            'nombre_prod'  => $this->request->getVar('nombre_prod'),
            'imagen'       => $nombre_aleatorio, // Guardar el nombre aleatorio generado
            'categoria_id' => $this->request->getVar('categoria'),
            'precio'       => $this->request->getVar('precio'),
            'precio_vta'   => $this->request->getVar('precio_vta'),
            'stock'        => $this->request->getVar('stock'),
            'stock_min'    => $this->request->getVar('stock_min'),
            // 'eliminado' => 'NO' // Si tu tabla lo requiere
        ];

        $productoModel = new Productos_model();
        $productoModel->insert($data);
        session()->setFlashdata('success', 'Alta Exitosa...');
        return $this->response->redirect(site_url('crear'));
    }
}