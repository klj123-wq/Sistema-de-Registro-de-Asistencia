<?php

/** @var array $data */
/** @var string $id */
?>

<style>
    /* Aire para los controles del DataTable dentro de la tarjeta */
    #tablaPermisos_wrapper {
        padding: 1rem 1.25rem;
    }

    #tablaPermisos_wrapper .dataTables_length,
    #tablaPermisos_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    #tablaPermisos_wrapper .dataTables_info {
        padding-top: 1rem;
    }

    /* La tabla mantiene su tamaño y se desliza horizontalmente en móvil */
    #tablaPermisos {
        min-width: 640px;
    }
</style>

<div class="container-fluid py-3">

    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-calendar2-x me-2 text-success"></i>Permisos del Personal
            </h3>
            <p class="text-secondary small mb-0">Permisos registrados para este trabajador.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a class="btn btn-success shadow-sm px-4 fw-semibold rounded-3" href="/permisos/<?= $id ?>/registrar">
                <i class="bi bi-calendar-plus me-1"></i> Registrar Permiso
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tablaPermisos" class="table table-hover align-middle w-100 mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="text-uppercase small fw-bold text-dark py-3" style="width: 40%;">Descripción</th>
                            <th class="text-uppercase small fw-bold text-dark px-4 py-3" style="width: 21%;">Fecha de Inicio</th>
                            <th class="text-uppercase small fw-bold text-dark py-3" style="width: 21%;">Fecha de Fin</th>
                            <th class="text-uppercase small fw-bold text-dark py-3 text-center" style="width: 14%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach ($data as $dato):
                            $descSegura = addslashes(trim($dato['descripcion'] ?? ''));
                        ?>
                            <tr class="border-bottom">
                                <td class="text-dark">
                                    <span class="d-inline-block text-truncate" style="max-width: 280px;" title="<?= htmlspecialchars($dato['descripcion'] ?? '') ?>">
                                        <?= htmlspecialchars($dato['descripcion'] ?? '') ?>
                                    </span>
                                </td>
                                <td class="px-4 text-dark fw-medium">
                                    <?= date("d/m/Y", strtotime($dato["fecha_inicio"])) ?>
                                </td>
                                <td class="text-dark fw-medium">
                                    <?= date("d/m/Y", strtotime($dato["fecha_fin"])) ?>
                                </td>

                                <td class="text-center">
                                    <a href="/permisos/<?= $dato["id"] ?>/edit" class="btn btn-sm btn-outline-secondary rounded-3 me-1" title="Editar Permiso">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3" title="Eliminar Permiso"
                                        onclick="confirmarEliminarPermiso(<?= $dato['id'] ?>, '<?= $descSegura ?>')">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // DataTable mínimo: solo ordenar por columna (sin buscar, paginar ni info)
        // DataTable completo: buscar, paginar, ordenar e info
        if (document.getElementById('tablaPermisos')) {
            jQuery('#tablaPermisos').DataTable({
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
                        "targets": 3
                    } // Acciones sin ordenar
                ]
            });
        }
    });

    function confirmarEliminarPermiso(id, descripcion) {
        Swal.fire({
            title: '¿Eliminar permiso?',
            html: `Se eliminará el permiso: <strong>${descripcion}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/permisos/${id}/delete`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>