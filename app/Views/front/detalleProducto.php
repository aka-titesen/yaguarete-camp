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
    <div class="row" id="relacionadosRow">
        <?php if (!empty($relacionados)): ?>
            <?php foreach ($relacionados as $rel): ?>
                <div class="col-6 col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <a href="<?= base_url('detalle-producto/' . $rel['id']) ?>" class="text-decoration-none">
                            <?php if (!empty($rel['imagen'])): ?>
                                <img src="<?= base_url('assets/uploads/' . esc($rel['imagen'])) ?>" class="card-img-top" alt="<?= esc($rel['nombre_prod']) ?>">
                            <?php else: ?>
                                <img src="<?= base_url('assets/img/imagenes_pagina/logo.png') ?>" class="card-img-top" alt="Sin imagen">
                            <?php endif; ?>
                            <div class="card-body">
                                <h6 class="card-title text-dark mb-2"><?= esc($rel['nombre_prod']) ?></h6>
                                <div class="text-success fw-bold mb-1">$<?= number_format($rel['precio_vta'], 2, ',', '.') ?></div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted">No hay productos relacionados.</div>
        <?php endif; ?>
    </div>
</div>

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
