<?php
// filepath: app/Views/front/carritoVista.php
// Vista para mostrar el contenido del carrito en el offcanvas o página
$cart = \Config\Services::cart();
$items = $cart->contents();
$total = $cart->total();
?>
<div class="cart-list">
    <?php if (empty($items)): ?>
        <p class="text-center text-muted">El carrito está vacío.</p>
    <?php else: ?>
        <ul class="list-group mb-3">
            <?php foreach ($items as $item): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <?php if (!empty($item['imagen'])): ?>
                            <img src="<?= base_url('assets/uploads/' . esc($item['imagen'])) ?>" alt="<?= esc($item['name']) ?>" width="40" height="40" class="rounded me-2" style="object-fit:cover;">
                        <?php endif; ?>
                        <div>
                            <div class="fw-bold"><?= esc($item['name']) ?></div>
                            <small class="text-muted">x<?= esc($item['qty']) ?></small>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end">
                        <span class="fw-bold">$<?= number_format($item['subtotal'], 2, ',', '.') ?></span>
                        <div class="btn-group mt-1" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-resta-item" data-rowid="<?= esc($item['rowid']) ?>" title="Quitar una unidad"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-suma-item" data-rowid="<?= esc($item['rowid']) ?>" title="Sumar una unidad" <?= (isset($item['stock']) && $item['qty'] >= $item['stock']) ? 'disabled' : '' ?>><i class="fas fa-plus"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-item" data-rowid="<?= esc($item['rowid']) ?>" title="Eliminar producto"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="text-end mb-2">
            <strong>Total: $<?= number_format($total, 2, ',', '.') ?></strong>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('carrito-comprar') ?>" class="btn btn-cta flex-fill">Finalizar compra</a>
            <button type="button" class="btn btn-outline-danger flex-fill" id="btnLimpiarCarrito">Limpiar carrito</button>
        </div>
    <?php endif; ?>
</div>
<input type="hidden" id="cartTotalHidden" value="$<?= number_format($total, 2, ',', '.') ?>">
<!-- El script de botones del carrito lateral ahora está en navbar.php para funcionar siempre tras AJAX -->
