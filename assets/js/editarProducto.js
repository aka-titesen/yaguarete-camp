// Script para manejar el modal de edición de productos en la vista de administración

document.addEventListener('DOMContentLoaded', function() {
    // Pasar datos del producto al modal de edición
    const productos = window.productosData || [];
    document.querySelectorAll('.btn-editar-producto').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const producto = productos.find(p => p.id == id);
            if (producto) {
                document.getElementById('edit_id').value = producto.id;
                document.getElementById('edit_nombre_prod').value = producto.nombre_prod;
                document.getElementById('edit_categoria_id').value = producto.categoria_id;
                document.getElementById('edit_precio').value = producto.precio;
                document.getElementById('edit_precio_vta').value = producto.precio_vta;
                document.getElementById('edit_stock').value = producto.stock;
                document.getElementById('edit_stock_min').value = producto.stock_min;
                // Imagen actual
                let imagenHtml = '';
                if (producto.imagen) {
                    imagenHtml = `<img src='${window.baseUploadsUrl}/${producto.imagen}' alt='Imagen actual' width='60' class='rounded mb-2'><br><span class='text-muted'>Imagen actual</span>`;
                } else {
                    imagenHtml = `<span class='text-muted'>Sin imagen</span>`;
                }
                document.getElementById('edit_imagen_actual').innerHTML = imagenHtml;
                // Set form action
                document.getElementById('formEditarProducto').action = `${window.baseEditUrl}${producto.id}`;
                // Mostrar modal
                var modal = new bootstrap.Modal(document.getElementById('modalEditarProducto'));
                modal.show();
            }
        });
    });
});
