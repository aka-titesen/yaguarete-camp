<?php
$session = session();
$fecha_compra = '';
$nro_orden = '';

// Debug - Verificar qué datos llegaron a la vista
log_message('debug', 'vista_compras - ¿cabecera definida? ' . (isset($cabecera) ? 'SÍ' : 'NO'));
log_message('debug', 'vista_compras - ¿venta definida? ' . (isset($venta) ? 'SÍ' : 'NO'));
log_message('debug', 'vista_compras - Tipo de $venta: ' . (is_array($venta) ? 'Array' : gettype($venta)));

// Verificar si hay datos en la cabecera (viene del controlador)
if (!empty($cabecera) && is_array($cabecera)) {
    $fecha_compra = $cabecera['fecha'] ?? '';
    $nro_orden = $cabecera['id'] ?? '';
    // Para depuración - Verificar el formato de fecha
    log_message('debug', 'Fecha de compra (cabecera): ' . $fecha_compra);
    
    // Si llegamos aquí, los datos de cabecera están bien
    log_message('debug', 'Datos de cabecera completos: ' . print_r($cabecera, true));
}
// Como backup, obtener los datos de compra de la primera fila de detalles si hay resultados
else if (!empty($venta) && is_array($venta) && count($venta) > 0) {
    $primera_fila = $venta[0];
    log_message('debug', 'Primera fila de venta: ' . print_r($primera_fila, true));
    
    $fecha_compra = $primera_fila['fecha'] ?? '';
    $nro_orden = $primera_fila['venta_id'] ?? ($primera_fila['cabecera_id'] ?? ($primera_fila['id'] ?? ''));
    
    // Para depuración - Verificar el formato de fecha
    log_message('debug', 'Fecha de compra (primera fila): ' . $fecha_compra);
} else {
    log_message('warning', 'No se encontraron datos de cabecera ni detalles');
}

// Debug - Mostrar información de los datos disponibles
log_message('debug', 'Compra ID: ' . $nro_orden);
log_message('debug', 'Fecha formateada: ' . (empty($fecha_compra) ? 'Vacía' : date('d/m/Y', strtotime($fecha_compra))));
log_message('debug', 'Total filas en detalle: ' . (is_array($venta) ? count($venta) : 'No es un array'));
?>

<div class="container mt-5 pt-4">
    <!-- Botón para volver a mis compras -->                <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?= base_url('mis-compras') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Volver a mis compras
        </a>
        <?php if (!empty($fecha_compra)): ?>
        <div class="text-muted">
            <i class="fas fa-calendar-alt me-2"></i> 
            <?php 
                // Debug de fecha
                log_message('debug', 'Formato fecha en vista: ' . $fecha_compra);
                // Asegurar el formato correcto
                try {
                    echo date('d/m/Y', strtotime($fecha_compra));
                } catch (Exception $e) {
                    echo $fecha_compra;
                    log_message('error', 'Error al formatear fecha: ' . $e->getMessage());
                }
            ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Mostrar mensaje Flash si existe -->
    <?php if (session()->getFlashdata('mensaje')): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <?= session()->getFlashdata('mensaje') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <?php 
    // Debug - Estado de las variables
    log_message('debug', 'vista_compras - ¿$venta está vacío? ' . (empty($venta) ? 'SÍ' : 'NO'));
    log_message('debug', 'vista_compras - ¿$venta es array? ' . (is_array($venta) ? 'SÍ' : 'NO'));
    log_message('debug', 'vista_compras - ¿$cabecera está definido? ' . (isset($cabecera) ? 'SÍ' : 'NO'));
    
    if (empty($venta) || !is_array($venta)) { 
    ?>
        <!-- Estado vacío - Sin detalles de compra -->
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list fa-4x mb-4 text-muted"></i>
                <h3 class="mb-3">No se encontró información de esta compra</h3>
                <p class="text-muted mb-4">Es posible que la compra no exista o no tenga productos asociados.</p>
                <a class="btn btn-verde-selva" href="<?= base_url('mis-compras') ?>">
                    <i class="fas fa-arrow-left me-2"></i> Volver a mis compras
                </a>
                <a class="btn btn-outline-primary ms-2" href="<?= base_url('catalogo') ?>">
                    <i class="fas fa-store me-2"></i> Explorar catálogo
                </a>
            </div>
        </div>
    <?php } else { ?>
    
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-verde-selva text-white d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Detalle de compra #<?= $nro_orden ?>
                </h2>
                <span class="badge bg-light text-dark">Completada</span>
            </div>
            
            <div class="card-body">
                <!-- Información de la factura -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="text-muted mb-2">Información de la compra</h5>
                            <div class="d-flex mb-2">
                                <div class="me-3 text-muted" style="width: 120px;">
                                    <i class="fas fa-receipt me-2"></i> Orden:
                                </div>
                                <strong>#<?= $nro_orden ?></strong>
                            </div>
                            <div class="d-flex mb-2">
                                <div class="me-3 text-muted" style="width: 120px;">
                                    <i class="fas fa-calendar-alt me-2"></i> Fecha:
                                </div>
                                <strong><?= date('d/m/Y', strtotime($fecha_compra)) ?></strong>
                            </div>                                <!-- No mostramos la hora porque el campo solo almacena fecha -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="text-muted mb-2">Cliente</h5>
                            <div class="d-flex mb-2">
                                <div class="me-3 text-muted" style="width: 120px;">
                                    <i class="fas fa-user me-2"></i> Nombre:
                                </div>
                                <strong><?= $session->get('nombre') ?> <?= $session->get('apellido') ?></strong>
                            </div>
                            <div class="d-flex">
                                <div class="me-3 text-muted" style="width: 120px;">
                                    <i class="fas fa-envelope me-2"></i> Email:
                                </div>
                                <strong><?= $session->get('email') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Productos comprados -->
                <h5 class="border-bottom pb-2 mb-3">Productos adquiridos</h5>
                
                <div class="list-group mb-4">                    <?php                    $total = 0;
                    if (!empty($venta) && is_array($venta)) {
                        // Debug - Imprimir la estructura del primer elemento
                        if (!empty($venta[0])) {
                            log_message('debug', 'Detalle vista_compras - Primer elemento: ' . print_r($venta[0], true));
                        } else {
                            log_message('warning', 'Detalle vista_compras - No hay elementos en $venta');
                        }
                        
                        foreach ($venta as $i => $row) {
                            echo '<pre style="background:#fffbe6; color:#333; border:1px solid #ccc; padding:8px;">';
                            print_r($row);
                            echo '</pre>';
                            $nombre = isset($row['nombre_prod']) && $row['nombre_prod'] !== null && $row['nombre_prod'] !== '' ? $row['nombre_prod'] : 'Producto sin nombre';
                            $imagen = isset($row['imagen']) && $row['imagen'] !== null && $row['imagen'] !== '' ? $row['imagen'] : '';
                            // Definir la ruta de la imagen correctamente antes de usarla
                            if (!empty($imagen)) {
                                $ruta_imagen = 'assets/uploads/' . $imagen;
                            } else {
                                $ruta_imagen = 'assets/img/producto-sin-imagen.jpg';
                            }
                            
                            // Manejo más seguro de precio y cantidad
                            $precio = 0;
                            try {
                                if (isset($row['precio']) && $row['precio'] !== null && is_numeric($row['precio']) && floatval($row['precio']) > 0) {
                                    $precio = floatval($row['precio']);
                                    log_message('debug', "Usando precio de venta del detalle: $precio");
                                } elseif (isset($row['precio_vta']) && $row['precio_vta'] !== null && is_numeric($row['precio_vta'])) {
                                    $precio = floatval($row['precio_vta']);
                                    log_message('debug', "Usando precio_vta del producto: $precio");
                                }
                            } catch (Exception $e) {
                                log_message('error', "Error al procesar precio: " . $e->getMessage());
                            }
                            
                            // Manejo seguro de cantidad
                            $cantidad = 1; // Valor predeterminado
                            try {
                                if (isset($row['cantidad']) && $row['cantidad'] !== null && is_numeric($row['cantidad'])) {
                                    $cantidad = max(1, intval($row['cantidad'])); // Asegurar que sea al menos 1
                                }
                            } catch (Exception $e) {
                                log_message('error', "Error al procesar cantidad: " . $e->getMessage());
                            }
                            
                            $subtotal = $precio * $cantidad;
                            $total += $subtotal;
                            // No tenemos categoría en esta consulta específica
                            $categoria = "Producto";
                            
                            // Debug - Información para cada producto
                            log_message('debug', "Producto $i: $nombre, Precio: $precio, Cantidad: $cantidad, Subtotal: $subtotal, Imagen: $imagen"); 
                    ?>
                        <div class="list-group-item list-group-item-action flex-column align-items-start p-3 border mb-2 rounded shadow-sm">
                            <div class="d-flex w-100">
                                <div class="me-3 product-image-container">
                                    <img class="product-image rounded shadow-sm"
                                         src="<?= base_url($ruta_imagen) ?>"
                                         alt="<?= esc($nombre) ?>"
                                         onerror="this.src='<?= base_url('assets/img/producto-sin-imagen.jpg') ?>'"
                                         style="width: 120px; height: 90px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-top mb-1">
                                        <h5 class="mb-0"><?= esc($nombre) ?></h5>
                                        <span class="text-success fw-bold">$<?= number_format($subtotal, 2, ',', '.') ?></span>
                                    </div>
                                    <p class="text-muted small mb-1 text-truncate"></p>
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <div>
                                            <span class="badge bg-light text-dark border"><?= esc($categoria) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-muted me-3">
                                                <i class="fas fa-box me-1"></i> <?= number_format($cantidad) ?> unidad(es)
                                            </span>
                                            <span class="text-primary">
                                                <i class="fas fa-tag me-1"></i> $<?= number_format($precio, 2, ',', '.') ?> c/u
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    } ?>
                </div>
                
                <!-- Resumen de compra -->
                <div class="card bg-light shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span>$<?= number_format($total, 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Envío:</span>
                            <span>Gratis</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0">Total:</h5>
                            <h5 class="mb-0 text-success">$<?= number_format($total, 2, ',', '.') ?></h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer text-center py-3">
                <div class="mb-2">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    <span class="text-success fw-bold">Compra realizada con éxito</span>
                </div>
                <p class="text-muted mb-2">Gracias por su compra en Yaguareté Camp.</p>
                <a class="btn btn-verde-selva" href="<?= base_url('catalogo') ?>">
                    <i class="fas fa-shopping-cart me-2"></i> Seguir comprando
                </a>
            </div>
        </div>
    <?php } ?>
</div>

<!-- Estilos adicionales para la página de detalle de compra -->
<style>
.product-image-container {
    position: relative;
    overflow: hidden;
}

.product-image {
    transition: transform 0.3s;
}

.product-image:hover {
    transform: scale(1.05);
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

.list-group-item {
    transition: transform 0.2s, box-shadow 0.2s;
}

.list-group-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1) !important;
}
</style>
<?php ?>