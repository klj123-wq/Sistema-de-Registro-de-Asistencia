<?php
/** @var array $data */

if (!isset($data) || !is_array($data)) {
    $data = [];
}

$categorias = [
    'auth'     => ['nombre' => 'Autenticación', 'clase' => 'bg-primary-subtle text-primary border border-primary-subtle'],
    'personal' => ['nombre' => 'Personal',      'clase' => 'bg-success-subtle text-success border border-success-subtle'],
    'config'   => ['nombre' => 'Configuración', 'clase' => 'bg-warning-subtle text-warning border border-warning-subtle'],
    'usuarios' => ['nombre' => 'Usuarios',      'clase' => 'bg-info-subtle text-info border border-info-subtle'],
];
?>

<style>
    #tablaBitacora_wrapper {
        padding: 1rem 1.25rem;
    }

    #tablaBitacora_wrapper .dataTables_length,
    #tablaBitacora_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    #tablaBitacora_wrapper .dataTables_info {
        padding-top: 1rem;
    }

    /* Fecha y hora con números de ancho fijo (misma fuente del sistema, alineación perfecta) */
    .fecha-mono {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
        letter-spacing: 0.2px;
    }

    /* La acción es el enlace que abre el modal (solo cuando hay trabajador afectado) */
    .link-accion {
        color: #059669;
        font-weight: 700;
        text-decoration: none;
        border-bottom: 1px dashed rgba(5, 150, 105, 0.5);
        cursor: pointer;
        white-space: nowrap;
    }

    .link-accion:hover {
        color: #065f46;
        border-bottom-style: solid;
    }
</style>

<div class="container-fluid py-3">

    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-journal-text me-2 text-success"></i>Bitácora de Cambios
            </h3>
            <p class="text-secondary small mb-0">Acciones realizadas en el sistema.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tablaBitacora" class="table table-hover align-middle w-100 mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="text-uppercase small fw-bold text-dark px-4 py-3 text-center">Fecha y hora</th>
                            <th class="text-uppercase small fw-bold text-dark py-3">Categoría</th>
                            <th class="text-uppercase small fw-bold text-dark py-3">Acción</th>
                            <th class="text-uppercase small fw-bold text-dark py-3">Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $registro):
                            $cat = $categorias[$registro['categoria'] ?? ''] ?? ['nombre' => 'Otro', 'clase' => 'bg-secondary-subtle text-secondary border border-secondary-subtle'];

                            $timestamp = strtotime($registro["fecha"]);
                            $fecha_corta = date("d", $timestamp) . ' ' . strtolower(date("M", $timestamp)) . ' ' . date("y", $timestamp);

                            // ===== Separar prefijo y nombre (solo para personal afectado) =====
                            $nombre_afectado = trim(($registro['afectado_nombre'] ?? '') . ' ' . ($registro['afectado_apellido'] ?? ''));
                            $tiene_afectado = ($registro['categoria'] ?? '') === 'personal' && !empty($nombre_afectado);

                            $prefijo_accion = $registro["accion"];
                            if ($tiene_afectado) {
                                $prefijo_accion = trim(str_replace($nombre_afectado, '', $prefijo_accion));
                                $prefijo_accion = rtrim($prefijo_accion, ':');
                            }
                        ?>
                            <tr class="border-bottom">
                                <td class="px-4 text-dark fw-medium text-nowrap text-center fecha-mono">
                                    <?= $fecha_corta ?> - <?= date("h:i a", strtotime($registro["hora"])) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $cat['clase'] ?>" style="font-size: 0.75rem;"><?= $cat['nombre'] ?></span>
                                </td>
                                <td class="text-dark">
                                    <?php if ($tiene_afectado): ?>
                                        <a class="link-accion"
                                            onclick='verPersonalAfectado(<?= json_encode([
                                                $registro['afectado_nombre'],
                                                $registro['afectado_apellido'],
                                                $registro['afectado_cedula']
                                            ]) ?>)'>
                                            <?= htmlspecialchars($prefijo_accion) ?>
                                        </a>
                                        <?= htmlspecialchars($nombre_afectado) ?>
                                    <?php else: ?>
                                        <?= htmlspecialchars($prefijo_accion) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($registro["usuario"] ?? '--------') ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL: TRABAJADOR AFECTADO ===== -->
<div class="modal fade" id="modalPersonalAfectado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 rounded-4 shadow-lg text-center">
            <div class="modal-body p-4">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3"
                    style="width: 64px; height: 64px; font-size: 1.6rem;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1" id="mpa-nombre">—</h5>
                <p class="text-muted mb-3" id="mpa-cedula">—</p>
                <button type="button" class="btn btn-success fw-bold rounded-3 px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        jQuery('#tablaBitacora').DataTable({
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
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "pageLength": 15,
            "order": [[0, "desc"]],
            "dom": '<"row mb-3"<"col-12 col-md-6"l><"col-12 col-md-6"f>>rt<"row mt-3"<"col-12 col-md-5"i><"col-12 col-md-7"p>>'
        });
    });

    // ===== Modal del trabajador afectado =====
    function verPersonalAfectado(datos) {
        const [nombre, apellido, cedula] = datos;
        document.getElementById('mpa-nombre').textContent = (nombre + ' ' + (apellido || '')).trim();
        document.getElementById('mpa-cedula').textContent = cedula ? 'C.I: V-' + Number(cedula).toLocaleString('es-VE') : '';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPersonalAfectado')).show();
    }
</script>