<?php

/** @var array $data */
/** @var string $id */

$meses = [
    1   =>  "enero",
    2 => "febrero",
    3 => "marzo",
    4 => "abril",
    5   =>  "mayo",
    6 => "junio",
    7 => "julio",
    8 => "agosto",
    9   =>  "septiembre",
    10 => "octubre",
    11 => "noviembre",
    12 => "diciembre"
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
        min-width: 600px;
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
            <p class="text-secondary small mb-0">Registro de asistencias del trabajador (de solo lectura).</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <!-- Única acción: imprimir reporte (abre modal) -->
            <button type="button" class="btn btn-outline-success shadow-sm px-4 fw-semibold rounded-3" data-bs-toggle="modal" data-bs-target="#modalImprimir">
                <i class="bi bi-printer me-1"></i> Imprimir Reporte
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tablaAsistencias" class="table table-hover align-middle w-100 mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="text-uppercase small fw-bold text-dark px-4 py-3" style="width: 16%;">Fecha</th>
                            <th class="text-uppercase small fw-bold text-dark py-3 text-center" style="width: 20%;">Hora Entrada</th>
                            <th class="text-uppercase small fw-bold text-dark py-3 text-center" style="width: 20%;">Hora Salida</th>
                            <th class="text-uppercase small fw-bold text-dark py-3 text-center" style="width: 22%;">Entrada</th>
                            <th class="text-uppercase small fw-bold text-dark py-3 text-center" style="width: 22%;">Salida</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $dato): ?>
                            <tr class="border-bottom">
                                <td class="px-4 text-dark fw-medium"><?= date("d/m/Y", strtotime($dato["fecha"])) ?></td>
                                <td class="text-center text-dark"><?= !empty($dato["hora_entrada"]) ? date("h:i a", strtotime($dato["hora_entrada"])) : '<span class="text-muted">—</span>' ?></td>
                                <td class="text-center text-dark"><?= !empty($dato["hora_salida"]) ? date("h:i a", strtotime($dato["hora_salida"])) : '<span class="text-muted">—</span>' ?></td>
                                <td class="text-center"><?= $badgeEstado($dato["estado_entrada"] ?? '') ?></td>
                                <td class="text-center"><?= $badgeEstado($dato["estado_salida"] ?? '') ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODAL: IMPRIMIR REPORTE ========== -->
<div class="modal fade" id="modalImprimir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-printer me-2 text-success"></i>Imprimir Reporte Mensual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted small mb-3">Seleccione el período del reporte de asistencia.</p>
                <form id="formImprimir" action="/reportes/<?= $id ?>/especifico" method="post" target="_blank">
                    <div class="row g-3">
                        <div class="col-6">
                            <label for="mesReporte" class="form-label small fw-bold text-secondary mb-1">Mes</label>
                            <select name="mes" id="mesReporte" class="form-select bg-light border-0">
                                <?php foreach ($meses as $value => $mes): ?>
                                    <option value="<?= $value ?>" <?= ($value == date("m")) ? "selected" : "" ?>><?= ucfirst($mes) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="anoReporte" class="form-label small fw-bold text-secondary mb-1">Año</label>
                            <select name="ano" id="anoReporte" class="form-select bg-light border-0">
                                <?php for ($i = 0; $i < 3; $i++):
                                    $anos = date("Y", strtotime(date("Y") . "- {$i} years")); ?>
                                    <option value="<?= $anos ?>"><?= $anos ?></option>
                                <?php endfor ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light border fw-semibold text-secondary rounded-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success fw-bold rounded-3" id="btnGenerarReporte">
                    <i class="bi bi-printer me-1"></i> Generar Reporte
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        setTimeout(function() {
            document.getElementById('tablaAsistencias').classList.add('table-visible');
        }, 1500);

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
            "order": [
                [0, "desc"]
            ],
            "dom": '<"row mb-3"<"col-12 col-md-6"l><"col-12 col-md-6"f>>rt<"row mt-3"<"col-12 col-md-5"i><"col-12 col-md-7"p>>',
            "initComplete": function() {
                document.getElementById('tablaAsistencias').classList.add('table-visible');
            }
        });

        // Generar reporte: envía el form (abre en pestaña nueva) y cierra el modal
        document.getElementById('btnGenerarReporte').addEventListener('click', function() {
            document.getElementById('formImprimir').submit();
            bootstrap.Modal.getInstance(document.getElementById('modalImprimir')).hide();
        });
    });
    // ===== Meses futuros: que NO aparezcan en el select =====
    const mesSelect = document.getElementById('mesReporte');
    const anoSelect = document.getElementById('anoReporte');
    const MES_ACTUAL = <?= (int)date("n") ?>;
    const ANO_ACTUAL = <?= (int)date("Y") ?>;

    // Guardamos el listado completo de meses para poder restaurarlo
    const opcionesOriginales = Array.from(mesSelect.options).map(o => ({
        value: o.value,
        texto: o.textContent
    }));

    function actualizarMeses() {
        const esAnoActual = parseInt(anoSelect.value, 10) === ANO_ACTUAL;

        mesSelect.innerHTML = ''; // vaciar

        opcionesOriginales.forEach(o => {
            const mesNum = parseInt(o.value, 10);
            // Si es el año actual, omitimos los meses futuros
            if (esAnoActual && mesNum > MES_ACTUAL) return;

            const opt = document.createElement('option');
            opt.value = o.value;
            opt.textContent = o.texto;
            if (mesNum === MES_ACTUAL) opt.selected = true;
            mesSelect.appendChild(opt);
        });
    }

    anoSelect.addEventListener('change', actualizarMeses);
    document.getElementById('modalImprimir').addEventListener('shown.bs.modal', actualizarMeses);
    actualizarMeses(); // estado inicial
</script>