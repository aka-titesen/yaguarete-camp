<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Mi carrito</h2>
            
            <!-- Mostrar mensaje Flash si existe -->
            <?php if (session()->getFlashdata('mensaje')): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('mensaje') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <div id="cartContent">
                        <!-- Aquí se carga el contenido del carrito desde carritoVista.php -->
                        <?php 
                            echo view('front/carrito_vista');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script para limpiar el carrito
document.addEventListener('DOMContentLoaded', function() {
    const btnLimpiarCarrito = document.getElementById('btnLimpiarCarrito');
    if (btnLimpiarCarrito) {
        btnLimpiarCarrito.addEventListener('click', async function() {
            if (confirm('¿Estás seguro que quieres vaciar el carrito?')) {
                await fetch('<?= base_url('borrar') ?>');
                window.location.reload();
            }
        });
    }
});
</script>
