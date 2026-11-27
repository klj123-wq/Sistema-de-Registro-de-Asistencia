<?php

/** @var array $data */
?>

<style>
    /* Aire para los controles del DataTable dentro de la tarjeta */
    #tablaUsuarios_wrapper {
        padding: 1rem 1.25rem;
    }

    #tablaUsuarios_wrapper .dataTables_length,
    #tablaUsuarios_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    #tablaUsuarios_wrapper .dataTables_info {
        padding-top: 1rem;
    }

    /* La tabla mantiene su tamaño y se desliza horizontalmente en móvil */
    #tablaUsuarios {
        min-width: 720px;
    }
</style>

<div class="container-fluid py-3" id="contenedor-usuarios">

    <div class="animate__animated animate__fadeIn d-print-none">

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h3 class="fw-bold text-dark mb-1">
                    <i class="bi bi-people-fill me-2 text-success"></i>Gestión de Usuarios
                </h3>
                <p class="text-secondary small mb-0">Administra los usuarios registrados en el sistema.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <a class="btn btn-success shadow-sm px-4 fw-semibold rounded-3" href="usuario/registrar">
                        <i class="bi bi-person-plus-fill me-1"></i> Registrar Usuario
                    </a>

                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="tablaUsuarios" class="table table-hover align-middle w-100 mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="text-uppercase small fw-bold text-dark px-4 py-3" style="width: 14%;">Cédula</th>
                                <th class="text-uppercase small fw-bold text-dark py-3" style="width: 28%;">Nombre Completo</th>
                                <th class="text-uppercase small fw-bold text-dark py-3" style="width: 26%;">Correo</th>
                                <th class="text-uppercase small fw-bold text-dark py-3" style="width: 12%;">Estado</th>
                                <th class="text-uppercase small fw-bold text-dark py-3 text-center" style="width: 20%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $persona):
                                $nombreSeguro = addslashes(trim(($persona['nombre'] ?? '') . ' ' . ($persona['apellido'] ?? '')));
                                $activo = ($persona["activo"] ?? 0) == 1;
                            ?>
                                <tr class="border-bottom">
                                    <td class="px-4 text-dark fw-medium">V-<?= htmlspecialchars($persona["cedula"] ?? '') ?></td>
                                    <td class="text-dark fw-semibold"><?= htmlspecialchars(trim(($persona["nombre"] ?? '') . ' ' . ($persona["apellido"] ?? ''))) ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($persona["correo"] ?? '') ?></td>
                                    <td>
                                        <?php if ($activo): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">Bloqueado</span>
                                        <?php endif ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($activo): ?>
                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-3 me-1" title="Bloquear usuario"
                                                onclick="confirmarBloquearUsuario(<?= $persona['id'] ?>, '<?= $nombreSeguro ?>')">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-3 me-1" title="Desbloquear usuario"
                                                onclick="confirmarDesbloquearUsuario(<?= $persona['id'] ?>, '<?= $nombreSeguro ?>')">
                                                <i class="bi bi-unlock"></i>
                                            </button>
                                        <?php endif ?>

                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3" title="Eliminar usuario"
                                            onclick="confirmarEliminarUsuario(<?= $persona['id'] ?>, '<?= $nombreSeguro ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Token CSRF para las peticiones de acción -->
<input type="hidden" id="token_secreto" value="<?= \libs\CSRF::generar() ?>">

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // DataTable completo: buscar, paginar, ordenar e info
        if (document.getElementById('tablaUsuarios')) {
            jQuery('#tablaUsuarios').DataTable({
                "language": {
                    "processing": "Procesando...",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "Ningún dato disponible en esta tabla",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Sig.",
                        "previous": "Ant."
                    }
                },
                "pageLength": 10,
                "dom": '<"row mb-3"<"col-12 col-md-6"l><"col-12 col-md-6"f>>rt<"row mt-3"<"col-12 col-md-5"i><"col-12 col-md-7"p>>',
                "columnDefs": [{
                        "orderable": false,
                        "targets": 4
                    } // Acciones sin ordenar
                ]
            });
        }
    });

    // =========================================================
    // FUNCIÓN MAESTRA: envía la petición con token CSRF
    // =========================================================
    function procesarPeticion(url, passwordOpcional) {
        Swal.fire({
            title: 'Procesando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        let tokenReal = document.getElementById('token_secreto').value;
        let datosFormulario = new FormData();
        datosFormulario.append('CSRF', tokenReal);

        if (passwordOpcional) {
            datosFormulario.append('password', passwordOpcional);
        }

        fetch(url, {
                method: 'POST',
                body: datosFormulario
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: data.icon || (data.success ? 'success' : 'error'),
                    title: data.success ? '¡Listo!' : 'Atención',
                    text: data.message,
                    confirmButtonColor: data.success ? '#198754' : '#059669'
                }).then(() => {
                    if (data.success) {
                        if (data.redirect && data.redirect !== "") {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    }
                });
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo comunicar con el servidor.',
                    confirmButtonColor: '#d33'
                });
            });
    }

    // =========================================================
    // ACCIONES (todas piden contraseña de confirmación)
    // =========================================================
    function confirmarEliminarUsuario(id, nombre) {
        Swal.fire({
            title: '¿Eliminar usuario?',
            html: `Se eliminará permanentemente a <strong>${nombre}</strong>.<br><br><span class="text-muted small">Por seguridad, confirma tu identidad:</span>`,
            icon: 'warning',
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off',
                placeholder: 'Ingresa tu contraseña'
            },
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            preConfirm: (password) => {
                if (!password) Swal.showValidationMessage('Debes ingresar tu contraseña para continuar');
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) procesarPeticion(`/usuario/${id}/delete`, result.value);
        });
    }

    function confirmarBloquearUsuario(id, nombre) {
        Swal.fire({
            title: '¿Bloquear usuario?',
            html: `Desea bloquear al usuario <strong>${nombre}</strong>`,
            icon: 'warning',
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off',
                placeholder: 'Ingresa tu contraseña'
            },
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-lock"></i> Sí, bloquear',
            cancelButtonText: 'Cancelar',
            preConfirm: (password) => {
                if (!password) Swal.showValidationMessage('Debes ingresar tu contraseña para continuar');
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) procesarPeticion(`/usuario/${id}/lock`, result.value);
        });
    }

    function confirmarDesbloquearUsuario(id, nombre) {
        Swal.fire({
            title: '¿Desbloquear usuario?',
            html: `Desea desbloquear al usuario <strong>${nombre}</strong>.<br><br><span class="text-muted small">Por seguridad, confirma tu identidad:</span>`,
            icon: 'question',
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off',
                placeholder: 'Ingresa tu contraseña'
            },
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-unlock"></i> Sí, desbloquear',
            cancelButtonText: 'Cancelar',
            preConfirm: (password) => {
                if (!password) Swal.showValidationMessage('Debes ingresar tu contraseña para continuar');
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) procesarPeticion(`/usuario/${id}/unlock`, result.value);
        });
    }
</script>