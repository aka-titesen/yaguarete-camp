<nav class="navbar navbar-expand-lg navbar-custom w-100 fixed-top">
  <div class="container-fluid px-2">
    <div class="d-flex justify-content-between align-items-center w-100">
      <!-- Wrapper para logo y promoción -->
      <div class="navbar-brand-wrapper">
        <div class="d-flex align-items-center flex-md-row flex-column">          <a class="navbar-brand d-flex align-items-center" href="<?= base_url('#') ?>">
            <img src="<?= base_url('assets/img/imagenes_pagina/logo.png') ?>" alt="Logo" width="50" height="50" />
            <span class="logo-text text-white ms-2 handwriting-font">Yaguareté Camp</span>
          </a>

          <!-- Leyenda de envío gratis (sin separador) -->
          <div class="shipping-banner d-flex align-items-center ms-md-3">
            <span class="promo-envio highlight-fade"
              style="color: #FFEB3B; font-style: italic; font-size: 0.8em; text-shadow: 1px 1px 1px #000; display: inline-block;">
              <i class="fas fa-shipping-fast"></i> ¡Compras +$70k tienen envío gratis!
            </span>
          </div>
        </div>
      </div>

      <!-- Botón hamburguesa -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>

    <!-- Menú colapsable -->
    <div class="collapse navbar-collapse mt-2" id="navbarNav">
      <ul class="navbar-nav ms-auto nav-spacing">
        
        <!-- Acceso para administradores -->
        <?php if (session()->get('perfil_id') == 1 && session()->get('isLoggedIn')): ?>        <li class="nav-item mx-1">
          <a class="nav-link text-white d-flex align-items-center" href="<?= base_url('dashboard') ?>">
            <i class="fas fa-tools me-1"></i>
            <span class="ms-1">Dashboard</span>
          </a>
        </li>
        <?php endif; ?>
  
          <!-- Menú +info -->
        <li class="nav-item dropdown mx-1">
          <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownInfo" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            + Info
          </a>
          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdownInfo">
            <li><a class="dropdown-item" href="<?= base_url('sobreNosotros') ?>">Sobre nosotros</a></li>
            <li><a class="dropdown-item" href="<?= base_url('termYCondiciones') ?>">Términos y condiciones</a></li>
            <li><a class="dropdown-item" href="<?= base_url('comercializacion') ?>">Comercialización</a></li>
            <li><a class="dropdown-item" href="<?= base_url('contacto') ?>">Contacto</a></li>
          </ul>
        </li>
        
        <!-- Enlace directo al catálogo con icono -->
        <?php if (session()->get('perfil_id') != 1): ?>
        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center" href="<?= base_url('catalogo') ?>">
            <i class="fas fa-th-large me-1"></i> Catálogo
          </a>
        </li>
        <?php endif; ?>
        <!-- Menú Guías -->
        <li class="nav-item dropdown mx-1">
          <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownGuias" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Guías
          </a>
          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdownGuias">
            <li>
              <a class="dropdown-item"
                href="https://tpwd.texas.gov/publications/pwdpubs/media/pwd_bk_k0700_0639e.pdf" target="_blank">Tus primeros pasos</a>
            </li>
            <li><a class="dropdown-item" href="https://www.guiadecamping.com/manual_camping.htm" target="_blank">Camping</a></li>
          </ul>
        </li>        <!-- Menú Usuario -->
        <li class="nav-item dropdown mx-1">
          <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle"></i> <span class="d-lg-none">Mi Cuenta</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" style="min-width: 250px;" aria-labelledby="navbarDropdownUser">
            <?php if (session()->get('isLoggedIn')): ?>
              <li>
                <span class="dropdown-item-text fw-bold">
                  <i class="fas fa-user me-2"></i>
                  <?= esc(session()->get('nombre')) ?> <?= esc(session()->get('apellido')) ?>
                </span>
              </li>
              <!-- DEBUG: Mostrar perfil_id en sesión -->
              <li>
                <span class="dropdown-item-text text-white small">
                  <?php if (session()->get('perfil_id')): ?>
                    Usuario: 
                    <?php 
                      $perfil = session()->get('perfil_id');
                      if ($perfil == 1) {
                        echo 'Administrador';
                      } elseif ($perfil == 2) {
                        echo 'Cliente';
                      } elseif ($perfil == 3) {
                        echo 'Vendedor';
                      } else {
                        echo 'Otro';
                      }
                    ?>
                  <?php endif; ?>
                </span>
              </li>
              <li><hr class="dropdown-divider"></li>
              <!-- Enlace a Mis Compras -->
              <?php if (session()->get('perfil_id') != 1): ?>
              <li>
                <a class="dropdown-item" href="<?= base_url('mis-compras') ?>">
                  <i class="fas fa-shopping-bag me-2"></i> Mis Compras
                </a>
              </li>
              <?php endif; ?>
              <li>
                <a class="dropdown-item" href="<?= base_url('logout') ?>">
                  <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                </a>
              </li>
            <?php else: ?>
              <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
                  <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#registroModal">
                  <i class="fas fa-user-plus me-2"></i> Registrarse
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>

        <li class="nav-item mx-1">
          <?php if (session()->get('perfil_id') != 1): ?>
          <a class="nav-link text-white d-flex align-items-center" href="#" id="btnAbrirCarrito">
            <div class="position-relative me-1">
              <i class="fas fa-shopping-cart"></i>
              <span class="badge bg-danger position-absolute top-0 start-100 translate-middle badge-pill" id="cartCount">0</span>
            </div>
            <span class="ms-1">Carrito</span>
          </a>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- Espaciador para compensar el navbar fijo -->
<div class="navbar-spacer"></div>
</header>

<!-- Modal de Registro - Movido fuera del navbar -->
<div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
  <!--usamos el servicio de validación de codeigniter Services:: validation()-->
  <?php $validation = \Config\Services::validation(); ?>
  <form method="post" action="<?php echo base_url('/enviar-form') ?>">
    <?= csrf_field(); ?> <!-- genera un campo oculto o token de seguridad-->
    <?php if (!empty(session()->getFlashdata('fail'))): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('fail'); ?></div>
    <?php endif ?>
    <?php if (!empty(session()->getFlashdata('success'))): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('success'); ?></div>
    <?php endif ?>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-verde-selva text-white">
          <h5 class="modal-title" id="registroModalLabel">Crear una cuenta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <!--PONER LOS MISMO CAMPOS QUE BASE DE DATOS-->
        <div class="modal-body">
          <form onsubmit="return validarContrasenas()" method="post" action="<?php echo base_url('/enviar-form') ?>">
            <?= csrf_field(); ?>
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre completo</label>
              <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa tu nombre completo"
                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="El nombre no puede contener números ni caracteres especiales"
                required>
            </div>
            <div class="mb-3">
              <label for="apellido" class="form-label">Apellido</label>
              <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingresa tu apellido"
                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="El apellido no puede contener números ni caracteres especiales"
                required>
            </div>
            <div class="mb-3">
              <label for="usuario" class="form-label">Usuario</label>
              <input type="text" class="form-control" id="usuario" name="usuario"
                placeholder="Elige un nombre de usuario" pattern="[A-Za-z0-9_]{3,}"
                title="El usuario debe tener al menos 3 caracteres y solo puede contener letras, números y guiones bajos"
                required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email"
                placeholder="Ingresa tu correo electrónico" pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$"
                title="Por favor, ingresa un correo válido" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="password" name="pass" placeholder="Crea una contraseña"
                pattern=".{8,32}"
                title="La contraseña debe tener entre 8 y 32 caracteres, al menos una mayúscula, una minúscula, un número y un símbolo" required>
            </div>
            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Confirmar contraseña</label>
              <input type="password" class="form-control" id="confirmPassword" placeholder="Confirma tu contraseña"
                title="Por favor, confirma tu contraseña" required>
            </div>
            <button type="submit" class="btn btn-cta w-100">Registrarse</button>
          </form>
        </div>
        <div class="modal-footer">
          <p class="text-center w-100">¿Ya tienes una cuenta? <a href="#" class="text-verde-selva"
              data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Inicia sesión</a></p>
        </div>
      </div>
    </div>
</div>

<!-- Modal de Iniciar Sesión -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-verde-selva text-white">
        <h5 class="modal-title" id="loginModalLabel">Iniciar Sesión</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <?php if (session()->getFlashdata('msg')): ?>
          <?php $msg = session()->getFlashdata('msg'); ?>
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?= is_array($msg) ? $msg['body'] : $msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>
        <?php endif; ?>
        <form method="post" action="<?= base_url('login/auth') ?>">
          <!-- Validación del correo electrónico -->
          <div class="mb-3">
            <label for="loginEmail" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="Ingresa tu correo electrónico"
              pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$" title="Por favor, ingresa un correo válido" required>
          </div>

          <!-- Validación de la contraseña -->
          <div class="mb-3">
            <label for="loginPassword" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="loginPassword" name="pass" placeholder="Ingresa tu contraseña"
              pattern=".{8,}" title="La contraseña debe tener al menos 8 caracteres" required>
          </div>

          <button type="submit" class="btn btn-cta w-100">Iniciar Sesión</button>
        </form>
      </div>
      <div class="modal-footer">
        <p class="text-center w-100">¿No tienes una cuenta? <a href="#" class="text-verde-selva" data-bs-dismiss="modal"
            data-bs-toggle="modal" data-bs-target="#registroModal">Regístrate</a></p>
      </div>
    </div>
  </div>
  <script>
    function validarContrasenas() {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;

      if (password !== confirmPassword) {
        alert('Las contraseñas no coinciden. Por favor, verifica.');
        return false;
      }
      return true;
    }
  </script>
</div>
</header>
<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<!-- Off-canvas carrito lateral -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="sideCart" aria-labelledby="sideCartLabel">
  <div class="offcanvas-header bg-verde-selva text-white">
    <h5 class="offcanvas-title" id="sideCartLabel"><i class="fas fa-shopping-cart me-2"></i>Tu Carrito</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
  </div>
  <div class="offcanvas-body">
    <div id="cartItems">
      <!-- El contenido del carrito se carga aquí por AJAX -->
    </div>
  </div>
</div>

<script>
async function cargarCarritoLateral() {
    const response = await fetch('<?= base_url('carrito/ajax') ?>');
    const html = await response.text();
    document.getElementById('cartItems').innerHTML = html;
    // Actualizar total y botón SOLO si existen en el HTML cargado
    const total = document.getElementById('cartTotalHidden');
    if (total) {
        if(document.getElementById('cartTotal'))
            document.getElementById('cartTotal').textContent = total.value;
        if(document.getElementById('checkoutBtn'))
            document.getElementById('checkoutBtn').disabled = (parseFloat(total.value.replace(/[^0-9,\.]/g, '').replace(',', '.')) <= 0);
    }
    // SIEMPRE actualizar contador después de cargar el carrito
    actualizarContadorCarrito();
}

async function actualizarContadorCarrito() {
    let count = 0;
    try {
        const response = await fetch('<?= base_url('carrito/devolver_carrito') ?>');
        if (!response.ok) throw new Error('No response');
        const data = await response.json();
        if (data && typeof data === 'object') {
            if ('total_items' in data) {
                count = parseInt(data['total_items']);
                if (isNaN(count) || count < 0) count = 0;
            } else {
                for (const key in data) {
                    if (
                        data.hasOwnProperty(key) &&
                        typeof data[key] === 'object' &&
                        data[key] !== null &&
                        'qty' in data[key] &&
                        'id' in data[key]
                    ) {
                        let qty = data[key].qty;
                        if (typeof qty === 'string') qty = parseInt(qty);
                        if (!isNaN(qty) && qty > 0) count += qty;
                    }
                }
            }
        }
    } catch (e) {
        count = 0;
    }
    // Log temporal para depuración
    console.log('Carrito total_items:', count);
    document.getElementById('cartCount').textContent = count;
}
// Inicializar contador al cargar la página
actualizarContadorCarrito();

// Corregir apertura del carrito lateral
const cartBtn = document.getElementById('btnAbrirCarrito');
if(cartBtn) {
  cartBtn.addEventListener('click', function(e) {
    e.preventDefault();
    cargarCarritoLateral();
    actualizarContadorCarrito(); // <-- Asegura actualización al abrir
    var sideCart = new bootstrap.Offcanvas(document.getElementById('sideCart'));
    sideCart.show();
  });
}
// Carrito lateral: event delegation global para AJAX
(function() {
    document.addEventListener('click', function(e) {
        // Eliminar producto
        if (e.target.closest('.btn-eliminar-item')) {
            const rowid = e.target.closest('.btn-eliminar-item').dataset.rowid;
            fetch('<?= base_url('carrito_elimina/') ?>' + rowid, { method: 'GET' })
                .then(() => { cargarCarritoLateral(); actualizarContadorCarrito(); });
            e.preventDefault();
            return;
        }
        // Restar unidad
        if (e.target.closest('.btn-resta-item')) {
            const rowid = e.target.closest('.btn-resta-item').dataset.rowid;
            fetch('<?= base_url('carrito_resta/') ?>' + rowid, { method: 'GET' })
                .then(() => { cargarCarritoLateral(); actualizarContadorCarrito(); });
            e.preventDefault();
            return;
        }
        // Sumar unidad
        if (e.target.closest('.btn-suma-item')) {
            const rowid = e.target.closest('.btn-suma-item').dataset.rowid;
            fetch('<?= base_url('carrito_suma/') ?>' + rowid, { method: 'GET' })
                .then(() => { cargarCarritoLateral(); actualizarContadorCarrito(); });
            e.preventDefault();
            return;
        }
        // Limpiar carrito
        if (e.target.closest('#btnLimpiarCarrito')) {
            fetch('<?= base_url('/borrar') ?>', { method: 'GET' })
                .then(() => { cargarCarritoLateral(); actualizarContadorCarrito(); });
            e.preventDefault();
            return;
        }
    });
})();
</script>