// JS para el filtro de productos activos/eliminados
const filtro = document.getElementById('filtroProductos');
filtro.addEventListener('change', function () {
    const mostrar = this.value;
    document.querySelectorAll('.producto-row').forEach(row => {
        if (mostrar === 'activos') {
            row.style.display = row.classList.contains('producto-activo') ? '' : 'none';
        } else {
            row.style.display = row.classList.contains('producto-eliminado') ? '' : 'none';
        }
    });
});
