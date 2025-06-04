<?php
namespace App\Controllers;

use App\Models\Producto_model;
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
        $productoModel = new Producto_model();
        // Solo productos no eliminados
        $productos = $productoModel->findAll(); // Trae todos, activos y eliminados
        $data = [
            'titulo' => 'Gestión de Productos',
            'productos' => $productos
        ];
        echo view('front/layouts/header');
        echo view('front/layouts/navbar');
        echo view('front/administrarProductos', $data);
        echo view('front/layouts/footer');
    }

    public function creaproducto()
    {
        $categoriasModel = new Categorias_model();
        $data['categorias'] = $categoriasModel->getCategorias(); // traer las categorías desde la db

        $productoModel = new Producto_model();
        $data['producto'] = $productoModel->getProductoAll();

        // Aquí puedes agregar la lógica para mostrar la vista de creación de producto
        // echo view(...);
    }

    public function store()
    {
        // Construimos las reglas de validación
        $input = $this->validate([
            'nombre_prod' => 'required|min_length[3]',
            'categoria_id' => 'is_not_unique[categorias.id]',
            'precio' => 'required|numeric',
            'precio_vta' => 'required|numeric',
            'stock' => 'required|numeric',
            'stock_min' => 'required|numeric',
            'imagen' => 'uploaded[imagen]'
        ]);

        $productoModel = new Producto_model(); // Se instancia el modelo

        if (!$input) {
            $categoria_model = new Categorias_model();
            $data['categorias'] = $categoria_model->getCategorias();
            $data['validation'] = $this->validator;
            $data['titulo'] = 'Alta';
            echo view('front/head_view', $data);
            echo view('front/nav_view');
            echo view('back/productos/alta_producto_view', $data);
            echo view('front/footer_view');
            return;
        }

        // Procesar imagen y guardar producto SOLO si la validación es exitosa
        $img = $this->request->getFile('imagen');
        $nombre_aleatorio = $img->getRandomName();
        $img->move(ROOTPATH . 'assets/uploads/', $nombre_aleatorio);

        $data = [
            'nombre_prod' => $this->request->getVar('nombre_prod'),
            'imagen' => $nombre_aleatorio, // Guardar el nombre aleatorio generado
            'categoria_id' => $this->request->getVar('categoria_id'),
            'precio' => $this->request->getVar('precio'),
            'precio_vta' => $this->request->getVar('precio_vta'),
            'stock' => $this->request->getVar('stock'),
            'stock_min' => $this->request->getVar('stock_min'),
            // 'eliminado' => 'NO' // Si tu tabla lo requiere
        ];

        $productoModel = new Producto_model();
        $productoModel->insert($data);
        return $this->response->redirect(site_url('administrarProductos'));
    }

    public function modifica($id)
    {
        $productoModel = new Producto_model();
        $producto = $productoModel->where('id', $id)->first();
        $img = $this->request->getFile('imagen');

        if ($img && $img->isValid()) {
            // Se cargó una imagen válida correctamente
            $nombre_aleatorio = $img->getRandomName();
            $img->move(ROOTPATH . 'assets/uploads/', $nombre_aleatorio);
            $data = [
                'nombre_prod' => $this->request->getVar('nombre_prod'),
                'imagen' => $img->getName(),
                'categoria_id' => $this->request->getVar('categoria_id'),
                'precio' => $this->request->getVar('precio'),
                'precio_vta' => $this->request->getVar('precio_vta'),
                'stock' => $this->request->getVar('stock'),
                'stock_min' => $this->request->getVar('stock_min'),
                // 'eliminado' => 'NO',
            ];
        } else {
            // No se cargó una nueva imagen, solo actualiza los datos del producto sin sobrescribir la imagen
            $data = [
                'nombre_prod' => $this->request->getVar('nombre_prod'),
                'categoria_id' => $this->request->getVar('categoria_id'),
                'precio' => $this->request->getVar('precio'),
                'precio_vta' => $this->request->getVar('precio_vta'),
                'stock' => $this->request->getVar('stock'),
                'stock_min' => $this->request->getVar('stock_min'),
                // 'eliminado' => 'NO',
            ];
        }

        $productoModel->update($id, $data);
        session()->setFlashdata('success', 'Modificación Exitosa...');
        return $this->response->redirect(site_url('administrarProductos'));
    }

    // Eliminar (dar de baja) producto
    public function deleteproducto($id)
    {
        $productoModel = new Producto_model();
        $producto = $productoModel->where('id', $id)->first();
        if ($producto) {
            $producto['eliminado'] = 'SI';
            $productoModel->update($id, $producto);
        }
        return $this->response->redirect(site_url('administrarProductos'));
    }

    // Reactivar producto dado de baja
    public function activarproducto($id)
    {
        $productoModel = new Producto_model();
        $producto = $productoModel->where('id', $id)->first();
        if ($producto) {
            $producto['eliminado'] = 'NO';
            $productoModel->update($id, $producto);
        }
        session()->setFlashdata('success', 'Activación Exitosa...');
        return $this->response->redirect(site_url('administrarProductos'));
    }

    // Mostrar productos eliminados
    public function eliminados()
    {
        $productoModel = new Producto_model();
        $data['producto'] = $productoModel->getProductoAll();
        $data['titulo'] = 'Crud_productos';
        echo view('front/head_view_crud', $data);
        echo view('front/nav_view');
        echo view('back/productos/producto_eliminado', $data);
        echo view('front/footer_view');
    }
}