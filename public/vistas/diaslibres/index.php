<?php

/** @var array $data */
?>

<style>
    /* Aire para los controles del DataTable dentro de la tarjeta */
    #tablaDiasLibres_wrapper {
        padding: 1rem 1.25rem;
    }

    #tablaDiasLibres_wrapper .dataTables_length,
    #tablaDiasLibres_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    #tablaDiasLibres_wrapper .dataTables_info {
        padding-top: 1rem;
    }

    /* La tabla mantiene su tamaño y se desliza horizontalmente en móvil */
    #tablaDiasLibres {
        min-width: 640px;
    }
</style>

<div class="container-fluid py-3">

    <div class="animate__animated animate__fadeIn d-print-none">

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h3 class="fw-bold text-dark mb-1">
                    <i class="bi bi-calendar2-x me-2 text-success"></i>Gestión de Días Libres
                </h3>
                <p class="text-secondary small mb-0">Feriados, asuetos y eventos no laborables del año.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a class="btn btn-success shadow-sm px-4 fw-semibold rounded-3" href="diaslibres/registrar">
                    <i class="bi bi-calendar-plus me-1"></i> Registrar Día Libre
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="tablaDiasLibres" class="table table-hover align-middle w-100 mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="text-uppercase small fw-bold text-dark py-3" style="width: 52%;">Descripción</th>
                                <th class="text-uppercase small fw-bold text-dark px-4 py-3" style="width: 18%;">Fecha de Inicio</th>
                                <th class="text-uppercase small fw-bold text-dark py-3" style="width: 16%;">Fecha de Fin</th>
                                <th class="text-uppercase small fw-bold text-dark py-3 text-center" style="width: 14%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $fecha):
                                $esRecurrente = empty($fecha["ano"]);
                                // Formato d/m; si no se repite, mostramos también el año
                                $fmtIni = date("d/m", strtotime($fecha["fecha_inicio"])) . (!$esRecurrente ? "/" . date("Y", strtotime($fecha["fecha_inicio"])) : "");
                                $fmtFin = date("d/m", strtotime($fecha["fecha_fin"])) . (!$esRecurrente ? "/" . date("Y", strtotime($fecha["fecha_fin"])) : "");
                            ?>
                                <tr class="border-bottom">
                                    <td class="text-dark">
                                        <span class="d-inline-block text-truncate w-100" style="max-width: 100%;" title="<?= htmlspecialchars($fecha["nombre"]) ?>">
                                            <?= htmlspecialchars($fecha["nombre"]) ?>
                                        </span>
                                        <?php if ($esRecurrente): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle ms-1">Cada año</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle ms-1">Solo este año</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 text-dark fw-medium"><?= $fmtIni ?></td>
                                    <td class="text-dark fw-medium"><?= $fmtFin ?></td>
                                    <td class="text-center">
                                        <!-- Editar fuera del formulario de eliminar -->
                                        <a href="/diaslibres/<?= $fecha["id"] ?>/edit" class="btn btn-sm btn-outline-secondary rounded-3 me-1" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="/diaslibres/<?= $fecha["id"] ?>/delete" method="post" class="form-eliminar d-inline-block">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // DataTable completo: buscar, paginar, ordenar e info
        if (document.getElementById('tablaDiasLibres')) {
            jQuery('#tablaDiasLibres').DataTable({
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

        // ✅ Confirmación antes de eliminar (antes no la tenía: un clic y se borraba)
        document.querySelectorAll('.form-eliminar').forEach(formulario => {
            formulario.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: '¿Eliminar día libre?',
                    text: "Esta acción no se puede revertir.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>