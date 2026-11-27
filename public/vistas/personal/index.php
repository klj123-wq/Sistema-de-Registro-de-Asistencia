<?php

/** @var array $data
 * @var array $data_dia
 */

use libs\CSRF;
?>

<style>
    /* ===== Aire para los controles del DataTable ===== */
    #tablaPersonal_wrapper {
        padding: 1rem 1.25rem;
    }

    #tablaPersonal_wrapper .dataTables_length,
    #tablaPersonal_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    #tablaPersonal_wrapper .dataTables_info {
        padding-top: 1rem;
    }

    /* La tabla mantiene su tamaño y se desliza horizontalmente en móvil */
    #tablaPersonal {
        min-width: 640px;
    }

    /* Anti-FOUC (DataTable la inicializa el config global) */
    .contenedor-tabla {
        min-height: 400px;
        position: relative;
    }

    .contenedor-tabla::before {
        content: '';
        position: absolute;
        inset: 0;
        background: #fff;
        z-index: 5;
    }

    .contenedor-tabla.cargado::before {
        display: none;
    }

    .table-oculta-inicial {
        opacity: 0;
    }

    .table-visible {
        opacity: 1 !important;
        transition: opacity 0.4s ease;
    }

    /* =========================================================
       MODAL PERFIL — estilo del sistema (verde institucional)
       ========================================================= */
    #modalPerfil .modal-content {
        overflow: hidden;
        border-radius: 20px;
    }

    #modalPerfil .modal-content::before {
        content: '';
        display: block;
        height: 6px;
        background: linear-gradient(90deg, #065f46, #10b981, #34d399);
    }

    /* Medalla de la foto */
    .medalla-perfil {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto;
    }

    .medalla-perfil::before {
        content: '';
        position: absolute;
        inset: 0;
        background: #ffffff;
        border-radius: 50%;
        box-shadow:
            0 0 0 4px rgba(16, 185, 129, 0.40),
            0 0 0 8px rgba(16, 185, 129, 0.12),
            0 10px 24px rgba(0, 0, 0, 0.18);
    }

    .medalla-perfil img,
    .medalla-perfil .placeholder-foto {
        position: absolute;
        inset: 5px;
        width: calc(100% - 10px);
        height: calc(100% - 10px);
        border-radius: 50%;
        object-fit: cover;
    }

    .medalla-perfil .placeholder-foto {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        font-size: 4rem;
        line-height: 1;
    }

    /* Cédula: badge verde, AHORA MÁS GRANDE */
    .badge-cedula {
        background: rgba(16, 185, 129, 0.10);
        color: #065f46;
        border: 1px solid rgba(16, 185, 129, 0.30);
        font-weight: 700;
        font-size: 1rem;
        padding: 0.5rem 1.25rem;
    }

    /* Turno: badge soft verde (sin borde) */
    .badge-turno {
        background: rgba(16, 185, 129, 0.10);
        color: #059669;
        font-weight: 700;
        font-size: 1rem;
        padding: 0rem 1rem;
    }

    /* Botón editar flotante — AHORA NOTORIO (sólido verde, más grande) */
    .btn-editar-flotante {
        position: absolute;
        top: 2rem;
        left: 2rem;
        z-index: 11;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        color: #ffffff;
        font-size: 1.1rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.45);
        transition: all 0.25s ease;
        text-decoration: none;
    }

    .btn-editar-flotante:hover {
        transform: scale(1.12);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.6);
        color: #ffffff;
    }

    /* Tarjetas de estadísticas */
    .stat-box {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border-color: #ced4da !important;
        background: #ffffff;
    }

    .stat-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.15) !important;
        border-color: #10b981 !important;
    }

    /* Botones de acción — SIN animación de escala/rotación, solo cambio de color */
    .btn-unified {
        transition: background-color 0.2s ease, border-color 0.2s ease;
        border-color: #ced4da !important;
        background-color: #fff;
    }

    .btn-unified .icon-box {
        transition: background-color 0.2s ease, color 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background-color: #f0fdf4;
        color: #059669;
    }

    .btn-unified:hover {
        background-color: #f0fdf4 !important;
        border-color: #10b981 !important;
    }

    .btn-unified:hover .icon-box {
        background-color: #10b981;
        color: #ffffff;
    }

    .btn-danger-subtle .icon-box {
        background-color: #fdf2f2;
        color: #dc3545;
    }

    .btn-danger-subtle:hover {
        border-color: #f5c2c7 !important;
        background-color: #fff5f8 !important;
    }

    .btn-danger-subtle:hover .icon-box {
        background-color: #dc3545;
        color: #ffffff;
    }
</style>

<div class="container-fluid py-3">

    <div class="animate__animated animate__fadeIn d-print-none">

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h3 class="fw-bold text-dark mb-1">
                    <i class="bi bi-people-fill me-2 text-success"></i>Gestión de Personal
                </h3>
                <p class="text-secondary small mb-0">Administra el personal y genera los carnets con QR.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <a class="btn btn-success shadow-sm px-4 fw-semibold rounded-3" href="personal/registrar">
                        <i class="bi bi-person-plus-fill me-1"></i> Registrar
                    </a>
                    <a class="btn btn-outline-success shadow-sm px-4 fw-semibold rounded-3" href="reportes/todos/qr" target="_blank">
                        <i class="bi bi-qr-code-scan me-1"></i> Todos los Carnets
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive contenedor-tabla">
                    <table id="tablaPersonal" class="table table-hover align-middle w-100 mb-0 table-oculta-inicial">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="text-uppercase small fw-bold text-dark px-4 py-3" style="width: 18%;">Cédula</th>
                                <th class="text-uppercase small fw-bold text-dark py-3" style="width: 34%;">Nombre Completo</th>
                                <th class="text-uppercase small fw-bold text-dark py-3" style="width: 24%;">Cargo</th>
                                <th class="text-uppercase small fw-bold text-dark py-3 text-center" style="width: 24%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $todo_el_personal_crudo = array_merge($data ?? [], $data_dia ?? []);
                            $personal_limpio = [];
                            foreach ($todo_el_personal_crudo as $p) {
                                // Soporta ambas variantes de clave: cedula / cédula
                                $cedula = $p['cedula'] ?? null;
                                if (!empty($p['id']) && !empty($cedula) && is_numeric($cedula)) {
                                    $personal_limpio[$p['id']] = $p;
                                }
                            }
                            foreach ($personal_limpio as $persona):
                                // Soporta ambas variantes: cargo / carga
                                $cargo = ucfirst($persona['cargo'] ?? 'No definido');
                                $cedula = $persona['cedula'] ?? '';

                                // Foto: puede venir vacía, como "vacío", o con rutas raras
                                $fotoRaw = $persona['foto'] ?? '';
                                $fotoValida = (!empty($fotoRaw) && $fotoRaw !== 'vacío');
                                $foto_db = $fotoValida ? 'imagenes.php?imagen=' . urlencode(basename($fotoRaw)) : '';

                                // Turno: si no tiene id_turno (o sin nombre) → Mixto
                                $idTurno = $persona['id_turno'] ?? null;
                                $nombreTurno = $persona['nombre_turno'] ?? null;
                                $turno = (!empty($idTurno) && !empty($nombreTurno)) ? ucfirst($nombreTurno) : 'Mixto';

                                $tot_asistencias = $persona['asistencias'] ?? 0;
                                $tot_faltas = $persona['faltas'] ?? 0;
                                $tot_permisos = $persona['permisos'] ?? 0;
                                $nombreCompleto = trim(($persona['nombre'] ?? '') . ' ' . ($persona['apellido'] ?? ''));

                                $argsPerfil = json_encode([
                                    $persona['id'],
                                    $cedula,
                                    $persona['nombre'],
                                    $persona['apellido'],
                                    $cargo,
                                    $turno,
                                    $foto_db,
                                    $tot_asistencias,
                                    $tot_faltas,
                                    $tot_permisos
                                ]);
                            ?>
                                <tr class="border-bottom">
                                    <td class="px-4 text-dark fw-medium">V-<?= number_format($cedula, 0, ",", ".") ?></td>
                                    <td class="text-dark fw-semibold"><?= htmlspecialchars($nombreCompleto) ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($cargo) ?></td>
                                    <td class="text-center">
                                        <!-- Botón Perfil SOLO ícono (se entiende por el tooltip y el ícono de credencial) -->
                                        <button type="button" class="btn btn-sm btn-success rounded-3 me-1" title="Ver perfil de <?= htmlspecialchars($nombreCompleto) ?>"
                                            onclick='abrirModalPerfil(<?= $argsPerfil ?>)'>
                                            <i class="bi bi-person-badge"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3" title="Eliminar Personal"
                                            onclick='confirmarEliminarPersonal(<?= $persona['id'] ?>, <?= json_encode($nombreCompleto) ?>)'>
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

<!-- ========== MODAL: PERFIL DEL TRABAJADOR ========== -->
<div class="modal fade" id="modalPerfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 650px;">
        <div class="modal-content border-0 shadow-lg">

            <!-- Editar: botón sólido verde, notorio -->
            <a href="#" id="btn-prev-editar" class="btn-editar-flotante" title="Editar datos del trabajador">
                <i class="bi bi-pencil-fill"></i>
            </a>

            <button type="button" class="btn-close shadow-none opacity-50 position-absolute" data-bs-dismiss="modal" aria-label="Close"
                style="top: 1.25rem; right: 1.25rem; z-index: 11;"></button>

            <div class="modal-body px-4 px-md-5 pt-4 pb-4 text-center">

                <div class="medalla-perfil mb-3">
                    <div id="prev-foto-icon" class="placeholder-foto d-none"><i class="bi bi-person-fill"></i></div>
                    <img id="prev-foto-img" src="" alt="Perfil" class="d-none"
                        onerror="this.classList.add('d-none'); document.getElementById('prev-foto-icon').classList.remove('d-none');">
                </div>

                <!-- Línea 1: Nombre -->
                <h4 class="fw-bold text-dark mb-2" id="prev-nombre">Cargando...</h4>

                <!-- Línea 2: Cédula -->
                <div class="mb-2">
                    <span class="badge badge-cedula" id="prev-cedula">V-00000000</span>
                </div>

                <!-- Línea 3: Cargo + Turno -->
                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap mb-4">
                    <span class="fw-bold" style="font-size: 1.05rem; color: #059669;" id="prev-cargo">Cargando...</span>
                    <span class="text-muted"> </span>
                    <span class="fw-bold" style="color: #059669;" id="prev-turno">—</span>
                </div>

                <div class="row g-3 mb-4 justify-content-center">
                    <div class="col-4">
                        <div class="p-3 rounded-4 border stat-box">
                            <h4 class="fw-bold text-dark mb-0" id="prev-stat-asistencias">0</h4>
                            <small class="text-muted fw-bold" style="font-size: 0.7rem;">Asistencias</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-4 border stat-box">
                            <h4 class="fw-bold text-dark mb-0" id="prev-stat-faltas">0</h4>
                            <small class="text-muted fw-bold" style="font-size: 0.7rem;">Faltas</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-4 border stat-box">
                            <h4 class="fw-bold text-dark mb-0" id="prev-stat-permisos">0</h4>
                            <small class="text-muted fw-bold" style="font-size: 0.7rem;">Permisos</small>
                        </div>
                    </div>
                </div>

                <div class="row g-2 text-start">
                    <div class="col-sm-6">
                        <a href="#" id="btn-prev-asistencia" class="btn btn-white border w-100 fw-medium text-dark py-2 text-start btn-unified">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-3"><i class="bi bi-calendar2-check"></i></div><span>Asistencias</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="#" id="btn-prev-permisos" class="btn btn-white border w-100 fw-medium text-dark py-2 text-start btn-unified">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-3"><i class="bi bi-file-earmark-medical"></i></div><span>Permisos</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="#" id="btn-prev-qr" target="_blank" class="btn btn-white border w-100 fw-medium text-dark py-2 text-start btn-unified">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-3"><i class="bi bi-qr-code"></i></div><span>Imprimir QR</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <button type="button" id="btn-prev-recuperar-qr" class="btn btn-white border w-100 fw-medium text-dark py-2 text-start btn-unified btn-danger-subtle">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-3"><i class="bi bi-shield-exclamation"></i></div><span>Reportar QR</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODAL: REPORTAR QR (SEGURIDAD) ========== -->
<div class="modal fade" id="modalRecuperarQR" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-warning text-dark border-bottom-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-shield-exclamation me-2"></i>Reporte de Seguridad QR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-dark small mb-4 rounded-3 p-3 shadow-sm d-flex align-items-center">
                    <i class="bi bi-info-circle-fill text-warning fs-3 me-3"></i>
                    <div><strong class="d-block mb-1">Acción Irreversible</strong>Al reportar este incidente, el Código QR actual del trabajador quedará <b>invalidado inmediatamente</b>.</div>
                </div>
                <form id="formRecuperarQR" class="needs-validation" novalidate>
                    <input type="hidden" id="recuperar_id_empleado" name="id_personal">
                    <input type="hidden" name="CSRF" value="<?php echo CSRF::generar() ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Motivo del Reporte</label>
                        <select required name="motivo" class="form-select bg-light border-0">
                            <option value="" disabled selected hidden>Seleccione un motivo...</option>
                            <option value="extraviado">Extraviado / Perdido</option>
                            <option value="robado">Robado</option>
                            <option value="dañado">Carnet Dañado / Ilegible</option>
                            <option value="otro">Otro Motivo</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary mb-1">Observaciones</label>
                        <textarea class="form-control bg-light border-0" name="observaciones" rows="2" placeholder="Detalles adicionales..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light border fw-semibold text-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnSubmitRecuperar" class="btn btn-warning fw-bold px-4 shadow-sm text-dark rounded-pill"><i class="bi bi-arrow-repeat me-1"></i> Generar Nuevo QR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tabla = document.getElementById('tablaPersonal');
        var contenedor = document.querySelector('.contenedor-tabla');

        if (tabla && contenedor) {
            var checkTable = setInterval(function() {
                if (tabla.classList.contains('dataTable') && tabla.rows.length > 0) {
                    contenedor.classList.add('cargado');
                    tabla.classList.add('table-visible');
                    clearInterval(checkTable);
                }
            }, 100);

            setTimeout(function() {
                clearInterval(checkTable);
                contenedor.classList.add('cargado');
                tabla.classList.add('table-visible');
            }, 2000);
        }
    });

    function confirmarEliminarPersonal(id, nombre) {
        Swal.fire({
            title: '¿Eliminar personal?',
            html: `Se eliminará permanentemente a <strong>${nombre}</strong>`,
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
                form.action = `/personal/${id}/delete`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function abrirModalPerfil(args) {
        const [id, cedula, nombre, apellido, cargo, turno, fotoUrl, asistencias, faltas, permisos] = args;
        const nombreCompleto = nombre + ' ' + apellido;
        const imgElement = document.getElementById('prev-foto-img');
        const iconElement = document.getElementById('prev-foto-icon');

        if (fotoUrl && fotoUrl.trim() !== '') {
            imgElement.src = fotoUrl;
            imgElement.classList.remove('d-none');
            iconElement.classList.add('d-none');
        } else {
            iconElement.classList.remove('d-none');
            imgElement.classList.add('d-none');
        }

        document.getElementById('prev-nombre').textContent = nombreCompleto;
        document.getElementById('prev-cargo').textContent = cargo;
        document.getElementById('prev-turno').textContent = 'Turno ' + turno;
        document.getElementById('prev-cedula').textContent = 'V-' + cedula;
        document.getElementById('btn-prev-asistencia').href = '/asistencia/' + id;
        document.getElementById('btn-prev-permisos').href = '/permisos/' + id;
        document.getElementById('btn-prev-qr').href = '/reportes/' + id + '/qr';
        document.getElementById('btn-prev-editar').href = '/personal/' + id + '/edit';
        document.getElementById('btn-prev-recuperar-qr').onclick = function() {
            abrirModalRecuperarQR(id);
        };

        document.getElementById('prev-stat-asistencias').textContent = '...';
        document.getElementById('prev-stat-faltas').textContent = '...';
        document.getElementById('prev-stat-permisos').textContent = '...';

        const modalElement = document.getElementById('modalPerfil');
        const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modalInstance.show();

        fetch(`/personal/${id}/stats`).then(r => r.json()).then(data => {
            if (data.success) {
                document.getElementById('prev-stat-asistencias').textContent = data.asistencias;
                document.getElementById('prev-stat-faltas').textContent = data.faltas;
                document.getElementById('prev-stat-permisos').textContent = data.permisos;
            }
        }).catch(() => {
            document.getElementById('prev-stat-asistencias').textContent = '0';
            document.getElementById('prev-stat-faltas').textContent = '0';
            document.getElementById('prev-stat-permisos').textContent = '0';
        });
    }

    function abrirModalRecuperarQR(idPersonal) {
        const modalPerfil = bootstrap.Modal.getInstance(document.getElementById('modalPerfil'));
        if (modalPerfil) modalPerfil.hide();
        const form = document.getElementById('formRecuperarQR');
        form.reset();
        form.classList.remove('was-validated');
        document.getElementById('recuperar_id_empleado').value = idPersonal;
        new bootstrap.Modal(document.getElementById('modalRecuperarQR')).show();
    }

    document.getElementById('formRecuperarQR').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const btnSubmit = document.getElementById('btnSubmitRecuperar');
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        const formData = new FormData(form);
        const idPersonal = document.getElementById('recuperar_id_empleado').value;
        const originalText = btnSubmit.innerHTML;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Procesando...';
        btnSubmit.disabled = true;

        try {
            const response = await fetch(`/personal/${idPersonal}/qr/recuperar`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡QR Regenerado!',
                    text: data.message,
                    confirmButtonColor: '#198754'
                }).then(() => {
                    bootstrap.Modal.getInstance(document.getElementById('modalRecuperarQR')).hide();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    confirmButtonColor: '#059669'
                });
                btnSubmit.innerHTML = originalText;
                btnSubmit.disabled = false;
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo comunicar con el servidor.'
            });
            btnSubmit.innerHTML = originalText;
            btnSubmit.disabled = false;
        }
    });
</script>