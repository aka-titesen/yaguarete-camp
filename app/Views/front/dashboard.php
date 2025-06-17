<?php // Solo el contenido principal del dashboard, sin head, body ni includes de navbar/footer/header
?>
<div class="container mt-5 pt-4">
    <h1 class="mb-4 text-center"><i class="fas fa-tools"></i> Panel de Administración</h1>
        <br>
        <div class="row g-4">
        <div class="col-md-6">
            <div class="card card-dashboard h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-box fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Productos</h5>
                    <p class="card-text">Gestiona el catálogo de productos: crear, modificar o eliminar productos.</p>
                    <a href="<?= base_url('administrarProductos')?>" class="btn btn-verde-selva w-100 dashboard-link">Administrar productos</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-dashboard h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text">Administra los usuarios del sistema: crear, modificar, eliminar y asignar roles.</p>
                    <a href="#" class="btn btn-verde-selva w-100 dashboard-link">Administrar usuarios</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-dashboard h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Ventas</h5>
                    <p class="card-text">Consulta y filtra todas las ventas realizadas en la plataforma.</p>
                    <a href="<?= base_url('admin-ventas')?>" class="btn btn-verde-selva w-100 dashboard-link">Administrar ventas</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-dashboard h-100 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-envelope fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Consultas y Contacto</h5>
                    <p class="card-text">Gestiona mensajes y consultas recibidas desde el formulario de contacto.</p>
                    <a href="<?= base_url('admin-consultas') ?>" class="btn btn-verde-selva w-100 dashboard-link">Ver consultas</a>
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
</div>