<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row justify-content-center align-items-stretch text-center text-md-start">
            <!-- Sección Newsletter -->
            <div class="col-12 col-md-4 d-flex flex-column align-items-center align-items-md-start justify-content-center mb-4 mb-md-0">
                <div class="w-100 mb-2 text-center text-md-start">
                    <h4 class="fw-bold mb-1" style="color:#2e5d3b;"><i class="fas fa-briefcase me-2"></i> Mantente
                        informado</h4>
                    <p class="mb-2" style="color:#e1b91a; font-style:italic; font-weight:500;">Recibe novedades, ofertas
                        exclusivas y consejos para tus aventuras</p>
                </div>
                <form class="d-flex w-100 justify-content-center justify-content-md-start gap-2"
                    style="max-width:600px;" id="newsletterForm">
                    <input type="email" class="form-control" placeholder="Tu email" required
                        style="min-width:220px;">
                    <button type="submit" class="btn btn-cta fw-bold" style="border-radius:0;">Suscribirse</button>
                </form>
            </div>
            <!-- Sección Métodos de Pago -->
            <div class="col-12 col-md-4 d-flex flex-column align-items-center justify-content-center mb-4 mb-md-0">
                <h5 class="text-center mb-3">Métodos de Pago</h5>
                <ul class="list-unstyled d-flex flex-wrap justify-content-center align-items-center gap-3 mb-0">                    <li>
                        <a href="https://www.visa.com.ar" target="_blank">
                            <img src="<?= base_url('assets/img/metodos_pago/visa.png') ?>" alt="Visa" class="img-fluid logo-hover"
                                style="width: 70px;">
                        </a>
                    </li>                    <li>
                        <a href="https://www.mastercard.com.ar" target="_blank">
                            <img src="<?= base_url('assets/img/metodos_pago/masterCard.png') ?>" alt="MasterCard"
                                class="img-fluid logo-hover" style="width: 70px;">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.mercadopago.com.ar" target="_blank">
                            <img src="<?= base_url('assets/img/metodos_pago/mercadoPago.png') ?>" alt="Mercado Pago"
                                class="img-fluid logo-hover" style="width: 120px;">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.pagofacil.com.ar" target="_blank">
                            <img src="<?= base_url('assets/img/metodos_pago/pagoFacil.png') ?>" alt="Pago Facil"
                                class="img-fluid logo-hover" style="width: 50px;">
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Sección Formulario de Contacto y Redes Sociales -->
            <div class="col-12 col-md-4 d-flex flex-column align-items-center align-items-md-end justify-content-center">
                <button class="btn btn-cta shadow mb-3" style="border-radius:2rem; min-width:170px;" data-bs-toggle="modal"
                    data-bs-target="#contactoModal">
                    <i class="fas fa-envelope me-2"></i>Contacto
                </button>
                <p class="mt-2 text-white small mb-2">Comunícate con nosotros</p>
                <div class="d-flex gap-3 justify-content-center justify-content-md-end mb-3">
                    <a href="https://www.facebook.com" target="_blank" aria-label="Facebook">
                        <i class="fab fa-facebook fs-4 text-white"></i>
                    </a>
                    <a href="https://www.instagram.com" target="_blank" aria-label="Instagram">
                        <i class="fab fa-instagram fs-4 text-white"></i>
                    </a>
                    <a href="https://wa.me/549123456789" target="_blank" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp fs-4 text-white"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Contacto -->
    <div class="modal fade" id="contactoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-verde-selva text-white">
                    <h5 class="modal-title">Deja tu consulta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>                <div class="modal-body">
                    <form id="contactForm" method="post">
                        <!-- Mensaje de feedback para el usuario -->
                        <div id="formFeedback"></div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="text" name="nombre" class="form-control" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                                    title="El nombre no puede contener números ni caracteres especiales"
                                    placeholder="Nombre" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="text" name="apellido" class="form-control" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                                    title="El apellido no puede contener números ni caracteres especiales"
                                    placeholder="Apellido" required />
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Correo electrónico"
                                    pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$" title="Por favor, ingresa un correo válido"
                                    required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="tel" name="telefono" class="form-control" placeholder="Teléfono (opcional)" />
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <textarea name="mensaje" class="form-control" rows="4" placeholder="Escribe tu consulta..." required></textarea>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-cta" id="submitConsulta">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="submitSpinner"></span>
                                Enviar consulta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/carouselError.js') ?>"></script>
    <script src="<?= base_url('assets/js/timer.js') ?>"></script>
    <script>
    // Mensaje "próximamente" al enviar newsletter
    document.getElementById('newsletterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('¡Próximamente te podrás suscribir!');
    });
    
    // Envío del formulario de contacto usando AJAX
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Referencias a elementos del DOM
        const submitBtn = document.getElementById('submitConsulta');
        const spinner = document.getElementById('submitSpinner');
        const feedbackDiv = document.getElementById('formFeedback');
        
        // Desactivar botón y mostrar spinner
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        
        // Obtener los datos del formulario
        const formData = new FormData(this);
        
        // Enviar los datos al servidor
        fetch('<?= base_url('enviar-consulta') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Crear el mensaje de feedback
            feedbackDiv.innerHTML = `
                <div class="alert alert-${data.status === 'success' ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Si fue exitoso, limpiar el formulario
            if (data.status === 'success') {
                document.getElementById('contactForm').reset();
                // Opcional: cerrar el modal después de unos segundos
                setTimeout(() => {
                    const contactModal = bootstrap.Modal.getInstance(document.getElementById('contactoModal'));
                    contactModal.hide();
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            feedbackDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Ocurrió un error inesperado. Inténtalo nuevamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        })
        .finally(() => {
            // Reactivar el botón y ocultar el spinner
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    });
    </script>
</footer>
</body>

</html>