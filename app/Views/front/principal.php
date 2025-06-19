<?php
// Mostrar mensajes flash de acceso o advertencia (Auth)
$msg = session()->getFlashdata('msg');
if ($msg): ?>
  <div class="alert alert-<?= isset($msg['type']) ? esc($msg['type']) : 'info' ?> alert-dismissible fade show mt-3" role="alert">
    <?= is_array($msg) ? esc($msg['body']) : esc($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>
<?php
$validation = session()->getFlashdata('validation');
if ($validation):
    // Si es un objeto de validación, extrae los mensajes
    if ($validation instanceof \CodeIgniter\Validation\Validation) {
        $errors = $validation->getErrors();
    } else {
        $errors = $validation;
    }
?>
  <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
    <?php
    if (is_array($errors)) {
        foreach ($errors as $error) {
            echo esc($error) . '<br>';
        }
    } else {
        echo esc($errors);
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>
<!-- Sección Carrusel ajustada a pantalla completa -->
<section class="carousel-section">
  <div
    id="mainCarousel"
    class="carousel slide"
    data-bs-ride="carousel"
    data-bs-interval="5000"
  >
  
    <!-- Slides -->
    <div class="carousel-inner">
      <!-- Slide 1 (Pesca) -->
      <div class="carousel-item active">
        <img
          src="<?= base_url('assets/img/imagenes_pagina/pescador.png') ?>"
          class="d-block w-100"
          alt="Pesca"
        />
        <div class="carousel-caption">
          <h2 class="display-4">Equipamiento de pesca</h2>
          <p class="lead">¡Hasta 30% OFF en cañas de alta gama!</p>
          <button class="btn btn-cta btn-lg" onclick="location.href='#ofertas-relampago'">Ver Ofertas</button>
        </div>
      </div>

      <!-- Slide 2 (Camping) -->
      <div class="carousel-item">
        <img
          src="<?= base_url('assets/img/imagenes_pagina/carpa.png') ?>"
          class="d-block w-100"
          alt="Camping"
        />
        <div class="carousel-caption">
          <h2 class="display-4">Equipos para la aventura</h2>
          <p class="lead">
            Tiendas y sacos de dormir con 25% OFF
          </p>
          <a class="btn btn-cta btn-lg" href="<?= base_url( 'catalogo') ?>">Ver Colección</a>
        </div>
      </div>

      <!-- Slide 3 (Naturaleza) -->
      <div class="carousel-item">
        <img
          src="<?= base_url('assets/img/imagenes_pagina/paisaje.png') ?>"
          class="d-block w-100"
          alt="Naturaleza"
        />
        <div class="carousel-caption">
          <h2 class="display-4">Conecta con la naturaleza</h2>
          <p class="lead">
            Equipamiento especializado para la flora y fauna de Corrientes
          </p>
          <a class="btn btn-cta btn-lg" href="<?= base_url( 'catalogo') ?>">Descubrir</a>
        </div>
      </div>
    </div>

    <!-- Controles de navegación -->
    <button
      class="carousel-control-prev"
      type="button"
      data-bs-target="#mainCarousel"
      data-bs-slide="prev"
    >
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Anterior</span>
    </button>
    <button
      class="carousel-control-next"
      type="button"
      data-bs-target="#mainCarousel"
      data-bs-slide="next"
    >
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Siguiente</span>
    </button>
  </div>
</section>
<br>

<!-- Sección Productos Destacados -->
<section class="container mb-5" id="productos-destacados">
  <h2 class="text-center mb-4">PRODUCTOS DESTACADOS DE PESCA</h2>
  <p class="text-muted text-center mb-4">
    Los productos que arrasan en ventas
  </p>
  <div class="row g-4">
    <?php if (!empty($destacadosPesca)): ?>
      <?php foreach ($destacadosPesca as $producto): ?>
        <div class="col-6 col-md-6 col-lg-4">
          <div class="card card-producto h-100">
            <span class="badge badge-oferta">3 cuotas sin interés</span>
            <?php if (!empty($producto['imagen'])): ?>
              <img src="<?= base_url('assets/uploads/' . esc($producto['imagen'])) ?>" class="card-img-top" alt="<?= esc($producto['nombre_prod']) ?>" />
            <?php else: ?>
              <img src="<?= base_url('assets/img/imagenes_pagina/logo.png') ?>" class="card-img-top" alt="Sin imagen" />
            <?php endif; ?>
            <div class="card-body">
              <div class="card-text-container">
                <h5 class="card-title"><?= esc($producto['nombre_prod']) ?></h5>
                <p class="card-text">
                  Stock: <?= esc($producto['stock']) ?>
                </p>
              </div>
              <div class="card-action-container">
                <h4 class="text-success">$<?= number_format($producto['precio_vta'], 2, ',', '.') ?></h4>
                <div class="btn-container">
                  <a href="<?= base_url('producto/' . $producto['id']) ?>" class="btn btn-productos">Ver más</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info text-center">No hay productos destacados de pesca.</div>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Primera sección con efecto paralaje -->
<section class="seccion-paralaje" style="background-image: url('<?= base_url('assets/img/imagenes_pagina/paralaje1.png') ?>');">
  <div class="contenido-paralaje">
    <h3>Experiencias únicas en la naturaleza</h3>
    <p>Descubre nuestros productos de alta calidad para tus aventuras al aire libre</p>
  </div>
</section>

<!-- Sección Ofertas Relámpago Camping -->

<section id="ofertas-relampago" class="container mb-5">
  <h2 class="text-center mb-4">OFERTAS RELÁMPAGO CAMPING</h2>
  <p class="text-muted text-center mb-4">
    Los productos a precios inigualables
  </p>
  <div class="row g-4">
    <?php if (!empty($ofertasCamping)): ?>
      <?php foreach ($ofertasCamping as $producto): ?>
        <div class="col-6 col-md-3">
          <div class="card card-producto h-100">
            <span class="badge bg-danger position-absolute top-0 end-0 m-2">
              ¡Oferta!
            </span>
            <?php if (!empty($producto['imagen'])): ?>
              <img src="<?= base_url('assets/uploads/' . esc($producto['imagen'])) ?>" class="card-img-top" alt="<?= esc($producto['nombre_prod']) ?>" />
            <?php else: ?>
              <img src="<?= base_url('assets/img/imagenes_pagina/logo.png') ?>" class="card-img-top" alt="Sin imagen" />
            <?php endif; ?>
            <div class="card-body">
              <div class="card-text-container">
                <h5 class="card-title"><?= esc($producto['nombre_prod']) ?></h5>
                <p class="card-text">Stock: <?= esc($producto['stock']) ?></p>
              </div>
              <div class="card-action-container">
                <h4 class="text-danger">$<?= number_format($producto['precio_vta'], 2, ',', '.') ?></h4>
                <small class="text-success d-block">3 cuotas sin interés</small>
                <div class="btn-container">
                  <a href="<?= base_url('producto/' . $producto['id']) ?>" class="btn btn-productos">Ver más</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info text-center">No hay ofertas relámpago de camping.</div>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Segunda sección con efecto paralaje -->
<section class="seccion-paralaje" style="background-image: url('<?= base_url('assets/img/imagenes_pagina/paralaje2.png') ?>');">
  <div class="contenido-paralaje">
    <h3>Conecta con la naturaleza</h3>
    <p>El mejor equipamiento para tus expediciones de pesca y aventura</p>
  </div>
</section>

<?= view('front/layouts/ubicacion_horarios'); ?>









