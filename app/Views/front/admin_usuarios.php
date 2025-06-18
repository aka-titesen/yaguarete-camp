<div class="container mt-5 pt-4">
    <h2 class="mb-4 text-center"><i class="fas fa-users"></i> Gestión de Usuarios</h2>

    <!-- Botón para abrir la modal de agregar usuario -->
    <div class="text-end mb-3">
        <button type="button" class="btn btn-verde-selva" data-bs-toggle="modal" data-bs-target="#modalAgregarUsuario">
            <i class="fas fa-user-plus"></i> Agregar Usuario
        </button>
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
          <form action="<?= base_url('/store') ?>" method="post">
            <div class="modal-body">
              <?php $validation = \Config\Services::validation(); ?>
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input name="nombre" type="text" class="form-control" placeholder="Nombre" value="<?= set_value('nombre') ?>">
                <?php if($validation->getError('nombre')): ?>
                  <div class="alert alert-danger mt-2"><?= $validation->getError('nombre'); ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Apellido</label>
                <input name="apellido" type="text" class="form-control" placeholder="Apellido" value="<?= set_value('apellido') ?>">
                <?php if($validation->getError('apellido')): ?>
                  <div class="alert alert-danger mt-2"><?= $validation->getError('apellido'); ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input name="usuario" type="text" class="form-control" placeholder="Usuario" value="<?= set_value('usuario') ?>">
                <?php if($validation->getError('usuario')): ?>
                  <div class="alert alert-danger mt-2"><?= $validation->getError('usuario'); ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" placeholder="correo@algo.com" value="<?= set_value('email') ?>">
                <?php if($validation->getError('email')): ?>
                  <div class="alert alert-danger mt-2"><?= $validation->getError('email'); ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="pass" type="password" class="form-control" placeholder="Password">
                <?php if($validation->getError('pass')): ?>
                  <div class="alert alert-danger mt-2"><?= $validation->getError('pass'); ?></div>
                <?php endif; ?>
              </div>
              <div class="mb-3">
                <label class="form-label">Perfil</label>
                <select name="perfil_id" class="form-control">
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
        </div>
      </div>
    </div>

    <!-- Modal para editar usuario -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form id="formEditarUsuario" method="post" action="<?= base_url('update-user') ?>">
            <div class="modal-header bg-verde-selva text-white">
              <h5 class="modal-title" id="modalEditarUsuarioLabel"><i class="fas fa-edit"></i> Editar Usuario</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id" id="edit_id_usuario">
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input name="nombre" id="edit_nombre" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Apellido</label>
                <input name="apellido" id="edit_apellido" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input name="usuario" id="edit_usuario" type="text" class="form-control" required>
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
                <tbody>
                    <?php if($users): ?>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?= $user['id']; ?></td>
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
                                <td><?= $user['baja']; ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1 btn-editar-usuario"
                                            title="Editar" data-id="<?= $user['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?= base_url('deletelogico/'.$user['id']);?>" class="btn btn-sm btn-outline-danger me-1" title="Desactivar">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    <a href="<?= base_url('activar/'.$user['id']);?>" class="btn btn-sm btn-outline-success" title="Activar">
                                        <i class="fas fa-undo"></i>
                                    </a>
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