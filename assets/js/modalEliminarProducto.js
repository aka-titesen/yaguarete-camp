// JS para el modal de confirmación de eliminación de producto
document.querySelectorAll('.btn-eliminar-producto').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const action = this.getAttribute('data-action');
        document.getElementById('formEliminarProducto').action = action;
        var modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
        modal.show();
    });
});
