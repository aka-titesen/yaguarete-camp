<?php
$session = session();
$perfil_id = $session->get('perfil_id'); // 2 = admin, otro = cliente
$fecha_compra = '';
$nro_orden = '';
$total_venta = null;
$nombre_cliente = '';
$email_cliente = '';
if (isset($cabecera) && is_array($cabecera)) {
    $fecha_compra = $cabecera['fecha'] ?? '';
    $nro_orden = $cabecera['id'] ?? '';
    $total_venta = $cabecera['total_venta'] ?? null;
    $nombre_cliente = trim(($cabecera['nombre'] ?? '') . ' ' . ($cabecera['apellido'] ?? ''));
    $email_cliente = $cabecera['email'] ?? '';
}
$productos = isset($detalles) && is_array($detalles) && count($detalles) > 0 ? $detalles : [];
?>
<div class="container mt-5 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?= base_url($perfil_id == 2 ? 'admin-ventas' : 'mis-compras') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Volver a <?= $perfil_id == 2 ? 'ventas' : 'mis compras' ?>
        </a>
        <?php if (!empty($fecha_compra)): ?>
        <div class="text-muted">
            <i class="fas fa-calendar-alt me-2"></i> 
            <?= $fecha_compra ? date('d/m/Y', strtotime($fecha_compra)) : '-' ?>
        </div>
        <?php endif; ?>
    </div>
    <!-- Mostrar mensaje Flash si existe -->
    <?php if (session()->getFlashdata('mensaje')): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <?= session()->getFlashdata('mensaje') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <?php 
    // Eliminar logs y referencias inseguras a $detalles
    
    if (empty($productos)) { 
    ?>
        <!-- Estado vacío - Sin detalles de compra -->
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list fa-4x mb-4 text-muted"></i>
                <h3 class="mb-3">No se encontró información de esta compra</h3>
                <p class="text-muted mb-4">Es posible que la compra no exista o no tenga productos asociados.</p>
                <a class="btn btn-verde-selva" href="<?= base_url('mis-compras') ?>">
                    <i class="fas fa-arrow-left me-2"></i> Volver a mis compras
                </a>
                <a class="btn btn-outline-primary ms-2" href="<?= base_url('catalogo') ?>">
                    <i class="fas fa-store me-2"></i> Explorar catálogo
                </a>
            </div>
        </div>
    <?php } else { ?>
    
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-verde-selva text-white d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Detalle de <?= $perfil_id == 2 ? 'venta' : 'compra' ?> #<?= $nro_orden ?>
                </h2>
                <span class="badge bg-light text-dark">Completada</span>
            </div>
            
            <div class="card-body">
                <!-- Información de la factura -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="text-muted mb-2">Información de la <?= $perfil_id == 2 ? 'venta' : 'compra' ?></h5>
                            <div class="d-flex mb-2">
                                <div class="me-3 text-muted" style="width: 120px;">
                                    <i class="fas fa-receipt me-2"></i> Orden:
                                </div>
                                <strong>#<?= $nro_orden ?></strong>
                            </div>
                            <div class="d-flex mb-2">
                                <div class="me-3 text-muted" style="width: 120px;">
                                    <i class="fas fa-calendar-alt me-2"></i> Fecha:
                                </div>
                                <strong><?= $fecha_compra ? date('d/m/Y', strtotime($fecha_compra)) : '-' ?></strong>
                            </div>                                <!-- No mostramos la hora porque el campo solo almacena fecha -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="text-muted mb-2">Cliente</h5>
                            <div class="d-flex mb-2">
                                <div class="me-3 text-muted" style="width: 120px;">
                                    <i class="fas fa-user me-2"></i> Nombre:
                                </div>
                                <strong><?= esc($nombre_cliente) ?></strong>
                            </div>
                            <div class="d-flex">
                                <div class="me-3 text-muted" style="width: 120px;">
                                    <i class="fas fa-envelope me-2"></i> Email:
                                </div>
                                <strong><?= esc($email_cliente) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Productos comprados -->
                <h5 class="border-bottom pb-2 mb-3">Productos adquiridos</h5>
                
                <div class="list-group mb-4">
                    <?php
                    $total = 0;
                    foreach ($productos as $i => $row) {
                        // Nombre del producto
                        $nombre = isset($row['nombre_prod']) && $row['nombre_prod'] ? $row['nombre_prod'] : 'Producto';
                        // Imagen del producto
                        $imagen = isset($row['imagen']) && $row['imagen'] ? $row['imagen'] : '';
                        $ruta_imagen = $imagen ? 'assets/uploads/' . $imagen : 'assets/img/producto-sin-imagen.jpg';
                        // Precio del producto
                        $precio = isset($row['precio']) && is_numeric($row['precio']) ? floatval($row['precio']) : (isset($row['precio_vta']) && is_numeric($row['precio_vta']) ? floatval($row['precio_vta']) : 0);
                        // Cantidad
                        $cantidad = isset($row['cantidad']) && is_numeric($row['cantidad']) ? intval($row['cantidad']) : 1;
                        $subtotal = $precio * $cantidad;
                        $total += $subtotal;
                        $categoria = 'Producto';
                    ?>
                    <div class="list-group-item list-group-item-action flex-column align-items-start p-3 border mb-2 rounded shadow-sm">
                        <div class="d-flex w-100">
                            <div class="me-3 product-image-container">
                                <img class="product-image rounded shadow-sm" src="<?= base_url($ruta_imagen) ?>" alt="<?= esc($nombre) ?>" onerror="this.src='<?= base_url('assets/img/producto-sin-imagen.jpg') ?>'" style="width: 120px; height: 90px; object-fit: cover;">
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-top mb-1">
                                    <h5 class="mb-0"><?= esc($nombre) ?></h5>
                                    <span class="text-success fw-bold">$<?= number_format($subtotal, 2, ',', '.') ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center small">
                                    <div>
                                        <span class="badge bg-light text-dark border">Producto</span>
                                    </div>
                                    <div>
                                        <span class="text-muted me-3">
                                            <i class="fas fa-box me-1"></i> <?= number_format($cantidad) ?> unidad(es)
                                        </span>
                                        <span class="text-primary">
                                            <i class="fas fa-tag me-1"></i> $<?= number_format($precio, 2, ',', '.') ?> c/u
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                
                <!-- Resumen de compra -->
                <div class="card bg-light shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total:</span>
                            <span>$<?= $total_venta !== null ? number_format($total_venta, 2, ',', '.') : number_format($total, 2, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer text-center py-3">
                <?php if ($perfil_id != 2): ?>
                    <div class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span class="text-success fw-bold">Compra realizada con éxito</span>
                    </div>
                    <p class="text-muted mb-2">Gracias por su compra en Yaguareté Camp.</p>
                    <a class="btn btn-verde-selva" href="<?= base_url('catalogo') ?>">
                        <i class="fas fa-shopping-cart me-2"></i> Seguir comprando
                    </a>
                <?php else: ?>
                    <div class="mb-2">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <span class="text-primary fw-bold">Vista de administrador</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php } ?>
</div>

<!-- Estilos adicionales para la página de detalle de compra -->
<style>
.product-image-container {
    position: relative;
    overflow: hidden;
}

.product-image {
    transition: transform 0.3s;
}

.product-image:hover {
    transform: scale(1.05);
}

.bg-verde-selva {
    background-color: #2E7D32;
}

.btn-verde-selva {
    background-color: #2E7D32;
    color: white;
}

.btn-verde-selva:hover {
    background-color: #1B5E20;
    color: white;
}

.list-group-item {
    transition: transform 0.2s, box-shadow 0.2s;
}

.list-group-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1) !important;
}
</style>
<?php ?>