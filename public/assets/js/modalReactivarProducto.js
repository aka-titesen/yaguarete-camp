// JS para el modal de confirmación de reactivación de producto
document.querySelectorAll('.btn-activar-producto').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const action = this.getAttribute('data-action');
        document.getElementById('formReactivarProducto').action = action;
        var modal = new bootstrap.Modal(document.getElementById('modalConfirmarReactivar'));
        modal.show();
    });
});
