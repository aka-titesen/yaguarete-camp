<?php
$session = session();
$venta = $venta ?? [];
$detalles = $detalles ?? [];

// Validar y preparar datos de cabecera
$fecha_venta = $venta['fecha'] ?? '';
$nro_orden = $venta['id'] ?? '';
$nombre = $venta['nombre'] ?? '';
$apellido = $venta['apellido'] ?? '';
$email = $venta['email'] ?? '';
$usuario = $venta['usuario'] ?? '';
$total_venta = isset($venta['total_venta']) && is_numeric($venta['total_venta']) ? (float)$venta['total_venta'] : 0;
?>
<div class="container mt-5 pt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-verde-selva text-white">
            <h2 class="mb-0 d-flex align-items-center">
                <i class="fas fa-file-invoice-dollar me-3"></i> Detalle de Venta #<?= esc($nro_orden) ?>
            </h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="mb-1">Cliente:</h5>
                    <p class="mb-0"><i class="fas fa-user me-2"></i><?= esc($nombre) ?> <?= esc($apellido) ?></p>
                    <p class="mb-0"><i class="fas fa-envelope me-2"></i><?= esc($email) ?></p>
                    <p class="mb-0"><i class="fas fa-user-tag me-2"></i><?= esc($usuario) ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5 class="mb-1">Fecha de venta:</h5>
                    <p class="mb-0"><i class="fas fa-calendar-alt me-2"></i><?= $fecha_venta ? date('d/m/Y', strtotime($fecha_venta)) : '-' ?></p>
                    <h5 class="mt-3">Total:</h5>
                    <p class="fs-4 text-success">$<?= number_format($total_venta, 2, ',', '.') ?></p>
                </div>
            </div>
            <hr>
            <h4 class="mb-3"><i class="fas fa-boxes me-2"></i>Productos de la venta</h4>
            <?php if (!empty($detalles)): ?>
            <div class="row g-4">
                <?php foreach ($detalles as $detalle):
                    $nombre_prod = $detalle['nombre_prod'] ?? 'Producto sin nombre';
                    $imagen = $detalle['imagen'] ?? '';
                    $cantidad = isset($detalle['cantidad']) && is_numeric($detalle['cantidad']) ? intval($detalle['cantidad']) : 1;
                    $precio = isset($detalle['precio_unitario']) && is_numeric($detalle['precio_unitario']) ? floatval($detalle['precio_unitario']) : 0;
                    $subtotal = $cantidad * $precio;
                    $ruta_imagen = $imagen ? base_url('assets/uploads/' . $imagen) : base_url('assets/img/producto-sin-imagen.jpg');
                ?>
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-light">
                            <span class="badge bg-primary">Producto #<?= esc($detalle['producto_id'] ?? '') ?></span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="<?= $ruta_imagen ?>" alt="<?= esc($nombre_prod) ?>" class="img-fluid rounded me-3" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.src='<?= base_url('assets/img/producto-sin-imagen.jpg') ?>'">
                                <div>
                                    <h5 class="mb-1"><?= esc($nombre_prod) ?></h5>
                                    <p class="mb-0"><strong>Cantidad:</strong> <?= esc($cantidad) ?></p>
                                    <p class="mb-0"><strong>Precio unitario:</strong> $<?= number_format($precio, 2, ',', '.') ?></p>
                                    <p class="mb-0"><strong>Subtotal:</strong> $<?= number_format($subtotal, 2, ',', '.') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <div class="alert alert-info text-center">No hay productos en esta venta.</div>
            <?php endif; ?>
            <div class="mt-4">
                <a href="<?= base_url('admin-ventas') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Volver a ventas
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.bg-verde-selva {
    background-color: #2E7D32;
}
</style>
