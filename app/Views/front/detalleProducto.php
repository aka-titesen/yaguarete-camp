<div class="container detalle-producto">
    <div class="row">
        <!-- Imagen del producto -->        <div class="col-md-6">
            <?php if (!empty($producto['imagen'])): ?>
                <img src="<?= base_url('assets/uploads/' . esc($producto['imagen'])) ?>" class="producto-imagen" alt="<?= esc($producto['nombre_prod']) ?>">
            <?php else: ?>
                <img src="<?= base_url('assets/img/imagenes_pagina/logo.png') ?>" class="producto-imagen" alt="Sin imagen">
            <?php endif; ?>
        </div>
        
        <!-- Información del producto -->
        <div class="col-md-6 producto-info">
            <h1 class="producto-titulo"><?= esc($producto['nombre_prod']) ?></h1>
            
            <span class="categoria-badge">
                <i class="fas fa-tag me-2"></i>
                <?php
                $categorias = [
                    1 => 'Camping',
                    2 => 'Pesca', 
                    3 => 'Ropa',
                    4 => 'Calzado',
                    5 => 'Mochilas',
                    6 => 'Accesorios'
                ];
                echo esc($categorias[$producto['categoria_id']] ?? 'Sin categoría');
                ?>
            </span>
            
            <div class="precio-principal">$<?= number_format($producto['precio_vta'], 2, ',', '.') ?></div>
            
            <div class="cuotas-info-detalle">
                <i class="fas fa-credit-card me-2"></i>
                3 cuotas sin interés de $<?= number_format($producto['precio_vta'] / 3, 2, ',', '.') ?>
            </div>
            
            <div class="stock-info">
                <i class="fas fa-box me-2"></i>
                <strong>Stock disponible:</strong> <?= esc($producto['stock']) ?> unidades
                <?php if ($producto['stock'] <= $producto['stock_min']): ?>
                    <br><small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>¡Últimas unidades!</small>
                <?php endif; ?>
            </div>
            
            <div class="btn-acciones">
                <div class="d-flex align-items-center mb-3">
                    <input type="number" min="1" max="<?= $producto['stock'] ?>" value="1" class="cantidad-selector" title="Cantidad" aria-label="Cantidad">
                    <button class="btn btn-agregar-carrito-detalle">
                        <i class="fas fa-cart-plus me-2"></i>Agregar al Carrito
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Productos relacionados -->
<div class="container mt-5">
    <h3 class="mb-4"><i class="fas fa-link me-2"></i>Productos relacionados</h3>
    <div class="row g-4" id="relacionadosRow">
        <?php if (!empty($relacionados)): ?>
            <?php foreach ($relacionados as $rel): ?>
                <?php if (!isset($rel['eliminado']) || $rel['eliminado'] !== 'SI'): ?>
                <div class="col-6 col-md-6 col-lg-4 producto-card" data-categoria="<?= esc($rel['categoria_id']) ?>">
                    <div class="card card-producto h-100">
                        <!-- Imagen del producto -->
                        <?php if (!empty($rel['imagen'])): ?>
                            <img src="<?= base_url('assets/uploads/' . esc($rel['imagen'])) ?>" class="card-img-top" alt="<?= esc($rel['nombre_prod']) ?>">
                        <?php else: ?>
                            <img src="<?= base_url('assets/img/imagenes_pagina/logo.png') ?>" class="card-img-top" alt="Sin imagen">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title mb-2"><?= esc($rel['nombre_prod']) ?></h5>
                            <p class="card-text mb-1">Categoría: <?php
                                $cats = [1=>'Camping',2=>'Pesca',3=>'Ropa',4=>'Calzado',5=>'Mochilas',6=>'Accesorios'];
                                echo esc($cats[$rel['categoria_id']] ?? $rel['categoria_id']);
                            ?></p>
                            <p class="card-text mb-2"><small class="text-muted">Stock: <?= esc($rel['stock']) ?></small></p>
                            <h4 class="text-success mb-2">$<?= number_format($rel['precio_vta'], 2, ',', '.') ?></h4>
                            <p class="cuotas-info mb-3">3 cuotas sin interés</p>
                            <div class="d-flex flex-column flex-md-row align-items-stretch gap-2 mt-2">
                                <a href="<?= base_url('producto/' . $rel['id']) ?>" target="_blank" class="btn btn-ver-producto flex-fill mb-2 mb-md-0">
                                    <i class="fas fa-eye me-1"></i> Ver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted">No hay productos relacionados.</div>
        <?php endif; ?>
    </div>
</div>
<br>
<style>
/* Espacio extra debajo de la sección de productos relacionados */
#relacionadosRow {
    margin-bottom: 2.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad del selector de cantidad
    const cantidadInput = document.querySelector('.cantidad-selector');
    const maxStock = <?= $producto['stock'] ?>;
    
    cantidadInput.addEventListener('change', function() {
        if (this.value > maxStock) {
            this.value = maxStock;
        }
        if (this.value < 1) {
            this.value = 1;
        }
    });
    
    // Funcionalidad del botón agregar al carrito
    document.querySelector('.btn-agregar-carrito-detalle').addEventListener('click', function() {
        const cantidad = cantidadInput.value;
        
        // Aquí puedes agregar la lógica para agregar al carrito
        alert(`Producto agregado al carrito. Cantidad: ${cantidad}`);
        
        // Ejemplo de animación del botón
        this.innerHTML = '<i class="fas fa-check me-2"></i>¡Agregado!';
        this.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
          setTimeout(() => {
            this.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Agregar al Carrito';
            this.style.background = 'linear-gradient(135deg, var(--verde-selva), #1e4a36)';
        }, 2000);
    });
});
</script>
