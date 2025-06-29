<!-- Inclusión de FontAwesome para íconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-dyZtM6zQ+1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="container my-5">
    <h2 class="text-center mb-4">
        <i class="fas fa-th-large me-2"></i>Catálogo de Productos
    </h2>
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
    <!-- Buscador por nombre de producto -->
    <div class="row mb-3 justify-content-end">
        <div class="col-auto buscador-con-icono">
            <i class="fas fa-search input-search-icon"></i>
            <input type="text" id="buscadorNombreCatalogo" class="form-control buscador-input w-100" placeholder="Buscar por nombre...">
        </div>
    </div>
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
                            <img src="<?= base_url('assets/img/imagenes_pagina/logo.png') ?>" class="card-img-top" alt="Sin imagen">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title mb-2"><?= esc($producto['nombre_prod']) ?></h5>
                            <p class="card-text mb-1">
                                <span class="categoria-badge">
                                    <i class="fas fa-tag me-2"></i>
                                    <?php
                                    $cats = [1=>'Camping',2=>'Pesca',3=>'Ropa',4=>'Calzado',5=>'Mochilas',6=>'Accesorios'];
                                    echo esc($cats[$producto['categoria_id']] ?? $producto['categoria_id']);
                                    ?>
                                </span>
                            </p>
                            <p class="card-text mb-2"><small class="text-muted">Stock: <?= esc($producto['stock']) ?></small></p>
                            <h4 class="text-success mb-2">$<?= number_format($producto['precio_vta'], 2, ',', '.') ?></h4>
                            <p class="cuotas-info mb-3">3 cuotas sin interés</p>
                            <div class="d-flex flex-column flex-md-row align-items-stretch gap-2 mt-2">
                                <a href="<?= base_url('producto/' . $producto['id']) ?>" target="_blank" class="btn btn-ver-producto flex-fill mb-2 mb-md-0">
                                    <i class="fas fa-eye me-1"></i> Ver
                                </a>
                                <?php if (session()->get('perfil_id') != 2): ?>
                                <form class="formAgregarCarrito flex-fill" method="post" action="<?= base_url('carrito/add') ?>">
                                    <input type="hidden" name="id" value="<?= esc($producto['id']) ?>">
                                    <input type="hidden" name="nombre_prod" value="<?= esc($producto['nombre_prod']) ?>">
                                    <input type="hidden" name="precio_vta" value="<?= esc($producto['precio_vta']) ?>">
                                    <input type="hidden" name="imagen" value="<?= esc($producto['imagen'] ?? '') ?>">
                                    <div class="input-group">
                                        <input type="number" name="qty" class="form-control cantidad-selector"
                                            min="1"
                                            max="<?= esc($producto['stock']) ?>"
                                            value="1"
                                            <?= ($producto['stock'] < 1) ? 'disabled' : '' ?>>
                                        <button type="submit" class="btn btn-agregar-carrito"
                                            <?= ($producto['stock'] < 1) ? 'disabled' : '' ?>>
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </form>
                                <?php endif; ?>
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

<style>
/* Homogeneizar el banner de categoría con el de detalle y relacionados */
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
// Filtro y buscador de productos por categoría y nombre en la vista
const filtro = document.getElementById('filtroCategoria');
const buscador = document.getElementById('buscadorNombreCatalogo');

function filtrarCatalogo() {
    const cat = filtro.value;
    const texto = buscador.value.toLowerCase();
    document.querySelectorAll('.producto-card').forEach(card => {
        const nombre = card.querySelector('.card-title');
        const coincideNombre = nombre && nombre.textContent.toLowerCase().includes(texto);
        const coincideCategoria = !cat || card.getAttribute('data-categoria') === cat;
        if (coincideNombre && coincideCategoria) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

filtro.addEventListener('change', filtrarCatalogo);
buscador.addEventListener('input', filtrarCatalogo);

document.addEventListener('submit', async function(e) {
    if (e.target.classList.contains('formAgregarCarrito')) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            let msg = document.createElement('div');
            msg.style.position = 'fixed';
            msg.style.bottom = '20px';
            msg.style.right = '20px';
            msg.style.zIndex = '9999';
            msg.style.maxWidth = '300px';
            
            if (data.status === 'success') {
                msg.className = 'alert alert-success';
                msg.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + data.msg;
            } else if (data.status === 'warning') {
                msg.className = 'alert alert-warning';
                msg.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + data.msg;
            } else if (data.status === 'info') {
                msg.className = 'alert alert-info';
                msg.innerHTML = '<i class="fas fa-info-circle me-2"></i>' + data.msg;
            } else {
                msg.className = 'alert alert-danger';
                msg.innerHTML = '<i class="fas fa-times-circle me-2"></i>' + (data.msg || 'Error al agregar al carrito');
            }
            
            document.body.appendChild(msg);
            
            if (typeof cargarCarritoLateral === 'function') {
                cargarCarritoLateral();
            }
            if (typeof actualizarContadorCarrito === 'function') {
                actualizarContadorCarrito();
            }
            
            setTimeout(() => msg.remove(), 3500);
            
        } catch (error) {
            console.error('Error adding to cart:', error);
        }
    }
});
</script>
