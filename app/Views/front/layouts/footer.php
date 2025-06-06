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
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <input type="text" class="form-control" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                                title="El nombre no puede contener números ni caracteres especiales"
                                placeholder="Nombre completo" required />
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Correo electrónico"
                                pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$" title="Por favor, ingresa un correo válido"
                                required />
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Asunto" />
                        </div>
                        <div class="mb-3">
                            <input type="file" class="form-control" />
                            <small class="text-muted">Adjunta comprobante si es necesario</small>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="4" placeholder="Tu mensaje..." required></textarea>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-cta">Enviar mensaje</button>
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
    </script>
</footer>
</body>

</html>