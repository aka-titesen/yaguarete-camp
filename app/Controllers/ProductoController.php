<?php
namespace App\Controllers;

use App\Models\ProductoModel;
use App\Models\CategoriasModel;
use CodeIgniter\Controller;

class ProductoController extends Controller
{
    protected $productoModel;
    protected $categoriasModel;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->productoModel = new ProductoModel();
        $this->categoriasModel = new CategoriasModel();
    }

    // Mostrar los productos en lista
    public function index()
    {
        // Solo productos no eliminados
        $productos = $this->productoModel->findAll(); // Trae todos, activos y eliminados
        $data = [
            'titulo' => 'Gestión de Productos',
            'productos' => $productos
        ];
        echo view('front/layouts/header');
        echo view('front/layouts/navbar');
        echo view('front/administrar_productos', $data);
        echo view('front/layouts/footer');
    }

    public function creaProducto()
    {
        $data['categorias'] = $this->categoriasModel->getCategorias(); // traer las categorías desde la db
        $data['producto'] = $this->productoModel->getProductoAll();
        // Aquí puedes agregar la lógica para mostrar la vista de creación de producto
        // echo view(...);
    }

    public function store()
    {
        $nombre_prod = trim($this->request->getVar('nombre_prod'));
        $categoria_id = $this->request->getVar('categoria_id');
        $precio = $this->request->getVar('precio');
        $precio_vta = $this->request->getVar('precio_vta');
        $stock = $this->request->getVar('stock');
        $stock_min = $this->request->getVar('stock_min');
        $img = $this->request->getFile('imagen');
        $errores = [];

        // Validación de unicidad de nombre
        if ($this->productoModel->where('nombre_prod', $nombre_prod)->first()) {
            $errores[] = 'Ya existe un producto con ese nombre.';
        }
        // Validación de campos obligatorios y reglas
        if (strlen($nombre_prod) < 3) {
            $errores[] = 'El nombre debe tener al menos 3 caracteres.';
        }
        if (!$categoria_id) {
            $errores[] = 'Debe seleccionar una categoría.';
        }
        if (!is_numeric($precio) || $precio < 0) {
            $errores[] = 'El precio de compra debe ser un número mayor o igual a 0.';
        }
        if (!is_numeric($precio_vta) || $precio_vta < 0) {
            $errores[] = 'El precio de venta debe ser un número mayor o igual a 0.';
        }
        if ($precio_vta < $precio) {
            $errores[] = 'El precio de venta debe ser mayor o igual al de compra.';
        }
        if (!is_numeric($stock) || $stock < 0) {
            $errores[] = 'El stock debe ser un número mayor o igual a 0.';
        }
        if (!is_numeric($stock_min) || $stock_min < 0) {
            $errores[] = 'El stock mínimo debe ser un número mayor o igual a 0.';
        }
        if ($stock_min > $stock) {
            $errores[] = 'El stock mínimo no puede ser mayor al stock actual.';
        }
        // Validación de imagen
        if (!$img->isValid()) {
            $errores[] = 'Debe subir una imagen válida.';
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($img->getMimeType(), $allowedTypes)) {
                $errores[] = 'La imagen debe ser JPG, PNG, GIF o WEBP.';
            }
            if ($img->getSize() > 2*1024*1024) { // 2MB
                $errores[] = 'La imagen no debe superar los 2MB.';
            }
        }
        if (count($errores) > 0) {
            $data['categorias'] = $this->categoriasModel->getCategorias();
            $data['errores'] = $errores;
            $data['old'] = $this->request->getPost();
            echo view('front/administrar_productos', $data);
            return;
        }
        // Procesar imagen y guardar producto SOLO si la validación es exitosa
        $nombre_aleatorio = $img->getRandomName();
        $img->move(ROOTPATH . 'assets/uploads/', $nombre_aleatorio);
        $data = [
            'nombre_prod' => $nombre_prod,
            'imagen' => $nombre_aleatorio,
            'categoria_id' => $categoria_id,
            'precio' => $precio,
            'precio_vta' => $precio_vta,
            'stock' => $stock,
            'stock_min' => $stock_min,
        ];
        $this->productoModel->insert($data);
        session()->setFlashdata('success', 'Producto creado exitosamente.');
        return $this->response->redirect(site_url('administrar_productos'));
    }

    public function modifica($id)
    {
        $producto = $this->productoModel->where('id', $id)->first();
        $nombre_prod = trim($this->request->getVar('nombre_prod'));
        $categoria_id = $this->request->getVar('categoria_id');
        $precio = $this->request->getVar('precio');
        $precio_vta = $this->request->getVar('precio_vta');
        $stock = $this->request->getVar('stock');
        $stock_min = $this->request->getVar('stock_min');
        $img = $this->request->getFile('imagen');
        $errores = [];
        // Validación de unicidad de nombre (excepto el propio)
        $existe = $this->productoModel->where('nombre_prod', $nombre_prod)->where('id !=', $id)->first();
        if ($existe) {
            $errores[] = 'Ya existe un producto con ese nombre.';
        }
        if (strlen($nombre_prod) < 3) {
            $errores[] = 'El nombre debe tener al menos 3 caracteres.';
        }
        if (!$categoria_id) {
            $errores[] = 'Debe seleccionar una categoría.';
        }
        if (!is_numeric($precio) || $precio < 0) {
            $errores[] = 'El precio de compra debe ser un número mayor o igual a 0.';
        }
        if (!is_numeric($precio_vta) || $precio_vta < 0) {
            $errores[] = 'El precio de venta debe ser un número mayor o igual a 0.';
        }
        if ($precio_vta < $precio) {
            $errores[] = 'El precio de venta debe ser mayor o igual al de compra.';
        }
        if (!is_numeric($stock) || $stock < 0) {
            $errores[] = 'El stock debe ser un número mayor o igual a 0.';
        }
        if (!is_numeric($stock_min) || $stock_min < 0) {
            $errores[] = 'El stock mínimo debe ser un número mayor o igual a 0.';
        }
        if ($stock_min > $stock) {
            $errores[] = 'El stock mínimo no puede ser mayor al stock actual.';
        }
        // Validación de imagen (opcional)
        if ($img && $img->isValid() && $img->getName() != '') {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($img->getMimeType(), $allowedTypes)) {
                $errores[] = 'La imagen debe ser JPG, PNG, GIF o WEBP.';
            }
            if ($img->getSize() > 2*1024*1024) {
                $errores[] = 'La imagen no debe superar los 2MB.';
            }
        }
        if (count($errores) > 0) {
            $data['categorias'] = $this->categoriasModel->getCategorias();
            $data['errores'] = $errores;
            $data['old'] = $this->request->getPost();
            $data['edit_id'] = $id;
            echo view('front/administrar_productos', $data);
            return;
        }
        // Procesar imagen si se subió una nueva
        if ($img && $img->isValid() && $img->getName() != '') {
            $nombre_aleatorio = $img->getRandomName();
            $img->move(ROOTPATH . 'assets/uploads/', $nombre_aleatorio);
            $data = [
                'nombre_prod' => $nombre_prod,
                'imagen' => $nombre_aleatorio,
                'categoria_id' => $categoria_id,
                'precio' => $precio,
                'precio_vta' => $precio_vta,
                'stock' => $stock,
                'stock_min' => $stock_min,
            ];
        } else {
            $data = [
                'nombre_prod' => $nombre_prod,
                'categoria_id' => $categoria_id,
                'precio' => $precio,
                'precio_vta' => $precio_vta,
                'stock' => $stock,
                'stock_min' => $stock_min,
            ];
        }
        $this->productoModel->update($id, $data);
        session()->setFlashdata('success', 'Modificación exitosa.');
        return $this->response->redirect(site_url('administrar_productos'));
    }

    // Eliminar (dar de baja) producto
    public function deleteProducto($id)
    {
        $producto = $this->productoModel->where('id', $id)->first();
        if ($producto) {
            $producto['eliminado'] = 'SI';
            $this->productoModel->update($id, $producto);
        }
        return $this->response->redirect(site_url('administrar_productos'));
    }

    // Reactivar producto dado de baja
    public function activarProducto($id)
    {
        $producto = $this->productoModel->where('id', $id)->first();
        if ($producto) {
            $producto['eliminado'] = 'NO';
            $this->productoModel->update($id, $producto);
        }
        session()->setFlashdata('success', 'Activación Exitosa...');
        return $this->response->redirect(site_url('administrar_productos'));
    }

    // Mostrar productos eliminados
    public function eliminados()
    {
        $data['producto'] = $this->productoModel->getProductoAll();
        $data['titulo'] = 'Crud_productos';
        echo view('front/head_view_crud', $data);
        echo view('front/nav_view');
        echo view('back/productos/producto_eliminado', $data);
        echo view('front/footer_view');
    }
}