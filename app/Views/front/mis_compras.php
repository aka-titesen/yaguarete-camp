<?php
$session = session();
?>
<div class="container mt-5 pt-4">
    <!-- Mostrar mensaje Flash si existe -->
    <?php if (session()->getFlashdata('mensaje')): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <?= session()->getFlashdata('mensaje') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-verde-selva text-white">
            <h2 class="mb-0 d-flex align-items-center">
                <i class="fas fa-shopping-bag me-3"></i> Mi historial de compras
            </h2>
        </div>
        
        <div class="card-body">
            <?php if (empty($compras)) : ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x mb-4 text-muted"></i>
                    <h4 class="text-muted">No tienes compras registradas</h4>
                    <p class="text-secondary">¡Explora nuestro catálogo y encuentra productos que te encantarán!</p>
                    <a class="btn btn-verde-selva mt-3" href="<?= base_url('catalogo') ?>">
                        <i class="fas fa-store me-2"></i> Ir al catálogo
                    </a>
                </div>
            <?php else : ?>
                <div class="row">
                    <?php foreach ($compras as $i => $compra) : ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 purchase-card shadow-sm border-0">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <span class="badge bg-verde-selva">Compra #<?= $compra['id'] ?></span>
                                    <span class="text-muted small"><?= date('d/m/Y', strtotime($compra['fecha'] ?? '')) ?></span>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-receipt me-2 text-primary"></i>
                                            Orden #<?= $compra['id'] ?>
                                        </h5>
                                        <h5 class="mb-0 text-success">
                                            $<?= number_format($compra['total_venta'], 2, ',', '.') ?>
                                        </h5>
                                    </div>
                                    <p class="card-text text-muted">                                    <i class="fas fa-calendar-alt me-2"></i>
                                        <?= date('d/m/Y', strtotime($compra['fecha'] ?? '')) ?>
                                    </p>
                                    <div class="d-grid">
                                        <a href="<?= base_url('detalle-compra/' . $compra['id']) ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-2"></i> Ver detalles de la compra
                                        </a>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span class="text-success">Completada</span>
                                        <!-- Solo mostramos la fecha, no la hora -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Estilos adicionales para las tarjetas de compras -->
<style>
.purchase-card {
    transition: transform 0.2s;
    border-radius: 8px;
}

.purchase-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

.bg-verde-selva {
    background-color: #2E7D32;
}

.btn-verde-selva {
    background-color: #2E7D32;
    color: white;
}

.btn-verde-selva:hover {
    background-color: #1B5E20;
    color: white;
}
</style>
