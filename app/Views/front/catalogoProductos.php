<div class="container my-5">
    <h2 class="text-center mb-4">Catálogo de Productos</h2>
    <!-- Filtro por categoría solo en la vista -->
    <form class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="filtroCategoria" class="form-label mb-0">Filtrar por categoría:</label>
            </div>
            <div class="col-auto">
                <select id="filtroCategoria" class="form-select">
                    <option value="">Todas</option>
                    <option value="1">Camping</option>
                    <option value="2">Pesca</option>
                    <option value="3">Ropa</option>
                    <option value="4">Calzado</option>
                    <option value="5">Mochilas</option>
                    <option value="6">Accesorios</option>
                </select>
            </div>
        </div>
    </form>
    <div class="row g-4" id="productosCatalogo">
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $producto): ?>
                <?php if (!isset($producto['eliminado']) || $producto['eliminado'] !== 'SI'): ?>
                <div class="col-6 col-md-6 col-lg-4 producto-card" data-categoria="<?= esc($producto['categoria_id']) ?>">
                    <div class="card card-producto h-100">
                        <!-- Imagen del producto -->
                        <?php if (!empty($producto['imagen'])): ?>
                            <img src="<?= base_url('assets/uploads/' . esc($producto['imagen'])) ?>" class="card-img-top" alt="<?= esc($producto['nombre_prod']) ?>">
                        <?php else: ?>
                            <img src="<?= base_url('assets/img/imagenes_pagina/sin-imagen.png') ?>" class="card-img-top" alt="Sin imagen">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title mb-2"><?= esc($producto['nombre_prod']) ?></h5>
                            <p class="card-text mb-1">Categoría: <?php
                                $cats = [1=>'Camping',2=>'Pesca',3=>'Ropa',4=>'Calzado',5=>'Mochilas',6=>'Accesorios'];
                                echo esc($cats[$producto['categoria_id']] ?? $producto['categoria_id']);
                            ?></p>
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
<script>
// Filtro de productos por categoría solo en la vista
const filtro = document.getElementById('filtroCategoria');
filtro.addEventListener('change', function() {
    const cat = this.value;
    document.querySelectorAll('.producto-card').forEach(card => {
        if (!cat || card.getAttribute('data-categoria') === cat) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
