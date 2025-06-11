<?php if (empty($ventas)) { ?>
    <!-- avisamos que no hay consultas -->
    <div class="container">
        <div class="alert alert-dark text-center" role="alert">
            <h4 class="alert-heading">No posee compras registradas</h4>
            <p>Para realizar una compra visite nuestro catalogo.</p>
            <hr>
            <a class="btn btn-warning my-2 w-10" href="<?php echo base_url('todos_p') ?>">Catalogo</a>
        </div>
    </div>
<?php } else { ?>
    <div class="container">
        <div class="table-responsive-sm text-center">
            <h1 class="text-center">Mis compras</h1>
            <table class="table table-warning table-striped rounded">
                <thead class="thead-dark">
                    <tr>
                        <th>Nombre cliente</th>
                        <th>Email</th>
                        <th>Usuario</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th>Venta_id</th>
                        <th>Opcion</th>
                    </tr>
                </thead>
                <tbody>
                    <!--Si es array de reservas y no esta vacio-->
                    <?php if (!empty($ventas) && is_array($ventas)) {
                        foreach ($ventas as $row) { ?>
                            <tr>
                                <td><?= $row['nombre'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['usuario'] ?></td>
                                <td><?= $row['total_venta'] ?></td>
                                <td><?= $row['fecha'] ?></td>
                                <td><?= $row['id'] ?></td>
                                <th>
                                    <a href="<?php echo base_url('vista_compras/' . $row['id']) ?>" class="btn btn-success btn-sm">Ver Detalle</a>
                                </th>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>