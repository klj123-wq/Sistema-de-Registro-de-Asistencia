<?php
/** @var array $data */
/** @var string $id */

$meses = [
   1   =>  "enero", 2 => "febrero", 3 => "marzo", 4 => "abril",
   5   =>  "mayo", 6 => "junio", 7 => "julio", 8 => "agosto",
   9   => "septiembre", 10 => "octubre", 11 => "noviembre", 12 => "diciembre"
];

// Badge de estado (colores según tipo)
$badgeEstado = function ($estado) {
    $e = mb_strtolower(trim($estado ?? ''));
    $original = htmlspecialchars($estado ?? '');
    if ($e === '') return '<span class="text-muted">—</span>';
    if ($e === 'a tiempo')
        return '<span class="badge bg-success-subtle text-success border border-success-subtle">' . $original . '</span>';
    if (mb_strpos($e, 'retardo') !== false || mb_strpos($e, 'tarde') !== false)
        return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">' . $original . '</span>';
    if (mb_strpos($e, 'falta') !== false)
        return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">' . $original . '</span>';
    return '<span class="text-dark">' . $original . '</span>';
};
?>

<style>
    /* Aire para los controles del DataTable dentro de la tarjeta */
    #tablaAsistencias_wrapper {
        padding: 1rem 1.25rem;
    }
    #tablaAsistencias_wrapper .dataTables_length,
    #tablaAsistencias_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    #tablaAsistencias_wrapper .dataTables_info {
        padding-top: 1rem;
    }

    /* La tabla mantiene su tamaño y se desliza horizontalmente en móvil */
    #tablaAsistencias {
        min-width: 720px;
    }

    /* Fade suave al inicializar DataTables (sin overlay pesado) */
    #tablaAsistencias {
        opacity: 0;
    }
    #tablaAsistencias.table-visible {
        opacity: 1 !important;
        transition: opacity 0.4s ease;
    }
</style>

<div class="container-fluid py-3">

    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-clipboard2-check me-2 text-success"></i>Asistencias del Personal
            </h3>
            <p class="text-secondary small mb-0">Registro de asistencias del trabajador.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="d-flex flex-wrap gap-2 justify-content-md-end align-items-center">

                <a class="btn btn-success shadow-sm px-4 fw-semibold rounded-3" href="/asistencia/<?= $id ?>/registrar">
                    <i class="bi bi-calendar-plus me-1"></i> Registrar Asistencia
                </a>

                <!-- Imprimir reporte mensual -->
                <form action="/reportes/<?= $id ?>/especifico" method="post" target="_blank" class="d-flex align-items-center gap-1">
                    <select name="mes" class="form-select form-select-sm" style="width: auto;">
                        <?php foreach ($meses as $value => $mes): ?>
                            <option value="<?= $value ?>" <?= ($value == date("m")) ? "selected" : "" ?>><?= ucfirst($mes) ?></option>
                        <?php endforeach ?>
                    </select>
                    <select name="ano" class="form-select form-select-sm" style="width: auto;">
                        <?php for ($i = 0; $i < 3; $i++):
                            $anos = date("Y", strtotime(date("Y") . "- {$i} years")); ?>
                            <option value="<?= $anos ?>"><?= $anos ?></option>
                        <?php endfor ?>
                    </select>
                    <button type="submit" class="btn btn-outline-success btn-sm fw-semibold">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                </form>

            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tablaAsistencias" class="table table-hover align-middle w-100 mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="text-uppercase small fw-bold text-dark px-4 py-3" style="width: 12%;">Fecha</th>
                            <th class="text-uppercase small fw-bold text-dark py-3" style="width: 15%;">Hora Entrada</th>
                            <th class="text-uppercase small fw-bold text-dark py-3" style="width: 15%;">Hora Salida</th>
                            <th class="text-uppercase small fw-bold text-dark py-3" style="width: 17%;">Entrada</th>
                            <th class="text-uppercase small fw-bold text-dark py-3" style="width: 17%;">Salida</th>
                            <th class="text-uppercase small fw-bold text-dark py-3 text-center" style="width: 14%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $dato): ?>
                            <tr class="border-bottom">
                                <td class="px-4 text-dark fw-medium"><?= date("d/m/Y", strtotime($dato["fecha"])) ?></td>
                                <td class="text-dark"><?= !empty($dato["hora_entrada"]) ? date("h:i a", strtotime($dato["hora_entrada"])) : '<span class="text-muted">—</span>' ?></td>
                                <td class="text-dark"><?= !empty($dato["hora_salida"]) ? date("h:i a", strtotime($dato["hora_salida"])) : '<span class="text-muted">—</span>' ?></td>
                                <td><?= $badgeEstado($dato["estado_entrada"] ?? '') ?></td>
                                <td><?= $badgeEstado($dato["estado_salida"] ?? '') ?></td>
                                <td class="text-center">
                                    <a href="/asistencia/<?= $dato["id"] ?>/edit" class="btn btn-sm btn-outline-secondary rounded-3 me-1" title="Editar Asistencia">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3" title="Eliminar Asistencia"
                                        onclick="eliminarAsistencia(<?= $dato['id'] ?>)">
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

<script src="/js/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Fade de seguridad: si DataTables tarda o falla, la tabla igual aparece
    setTimeout(function() {
        document.getElementById('tablaAsistencias').classList.add('table-visible');
    }, 1500);

    // DataTable completo
    jQuery('#tablaAsistencias').DataTable({
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
        "order": [[0, "desc"]], // Fecha más reciente primero
        "dom": '<"row mb-3"<"col-12 col-md-6"l><"col-12 col-md-6"f>>rt<"row mt-3"<"col-12 col-md-5"i><"col-12 col-md-7"p>>',
        "columnDefs": [
            { "orderable": false, "targets": 5 } // Acciones sin ordenar
        ],
        "initComplete": function() {
            document.getElementById('tablaAsistencias').classList.add('table-visible');
        }
    });
});

async function eliminarAsistencia(id) {
    const confirm = await Swal.fire({
        title: '¿Eliminar asistencia?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirm.isConfirmed) return;

    const formData = new FormData();
    formData.append('id_user', '<?= $_SESSION["user_id"] ?? 1 ?>');

    try {
        const res = await fetch('/asistencia/' + id + '/delete', { method: 'POST', body: formData });
        const data = await res.json();

        Swal.fire({
            icon: data.icon || (data.success ? 'success' : 'error'),
            title: data.success ? '¡Eliminado!' : 'Atención',
            text: data.message,
            confirmButtonColor: data.success ? '#198754' : '#059669'
        }).then(() => {
            if (data.success) location.reload();
        });
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
    }
}
</script>