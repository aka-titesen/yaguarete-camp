<div class="container detalle-producto">
    <div class="row">
        <div class="col-12">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Imagen del producto -->
    <div class="col-md-6">
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
                <form id="formAgregarCarrito" method="post" action="<?= base_url('carrito/add') ?>" class="d-flex align-items-center w-100 gap-2">
                    <input type="hidden" name="id" value="<?= esc($producto['id']) ?>">
                    <input type="hidden" name="nombre_prod" value="<?= esc($producto['nombre_prod']) ?>">
                    <input type="hidden" name="precio_vta" value="<?= esc($producto['precio_vta']) ?>">
                    <input type="hidden" name="imagen" value="<?= esc($producto['imagen']) ?>">
                    <input type="number" name="qty" class="cantidad-selector" min="1" max="<?= esc($producto['stock']) ?>" value="1" style="margin-right: 10px;">
                    <button type="submit" class="btn btn-agregar-carrito-detalle">
                        <i class="fas fa-cart-plus me-2"></i>Agregar al Carrito
                    </button>
                </form>
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
                            <p class="card-text mb-1">
                                <span class="categoria-badge">
                                    <i class="fas fa-tag me-2"></i>
                                    <?php
                                    $cats = [1=>'Camping',2=>'Pesca',3=>'Ropa',4=>'Calzado',5=>'Mochilas',6=>'Accesorios'];
                                    echo esc($cats[$rel['categoria_id']] ?? $rel['categoria_id']);
                                    ?>
                                </span>
                            </p>
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

/* Reducir tamaño de la información al lado de la imagen en detalle producto */
.detalle-producto .producto-info {
    font-size: 0.97rem;
    line-height: 1.4;
}
.detalle-producto .producto-titulo {
    font-size: 1.6rem;
}
.detalle-producto .precio-principal {
    font-size: 1.3rem;
}
.detalle-producto .cuotas-info-detalle {
    font-size: 1rem;
}
.detalle-producto .stock-info {
    font-size: 0.95rem;
}
.detalle-producto .btn-acciones .btn {
    font-size: 0.97rem;
    padding: 0.45rem 1.1rem;
}

/* Badge de categoría igual en cards y detalle */
.categoria-badge, .card-producto .categoria-badge {
    display: inline-block;
    background: var(--beige, #f5f5dc);
    color: #6c757d;
    border-radius: 1rem;
    padding: 0.18em 0.85em 0.18em 0.7em;
    font-size: 0.97em;
    font-weight: 500;
    margin-bottom: 0.5em;
    margin-right: 0.2em;
    vertical-align: middle;
    border: 1px solid #e0e0c0;
}
.categoria-badge i, .card-producto .categoria-badge i {
    margin-right: 0.4em;
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
<script>
document.getElementById('formAgregarCarrito').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const response = await fetch(form.action, {
        method: 'POST',
        body: formData
    });
    let msg = document.createElement('div');
    let data = null;
    try {
        data = await response.json();
    } catch (err) {}
    if (data && data.status) {
        if (typeof cargarCarritoLateral === 'function') cargarCarritoLateral();
        if (data.status === 'success') {
            msg.className = 'alert alert-success mt-2';
            msg.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + data.msg;
        } else if (data.status === 'warning') {
            msg.className = 'alert alert-warning mt-2';
            msg.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + data.msg;
        } else if (data.status === 'info') {
            msg.className = 'alert alert-info mt-2';
            msg.innerHTML = '<i class="fas fa-info-circle me-2"></i>' + data.msg;
        } else {
            msg.className = 'alert alert-danger mt-2';
            msg.innerHTML = '<i class="fas fa-times-circle me-2"></i>' + (data.msg || 'Error al agregar al carrito');
        }
        form.parentNode.insertBefore(msg, form.nextSibling);
        setTimeout(() => msg.remove(), 3500);
    } else {
        alert('Error al agregar al carrito');
    }
});
</script>
