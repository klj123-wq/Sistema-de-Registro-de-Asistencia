<?php


/**
 * @var int $mes_actual
 * @var int $anio_actual
 * @var array $totales
 * @var array $reporte_personal
 * @var array $meses
 * 
 */



$mes_actual       = (int)($mes_actual  ?? date('n'));
$anio_actual      = (int)($anio_actual ?? date('Y'));
$totales          = is_array($totales ?? null)          ? $totales          : ['trabajadores' => 0, 'asistencias' => 0, 'faltas' => 0, 'permisos' => 0];
$reporte_personal = is_array($reporte_personal ?? null) ? $reporte_personal : [];



$alerta = $_SESSION['alerta'] ?? null;
if ($alerta) unset($_SESSION['alerta']);
?>

<style>
    .kpi-card {
        border: 1px solid #eef0f2 !important;
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08) !important;
    }

    .kpi-icono {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    #tablaReporte {
        min-width: 640px;
    }

    /* Transición suave de carga */
    #zonaDatos {
        transition: opacity 0.25s ease;
    }

    #zonaDatos.cargando {
        opacity: 0.45;
    }

    .spinner-overlay {
        position: absolute;
        inset: 0;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.55);
        border-radius: 24px;
    }

    /* Aire dentro de la tarjeta de la tabla */
    #tablaReporte_wrapper {
        padding: 1rem 1.25rem;
    }
</style>

<div class="container-fluid py-3" id="areaReporte">

    <?php if ($alerta): ?>
        <script>
            Swal.fire({
                icon: '<?= $alerta['tipo'] ?>',
                title: '<?= $alerta['tipo'] === "error" ? "Atención" : "Éxito" ?>',
                text: <?= json_encode($alerta['mensaje'] ?? '') ?>,
                confirmButtonColor: '<?= $alerta['tipo'] === "error" ? "#dc3545" : "#198754" ?>'
            });
        </script>
    <?php endif; ?>

    <!-- ===== Encabezado ===== -->
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-8">
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-clipboard2-data-fill me-2 text-success"></i>Estadísticas de Asistencia
            </h3>
            <p class="text-secondary small mb-0">
                Período: <strong id="textoPeriodo"><?= ucfirst($meses[$mes_actual]) ?> <?= $anio_actual ?></strong>
            </p>
        </div>
        <div class="col-md-4 text-md-end no-print">
            <button type="button" class="btn btn-success btn-sm fw-semibold rounded-3"
                data-bs-toggle="modal" data-bs-target="#modalImprimir"
                title="Imprimir reporte en PDF">
                <i class="bi bi-printer me-1"></i> Imprimir
            </button>
        </div>
    </div>
    <div id="zonaDatos">
        <!-- ===== KPIs ===== -->
        <div class="row g-3 mb-3">
            <div class="col-6 col-md-3">
                <div class="card kpi-card border-0 shadow-sm h-100">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="kpi-icono bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Trabajadores</small>
                            <h3 class="fw-bold mb-0 text-dark" id="kpiTrabajadores"><?= (int)$totales['trabajadores'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card kpi-card border-0 shadow-sm h-100">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="kpi-icono bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Asistencias</small>
                            <h3 class="fw-bold mb-0 text-dark" id="kpiAsistencias"><?= (int)$totales['asistencias'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card kpi-card border-0 shadow-sm h-100">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="kpi-icono bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-circle-fill"></i></div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Faltas</small>
                            <h3 class="fw-bold mb-0 text-dark" id="kpiFaltas"><?= (int)$totales['faltas'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card kpi-card border-0 shadow-sm h-100">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="kpi-icono bg-warning bg-opacity-10 text-warning"><i class="bi bi-journal-check"></i></div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Permisos</small>
                            <h3 class="fw-bold mb-0 text-dark" id="kpiPermisos"><?= (int)$totales['permisos'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Selects de período (despegados del borde) ===== -->
        <div class="d-flex gap-2 mb-3 ms-2 ms-md-3 no-print">
            <select id="selMes" class="form-select form-select-sm bg-white border" style="max-width: 130px;">
                <?php foreach ($meses as $num => $nombre): ?>
                    <option value="<?= $num ?>" <?= $mes_actual === $num ? 'selected' : '' ?>><?= ucfirst($nombre) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="selAnio" class="form-select form-select-sm bg-white border" style="max-width: 95px;">
                <?php for ($i = 0; $i <= 3; $i++): $anio = (int)date('Y') - $i; ?>
                    <option value="<?= $anio ?>" <?= $anio_actual === $anio ? 'selected' : '' ?>><?= $anio ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- ===== Tabla (sin título) ===== -->
        <div class="position-relative">
            <!-- Spinner de carga -->
            <div id="spinnerCarga" class="spinner-overlay d-none">
                <div class="spinner-border" style="width: 2.5rem; height: 2.5rem; color: #10b981;" role="status"></div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="tablaReporte" class="table table-hover align-middle w-100 mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-uppercase small fw-bold text-dark px-4 py-3">Trabajador</th>
                                    <th class="text-uppercase small fw-bold text-dark py-3">Cargo</th>
                                    <th class="text-uppercase small fw-bold text-dark py-3">Turno</th>
                                    <th class="text-uppercase small fw-bold text-dark py-3 text-center">Asist.</th>
                                    <th class="text-uppercase small fw-bold text-dark py-3 text-center">Faltas</th>
                                    <th class="text-uppercase small fw-bold text-dark py-3 text-center">Retard.</th>
                                    <th class="text-uppercase small fw-bold text-dark py-3 text-center">Perm.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reporte_personal as $empleado):
                                    $nombreCompleto = trim(($empleado['nombre'] ?? '') . ' ' . ($empleado['apellido'] ?? ''));
                                    $iniciales = strtoupper(substr($empleado['nombre'] ?? '?', 0, 1) . substr($empleado['apellido'] ?? '', 0, 1));
                                ?>
                                    <tr class="border-bottom">
                                        <td class="px-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-success bg-opacity-10 text-success fw-bold d-flex align-items-center justify-content-center"
                                                    style="width: 34px; height: 34px; font-size: 0.8rem; flex-shrink: 0;">
                                                    <?= $iniciales ?>
                                                </div>
                                                <span class="fw-semibold text-dark"><?= htmlspecialchars($nombreCompleto) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-muted"><?= htmlspecialchars(ucfirst($empleado['cargo'] ?? '—')) ?></td>
                                        <td><span class="badge bg-light border text-secondary"><?= htmlspecialchars($empleado['turno'] ?? 'Mixto') ?></span></td>
                                        <td class="text-center fw-bold text-dark"><?= (int)$empleado['total_asistencias'] ?></td>
                                        <td class="text-center fw-bold <?= (int)$empleado['total_faltas'] > 0 ? 'text-danger' : 'text-muted' ?>"><?= (int)$empleado['total_faltas'] ?></td>
                                        <td class="text-center fw-bold <?= (int)$empleado['total_retardos'] > 0 ? 'text-warning' : 'text-muted' ?>"><?= (int)$empleado['total_retardos'] ?></td>
                                        <td class="text-center fw-bold text-muted"><?= (int)$empleado['total_permisos'] ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /#zonaDatos -->
    </div>

    <!-- ===== MODAL: Imprimir PDF ===== -->
    <div class="modal fade" id="modalImprimir" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-printer me-2 text-success"></i>Imprimir Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-muted small mb-3">Seleccione los filtros para el PDF.</p>
                    <form id="formImprimirPdf" action="/reportes/general" method="post">
                        <input type="hidden" name="CSRF" value="<?php echo \libs\CSRF::generar() ?>">
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="pdfCargo" class="form-label small fw-bold text-secondary mb-1">Cargo</label>
                                <select name="cargo" id="pdfCargo" class="form-select bg-light border-0">
                                    <option value="todos">Todos</option>
                                    <?php foreach (['administrativo', 'docente', 'obrero', 'cocinero'] as $c): ?>
                                        <option value="<?= $c ?>"><?= ucfirst($c) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="pdfTurno" class="form-label small fw-bold text-secondary mb-1">Turno</label>
                                <select name="turno" id="pdfTurno" class="form-select bg-light border-0">
                                    <option value="todos">Todos</option>
                                    <option value="mañana">Mañana</option>
                                    <option value="tarde">Tarde</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="pdfMes" class="form-label small fw-bold text-secondary mb-1">Mes</label>
                                <select name="mes" id="pdfMes" class="form-select bg-light border-0">
                                    <?php foreach ($meses as $num => $nombre): ?>
                                        <option value="<?= $num ?>" <?= $mes_actual === $num ? 'selected' : '' ?>><?= ucfirst($nombre) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="pdfAno" class="form-label small fw-bold text-secondary mb-1">Año</label>
                                <select name="ano" id="pdfAno" class="form-select bg-light border-0">
                                    <?php for ($i = 0; $i <= 3; $i++): $anio = (int)date('Y') - $i; ?>
                                        <option value="<?= $anio ?>" <?= $anio_actual === $anio ? 'selected' : '' ?>><?= $anio ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border fw-semibold text-secondary rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnGenerarPdf" class="btn btn-success fw-bold rounded-3">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Generar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                background-color: #fff !important;
            }

            .no-print,
            nav,
            .offcanvas,
            footer,
            .modal {
                display: none !important;
            }

            #areaReporte {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===== DataTable (se conserva la instancia para actualizarla vía JSON) =====
            const tabla = jQuery('#tablaReporte').DataTable({
                "language": {
                    "processing": "Procesando...",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "Ningún dato disponible para este período",
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
                "order": [
                    [0, "asc"]
                ]
            });

            // ===== Utilidades =====
            function escapar(texto) {
                return String(texto ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }

            function celdaTrabajador(nombre, apellido) {
                const completo = (nombre + ' ' + (apellido || '')).trim();
                const iniciales = ((nombre ? nombre[0] : '?') + (apellido ? apellido[0] : '')).toUpperCase();
                return '<div class="d-flex align-items-center gap-2">' +
                    '<div class="rounded-circle bg-success bg-opacity-10 text-success fw-bold d-flex align-items-center justify-content-center" ' +
                    'style="width:34px;height:34px;font-size:0.8rem;flex-shrink:0;">' + escapar(iniciales) + '</div>' +
                    '<span class="fw-semibold text-dark">' + escapar(completo) + '</span></div>';
            }

            function celdaConteo(valor, clase) {
                return '<span class="fw-bold ' + ((parseInt(valor) > 0 && clase) ? clase : 'text-muted') + '">' + (parseInt(valor) || 0) + '</span>';
            }

            // ✅ Copia completa de las opciones de mes (se llena una sola vez)
            const mesesCompletos = {};

            // ✅ Elimina los meses futuros cuando el año elegido es el actual
            function actualizarMesesDisponibles() {
                const anioActual = new Date().getFullYear();
                const mesActual = new Date().getMonth() + 1; // 1-12

                [
                    ['selMes', 'selAnio'],
                    ['pdfMes', 'pdfAno']
                ].forEach(function(pares) {
                    const selMes = document.getElementById(pares[0]);
                    const selAnio = document.getElementById(pares[1]);
                    if (!selMes || !selAnio) return;

                    // Primera pasada: guardar copia completa de opciones
                    if (!mesesCompletos[selMes.id]) {
                        mesesCompletos[selMes.id] = Array.from(selMes.options).map(function(o) {
                            return {
                                value: o.value,
                                texto: o.textContent
                            };
                        });
                    }

                    const esAnioActual = parseInt(selAnio.value) === anioActual;
                    const mesPrevio = selMes.value;

                    // Reconstruir el select solo con los meses permitidos
                    selMes.innerHTML = '';
                    mesesCompletos[selMes.id].forEach(function(op) {
                        const mes = parseInt(op.value);
                        if (esAnioActual && mes > mesActual) return; // 👻 no se agrega = desaparece
                        const opcion = document.createElement('option');
                        opcion.value = op.value;
                        opcion.textContent = op.texto;
                        selMes.appendChild(opcion);
                    });

                    // Restaurar el mes que estaba seleccionado si aún existe;
                    // si no, quedarse con el último mes visible (el más reciente)
                    const aunExiste = Array.from(selMes.options).some(function(o) {
                        return o.value === mesPrevio;
                    });
                    selMes.value = aunExiste ?
                        mesPrevio :
                        selMes.options[selMes.options.length - 1].value;
                });
            }

            // ===== Actualización vía JSON (sin recargar la página) =====
            const selMes = document.getElementById('selMes');
            const selAnio = document.getElementById('selAnio');

            function setCargando(activo) {
                document.getElementById('zonaDatos').classList.toggle('cargando', activo);
                document.getElementById('spinnerCarga').classList.toggle('d-none', !activo);
                selMes.disabled = activo;
                selAnio.disabled = activo;
            }

            async function cargarDatos() {
                const mes = selMes.value;
                const anio = selAnio.value;

                setCargando(true); // atenúa + spinner + bloquea selects

                try {
                    const params = new URLSearchParams();
                    params.append('mes', mes);
                    params.append('anio', anio);

                    const res = await fetch('/reportes/general-datos', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: params
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const data = await res.json();

                    // 1. KPIs
                    document.getElementById('kpiTrabajadores').textContent = data.totales.trabajadores ?? 0;
                    document.getElementById('kpiAsistencias').textContent = data.totales.asistencias ?? 0;
                    document.getElementById('kpiFaltas').textContent = data.totales.faltas ?? 0;
                    document.getElementById('kpiPermisos').textContent = data.totales.permisos ?? 0;

                    // 2. Texto del período
                    const mesTexto = selMes.options[selMes.selectedIndex].text;
                    document.getElementById('textoPeriodo').textContent = mesTexto + ' ' + anio;

                    // 3. Tabla
                    tabla.clear();
                    (data.reporte_personal || []).forEach(function(e) {
                        tabla.row.add([
                            celdaTrabajador(e.nombre, e.apellido),
                            escapar(e.cargo ? e.cargo.charAt(0).toUpperCase() + e.cargo.slice(1) : '—'),
                            '<span class="badge bg-light border text-secondary">' + escapar(e.turno || 'Mixto') + '</span>',
                            celdaConteo(e.total_asistencias, ''),
                            celdaConteo(e.total_faltas, 'text-danger'),
                            celdaConteo(e.total_retardos, 'text-warning'),
                            celdaConteo(e.total_permisos, '')
                        ]);
                    });
                    tabla.draw();

                } catch (error) {
                    console.error('Error cargando datos:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar los datos del período.',
                        confirmButtonColor: '#d33'
                    });
                } finally {
                    setCargando(false); // restaura con el fade suave
                }
            }

            selMes.addEventListener('change', cargarDatos);
            selAnio.addEventListener('change', function() {
                actualizarMesesDisponibles(); // primero ajustar meses, luego cargar
                cargarDatos();
            });

            // Aplicar al iniciar (por si el mes preseleccionado por PHP es futuro)
            actualizarMesesDisponibles();

            // ===== Generar PDF desde el modal =====
            document.getElementById('btnGenerarPdf').addEventListener('click', async function() {
                const btn = this;
                const original = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generando...';
                btn.disabled = true;

                try {
                    const response = await fetch('/reportes/general', {
                        method: 'POST',
                        body: new FormData(document.getElementById('formImprimirPdf'))
                    });

                    const clone = response.clone();
                    let dataError = null;
                    try {
                        dataError = await clone.json();
                    } catch (e) {
                        /* es el PDF */
                    }

                    if (dataError) {
                        Swal.fire({
                            icon: dataError.icon || 'error',
                            title: 'Atención',
                            text: dataError.message || 'No se pudo generar el reporte',
                            confirmButtonColor: '#059669'
                        });
                    } else {
                        const blob = await response.blob();
                        const fileURL = URL.createObjectURL(blob);
                        const pestana = window.open(fileURL, '_blank');
                        if (!pestana) {
                            const a = document.createElement('a');
                            a.href = fileURL;
                            a.download = 'Reporte_General.pdf';
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                        }
                        bootstrap.Modal.getInstance(document.getElementById('modalImprimir')).hide();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: 'No se pudo comunicar con el servidor.',
                        confirmButtonColor: '#d33'
                    });
                } finally {
                    btn.innerHTML = original;
                    btn.disabled = false;
                }
            });
        });
    </script>