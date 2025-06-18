<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\Producto_model;
use App\Models\Usuarios_model;
use App\Models\VentasCabeceraModel;
use App\Models\VentasDetalleModel;

class Ventascontroller extends Controller{    public function registrar_venta()    {
        $session = session();
        // Instanciar correctamente los controladores y modelos
        $cartController = new \App\Controllers\CarritoController();
        $productoModel = new Producto_model();
        $ventasModel = new VentasCabeceraModel();
        $detalleModel = new VentasDetalleModel();        $carrito_contents = $cartController->devolver_carrito(true); // Pasar true para obtener el array directamente
        
        // Verificar si obtenemos correctamente los contenidos del carrito
        if (empty($carrito_contents)) {
            $session->setFlashdata('mensaje', 'Su carrito está vacío. Por favor agregue productos antes de completar la compra.');
            return redirect()->to(base_url('muestro'));
        }

        $productos_validos = [];
        $productos_sin_stock = [];
        $total = 0;

        // Validar stock y filtrar productos válidos
        foreach ($carrito_contents as $item) {
            $producto = $productoModel->getProducto($item['id']);
            if ($producto && $producto['stock'] >= $item['qty']) {
                $productos_validos[] = $item;
                $total += $item['subtotal'];
            } else {
                $productos_sin_stock[] = $item['name'];
                // Eliminar del carrito el producto sin stock
                $cartController->eliminar_item($item['rowid']);
            }
        }

        // Si hay productos sin stock, avisar y volver al carrito
        if (!empty($productos_sin_stock)) {
            $mensaje = 'Los siguientes productos no tienen stock suficiente y fueron eliminados del carrito: <br>' . implode(', ', $productos_sin_stock);
            $session->setFlashdata('mensaje', $mensaje);
            return redirect()->to(base_url('muestro'));
        }

        // Si no hay productos válidos, no se registra la venta
        if (empty($productos_validos)) {
            $session->setFlashdata('mensaje', 'No hay productos válidos para registrar la venta.');
            return redirect()->to(base_url('muestro'));
        }        // Registrar cabecera de la venta
        $usuario_id = $session->get('id');
        if (!$usuario_id) {
            $usuario_id = $session->get('id_usuario');
        }
        
        if (!$usuario_id) {
            $session->setFlashdata('mensaje', 'Error: No se pudo identificar el usuario. Por favor inicie sesión nuevamente.');
            return redirect()->to(base_url('muestro'));
        }
        
        $nueva_venta = [
            'usuario_id' => $usuario_id,
            'total_venta' => $total
        ];
        $venta_id = $ventasModel->insert($nueva_venta);

        // Registrar detalle y actualizar stock
        foreach ($productos_validos as $item) {
            $detalle = [
                'venta_id' => $venta_id,
                'producto_id' => $item['id'],
                'cantidad' => $item['qty'],
                'precio' => $item['subtotal']
            ];
            $detalleModel->insert($detalle);

            $producto = $productoModel->getProducto($item['id']);
            $productoModel->updateStock($item['id'], $producto['stock'] - $item['qty']);
        }        // Vaciar carrito y mostrar confirmación
        $cartController->borrar_carrito();
        $session->setFlashdata('mensaje', 'Compra registrada exitosamente.');
        return redirect()->to(base_url('detalle-compra/' . $venta_id));
    }    // Función del usuario cliente para ver sus compras
    public function ver_factura($venta_id)
    {
        $session = session();
        $usuario_id = $session->get('id') ?: $session->get('id_usuario');
        if (!$usuario_id) {
            $session->setFlashdata('mensaje', 'Error: No se pudo identificar el usuario. Por favor inicie sesión nuevamente.');
            return redirect()->to(base_url('login'));
        }
        // Validar que la compra pertenezca al usuario
        $cabeceraModel = new \App\Models\VentasCabeceraModel();
        $cabecera = $cabeceraModel->where('id', $venta_id)->where('usuario_id', $usuario_id)->first();
        if (!$cabecera) {
            $session->setFlashdata('mensaje', 'No tiene permiso para ver esta compra o la compra no existe.');
            return redirect()->to(base_url('mis-compras'));
        }
        $detalle_ventas = new \App\Models\VentasDetalleModel();
        $detalles = $detalle_ventas->where('venta_id', $venta_id)->findAll();
        // Si no hay detalles, mostrar mensaje claro
        if (empty($detalles)) {
            $session->setFlashdata('mensaje', 'Esta compra no tiene productos asociados.');
            return redirect()->to(base_url('mis-compras'));
        }
        // Obtener datos de producto para cada detalle
        $productoModel = new \App\Models\Producto_model();
        foreach ($detalles as &$detalle) {
            $producto = $productoModel->find($detalle['producto_id']);
            $detalle['nombre_prod'] = $producto['nombre_prod'] ?? 'Producto sin nombre';
            $detalle['imagen'] = $producto['imagen'] ?? '';
            $detalle['precio_vta'] = $producto['precio_vta'] ?? $detalle['precio'];
            $detalle['descripcion'] = $producto['descripcion'] ?? '';
        }
        unset($detalle);
        $data['venta'] = $detalles;
        $data['cabecera'] = $cabecera;
        $dato['titulo'] = "Mi compra";
        echo view('front/layouts/header', $dato);
        echo view('front/layouts/navbar');
        echo view('front/vista_compras', $data);
        echo view('front/layouts/footer');
    }    // Función del cliente para ver el detalle de sus facturas de compras
    public function ver_facturas_usuario($id_usuario)
    {
        $ventas = new VentasCabeceraModel();
        $data['ventas'] = $ventas->getVentas($id_usuario);
        $dato['titulo'] = "Todos mis compras";
        echo view('front/layouts/header', $dato);
        echo view('front/layouts/navbar');
        echo view('front/mis_compras', $data); // Usar la vista de cliente
        echo view('front/layouts/footer');
    }
    public function ventas () {
        $venta_id = $this->request->getGet('id');
        $detalle_ventas = new VentasDetalleModel();
        $data['venta'] = $detalle_ventas->getDetalles($venta_id);
        $ventascabecera = new VentasCabeceraModel();
        $data['usuarios']=$ventascabecera->getBuilderVentas_cabecera();        $dato['titulo'] = "ventas";
        echo view("front/layouts/header", $dato);
        echo view("front/layouts/navbar");
        echo view("front/vista_compras", $data);
        echo view("front/layouts/footer");
    }    /**
     * Función del administrador para ver todas las ventas
     */
    public function administrar_ventas()
    {
        // Obtener parámetros de filtro
        $fecha_desde = $this->request->getGet('fecha_desde');
        $fecha_hasta = $this->request->getGet('fecha_hasta');
        $cliente_id = $this->request->getGet('cliente');
        $monto_min = $this->request->getGet('monto_min');
        $monto_max = $this->request->getGet('monto_max');
        
        // Guardar filtros para el formulario
        $filtros = [
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'cliente' => $cliente_id,
            'monto_min' => $monto_min,
            'monto_max' => $monto_max
        ];
        
        $ventasModel = new VentasCabeceraModel();
        
        // Si hay filtros activos, usar la función filtrada
        if ($fecha_desde || $fecha_hasta || $cliente_id || $monto_min || $monto_max) {
            $data['ventas'] = $ventasModel->getVentasFiltradas($fecha_desde, $fecha_hasta, $cliente_id, $monto_min, $monto_max);
        } else {
            $data['ventas'] = $ventasModel->getBuilderVentas_cabecera();
        }
        
        // Obtener lista de clientes para el filtro
        $db = \Config\Database::connect();
        $builder = $db->table('usuarios');
        $builder->where('perfil_id !=', 2); // Excluir administradores
        $query = $builder->get();
        $data['clientes'] = $query->getResultArray();
        
        // Pasar los filtros a la vista
        $data['filtros'] = $filtros;
        
        $dato['titulo'] = "Administración de Ventas";
        
        echo view('front/layouts/header', $dato);
        echo view('front/layouts/navbar');
        echo view('front/admin_ventas', $data);
        echo view('front/layouts/footer');
    }
      /**
     * Función del administrador para ver el detalle de una venta
     */
    public function detalle_venta($venta_id)
    {
        $detalle_ventas = new VentasDetalleModel();
        $data['venta'] = $detalle_ventas->getDetalles($venta_id);
        $dato['titulo'] = "Detalle de Venta";
        
        echo view('front/layouts/header', $dato);
        echo view('front/layouts/navbar');
        echo view('front/vista_compras', $data); // Reutilizamos la misma vista que para los clientes
        echo view('front/layouts/footer');
    }
    
    /**
     * Muestra el historial de compras del cliente actual
     */
    public function mis_compras()
    {
        $session = session();
        $usuario_id = $session->get('id');
        if (!$usuario_id) {
            $usuario_id = $session->get('id_usuario');
        }
        
        if (!$usuario_id) {
            $session->setFlashdata('mensaje', 'Error: No se pudo identificar el usuario. Por favor inicie sesión nuevamente.');
            return redirect()->to(base_url());
        }
        
        $ventasModel = new VentasCabeceraModel();
        $data['compras'] = $ventasModel->getVentas($usuario_id);
        $dato['titulo'] = "Mis compras";
        
        echo view('front/layouts/header', $dato);
        echo view('front/layouts/navbar');
        echo view('front/mis_compras', $data);
        echo view('front/layouts/footer');
    }
    /**
     * Muestra el detalle de una compra específica
     */    public function detalle_compra($venta_id)
    {
        $session = session();
        $usuario_id = $session->get('id');
        if (!$usuario_id) {
            $usuario_id = $session->get('id_usuario');
        }
        if (!$usuario_id) {
            $session->setFlashdata('mensaje', 'Error: No se pudo identificar el usuario. Por favor inicie sesión nuevamente.');
            return redirect()->to(base_url());
        }
        // Validar que $venta_id sea numérico
        if (!is_numeric($venta_id)) {
            $session->setFlashdata('mensaje', 'Error: ID de compra inválido.');
            return redirect()->to(base_url('mis-compras'));
        }
        try {
            // 1. Verificar que la compra exista y pertenezca al usuario
            $ventasCabecera = new \App\Models\VentasCabeceraModel();
            $venta_cabecera = $ventasCabecera->where('id', $venta_id)
                ->where('usuario_id', $usuario_id)
                ->first();
            if (!$venta_cabecera) {
                $session->setFlashdata('mensaje', 'No tiene permiso para ver esta compra o la compra no existe.');
                return redirect()->to(base_url('mis-compras'));
            }
            // 2. Obtener detalles usando el modelo (más seguro)
            $detalle_ventas = new \App\Models\VentasDetalleModel();
            $data['venta'] = $detalle_ventas->getDetalles($venta_id);
            if (empty($data['venta'])) {
                $session->setFlashdata('mensaje', 'No se encontraron detalles para esta compra.');
                return redirect()->to(base_url('mis-compras'));
            }
            $data['cabecera'] = $venta_cabecera;
            $dato['titulo'] = "Detalle de compra";
            echo view('front/layouts/header', $dato);
            echo view('front/layouts/navbar');
            echo view('front/vista_compras', $data);
            echo view('front/layouts/footer');
        } catch (\Exception $e) {
            log_message('error', "Error al obtener detalle de compra ID $venta_id: " . $e->getMessage());
            $session->setFlashdata('mensaje', 'Error al recuperar los detalles de la compra. Por favor intente nuevamente.');
            return redirect()->to(base_url('mis-compras'));
        }
    }
    
    /**
     * Método de diagnóstico para analizar problemas con una venta específica
     */
    public function diagnosticar($venta_id)
    {
        // Validar que venta_id sea numérico
        if (!is_numeric($venta_id)) {
            echo "Error: ID de venta inválido";
            return;
        }
        
        // Verificar que el usuario tenga permisos (debe ser administrador)
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('perfil_id') != 2) {
            echo "Error: No tiene permisos para acceder a este diagnóstico";
            return;
        }
        
        // Redirigir al script de diagnóstico
        return redirect()->to(base_url('diagnostico_detalle_compra.php?id=' . $venta_id));
    }
}
