<?php
$session = session();
if (empty($venta)) { ?>
    <!-- avisamos que no hay consultas -->
    <div class="container">
        <div class="alert alert-dark text-center" role="alert">
            <h4 class="alert-heading">No posee compras registradas</h4>
            <p>Para realizar una compra visite nuestro catalogo.</p>
            <hr>
            <a class="btn btn-warning my-2 w-10" href="<?php echo base_url('catalogo') ?>">Catalogo</a>
        </div>
    </div>
<?php } ?>

<!-- Mostrar mensaje Flash si existe -->
<?php if (session()->getFlashdata('mensaje')): ?>
    <div class="alert alert-warning alert-dismissible fade show mt-3 mx-3" role="alert">
        <?= session()->getFlashdata('mensaje') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="container">
        <div class="col-xl-12 col-xs-12">
            <table class="table table-secondary table-responsive table-bordered table-striped rounded">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>Producto</th>
                        <th>Imagen</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    ?>
                    <?php if (!empty($venta) && is_array($venta)) {
                        foreach ($venta as $i => $row) {
                            $imagen = $row['imagen'] ?? ($row['productos.imagen'] ?? '');
                            $nombre = $row['nombre_prod'] ?? ($row['productos.nombre_prod'] ?? '');
                            $precio = $row['precio_vta'] ?? ($row['productos.precio_vta'] ?? $row['precio'] ?? 0);
                            $cantidad = $row['cantidad'] ?? 1;
                            $subtotal = $precio * $cantidad;
                            $total += $subtotal;
                    ?>
                    <tr class="text-center">
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($nombre) ?></td>
                        <td><img width="100" height="65" src="<?= base_url($imagen) ?>"></td>
                        <td><?= number_format($cantidad) ?></td>
                        <td>$<?= number_format($precio, 2) ?></td>
                        <td>$<?= number_format($subtotal, 2) ?></td>
                    </tr>
                    <?php }} ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">
                            <h4>Total</h4>
                        </td>
                        <td class="text-right">
                            <h4>$<?= number_format($total, 2) ?></h4>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row">
            <div class="col-xl-12 col-xs-12 text-center">
                <p class="h5 text-warning">Gracias por su compra</p>
            </div>
        </div>
    </div>
</div>
<?php ?>