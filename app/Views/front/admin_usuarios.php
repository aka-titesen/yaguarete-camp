<div class="container mt-4">
    <div class="d-flex justify-content-end">
        <!-- Botón para abrir la modal de agregar usuario -->
        <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#modalAgregarUsuario">
            Agregar Usuarios
        </button>
    </div>
    <?php
    if(isset($_SESSION['msg'])){
        echo $_SESSION['msg'];
    }
    ?>
    <div class="mt-2">
        <table class="table table-bordered table-secondary table-hover" id="users-list">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Baja</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if($users): ?>
                    <?php foreach($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['nombre']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['perfil_id']; ?></td>
                            <td><?php echo $user['baja']; ?></td>
                            <td>
                                <a href="<?php echo base_url('edit-view/'.$user['id']);?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="<?php echo base_url('deletelogico/'.$user['id']);?>" class="btn btn-danger btn-sm">Borrar</a>
                                <a href="<?php echo base_url('activar/'.$user['id']);?>" class="btn btn-secondary btn-sm">Activar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para agregar usuario -->
<div class="modal fade" id="modalAgregarUsuario" tabindex="-1" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('/store') ?>" method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAgregarUsuarioLabel">Alta de Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
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
              <option value="2">Vendedor</option>
              <!-- Agrega más perfiles si es necesario -->
            </select>
            <?php if($validation->getError('perfil_id')): ?>
              <div class="alert alert-danger mt-2"><?= $validation->getError('perfil_id'); ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-secondary">Limpiar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready( function () {
    $('#users-list').DataTable();
});
</script>