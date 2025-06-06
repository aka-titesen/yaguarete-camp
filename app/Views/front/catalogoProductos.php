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
                            <img src="<?= base_url('assets/img/imagenes_pagina/sin-imagen.png') ?>" class="card-img-top" alt="Sin imagen">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title mb-2"><?= esc($producto['nombre_prod']) ?></h5>
                            <p class="card-text mb-1">Categoría: <?php
                                $cats = [1=>'Camping',2=>'Pesca',3=>'Ropa',4=>'Calzado',5=>'Mochilas',6=>'Accesorios'];
                                echo esc($cats[$producto['categoria_id']] ?? $producto['categoria_id']);
                            ?></p>
                            <p class="card-text mb-2"><small class="text-muted">Stock: <?= esc($producto['stock']) ?></small></p>
                            <h4 class="text-success mb-2">$<?= number_format($producto['precio_vta'], 2, ',', '.') ?></h4>
                            <p class="cuotas-info mb-3">3 cuotas sin interés</p>
                            <div class="d-flex flex-column flex-md-row align-items-stretch gap-2 mt-2">
                                <a href="<?= base_url('producto/' . $producto['id']) ?>" class="btn btn-ver-producto flex-fill mb-2 mb-md-0 order-1 order-md-0" data-id="<?= $producto['id'] ?>">
                                    <i class="fas fa-eye me-1"></i> Ver
                                </a>
                                <div class="agregar-carrito-group flex-shrink-0 order-0 order-md-1">
                                    <input type="number" min="1" value="1" class="form-control cantidad-carrito me-2" style="width: 60px;" title="Cantidad" aria-label="Cantidad">
                                    <button class="btn btn-agregar-carrito d-flex align-items-center px-3" type="button">
                                        <i class="fas fa-cart-plus me-1"></i> <span>Agregar</span>
                                    </button>
                                </div>
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

<!-- Modal para ver producto -->
<div class="modal fade" id="modalVerProducto" tabindex="-1" aria-labelledby="modalVerProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-verde-selva text-white">
                <h5 class="modal-title text-amarillo-sistema" id="modalVerProductoLabel">
                    <i class="fas fa-eye"></i> Detalle del producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="verProductoImagen" src="" alt="Imagen del producto" class="img-fluid rounded" style="max-height:180px;object-fit:contain;">
                </div>
                <h4 id="verProductoNombre" class="mb-2"></h4>
                <p class="mb-1"><strong>Categoría:</strong> <span id="verProductoCategoria"></span></p>
                <p class="mb-1"><strong>Stock:</strong> <span id="verProductoStock"></span></p>
                <h5 class="text-success mb-2">$<span id="verProductoPrecio"></span></h5>
                <p class="cuotas-info mb-3">3 cuotas sin interés</p>
                <p class="mb-0" id="verProductoDescripcion"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

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

// MODAL VER PRODUCTO
const productosCatalogo = <?php echo json_encode($productos); ?>;
const modalVer = document.getElementById('modalVerProducto');
const verProductoNombre = document.getElementById('verProductoNombre');
const verProductoImagen = document.getElementById('verProductoImagen');
const verProductoCategoria = document.getElementById('verProductoCategoria');
const verProductoStock = document.getElementById('verProductoStock');
const verProductoPrecio = document.getElementById('verProductoPrecio');
const verProductoDescripcion = document.getElementById('verProductoDescripcion');

// Mapeo de categorías
const categorias = {1:'Camping',2:'Pesca',3:'Ropa',4:'Calzado',5:'Mochilas',6:'Accesorios'};

document.querySelectorAll('.btn-ver-producto').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.getAttribute('data-id');
        const prod = productosCatalogo.find(p => p.id == id);
        if (prod) {
            verProductoNombre.textContent = prod.nombre_prod;
            verProductoImagen.src = prod.imagen ? '<?= base_url('assets/uploads/') ?>' + prod.imagen : '<?= base_url('assets/img/imagenes_pagina/sin-imagen.png') ?>';
            verProductoCategoria.textContent = categorias[prod.categoria_id] || prod.categoria_id;
            verProductoStock.textContent = prod.stock;
            verProductoPrecio.textContent = parseFloat(prod.precio_vta).toLocaleString('es-AR', {minimumFractionDigits:2});
            verProductoDescripcion.textContent = prod.descripcion || '';
            const modal = new bootstrap.Modal(modalVer);
            modal.show();
        }
    });
});
</script>
