<?php
$session = session();
?>

<div class="container mt-5 mb-4 pt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header text-white">
                    <h2 class="text-center">Detalle de ventas del Administrador</h2>
                </div>
                <div class="card-body">
                    
                    <!-- Mostrar mensaje Flash si existe -->
                    <?php if (session()->getFlashdata('mensaje')): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('mensaje') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Formulario de filtros -->
                    <div class="card mb-4">
                        <div class="card-header text-white">
                            <h5 class="mb-0">Filtrar Ventas</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('admin-ventas') ?>" method="get" class="row g-3">
                                <div class="col-md-3">
                                    <label for="fecha_desde" class="form-label">Fecha desde</label>
                                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                                           value="<?= isset($filtros['fecha_desde']) ? $filtros['fecha_desde'] : '' ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="fecha_hasta" class="form-label">Fecha hasta</label>
                                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                                           value="<?= isset($filtros['fecha_hasta']) ? $filtros['fecha_hasta'] : '' ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="cliente" class="form-label">Cliente</label>
                                    <input type="text" class="form-control" id="cliente" name="cliente" placeholder="Buscar por nombre o apellido" value="<?= isset($filtros['cliente']) ? esc($filtros['cliente']) : '' ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="monto_min" class="form-label">Monto mínimo</label>
                                    <input type="number" class="form-control" id="monto_min" name="monto_min" 
                                           value="<?= isset($filtros['monto_min']) ? $filtros['monto_min'] : '' ?>" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label for="monto_max" class="form-label">Monto máximo</label>
                                    <input type="number" class="form-control" id="monto_max" name="monto_max" 
                                           value="<?= isset($filtros['monto_max']) ? $filtros['monto_max'] : '' ?>" step="0.01">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-filter"></i> Filtrar
                                    </button>
                                    <a href="<?= base_url('admin-ventas') ?>" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Limpiar filtros
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if (empty($ventas)): ?>
                        <div class="alert alert-info text-center" role="alert">
                            <h4 class="alert-heading">No hay ventas registradas en el sistema</h4>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr class="text-center">
                                        <th>N° Venta</th>
                                        <th>Nombre cliente</th>
                                        <th>Email</th>
                                        <th>Usuario</th>
                                        <th>Total</th>
                                        <th>Fecha</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($ventas as $venta): ?>
                                    <tr class="text-center">
                                        <td><?= $venta['id'] ?></td>
                                        <td><?= $venta['nombre'] . ' ' . $venta['apellido'] ?></td>
                                        <td><?= $venta['email'] ?></td>
                                        <td><?= $venta['usuario'] ?></td>
                                        <td>$<?= number_format($venta['total_venta'], 2) ?></td>
                                        <td><?= $venta['fecha'] ?></td>
                                        <td>
                                            <a href="<?= base_url('detalle-venta/'.$venta['id']) ?>" class="btn btn-sm btn-success">
                                                <i class="fas fa-eye"></i> Ver Detalle
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total de Ventas:</strong></td>
                                        <td class="text-center">
                                            <?php 
                                            $total = 0;
                                            foreach($ventas as $venta) {
                                                $total += $venta['total_venta'];
                                            }
                                            echo '$' . number_format($total, 2);
                                            ?>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Cantidad de Ventas:</strong></td>
                                        <td class="text-center"><?= count($ventas) ?></td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validar fechas
    var fechaDesde = document.getElementById('fecha_desde');
    var fechaHasta = document.getElementById('fecha_hasta');
    
    if(fechaDesde && fechaHasta) {
        fechaHasta.addEventListener('change', function() {
            if(fechaDesde.value && fechaHasta.value && fechaHasta.value < fechaDesde.value) {
                alert('La fecha hasta no puede ser menor que la fecha desde');
                fechaHasta.value = fechaDesde.value;
            }
        });
        
        fechaDesde.addEventListener('change', function() {
            if(fechaDesde.value && fechaHasta.value && fechaHasta.value < fechaDesde.value) {
                fechaHasta.value = fechaDesde.value;
            }
        });
    }

    // Búsqueda en vivo por cliente
    const clienteInput = document.getElementById('cliente');
    if (clienteInput) {
        clienteInput.addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            const filas = document.querySelectorAll('table tbody tr');
            filas.forEach(function(fila) {
                const nombre = fila.children[1].textContent.toLowerCase();
                if (nombre.includes(filtro)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    }
});
</script>
