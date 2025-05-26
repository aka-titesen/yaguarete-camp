<?php // Solo el contenido principal del dashboard, sin head, body ni includes de navbar/footer/header
?>
<div class="container mt-5 pt-4">
    <h1 class="mb-4 text-center"><i class="fas fa-tools"></i> Panel de Administración</h1>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-dashboard h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-box fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Productos</h5>
                    <p class="card-text">Gestiona el catálogo de productos: crear, modificar o eliminar productos.</p>
                    <a href="<?= base_url('administrarProductos')?>" class="btn btn-verde-selva w-100 dashboard-link">Administrar productos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-dashboard h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text">Administra los usuarios del sistema: crear, modificar, eliminar y asignar
                        roles.</p>
                    <a href="#" class="btn btn-verde-selva w-100 dashboard-link">Administrar usuarios</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-dashboard h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Órdenes</h5>
                    <p class="card-text">Visualiza y gestiona las órdenes de compra realizadas por los clientes.</p>
                    <a href="#" class="btn btn-verde-selva w-100 dashboard-link">Ver órdenes</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-dashboard h-100 shadow-sm mt-4">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Reportes</h5>
                    <p class="card-text">Genera reportes de ventas, productos más vendidos y actividad de usuarios.</p>
                    <a href="#" class="btn btn-verde-selva w-100 dashboard-link">Ver reportes</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-dashboard h-100 shadow-sm mt-4">
                <div class="card-body text-center">
                    <i class="fas fa-cogs fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Configuración</h5>
                    <p class="card-text">Ajusta parámetros del sistema, métodos de pago, envíos y más.</p>
                    <a href="#" class="btn btn-verde-selva w-100 dashboard-link">Configuración</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-dashboard h-100 shadow-sm mt-4">
                <div class="card-body text-center">
                    <i class="fas fa-envelope fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Consultas y Contacto</h5>
                    <p class="card-text">Gestiona mensajes y consultas recibidas desde el formulario de contacto.</p>
                    <a href="#" class="btn btn-verde-selva w-100 dashboard-link">Ver consultas</a>
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
</div>