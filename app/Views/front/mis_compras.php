<?php
$session = session();
?>
<div class="container mt-4">
    <h2 class="mb-4">Mis compras</h2>
    <?php if (empty($compras)) : ?>
        <div class="alert alert-info">No tienes compras registradas.</div>
        <a class="btn btn-warning my-2" href="<?php echo base_url('catalogo') ?>">Catálogo</a>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compras as $i => $compra) : ?>
                        <tr class="text-center">
                            <td><?= $i + 1 ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($compra['fecha'] ?? '')) ?></td>
                            <td>$<?= number_format($compra['total_venta'], 2, ',', '.') ?></td>
                            <td>
                                <a href="<?= base_url('detalle-compra/' . $compra['id']) ?>" class="btn btn-primary btn-sm">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
