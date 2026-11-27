<?php

/**
 * @var array $turnos
 * @var ?string $turno_actual
 * @var array $metricas
 * @var array $detalle
 * @var array $asistencias
 * @var string $fecha_hoy
 */

?>

<style>
    /* ===== Selector de turno ===== */
    .selector-turno {
        display: inline-flex;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 50rem;
        padding: 4px;
        gap: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .btn-segmento {
        border: none;
        background: transparent;
        border-radius: 50rem;
        padding: 0.5rem 1.25rem;
        font-weight: 700;
        font-size: 0.9rem;
        color: #6b7280;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-segmento:hover {
        color: #374151;
    }

    .btn-segmento.activo-mañana {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    }

    .btn-segmento.activo-tarde {
        background: linear-gradient(135deg, #6366f1, #4338ca);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    .btn-segmento.activo {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    /* ===== Tarjetas ===== */
    .tarjeta-metrica {
        background: #ffffff;
        border: 1px solid #eef0f2;
        border-radius: 16px;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.9rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        cursor: pointer;
    }

    .tarjeta-metrica:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.10);
    }

    .icono-metrica {
        width: 48px;
        height: 48px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.3rem;
    }

    .icono-total {
        background: rgba(108, 117, 125, 0.12);
        color: #6c757d;
    }

    .icono-asistieron {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
    }

    .icono-retardos {
        background: rgba(245, 158, 11, 0.12);
        color: #d97706;
    }

    .icono-permisos {
        background: rgba(13, 110, 253, 0.10);
        color: #0d6efd;
    }

    .icono-ausentes {
        background: rgba(220, 53, 69, 0.10);
        color: #dc3545;
    }

    .numero-metrica {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1.1;
        color: #1f2937;
    }

    .etiqueta-metrica {
        font-size: 0.72rem;
        color: #9ca3af;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .bloque-metricas {
        animation: cambioTurno 0.35s ease;
    }

    @keyframes cambioTurno {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Fecha alineada a la derecha en móvil */
    @media (max-width: 767px) {
        .fecha-header {
            width: 100%;
            text-align: right;
        }
    }

    /* Tarjetas compactas en pantallas muy pequeñas */
    @media (max-width: 480px) {
        .tarjeta-metrica {
            padding: 0.8rem 0.9rem;
            gap: 0.6rem;
            align-items: flex-start;
        }

        .icono-metrica {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            font-size: 1rem;
        }

        .numero-metrica {
            font-size: 1.25rem;
        }

        .etiqueta-metrica {
            font-size: 0.6rem;
            letter-spacing: 0.2px;
            line-height: 1.2;
            white-space: normal;
            word-break: break-word;
        }
    }
</style>

<div class="container-fluid">

    <!-- ===== Header: selector de turno + fecha ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 mt-2">
        <div class="selector-turno" role="group" aria-label="Selección de turno">
            <?php if (empty($turnos)): ?>
                <button type="button" class="btn-segmento" disabled style="opacity: 0.55; cursor: default;">
                    <i class="bi bi-x-circle"></i> SIN TURNOS
                </button>
            <?php else: ?>
                <?php foreach ($turnos as $t): ?>
                    <button type="button" class="btn-segmento" data-turno="<?= $t['nombre'] ?>" onclick="cambiarTurno('<?= $t['nombre'] ?>')">
                        <i class="bi <?= $t['nombre'] === 'tarde' ? 'bi-moon-fill' : 'bi-sun-fill' ?>"></i>
                        <?= strtoupper($t['nombre']) ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <span class="text-muted small fw-semibold fecha-header">
            <i class="bi bi-calendar3 me-1"></i><?= $fecha_hoy ?>
        </span>
    </div>

    <!-- ===== Métricas por turno ===== -->
    <?php if (empty($turnos)): ?>
        
        <div class="bloque-metricas">
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">

                <?php
                $tarjetas_vacias = [
                    ['icono' => 'bi-people-fill',         'clase' => 'icono-total',      'label' => 'Personal del turno'],
                    ['icono' => 'bi-check-circle-fill',   'clase' => 'icono-asistieron', 'label' => 'Asistentes'],
                    ['icono' => 'bi-clock-fill',          'clase' => 'icono-retardos',   'label' => 'Retardos'],
                    ['icono' => 'bi-calendar-check-fill', 'clase' => 'icono-permisos',   'label' => 'Permisos'],
                    ['icono' => 'bi-person-dash-fill',    'clase' => 'icono-ausentes',   'label' => 'Ausentes'],
                ];
                foreach ($tarjetas_vacias as $tv):
                ?>
                    <div class="col">
                        <div class="tarjeta-metrica" style="cursor: default; opacity: 0.65;">
                            <div class="icono-metrica <?= $tv['clase'] ?>"><i class="bi <?= $tv['icono'] ?>"></i></div>
                            <div>
                                <div class="numero-metrica text-muted">--</div>
                                <div class="etiqueta-metrica"><?= $tv['label'] ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    <?php else: ?>
        <?php foreach ($turnos as $t):
            $nombreTurno = $t['nombre'];
            $m = $metricas[$nombreTurno] ?? ['total' => 0, 'asistieron' => 0, 'retardos' => 0, 'permisos' => 0, 'ausentes' => 0];
        ?>
            <div class="bloque-metricas <?= $nombreTurno === $turno_actual ? '' : 'd-none' ?>" data-shift="<?= $nombreTurno ?>">
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">

                    <div class="col">
                        <div class="tarjeta-metrica" onclick="verDetalle('<?= $nombreTurno ?>', 'total')">
                            <div class="icono-metrica icono-total"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <div class="numero-metrica"><?= $m['total'] ?></div>
                                <div class="etiqueta-metrica">Personal del turno</div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="tarjeta-metrica" onclick="verDetalle('<?= $nombreTurno ?>', 'asistieron')">
                            <div class="icono-metrica icono-asistieron"><i class="bi bi-check-circle-fill"></i></div>
                            <div>
                                <div class="numero-metrica"><?= $m['asistieron'] ?></div>
                                <div class="etiqueta-metrica">Asistentes</div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="tarjeta-metrica" onclick="verDetalle('<?= $nombreTurno ?>', 'retardos')">
                            <div class="icono-metrica icono-retardos"><i class="bi bi-clock-fill"></i></div>
                            <div>
                                <div class="numero-metrica"><?= $m['retardos'] ?></div>
                                <div class="etiqueta-metrica">Retardos</div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="tarjeta-metrica" onclick="verDetalle('<?= $nombreTurno ?>', 'permisos')">
                            <div class="icono-metrica icono-permisos"><i class="bi bi-calendar-check-fill"></i></div>
                            <div>
                                <div class="numero-metrica"><?= $m['permisos'] ?></div>
                                <div class="etiqueta-metrica">Permisos</div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="tarjeta-metrica" onclick="verDetalle('<?= $nombreTurno ?>', 'ausentes')">
                            <div class="icono-metrica icono-ausentes"><i class="bi bi-person-dash-fill"></i></div>
                            <div>
                                <div class="numero-metrica"><?= $m['ausentes'] ?></div>
                                <div class="etiqueta-metrica">Ausentes</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ===== Tabla ===== -->
    <div class="container-fluid px-0">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-check2-circle me-2 text-success"></i>Asistencias del Turno</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaAsistenciaInicio" class="table table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Hora</th>
                                <th>Trabajador</th>
                                <th>Cargo</th>
                                <th>Turno</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asistencias as $asistencia):
                                $horaEntrada    = isset($asistencia['hora_entrada']) ? date('h:i A', strtotime($asistencia['hora_entrada'])) : '--:--';
                                $nombreCompleto = trim(($asistencia['nombre'] ?? '—') . ' ' . ($asistencia['apellido'] ?? ''));
                                $cargo          = ucfirst($asistencia['cargo'] ?? 'No definido');
                                $turnoRaw       = $asistencia['turno'] ?? $asistencia['nombre_turno'] ?? '';
                                $turno          = empty(trim($turnoRaw)) ? 'Mixto' : ucfirst($turnoRaw);
                                $idAsistencia   = $asistencia['id'] ?? $asistencia['id_persona'] ?? 0;
                                $nombreAlerta   = addslashes(trim(($asistencia['nombre'] ?? 'Usuario') . ' ' . ($asistencia['apellido'] ?? '')));
                            ?>
                                <tr>
                                    <td><?= $horaEntrada ?></td>
                                    <td><?= htmlspecialchars($nombreCompleto) ?></td>
                                    <td><?= htmlspecialchars($cargo) ?></td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle"><?= $turno ?></span></td>
                                    <td>
                                        <?php if (($asistencia["estado_entrada"] ?? '') == "A tiempo"): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">A tiempo</span>
                                        <?php elseif (($asistencia["estado_entrada"] ?? '') == "Con retardo"): ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Con retardo</span>
                                        <?php endif ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm" role="group">
                                            <a href="/asistencia/<?= $idAsistencia ?>/edit" class="btn btn-sm btn-outline-secondary" title="Editar Asistencia">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Registro"
                                                onclick="confirmarEliminar(<?= $idAsistencia ?>, '<?= $nombreAlerta ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== Modal de detalle de tarjetas ===== -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 480px;">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="tituloDetalle">Detalle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body pt-2" id="cuerpoDetalle"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // --- DataTable ---
        var tablaAsistencia = jQuery('#tablaAsistenciaInicio').DataTable({
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
            "pageLength": 8,
            "dom": '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
            "columnDefs": [{
                "orderable": false,
                "targets": 5
            }]
        });

        // --- Cambio de turno ---
        window.cambiarTurno = function(turno) {
            document.querySelectorAll('.btn-segmento').forEach(function(b) {
                b.classList.remove('activo-mañana', 'activo-tarde', 'activo');
                if (b.dataset.turno === turno) {
                    if (turno === 'mañana') b.classList.add('activo-mañana');
                    else if (turno === 'tarde') b.classList.add('activo-tarde');
                    else b.classList.add('activo');
                }
            });

            document.querySelectorAll('.bloque-metricas').forEach(function(b) {
                if (b.dataset.shift === turno) {
                    b.classList.remove('d-none');
                    b.style.animation = 'none';
                    void b.offsetWidth;
                    b.style.animation = '';
                } else {
                    b.classList.add('d-none');
                }
            });

            tablaAsistencia.column(3).search(turno, true, false).draw();
        };

        // ✅ Si no hay turnos ($turno_actual = null), no se intenta seleccionar nada
        const turnoInicial = <?= json_encode($turno_actual) ?>;
        if (turnoInicial) cambiarTurno(turnoInicial);
    });

    // --- Detalle de tarjetas (modal) ---
    const datosDetalle = <?= json_encode($detalle ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    const titulosDetalle = {
        'total': 'Personal del turno',
        'asistieron': 'Asistieron',
        'retardos': 'Llegaron con retardo',
        'permisos': 'Permisos del día',
        'ausentes': 'Ausentes'
    };

    function verDetalle(turno, tipo) {
        const lista = (datosDetalle[turno] && datosDetalle[turno][tipo]) || [];
        const nombreTurno = turno.charAt(0).toUpperCase() + turno.slice(1);

        document.getElementById('tituloDetalle').textContent =
            (titulosDetalle[tipo] || 'Detalle') + ' — Turno ' + nombreTurno;

        const cuerpo = document.getElementById('cuerpoDetalle');

        if (lista.length === 0) {
            cuerpo.innerHTML =
                '<div class="text-center text-muted py-4">' +
                '<i class="bi bi-inbox fs-2 d-block mb-2"></i>' +
                'No hay registros para mostrar' +
                '</div>';
        } else {
            let filas = '';
            lista.forEach(function(p) {
                filas +=
                    '<div class="list-group-item px-0 d-flex justify-content-between align-items-center">' +
                    '<span class="fw-semibold">' + p.nombre + '</span>' +
                    '<small class="text-muted ms-2 text-end">' + (p.extra || '') + '</small>' +
                    '</div>';
            });

            cuerpo.innerHTML =
                '<div class="list-group list-group-flush">' + filas + '</div>' +
                '<p class="text-muted small mt-3 mb-0 text-center">' + lista.length + ' persona(s)</p>';
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetalle')).show();
    }

    // --- Eliminar ---
    function confirmarEliminar(id, nombre) {
        Swal.fire({
            title: '¿Eliminar registro?',
            html: `Se eliminará la asistencia de <strong>${nombre}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/personal/${id}/delete`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>