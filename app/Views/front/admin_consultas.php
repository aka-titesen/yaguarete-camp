<div class="container mt-5 mb-4 pt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Consultas de Clientes</h2>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver al dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Mostrar mensaje Flash si existe -->
                    <?php if (session()->getFlashdata('mensaje')): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('mensaje') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Pestañas de navegación -->
                    <ul class="nav nav-tabs mb-4" id="consultasTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="<?= base_url('admin-consultas?filtro=todas') ?>" class="nav-link <?= $filtro == 'todas' ? 'active' : '' ?>">
                                <i class="fas fa-inbox"></i> Todas (<?= count($consultas) ?>)
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="<?= base_url('admin-consultas?filtro=sin_responder') ?>" class="nav-link <?= $filtro == 'sin_responder' ? 'active' : '' ?>">
                                <i class="fas fa-question-circle"></i> Sin responder 
                                <?php 
                                $pendientes = array_filter($consultas, function($c) {
                                    return empty($c['respuesta']);
                                });
                                ?>
                                <span class="badge bg-danger"><?= count($pendientes) ?></span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="<?= base_url('admin-consultas?filtro=respondidas') ?>" class="nav-link <?= $filtro == 'respondidas' ? 'active' : '' ?>">
                                <i class="fas fa-check-circle"></i> Respondidas
                            </a>
                        </li>
                    </ul>

                    <?php if (empty($consultas)): ?>
                        <div class="alert alert-info text-center" role="alert">
                            <h4 class="alert-heading">
                                <?php if ($filtro == 'sin_responder'): ?>
                                    No hay consultas pendientes de respuesta.
                                <?php elseif ($filtro == 'respondidas'): ?>
                                    No hay consultas respondidas.
                                <?php else: ?>
                                    No hay consultas registradas.
                                <?php endif; ?>
                            </h4>
                        </div>
                    <?php else: ?>
                        <!-- Lista de consultas -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Mensaje</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($consultas as $consulta): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($consulta['fecha_consulta'])) ?></td>
                                        <td><?= $consulta['nombre'] ?> <?= $consulta['apellido'] ?></td>
                                        <td>
                                            <a href="mailto:<?= $consulta['email'] ?>"><?= $consulta['email'] ?></a>
                                            <?php if(!empty($consulta['telefono'])): ?>
                                                <br><small><?= $consulta['telefono'] ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= substr($consulta['mensaje'], 0, 100) ?><?= strlen($consulta['mensaje']) > 100 ? '...' : '' ?></td>
                                        <td>
                                            <?php if(empty($consulta['respuesta'])): ?>
                                                <span class="badge rounded-pill bg-warning text-dark">Pendiente</span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-success">Respondida</span>
                                                <br><small>el <?= date('d/m/Y', strtotime($consulta['fecha_respuesta'])) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary ver-consulta" data-id="<?= $consulta['id'] ?>" 
                                                    data-nombre="<?= $consulta['nombre'] ?> <?= $consulta['apellido'] ?>" 
                                                    data-email="<?= $consulta['email'] ?>"
                                                    data-telefono="<?= $consulta['telefono'] ?>"
                                                    data-mensaje="<?= htmlspecialchars($consulta['mensaje']) ?>"
                                                    data-respuesta="<?= htmlspecialchars($consulta['respuesta'] ?? '') ?>"
                                                    data-fecha-respuesta="<?= !empty($consulta['fecha_respuesta']) ? date('d/m/Y H:i', strtotime($consulta['fecha_respuesta'])) : '' ?>">
                                                <i class="fas fa-eye"></i> Ver
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver y responder consulta -->
<div class="modal fade" id="consultaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detalles de la consulta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalFeedback"></div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Nombre:</strong> <span id="modalNombre"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong> <span id="modalEmail"></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Teléfono:</strong> <span id="modalTelefono"></span>
                </div>
                
                <div class="card bg-light mb-4">
                    <div class="card-header">
                        <strong>Mensaje del cliente</strong>
                    </div>
                    <div class="card-body">
                        <p id="modalMensaje"></p>
                    </div>
                </div>
                
                <div id="seccionRespuesta">
                    <form id="respuestaForm">
                        <input type="hidden" id="modalConsultaId" name="id">
                        <div class="mb-3">
                            <label for="respuesta" class="form-label">Respuesta</label>
                            <textarea class="form-control" id="respuesta" name="respuesta" rows="5" required></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success" id="btnResponder">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="respuestaSpinner"></span>
                                Enviar respuesta
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="respuestaPrevia" class="mt-4 d-none">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            Respuesta enviada
                        </div>
                        <div class="card-body">
                            <p id="textoRespuesta"></p>
                            <small class="text-muted">Enviada el <span id="fechaRespuesta"></span></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const consultaModal = new bootstrap.Modal(document.getElementById('consultaModal'));
    
    // Mostrar modal de consulta
    document.querySelectorAll('.ver-consulta').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const email = this.getAttribute('data-email');
            const telefono = this.getAttribute('data-telefono') || 'No proporcionado';
            const mensaje = this.getAttribute('data-mensaje');
            const respuesta = this.getAttribute('data-respuesta');
            const fechaRespuesta = this.getAttribute('data-fecha-respuesta');
            
            // Llenar el modal con los datos
            document.getElementById('modalConsultaId').value = id;
            document.getElementById('modalNombre').textContent = nombre;
            document.getElementById('modalEmail').textContent = email;
            document.getElementById('modalTelefono').textContent = telefono;
            document.getElementById('modalMensaje').textContent = mensaje;
            
            // Si ya tiene respuesta, mostrarla
            if (respuesta) {
                document.getElementById('seccionRespuesta').classList.add('d-none');
                document.getElementById('respuestaPrevia').classList.remove('d-none');
                document.getElementById('textoRespuesta').textContent = respuesta;
                document.getElementById('fechaRespuesta').textContent = fechaRespuesta;
            } else {
                document.getElementById('seccionRespuesta').classList.remove('d-none');
                document.getElementById('respuestaPrevia').classList.add('d-none');
                document.getElementById('respuesta').value = '';
            }
            
            consultaModal.show();
        });
    });
    
    // Enviar respuesta
    document.getElementById('respuestaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btnResponder = document.getElementById('btnResponder');
        const spinner = document.getElementById('respuestaSpinner');
        const feedbackDiv = document.getElementById('modalFeedback');
        
        btnResponder.disabled = true;
        spinner.classList.remove('d-none');
        
        const formData = new FormData(this);
        
        fetch('<?= base_url('responder-consulta') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            feedbackDiv.innerHTML = `
                <div class="alert alert-${data.status === 'success' ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            if (data.status === 'success') {
                // Recargar la página después de unos segundos
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
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
            btnResponder.disabled = false;
            spinner.classList.add('d-none');
        });
    });
});
</script>
