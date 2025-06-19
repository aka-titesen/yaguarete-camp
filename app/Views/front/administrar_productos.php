<?php // Vista de gestión de productos para el administrador ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <!-- Estilos personalizados -->
    <link href="<?= base_url('assets/css/miestilo.css') ?>" rel="stylesheet">
    <!-- Importa los estilos del modal de edición de producto -->
    <link rel="stylesheet" href="<?= base_url('assets/css/modal.css') ?>">
    <!-- FontAwesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-dyZtM6zQ+1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q6Q1Qb1Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="container mt-5 pt-4">
        <h2 class="mb-4 text-center"><i class="fas fa-box"></i> Gestión de Productos</h2>

        <!-- Botón para abrir el modal de agregar producto -->
        <div class="text-end mb-3">
            <button class="btn btn-verde-selva" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
                <i class="fas fa-plus"></i> Añadir producto
            </button>
        </div>

        <!-- Modal para añadir producto -->
        <div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-labelledby="modalAgregarProductoLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-verde-selva text-white">
                        <h5 class="modal-title" id="modalAgregarProductoLabel"><i class="fas fa-box"></i> Añadir
                            producto
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <form method="post" action="<?= base_url('/crear-producto') ?>" enctype="multipart/form-data" id="formCrearProducto">
                        <?= csrf_field(); ?>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre_prod" maxlength="100"
                                    required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$" title="Solo letras y espacios">
                            </div>
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="categoria_id" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria_id" name="categoria_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    <!-- Aquí deberías cargar dinámicamente las categorías -->
                                    <option value="1">Camping</option>
                                    <option value="2">Pesca</option>
                                    <option value="3">Ropa</option>
                                    <option value="4">Calzado</option>
                                    <option value="5">Mochilas</option>
                                    <option value="6">Accesorios</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio de compra</label>
                                <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="precio_vta" class="form-label">Precio de venta</label>
                                <input type="number" class="form-control" id="precio_vta" name="precio_vta" min="0"
                                    step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label for="stock_min" class="form-label">Stock mínimo</label>
                                <input type="number" class="form-control" id="stock_min" name="stock_min" min="0"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-cta w-100">
                                <i class="fas fa-check"></i> Crear producto
                            </button>
                        </div>
                    </form>
                    <!-- Mostrar errores en la modal de alta -->
                    <?php if (isset($errores) && count($errores) > 0 && !isset($edit_id)): ?>
                      <div class="alert alert-danger">
                        <ul class="mb-0">
                          <?php foreach ($errores as $error): ?>
                            <li><?= esc($error) ?></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    <?php endif; ?>
                    <script>
                    // Validación JS para alta de producto
                    document.getElementById('formCrearProducto').addEventListener('submit', function(e) {
                        let errores = [];
                        const nombre = document.getElementById('nombre').value.trim();
                        if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(nombre)) errores.push('El nombre solo debe contener letras y espacios.');
                        const precio = parseFloat(document.getElementById('precio').value);
                        const precio_vta = parseFloat(document.getElementById('precio_vta').value);
                        const stock = parseInt(document.getElementById('stock').value);
                        const stock_min = parseInt(document.getElementById('stock_min').value);
                        const imagen = document.getElementById('imagen').files[0];
                        if (nombre.length < 3) errores.push('El nombre debe tener al menos 3 caracteres.');
                        if (precio_vta < precio) errores.push('El precio de venta debe ser mayor o igual al de compra.');
                        if (stock_min > stock) errores.push('El stock mínimo no puede ser mayor al stock actual.');
                        if (imagen) {
                            const tipos = ['image/jpeg','image/png','image/gif','image/webp'];
                            if (!tipos.includes(imagen.type)) errores.push('La imagen debe ser JPG, PNG, GIF o WEBP.');
                            if (imagen.size > 2*1024*1024) errores.push('La imagen no debe superar los 2MB.');
                        }
                        if (errores.length > 0) {
                            e.preventDefault();
                            alert(errores.join('\n'));
                        }
                    });
                    // Validación JS para edición de producto
                    document.getElementById('formEditarProducto').addEventListener('submit', function(e) {
                        let errores = [];
                        const nombre = document.getElementById('edit_nombre_prod').value.trim();
                        if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(nombre)) errores.push('El nombre solo debe contener letras y espacios.');
                        const precio = parseFloat(document.getElementById('edit_precio').value);
                        const precio_vta = parseFloat(document.getElementById('edit_precio_vta').value);
                        const stock = parseInt(document.getElementById('edit_stock').value);
                        const stock_min = parseInt(document.getElementById('edit_stock_min').value);
                        const imagen = document.getElementById('edit_imagen').files[0];
                        if (nombre.length < 3) errores.push('El nombre debe tener al menos 3 caracteres.');
                        if (precio_vta < precio) errores.push('El precio de venta debe ser mayor o igual al de compra.');
                        if (stock_min > stock) errores.push('El stock mínimo no puede ser mayor al stock actual.');
                        if (imagen) {
                            const tipos = ['image/jpeg','image/png','image/gif','image/webp'];
                            if (!tipos.includes(imagen.type)) errores.push('La imagen debe ser JPG, PNG, GIF o WEBP.');
                            if (imagen.size > 2*1024*1024) errores.push('La imagen no debe superar los 2MB.');
                        }
                        if (errores.length > 0) {
                            e.preventDefault();
                            alert(errores.join('\n'));
                        }
                    });
                    </script>
                </div>
            </div>
        </div>

        <!-- Modal para editar producto -->
        <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-labelledby="modalEditarProductoLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-verde-selva text-white">
                        <h5 class="modal-title text-amarillo-sistema" id="modalEditarProductoLabel"><i
                                class="fas fa-edit"></i> Editar producto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <form id="formEditarProducto" method="post" enctype="multipart/form-data">
                        <?= csrf_field(); ?>
                        <div class="modal-body">
                            <input type="hidden" id="edit_id" name="id">
                            <div class="mb-3">
                                <label for="edit_nombre_prod" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="edit_nombre_prod" name="nombre_prod"
                                    maxlength="100" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$" title="Solo letras y espacios">
                            </div>
                            <div class="mb-3">
                                <label for="edit_imagen" class="form-label">Imagen (opcional)</label>
                                <input type="file" class="form-control" id="edit_imagen" name="imagen" accept="image/*">
                                <div id="edit_imagen_actual" class="mt-2"></div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_categoria_id" class="form-label">Categoría</label>
                                <select class="form-select" id="edit_categoria_id" name="categoria_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    <option value="1">Camping</option>
                                    <option value="2">Pesca</option>
                                    <option value="3">Ropa</option>
                                    <option value="4">Calzado</option>
                                    <option value="5">Mochilas</option>
                                    <option value="6">Accesorios</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_precio" class="form-label">Precio de compra</label>
                                <input type="number" class="form-control" id="edit_precio" name="precio" min="0"
                                    step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_precio_vta" class="form-label">Precio de venta</label>
                                <input type="number" class="form-control" id="edit_precio_vta" name="precio_vta" min="0"
                                    step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="edit_stock" name="stock" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_stock_min" class="form-label">Stock mínimo</label>
                                <input type="number" class="form-control" id="edit_stock_min" name="stock_min" min="0"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-cta w-100">
                                <i class="fas fa-save"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                    <!-- Mostrar errores en la modal de edición -->
                    <?php if (isset($errores) && count($errores) > 0 && isset($edit_id)): ?>
                      <div class="alert alert-danger">
                        <ul class="mb-0">
                          <?php foreach ($errores as $error): ?>
                            <li><?= esc($error) ?></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación de eliminación de producto -->
        <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalConfirmarEliminarLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalConfirmarEliminarLabel"><i class="fas fa-trash-alt"></i>
                            Confirmar eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar este producto?</p>
                    </div>
                    <div class="modal-footer">
                        <form id="formEliminarProducto" method="post">
                            <?= csrf_field(); ?>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación de reactivación de producto -->
        <div class="modal fade" id="modalConfirmarReactivar" tabindex="-1"
            aria-labelledby="modalConfirmarReactivarLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="modalConfirmarReactivarLabel"><i class="fas fa-undo"></i> Confirmar
                            reactivación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas reactivar este producto?</p>
                    </div>
                    <div class="modal-footer">
                        <form id="formReactivarProducto" method="post">
                            <?= csrf_field(); ?>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Reactivar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtro de productos activos/eliminados -->
        <div class="mb-3 d-flex justify-content-end align-items-center">
            <label class="me-2 mb-0 fw-bold">Ver:</label>
            <select id="filtroProductos" class="form-select w-auto">
                <option value="activos" selected>Productos actuales</option>
                <option value="eliminados">Productos dados de baja</option>
            </select>
        </div>

        <!-- Buscador por nombre de producto -->
        <div class="row mb-3 justify-content-end">
            <div class="col-auto buscador-con-icono">
                <i class="fas fa-search input-search-icon"></i>
                <input type="text" id="buscadorNombreProducto" class="form-control buscador-input" placeholder="Buscar por nombre...">
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
                            <th scope="col">Costo</th>
                            <th scope="col">Precio venta</th>
                            <th scope="col">Stock</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaProductosBody">
                        <?php if (!empty($productos)): ?>
                            <?php foreach ($productos as $producto): ?>
                                <?php if ($producto['eliminado'] !== 'SI'): ?>
                                    <tr class="producto-row producto-activo">
                                        <td>
                                            <?php if (!empty($producto['imagen'])): ?>
                                                <img src="<?= base_url('assets/uploads/' . esc($producto['imagen'])) ?>" alt="Miniatura"
                                                    width="60" height="60" style="object-fit:cover; border-radius:8px;">
                                            <?php else: ?>
                                                <span class="text-muted">Sin imagen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($producto['nombre_prod']) ?></td>
                                        <td><?= esc([
                                                '1' => 'Camping',
                                                '2' => 'Pesca',
                                                '3' => 'Ropa',
                                                '4' => 'Calzado',
                                                '5' => 'Mochilas',
                                                '6' => 'Accesorios',
                                            ][$producto['categoria_id']] ?? $producto['categoria_id']) ?></td>
                                        <td>$<?= number_format($producto['precio'], 2, ',', '.') ?></td>
                                        <td>$<?= number_format($producto['precio_vta'], 2, ',', '.') ?></td>
                                        <td><?= esc($producto['stock']) ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1 btn-editar-producto"
                                                title="Editar" data-id="<?= $producto['id'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger me-1 btn-eliminar-producto"
                                                title="Eliminar" data-id="<?= $producto['id'] ?>"
                                                data-action="<?= base_url('deleteproducto/' . $producto['id']) ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr class="producto-row producto-eliminado" style="display:none;">
                                        <td>
                                            <?php if (!empty($producto['imagen'])): ?>
                                                <img src="<?= base_url('assets/uploads/' . esc($producto['imagen'])) ?>" alt="Miniatura"
                                                    width="60" height="60" style="object-fit:cover; border-radius:8px;">
                                            <?php else: ?>
                                                <span class="text-muted">Sin imagen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($producto['nombre_prod']) ?></td>
                                        <td><?= esc([
                                                '1' => 'Camping',
                                                '2' => 'Pesca',
                                                '3' => 'Ropa',
                                                '4' => 'Calzado',
                                                '5' => 'Mochilas',
                                                '6' => 'Accesorios',
                                            ][$producto['categoria_id']] ?? $producto['categoria_id']) ?></td>
                                        <td>$<?= number_format($producto['precio'], 2, ',', '.') ?></td>
                                        <td>$<?= number_format($producto['precio_vta'], 2, ',', '.') ?></td>
                                        <td><?= esc($producto['stock']) ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-success me-1 btn-activar-producto"
                                                title="Reactivar" data-id="<?= $producto['id'] ?>"
                                                data-action="<?= base_url('activarproducto/' . $producto['id']) ?>">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay productos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- NO QUITAR SALTOS DE LINEA -->
     <br>
     <br>
    <script>
        // Variables globales para JS externo
        window.productosData = <?php echo json_encode($productos); ?>;
        window.baseUploadsUrl = "<?= base_url('assets/uploads/') ?>";
        window.baseEditUrl = "<?= base_url('ProductoController/modifica/') ?>";
    </script>
    <script src="<?= base_url('assets/js/editarProducto.js') ?>"></script>
    <script src="<?= base_url('assets/js/modalEliminarProducto.js') ?>"></script>
    <script src="<?= base_url('assets/js/filtroProductos.js') ?>"></script>
    <script src="<?= base_url('assets/js/reactivarProducto.js') ?>"></script>
    <script src="<?= base_url('assets/js/modalReactivarProducto.js') ?>"></script>
    <script>
        // Buscador por nombre de producto
        document.addEventListener('DOMContentLoaded', function() {
            const buscador = document.getElementById('buscadorNombreProducto');
            const filas = document.querySelectorAll('#tablaProductosBody tr');
            buscador.addEventListener('input', function() {
                const texto = this.value.toLowerCase();
                filas.forEach(fila => {
                    const nombre = fila.querySelector('td:nth-child(2)');
                    if (nombre && nombre.textContent.toLowerCase().includes(texto)) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>

</html>