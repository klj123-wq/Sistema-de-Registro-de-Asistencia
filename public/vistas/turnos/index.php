<?php

/** @var array $data */
?>

<style>
    /* Aire para los controles del DataTable (buscador, paginación) dentro de la tarjeta */
    #tablaTurnos_wrapper {
        padding: 1rem 1.25rem;
    }

    /* Espacio entre los controles de arriba y la tabla */
    #tablaTurnos_wrapper .dataTables_length,
    #tablaTurnos_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    /* Espacio entre la tabla y la info/paginación de abajo */
    #tablaTurnos_wrapper .dataTables_info {
        padding-top: 1rem;
    }

    /* La tabla mantiene su tamaño y se desliza horizontalmente en pantallas chicas */
    #tablaTurnos {
        min-width: 640px;
    }
</style>

<div class="container-fluid py-3" id="contenedor-turnos">

    <div class="animate__animated animate__fadeIn d-print-none">

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h3 class="fw-bold text-dark mb-1">
                    <i class="bi bi-clock-history me-2 text-success"></i>Gestión de Turnos
                </h3>
                <p class="text-secondary small mb-0">Administra y configura los horarios de trabajo del personal.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a class="btn btn-success shadow-sm px-4 fw-semibold rounded-3" href="turnos/registrar">
                    <i class="bi bi-plus-circle-fill me-2"></i> Registrar Turno
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="tablaTurnos" class="table table-hover table-borderless align-middle w-100 mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="text-uppercase small fw-bold text-dark px-4 py-3">Turno</th>
                                <th class="text-uppercase small fw-bold text-dark py-3">Cargo</th>
                                <th class="text-uppercase small fw-bold text-dark py-3 text-center">Entrada</th>
                                <th class="text-uppercase small fw-bold text-dark py-3 text-center">Salida</th>
                                <th class="text-uppercase small fw-bold text-dark py-3 text-center d-none d-md-table-cell">Tolerancia</th>
                                <th class="text-uppercase small fw-bold text-dark py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($data as $turno) : ?>
                                <tr class="border-bottom">

                                    <td class="px-4 text-dark fw-medium">
                                        <?= htmlspecialchars(ucfirst($turno["nombre"])) ?>
                                    </td>

                                    <td class="fw-semibold text-dark">
                                        <?= htmlspecialchars(ucfirst($turno["cargo"])) ?>
                                    </td>

                                    <td class="text-center text-dark fw-medium">
                                        <?= date("h:i a", strtotime($turno["hora_entrada"])) ?>
                                    </td>

                                    <td class="text-center text-dark fw-medium">
                                        <?= date("h:i a", strtotime($turno["hora_salida"])) ?>
                                    </td>

                                    <td class="text-center text-dark fw-medium d-none d-md-table-cell">
                                        <?= htmlspecialchars($turno["minutos_tolerancia"]) ?> min
                                    </td>

                                    <td class="text-center">
                                        <a href="/turnos/<?= $turno["id"] ?>/edit" class="btn btn-sm btn-outline-secondary rounded-3 me-1" title="Editar Turno">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="/turnos/<?= $turno["id"] ?>/delete" method="post" class="form-eliminar d-inline-block">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Eliminar Turno">
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

<script src="/js/sweetalert2.all.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ===== ✅ DataTable explícito (ya no depende del config global) =====
        if (document.getElementById('tablaTurnos')) {
            jQuery('#tablaTurnos').DataTable({
                "dom": 't', // solo la tabla: sin buscar, paginar, info ni "mostrar"
                "paging": false, // sin paginación (muestra todos los registros)
                "searching": false, // sin buscador
                "info": false, // sin el texto "Mostrando X de Y"
                "lengthChange": false, // sin selector de registros
                "ordering": true, // ✅ ordenar tocando los títulos de columna
                "columnDefs": [{
                        "orderable": false,
                        "targets": [4, 5]
                    } // Tolerancia (4) y Acciones (5) sin ordenar
                ],
                "language": {
                    "emptyTable": "Ningún dato disponible en esta tabla"
                }
            });
        }

        // ===== Confirmación + eliminar con fetch (JSON) =====
        document.querySelectorAll('.form-eliminar').forEach(formulario => {
            formulario.addEventListener('submit', async function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');

                // 1. Confirmación (igual que antes)
                const confirmado = await Swal.fire({
                    title: '¿Eliminar turno?',
                    text: "Esta acción no se puede revertir.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (!confirmado.isConfirmed) return;

                // 2. Spinner en el botón mientras procesa
                const originalBtn = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                try {
                    // 3. Envío por fetch
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();

                    // 4. Mensaje según el JSON del servidor
                    await Swal.fire({
                        icon: data.icon || (data.success ? 'success' : 'error'),
                        title: data.success ? '¡Eliminado!' : 'Atención',
                        text: data.message,
                        confirmButtonColor: data.success ? '#198754' : '#dc3545'
                    });

                    // 5. ✅ Redirigir a donde diga el JSON
                    if (data.success && data.redirect) {
                        window.location.href = data.redirect;
                    }

                } catch (error) {
                    console.error("Error Fetch:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Red',
                        text: 'No se pudo recibir una respuesta del servidor.'
                    });
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalBtn;
                }
            });
        });
    });
</script>