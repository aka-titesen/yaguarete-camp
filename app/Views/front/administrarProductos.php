<?php // Vista de gestión de productos para el administrador ?>
<div class="container mt-5 pt-4">
    <h2 class="mb-4 text-center"><i class="fas fa-box"></i> Gestión de Productos</h2>

    <!-- Botón para abrir el modal de agregar producto -->
    <div class="text-end mb-3">
        <button class="btn btn-verde-selva" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
            <i class="fas fa-plus"></i> Añadir producto
        </button>
    </div>

    <!-- Modal para añadir producto -->
    <div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-verde-selva text-white">
                    <h5 class="modal-title" id="modalAgregarProductoLabel"><i class="fas fa-box"></i> Añadir producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form method="post" action="<?= base_url('admin/productos/crear') ?>" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label for="imagen" class="form-label">Imagen</label>
                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <option value="">Selecciona una categoría</option>
                                <!-- Aquí deberías cargar dinámicamente las categorías -->
                                <option value="1">Camping</option>
                                <option value="2">Montañismo</option>
                                <option value="3">Pesca</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="costo" class="form-label">Costo</label>
                            <input type="number" class="form-control" id="costo" name="costo" min="0" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock_min" class="form-label">Stock mínimo</label>
                            <input type="number" class="form-control" id="stock_min" name="stock_min" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="eliminado" class="form-label">Estado</label>
                            <select class="form-select" id="eliminado" name="eliminado" required>
                                <option value="NO" selected>Activo</option>
                                <option value="SI">Eliminado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-cta w-100">
                            <i class="fas fa-check"></i> Crear producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Listado de productos -->
    <div class="card mt-4">
        <div class="card-header bg-verde-selva text-negro">
            <i class="fas fa-list"></i> Productos registrados
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Miniatura</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Categoría</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Stock</th>
                        <th scope="col" class="text-center">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>