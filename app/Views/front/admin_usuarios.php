<div class="container mt-5 pt-4">
    <h2 class="mb-4 text-center"><i class="fas fa-users"></i> Gestión de Usuarios</h2>

    <!-- Botón para abrir la modal de agregar usuario -->
    <div class="text-end mb-3">
        <button type="button" class="btn btn-verde-selva" data-bs-toggle="modal" data-bs-target="#modalAgregarUsuario">
            <i class="fas fa-user-plus"></i> Agregar Usuario
        </button>
    </div>

    <!-- Filtro de usuarios activos/dados de baja -->
  <div class="mb-3 d-flex justify-content-end align-items-center">
      <label class="me-2 mb-0 fw-bold">Ver:</label>
      <select id="filtroUsuarios" class="form-select w-auto">
          <option value="activos" selected>Usuarios activos</option>
          <option value="baja">Usuarios dados de baja</option>
      </select>
  </div>

    <!-- Buscador por nombre de usuario -->
    <div class="row mb-3 justify-content-end">
        <div class="col-auto buscador-con-icono">
            <i class="fas fa-search input-search-icon"></i>
            <input type="text" id="buscadorNombreUsuario" class="form-control buscador-input" placeholder="Buscar por nombre...">
        </div>
    </div>

    <!-- Modal para agregar usuario -->
    <div class="modal fade" id="modalAgregarUsuario" tabindex="-1" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-verde-selva text-white">
            <h5 class="modal-title" id="modalAgregarUsuarioLabel"><i class="fas fa-user-plus"></i> Alta de Usuario</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <form action="<?= base_url('/store') ?>" method="post" id="formCrearUsuario" autocomplete="off">
            <div class="modal-body">
              <?php $validation = \Config\Services::validation(); ?>
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input name="nombre" type="text" class="form-control" placeholder="Nombre" value="<?= set_value('nombre') ?>" minlength="3" maxlength="25" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$" title="Solo letras y espacios">
                <?php if($validation->getError('nombre')): ?>
                  <div class="alert alert-danger mt-2"><?= $validation->getError('nombre'); ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Apellido</label>
                <input name="apellido" type="text" class="form-control" placeholder="Apellido" value="<?= set_value('apellido') ?>" minlength="3" maxlength="25" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$" title="Solo letras y espacios">
                <?php if($validation->getError('apellido')): ?>
                  <div class="alert alert-danger mt-2"><?= $validation->getError('apellido'); ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input name="usuario" type="text" class="form-control" placeholder="Usuario" value="<?= set_value('usuario') ?>" minlength="3" maxlength="10" required pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ]+$" title="Solo letras, sin espacios ni números">
                <?php if($validation->getError('usuario')): ?>
                  <div class="alert alert-danger mt-2"><?= $validation->getError('usuario'); ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" placeholder="correo@algo.com" value="<?= set_value('email') ?>" minlength="4" maxlength="100" required pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$">
                <?php if($validation->getError('email')): ?>
                  <div class="alert alert-danger mt-2"><?= $validation->getError('email'); ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="pass" id="pass" type="password" class="form-control" placeholder="Password" minlength="8" maxlength="32" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,32}" required>
                <small class="text-muted">Mínimo 8, máximo 32, mayúscula, minúscula, número y símbolo.</small>
                <?php if($validation->getError('pass')): ?>
                  <div class="alert alert-danger mt-2"><?= $validation->getError('pass'); ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Repetir Password</label>
                <input name="pass_confirm" id="pass_confirm" type="password" class="form-control" placeholder="Repetir Password" minlength="8" maxlength="32" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Perfil</label>
                <select name="perfil_id" class="form-control" required>
                  <option value="">Seleccione un perfil</option>
                  <option value="1">Cliente</option>
                  <option value="2">Administrador</option>
                  <option value="3">Vendedor</option>
                </select>
                <?php if($validation->getError('perfil_id')): ?>
                  <div class="alert alert-danger mt-2"><?= $validation->getError('perfil_id'); ?></div>
                <?php endif; ?>
              </div>
            </div>
            <div class="modal-footer">
              <button type="reset" class="btn btn-secondary">Limpiar</button>
              <button type="submit" class="btn btn-cta">Guardar</button>
            </div>
          </form>
          <script>
          // Validación JS para alta de usuario
          document.getElementById('formCrearUsuario').addEventListener('submit', function(e) {
            let errores = [];
            const nombre = this.nombre.value.trim();
            const apellido = this.apellido.value.trim();
            const usuario = this.usuario.value.trim();
            const email = this.email.value.trim();
            const pass = this.pass.value;
            const pass_confirm = this.pass_confirm.value;
            if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(nombre)) errores.push('El nombre solo debe contener letras y espacios.');
            if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(apellido)) errores.push('El apellido solo debe contener letras y espacios.');
            if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+$/.test(usuario)) errores.push('El usuario solo debe contener letras, sin espacios ni números.');
            if (nombre.length < 3 || nombre.length > 25) errores.push('El nombre debe tener entre 3 y 25 caracteres.');
            if (apellido.length < 3 || apellido.length > 25) errores.push('El apellido debe tener entre 3 y 25 caracteres.');
            if (usuario.length < 3 || usuario.length > 10) errores.push('El usuario debe tener entre 3 y 10 caracteres.');
            if (!/^.+@.+\..+$/.test(email)) errores.push('El email no es válido.');
            if (pass.length < 8 || pass.length > 32 || !/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d])/.test(pass)) {
              errores.push('La contraseña debe tener entre 8 y 32 caracteres, mayúscula, minúscula, número y símbolo.');
            }
            if (pass !== pass_confirm) {
              errores.push('Las contraseñas no coinciden.');
            }
            if (errores.length > 0) {
              e.preventDefault();
              alert(errores.join('\n'));
            }
          });
          // Validación JS para edición de usuario
          document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
            let errores = [];
            const nombre = document.getElementById('edit_nombre').value.trim();
            const apellido = document.getElementById('edit_apellido').value.trim();
            const usuario = document.getElementById('edit_usuario').value.trim();
            const email = document.getElementById('edit_email').value.trim();
            if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(nombre)) errores.push('El nombre solo debe contener letras y espacios.');
            if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(apellido)) errores.push('El apellido solo debe contener letras y espacios.');
            if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+$/.test(usuario)) errores.push('El usuario solo debe contener letras, sin espacios ni números.');
            if (nombre.length < 3 || nombre.length > 25) errores.push('El nombre debe tener entre 3 y 25 caracteres.');
            if (apellido.length < 3 || apellido.length > 25) errores.push('El apellido debe tener entre 3 y 25 caracteres.');
            if (usuario.length < 3 || usuario.length > 10) errores.push('El usuario debe tener entre 3 y 10 caracteres.');
            if (!/^.+@.+\..+$/.test(email)) errores.push('El email no es válido.');
            if (errores.length > 0) {
              e.preventDefault();
              alert(errores.join('\n'));
            }
          });
          </script>
        </div>
      </div>
    </div>

    <!-- Modal para editar usuario -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form id="formEditarUsuario" method="post" action="<?= base_url('editar_user') ?>">
            <div class="modal-header bg-verde-selva text-white">
              <h5 class="modal-title" id="modalEditarUsuarioLabel"><i class="fas fa-edit"></i> Editar Usuario</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <?php if (session()->has('validation') && session()->has('edit_data')): ?>
                <div class="alert alert-danger">
                  <?php 
                    $validation = session('validation');
                    if (is_array($validation)) {
                      foreach ($validation as $error) {
                        echo esc($error) . '<br>';
                      }
                    } else {
                      echo esc($validation);
                    }
                  ?>
                </div>
              <?php endif; ?>
              <input type="hidden" name="id" id="edit_id_usuario">
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input name="nombre" id="edit_nombre" type="text" class="form-control" required minlength="3" maxlength="25" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$" title="Solo letras y espacios">
              </div>
              <div class="mb-3">
                <label class="form-label">Apellido</label>
                <input name="apellido" id="edit_apellido" type="text" class="form-control" required minlength="3" maxlength="25" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$" title="Solo letras y espacios">
              </div>
              <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input name="usuario" id="edit_usuario" type="text" class="form-control" required minlength="3" maxlength="10" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ]+$" title="Solo letras, sin espacios ni números">
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" id="edit_email" type="email" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Perfil</label>
                <select name="perfil_id" id="edit_perfil_id" class="form-control" required>
                  <option value="">Seleccione un perfil</option>
                  <option value="1">Cliente</option>
                  <option value="2">Administrador</option>
                  <option value="3">Vendedor</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-cta">Guardar cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Confirmar Desactivar Usuario -->
<div class="modal fade" id="modalConfirmarDesactivar" tabindex="-1" aria-labelledby="modalConfirmarDesactivarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px;">
      <div class="modal-header border-0" style="border-radius: 12px 12px 0 0;">
        <h5 class="modal-title fw-bold d-flex align-items-center" id="modalConfirmarDesactivarLabel" style="color:#222;"><i class="fas fa-trash-alt me-2" style="color:#e74c3c;"></i> Confirmar eliminación</h5>
      </div>
      <div style="height:4px; background:#e74c3c; width:100%; margin-bottom:0;"></div>
      <div class="modal-body text-center">
        <p class="mb-0" style="color:#222; font-size:1.1em;">¿Estás seguro de que deseas eliminar este usuario?</p>
      </div>
      <div class="modal-footer border-0 d-flex justify-content-center gap-2 pb-4">
        <button type="button" class="btn btn-dark px-4 py-2 fw-bold btn-cancelar-modal" data-bs-dismiss="modal">Cancelar</button>
        <a id="btnConfirmarDesactivar" href="#" class="btn px-4 py-2 fw-bold btn-eliminar-modal" style="background:#e74c3c;color:#fff;">Eliminar</a>
      </div>
    </div>
  </div>
</div>
<!-- Modal Confirmar Activar Usuario -->
<div class="modal fade" id="modalConfirmarActivar" tabindex="-1" aria-labelledby="modalConfirmarActivarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px;">
      <div class="modal-header border-0" style="border-radius: 12px 12px 0 0;">
        <h5 class="modal-title fw-bold d-flex align-items-center" id="modalConfirmarActivarLabel" style="color:#222;"><i class="fas fa-undo me-2" style="color:#27ae60;"></i> Confirmar reactivación</h5>
      </div>
      <div style="height:4px; background:#27ae60; width:100%; margin-bottom:0;"></div>
      <div class="modal-body text-center">
        <p class="mb-0" style="color:#222; font-size:1.1em;">¿Estás seguro de que deseas reactivar este usuario?</p>
      </div>
      <div class="modal-footer border-0 d-flex justify-content-center gap-2 pb-4">
        <button type="button" class="btn btn-dark px-4 py-2 fw-bold btn-cancelar-modal" data-bs-dismiss="modal">Cancelar</button>
        <a id="btnConfirmarActivar" href="#" class="btn px-4 py-2 fw-bold btn-reactivar-modal" style="background:#27ae60;color:#fff;">Reactivar</a>
      </div>
    </div>
  </div>
</div>
    <style>
.btn-eliminar-modal {
  background: #e74c3c !important;
  color: #fff !important;
  border: none;
  transition: background 0.2s, transform 0.15s;
}
.btn-eliminar-modal:hover, .btn-eliminar-modal:focus {
  background: #c0392b !important;
  color: #fff !important;
  transform: scale(1.07);
}
.btn-reactivar-modal {
  background: #27ae60 !important;
  color: #fff !important;
  border: none;
  transition: background 0.2s, transform 0.15s;
}
.btn-reactivar-modal:hover, .btn-reactivar-modal:focus {
  background: #219150 !important;
  color: #fff !important;
  transform: scale(1.07);
}
.btn-cancelar-modal {
  background: #222 !important;
  color: #fff !important;
  border: none;
  transition: background 0.2s;
}
.btn-cancelar-modal:hover, .btn-cancelar-modal:focus {
  background: #444 !important;
  color: #fff !important;
}

/* Estilo para el usuario actual */
.usuario-actual {
  background-color: #f8f9fa !important;
  border-left: 4px solid #007bff !important;
}

.usuario-actual td {
  font-weight: 500;
}
</style>

    <!-- Listado de usuarios -->
    <div class="card mt-4">
        <div class="card-header bg-verde-selva text-negro">
            <i class="fas fa-list"></i> Usuarios registrados
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Email</th>
                        <th scope="col">Perfil</th>
                        <th scope="col">Baja</th>
                        <th scope="col" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaUsuariosBody">
                    <?php if($users): ?>
                        <?php foreach($users as $user): ?>                            <tr class="<?= ($user['baja'] === 'SI') ? 'usuario-baja' : 'usuario-activo'; ?> <?= ($user['id'] == session()->get('id')) ? 'usuario-actual' : ''; ?>">
                                <td>
                                    <?= $user['id']; ?>
                                    <?php if ($user['id'] == session()->get('id')): ?>
                                        <span class="badge bg-info ms-1" title="Tu usuario">TÚ</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $user['nombre'] . ' ' . $user['apellido']; ?></td>
                                <td><?= $user['email']; ?></td>
                                <td>
                                    <?php
                                    switch($user['perfil_id']) {
                                        case 1: echo 'Cliente'; break;
                                        case 2: echo 'Administrador'; break;
                                        case 3: echo 'Vendedor'; break;
                                        default: echo $user['perfil_id'];
                                    }
                                    ?>
                                </td>
                                <td><?= $user['baja']; ?></td>                                <td class="text-center">
                                    <?php if (isset($user['baja']) && $user['baja'] === 'SI'): ?>
                                        <a href="<?= base_url('activar/'.$user['id']);?>" class="btn btn-sm btn-outline-success" title="Activar">
                                            <i class="fas fa-undo"></i>
                                        </a>                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary me-1 btn-editar-usuario"
                                                title="Editar" data-id="<?= $user['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($user['id'] != session()->get('id')): ?>
                                            <a href="<?= base_url('deletelogico/'.$user['id']);?>" class="btn btn-sm btn-outline-danger me-1" title="Desactivar usuario">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary me-1" disabled title="No puedes desactivar tu propio usuario" style="cursor: not-allowed;">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay usuarios registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<br>
<br>
<!-- Scripts necesarios -->
 <script>
    window.usuariosData = <?php echo json_encode($users); ?>;
</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buscador = document.getElementById('buscadorNombreUsuario');
    const filas = document.querySelectorAll('table tbody tr');
    buscador.addEventListener('input', function() {
        const texto = this.value.toLowerCase();
        filas.forEach(fila => {
            const nombre = fila.querySelector('td:nth-child(2)');
            if (nombre && nombre.textContent.toLowerCase().includes(texto)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtro de usuarios activos/dados de baja
    const filtro = document.getElementById('filtroUsuarios');
    const filas = document.querySelectorAll('#tablaUsuariosBody tr');
    filtro.addEventListener('change', function() {
        const valor = this.value;
        filas.forEach(fila => {
            if (valor === 'activos' && fila.classList.contains('usuario-activo')) {
                fila.style.display = '';
            } else if (valor === 'baja' && fila.classList.contains('usuario-baja')) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });
    // Mostrar solo activos al cargar
    filtro.dispatchEvent(new Event('change'));
});
</script>

<script>
  // Script para manejar el modal de edición de usuarios
document.addEventListener('DOMContentLoaded', function() {
    // Pasar datos del usuario al modal de edición
    const usuarios = window.usuariosData || [];
    document.querySelectorAll('.btn-editar-usuario').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const usuario = usuarios.find(u => u.id == id);
            if (usuario) {
                document.getElementById('edit_id_usuario').value = usuario.id;
                document.getElementById('edit_nombre').value = usuario.nombre;
                document.getElementById('edit_apellido').value = usuario.apellido;
                document.getElementById('edit_usuario').value = usuario.usuario;
                document.getElementById('edit_email').value = usuario.email;
                document.getElementById('edit_perfil_id').value = usuario.perfil_id;
                // Mostrar modal
                var modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
                modal.show();
            }
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal para desactivar usuario
    document.querySelectorAll('a.btn-outline-danger[title="Desactivar"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            document.getElementById('btnConfirmarDesactivar').setAttribute('href', url);
            var modal = new bootstrap.Modal(document.getElementById('modalConfirmarDesactivar'));
            modal.show();
        });
    });
    // Modal para activar usuario
    document.querySelectorAll('a.btn-outline-success[title="Activar"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            document.getElementById('btnConfirmarActivar').setAttribute('href', url);
            var modal = new bootstrap.Modal(document.getElementById('modalConfirmarActivar'));
            modal.show();
        });
    });
});
</script>
<?php
// Mostrar mensaje de error y reabrir modal de edición SIEMPRE que haya edit_data y validation en sesión, sin depender de la URL.
if (session()->has('validation') && session()->has('edit_data')): ?>
  <div class="alert alert-danger mt-3">
    <?php 
      $validation = session('validation');
      if (is_array($validation)) {
        foreach ($validation as $error) {
          echo esc($error) . '<br>';
        }
      } else {
        echo esc($validation);
      }
    ?>
  </div>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      var modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
      <?php $edit = session('edit_data'); ?>
        document.getElementById('edit_id_usuario').value = "<?= isset($edit['id']) ? esc($edit['id']) : '' ?>";
        document.getElementById('edit_nombre').value = "<?= isset($edit['nombre']) ? esc($edit['nombre']) : '' ?>";
        document.getElementById('edit_apellido').value = "<?= isset($edit['apellido']) ? esc($edit['apellido']) : '' ?>";
        document.getElementById('edit_usuario').value = "<?= isset($edit['usuario']) ? esc($edit['usuario']) : '' ?>";
        document.getElementById('edit_email').value = "<?= isset($edit['email']) ? esc($edit['email']) : '' ?>";
        document.getElementById('edit_perfil_id').value = "<?= isset($edit['perfil_id']) ? esc($edit['perfil_id']) : '' ?>";
      modal.show();
    });
  </script>
<?php endif; ?>