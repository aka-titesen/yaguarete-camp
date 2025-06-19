<?php
// filepath: app/Views/front/carritoVista.php
// Vista para mostrar el contenido del carrito en el offcanvas o página
$cart = \Config\Services::cart();
$items = $cart->contents();
$total = $cart->total();
?>
<link rel="stylesheet" href="<?= base_url('assets/css/carrito.css') ?>">
<div class="cart-list">
    <?php if (empty($items)): ?>
        <p class="text-center text-muted">El carrito está vacío.</p>
    <?php else: ?>
        <ul class="carrito-sidebar-list mb-3">
            <?php foreach ($items as $item): ?>
                <li class="carrito-sidebar-item">
                    <div class="carrito-sidebar-main">
                        <div class="carrito-sidebar-img-title">
                            <?php if (!empty($item['imagen'])): ?>
                                <img src="<?= base_url('assets/uploads/' . esc($item['imagen'])) ?>" alt="<?= esc($item['name']) ?>" class="carrito-sidebar-img">
                            <?php endif; ?>
                            <div class="carrito-sidebar-title"><?= esc($item['name']) ?></div>
                        </div>
                        <div class="carrito-sidebar-category">
                            <span class="badge bg-light text-dark">
                                <?= isset($item['categoria']) ? esc($item['categoria']) : 'Sin categoría' ?>
                            </span>
                        </div>
                    </div>
                    <div class="carrito-sidebar-bottom d-flex justify-content-between align-items-end w-100">
                        <div class="carrito-sidebar-controls-bottom">
                            <div class="input-group input-group-sm" style="width: 110px;">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-resta-item" data-rowid="<?= esc($item['rowid']) ?>"><i class="fas fa-minus"></i></button>
                                <input type="text" readonly class="form-control text-center border-secondary" value="<?= esc($item['qty']) ?>">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-suma-item" data-rowid="<?= esc($item['rowid']) ?>" <?= (isset($item['stock']) && $item['qty'] >= $item['stock']) ? 'disabled' : '' ?>><i class="fas fa-plus"></i></button>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2 btn-eliminar-item" data-rowid="<?= esc($item['rowid']) ?>"><i class="fas fa-trash"></i></button>
                        </div>
                        <div class="carrito-sidebar-stock text-end">
                            <span>Stock: <?= isset($item['stock']) ? $item['stock'] : 'N/A' ?></span><br>
                            <span class="carrito-sidebar-subtotal">$<?= number_format($item['subtotal'], 2, ',', '.') ?></span>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="card mb-3">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Subtotal:</span>
                    <strong>$<?= number_format($total, 2, ',', '.') ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Total:</span>
                    <h5 class="mb-0 text-success">$<?= number_format($total, 2, ',', '.') ?></h5>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('carrito-comprar') ?>" class="btn btn-cta flex-fill" id="checkoutBtn">
                <i class="fas fa-shopping-bag me-2"></i>Finalizar compra
            </a>
            <button type="button" class="btn btn-outline-danger flex-fill" id="btnLimpiarCarrito">
                <i class="fas fa-trash-alt me-2"></i>Limpiar
            </button>
        </div>
    <?php endif; ?>
</div>
<input type="hidden" id="cartTotalHidden" value="$<?= number_format($total, 2, ',', '.') ?>">
<!-- El script de botones del carrito lateral ahora está en navbar.php para funcionar siempre tras AJAX -->
