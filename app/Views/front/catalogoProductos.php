<div class="container my-5">
    <h2 class="text-center mb-4">Catálogo de Productos</h2>
    <div class="row g-4">
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $producto): ?>
                <?php if (!isset($producto['eliminado']) || $producto['eliminado'] !== 'SI'): ?>
                <div class="col-6 col-md-6 col-lg-4">
                    <div class="card card-producto h-100">
                        <!-- Imagen del producto -->
                        <?php if (!empty($producto['imagen'])): ?>
                            <img src="<?= base_url('assets/uploads/' . esc($producto['imagen'])) ?>" class="card-img-top" alt="<?= esc($producto['nombre_prod']) ?>">
                        <?php else: ?>
                            <img src="<?= base_url('assets/img/imagenes_pagina/sin-imagen.png') ?>" class="card-img-top" alt="Sin imagen">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title mb-2"><?= esc($producto['nombre_prod']) ?></h5>
                            <p class="card-text mb-1">Categoría: <?= esc($producto['categoria_nombre'] ?? $producto['categoria_id']) ?></p>
                            <p class="card-text mb-2"><small class="text-muted">Stock: <?= esc($producto['stock']) ?></small></p>
                            <h4 class="text-success mb-3">$<?= number_format($producto['precio_vta'], 2, ',', '.') ?></h4>
                            <div class="d-grid">
                                <a href="<?= base_url('producto/' . $producto['id']) ?>" class="btn btn-productos">Ver más</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">No hay productos disponibles en este momento.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
