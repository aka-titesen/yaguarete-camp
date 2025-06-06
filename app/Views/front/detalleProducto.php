
<div class="container detalle-producto">
    <div class="row">
        <!-- Imagen del producto -->        <div class="col-md-6">
            <?php if (!empty($producto['imagen'])): ?>
                <img src="<?= base_url('assets/uploads/' . esc($producto['imagen'])) ?>" class="producto-imagen" alt="<?= esc($producto['nombre_prod']) ?>">
            <?php else: ?>
                <img src="<?= base_url('assets/img/imagenes_pagina/logo.png') ?>" class="producto-imagen" alt="Sin imagen">
            <?php endif; ?>
        </div>
        
        <!-- Información del producto -->
        <div class="col-md-6 producto-info">
            <h1 class="producto-titulo"><?= esc($producto['nombre_prod']) ?></h1>
            
            <span class="categoria-badge">
                <i class="fas fa-tag me-2"></i>
                <?php
                $categorias = [
                    1 => 'Camping',
                    2 => 'Pesca', 
                    3 => 'Ropa',
                    4 => 'Calzado',
                    5 => 'Mochilas',
                    6 => 'Accesorios'
                ];
                echo esc($categorias[$producto['categoria_id']] ?? 'Sin categoría');
                ?>
            </span>
            
            <div class="precio-principal">$<?= number_format($producto['precio_vta'], 2, ',', '.') ?></div>
            
            <div class="cuotas-info-detalle">
                <i class="fas fa-credit-card me-2"></i>
                3 cuotas sin interés de $<?= number_format($producto['precio_vta'] / 3, 2, ',', '.') ?>
            </div>
            
            <div class="stock-info">
                <i class="fas fa-box me-2"></i>
                <strong>Stock disponible:</strong> <?= esc($producto['stock']) ?> unidades
                <?php if ($producto['stock'] <= $producto['stock_min']): ?>
                    <br><small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>¡Últimas unidades!</small>
                <?php endif; ?>
            </div>
            
            <div class="btn-acciones">
                <div class="d-flex align-items-center mb-3">
                    <input type="number" min="1" max="<?= $producto['stock'] ?>" value="1" class="cantidad-selector" title="Cantidad" aria-label="Cantidad">
                    <button class="btn btn-agregar-carrito-detalle">
                        <i class="fas fa-cart-plus me-2"></i>Agregar al Carrito
                    </button>
                </div>
                
                <button class="btn btn-volver" onclick="history.back()">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Catálogo
                </button>
            </div>
        </div>
    </div>
    
    <!-- Medios de pago aceptados -->
    <div class="row">
        <div class="col-12">
            <div class="medios-pago">
                <h5><i class="fas fa-credit-card me-2"></i>Medios de Pago Aceptados</h5>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="pago-card">
                            <i class="fab fa-cc-visa pago-icon visa"></i>
                            <div>
                                <strong>Visa</strong>
                                <br><small class="text-muted">Crédito y débito</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="pago-card">
                            <i class="fab fa-cc-mastercard pago-icon mastercard"></i>
                            <div>
                                <strong>Mastercard</strong>
                                <br><small class="text-muted">Crédito y débito</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="pago-card">
                            <i class="fab fa-cc-amex pago-icon amex"></i>
                            <div>
                                <strong>American Express</strong>
                                <br><small class="text-muted">Crédito</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="pago-card">
                            <i class="fas fa-mobile-alt pago-icon mercadopago"></i>
                            <div>
                                <strong>MercadoPago</strong>
                                <br><small class="text-muted">Transferencia inmediata</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="pago-card">
                            <i class="fas fa-money-bill-wave pago-icon efectivo"></i>
                            <div>
                                <strong>Efectivo</strong>
                                <br><small class="text-muted">En sucursal</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="pago-card">
                            <i class="fas fa-university pago-icon transferencia"></i>
                            <div>
                                <strong>Transferencia Bancaria</strong>
                                <br><small class="text-muted">CBU disponible</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
      <!-- Información adicional -->
    <div class="row info-adicional">
        <div class="col-12">
            <div class="card info-card">
                <div class="card-header info-header bg-verde-selva text-white">
                    <h5 class="mb-0 text-center"><i class="fas fa-info-circle me-2"></i>Información Adicional</h5>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-4 info-item">
                            <h6><i class="fas fa-truck me-2 text-primary"></i>Envío</h6>
                            <p class="text-muted">Envío gratis en compras superiores a $50.000</p>
                        </div>
                        <div class="col-md-4 info-item">
                            <h6><i class="fas fa-shield-alt me-2 text-success"></i>Garantía</h6>
                            <p class="text-muted">6 meses de garantía del fabricante</p>
                        </div>
                        <div class="col-md-4 info-item">
                            <h6><i class="fas fa-undo me-2 text-warning"></i>Devolución</h6>
                            <p class="text-muted">15 días para devolución sin uso</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad del selector de cantidad
    const cantidadInput = document.querySelector('.cantidad-selector');
    const maxStock = <?= $producto['stock'] ?>;
    
    cantidadInput.addEventListener('change', function() {
        if (this.value > maxStock) {
            this.value = maxStock;
        }
        if (this.value < 1) {
            this.value = 1;
        }
    });
    
    // Funcionalidad del botón agregar al carrito
    document.querySelector('.btn-agregar-carrito-detalle').addEventListener('click', function() {
        const cantidad = cantidadInput.value;
        
        // Aquí puedes agregar la lógica para agregar al carrito
        alert(`Producto agregado al carrito. Cantidad: ${cantidad}`);
        
        // Ejemplo de animación del botón
        this.innerHTML = '<i class="fas fa-check me-2"></i>¡Agregado!';
        this.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
          setTimeout(() => {
            this.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Agregar al Carrito';
            this.style.background = 'linear-gradient(135deg, var(--verde-selva), #1e4a36)';
        }, 2000);
    });
});
</script>
