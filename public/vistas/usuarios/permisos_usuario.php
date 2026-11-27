<?php

/**
 * @var array $roles     // Filas: [['id'=>2,'rol'=>'administrador','usuarios'=>0,'director'=>0,'escanear'=>0,'general'=>0,'respaldo'=>0], ...]
 * @var array $modulos   // Columnas-permiso del controlador
 */

// El escáner es funcionalidad UNIVERSAL (todos los roles logueados escanean).
// Lo excluimos de la gestión: no es un permiso, no se muestra ni se cuenta.
$modulos = array_values(array_diff($modulos ?? [], ['escanear']));

// Mapa: columna de la BD => [etiqueta legible, ícono, tooltip]
$mapa_modulos = [
    'usuarios'  => ['Usuarios',  'bi-people-fill',    'Gestión de usuarios y roles'],
    'general'   => ['General',   'bi-grid-fill',      'Operación diaria: personal, asistencias, permisos, turnos'],
    'director'  => ['Director',  'bi-briefcase-fill', 'Datos y firma del director en reportes'],
    'respaldo'  => ['Respaldo',  'bi-database-fill',  'Respaldo de información'],
];

// Fallback para columnas sin definir en el mapa
foreach ($modulos as $modulo) {
    if (!isset($mapa_modulos[$modulo])) {
        $mapa_modulos[$modulo] = [ucfirst($modulo), 'bi-shield-fill', 'Módulo del sistema'];
    }
}

// Roles protegidos de edición (red de seguridad por si superadmin vuelve a pasarse)
$roles_protegidos = ['superadmin'];
?>

<style>
    /* ===== Tarjetas de rol ===== */
    .tarjeta-rol {
        background: #ffffff;
        border: 1px solid #eef0f2;
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }
    .tarjeta-rol:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.10);
    }

    .tarjeta-rol .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f3f4f6;
        border-radius: 16px 16px 0 0 !important;
    }

    .contador-modulos {
        background: rgba(16, 185, 129, 0.10);
        color: #059669;
        font-weight: 700;
        font-size: 0.75rem;
        border-radius: 50rem;
        padding: 0.25rem 0.75rem;
    }

    .fila-modulo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.7rem 0.25rem;
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.2s ease;
    }
    .fila-modulo:last-child { border-bottom: none; }
    .fila-modulo:hover { background-color: #f8fafc; border-radius: 8px; }

    .icono-modulo {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(16, 185, 129, 0.10);
        color: #059669;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }
    .form-check-input:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    .tarjeta-protegida {
        border-color: #fde68a !important;
        background: #fffbeb;
    }
</style>

<div class="container-fluid py-3">
    <div class="animate__animated animate__fadeIn">

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h3 class="fw-bold text-dark mb-1">
                    <i class="bi bi-shield-lock-fill me-2 text-success"></i>Permisos por Rol
                </h3>
                <p class="text-secondary small mb-0">Activa o desactiva el acceso a cada módulo. Los cambios se guardan automáticamente.</p>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
            <?php foreach ($roles as $rol):
                $esProtegido = in_array(strtolower($rol['rol']), $roles_protegidos);
                $totalModulos = count($modulos);
                $activos = 0;
                foreach ($modulos as $modulo) {
                    if (!empty($rol[$modulo])) $activos++;
                }
            ?>
                <div class="col">
                    <div class="card tarjeta-rol <?= $esProtegido ? 'tarjeta-protegida' : '' ?> h-100">
                        <div class="card-header d-flex justify-content-between align-items-center py-3">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-person-badge me-1 <?= $esProtegido ? 'text-warning' : 'text-muted' ?>"></i>
                                <?= htmlspecialchars($rol['rol']) ?>
                                <?php if ($esProtegido): ?>
                                    <i class="bi bi-lock-fill text-warning ms-1" title="Rol protegido (solo lectura)"></i>
                                <?php endif; ?>
                            </h6>
                            <span class="contador-modulos" id="contador-<?= $rol['id'] ?>"><?= $activos ?>/<?= $totalModulos ?></span>
                        </div>

                        <div class="card-body p-3">
                            <?php foreach ($modulos as $modulo):
                                [$etiqueta, $icono, $tooltip] = $mapa_modulos[$modulo];
                                $activo = !empty($rol[$modulo]);
                            ?>
                                <div class="fila-modulo">
                                    <div class="icono-modulo" title="<?= htmlspecialchars($tooltip) ?>">
                                        <i class="bi <?= $icono ?>"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($etiqueta) ?></div>
                                        <small class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars($tooltip) ?></small>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input switch-permiso"
                                            type="checkbox"
                                            style="width: 2.6em; height: 1.4em; cursor: pointer;"
                                            data-rol="<?= $rol['id'] ?>"
                                            data-campo="<?= htmlspecialchars($modulo) ?>"
                                            <?= $activo ? 'checked' : '' ?>
                                            <?= $esProtegido ? 'disabled' : '' ?>>
                                    </div>
                                </div>
                            <?php endforeach ?>

                            <?php if ($esProtegido): ?>
                                <p class="text-muted small text-center mt-3 mb-0" style="font-size: 0.72rem;">
                                    <i class="bi bi-info-circle me-1"></i>Rol protegido: sus permisos no se pueden modificar.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

    </div>
</div>

<input type="hidden" id="token_secreto" value="<?= \libs\CSRF::generar() ?>">

<script src="/js/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const token = document.getElementById('token_secreto').value;

    document.querySelector('.row').addEventListener('change', function(e) {

        const sw = e.target;
        if (!sw.classList.contains('switch-permiso') || sw.disabled) return;

        const rolId  = sw.dataset.rol;
        const campo  = sw.dataset.campo;
        const activo = sw.checked ? 1 : 0;

        sw.disabled = true;

        const datos = new FormData();
        datos.append('CSRF', token);
        datos.append('campo', campo);
        datos.append('activo', activo);

        fetch(`/roles/${rolId}/permisos/modificar`, {
            method: 'POST',
            body: datos
        })
        .then(r => r.json())
        .then(data => {
            sw.disabled = false;
            if (data.success) {
                actualizarContador(rolId);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message || 'Permiso actualizado',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
            } else {
                sw.checked = !sw.checked;
                actualizarContador(rolId);
                Swal.fire({
                    icon: 'error',
                    title: 'Atención',
                    text: data.message || 'No se pudo actualizar el permiso',
                    confirmButtonColor: '#059669'
                });
            }
        })
        .catch(() => {
            sw.disabled = false;
            sw.checked = !sw.checked;
            actualizarContador(rolId);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo comunicar con el servidor.',
                confirmButtonColor: '#d33'
            });
        });
    });

    function actualizarContador(rolId) {
        const card = document.getElementById('contador-' + rolId);
        if (!card) return;
        const switches = card.closest('.card').querySelectorAll('.switch-permiso');
        let activos = 0;
        switches.forEach(s => { if (s.checked) activos++; });
        card.textContent = activos + '/' + switches.length;
    }
});
</script>