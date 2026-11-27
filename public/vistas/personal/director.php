<?php

/** @var array $data */

use libs\CSRF;
?>

<style>
    /* Botones a ancho completo en móvil */
    @media (max-width: 480px) {
        form .d-flex.justify-content-end.gap-2 {
            flex-direction: column;
        }

        form .d-flex.justify-content-end.gap-2 .btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid pt-1 pb-3">
    <div class="text-center mb-3">
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-person-workspace me-2 text-success"></i>Datos del Director
        </h3>
        <p class="text-muted small mb-0">Actualice la información del director de la institución.</p>
    </div>

    <div class="card shadow-sm border-0 rounded-3" style="max-width: 640px; margin: 0 auto;">
        <div class="card-body p-4 p-md-4 bg-white">
            <form id="formDirector" action="/personal/director" method="post" novalidate>
                <input type="hidden" name="CSRF" value="<?= CSRF::generar() ?>">

                <h6 class="fw-bold text-success mb-3 border-bottom pb-2">Información del Director</h6>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <label for="nombre" class="form-label small fw-bold text-secondary mb-1">Nombre</label>
                        <input required type="text" name="nombre" id="nombre" class="form-control bg-light border-0"
                            value="<?= htmlspecialchars($data['nombre'] ?? '') ?>" maxlength="12" minlength="2"
                            oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '').slice(0, 12)" placeholder="Ingrese el nombre">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Solo letras, entre 2 y 12 caracteres.</div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="apellido" class="form-label small fw-bold text-secondary mb-1">Apellido</label>
                        <input required type="text" name="apellido" id="apellido" class="form-control bg-light border-0"
                            value="<?= htmlspecialchars($data['apellido'] ?? '') ?>" maxlength="12" minlength="2"
                            oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '').slice(0, 12)" placeholder="Ingrese el apellido">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Solo letras, entre 2 y 12 caracteres.</div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="cedula" class="form-label small fw-bold text-secondary mb-1">Cédula</label>
                        <input required type="text" name="cedula" id="cedula" class="form-control bg-light border-0"
                            value="<?= htmlspecialchars($data['cedula'] ?? '') ?>" maxlength="8" minlength="7" pattern="[0-9]{7,8}"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8)" placeholder="Ingrese la cédula">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Solo números, entre 7 y 8 dígitos.</div>
                    </div>
                </div>

                <hr class="text-muted opacity-10 my-4">

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" id="btnCancelar" class="btn btn-light border px-4 fw-semibold text-secondary rounded-3">Cancelar</button>
                    <button type="submit" id="btnSubmit" class="btn btn-success px-4 fw-bold shadow-sm rounded-3">
                        <i class="bi bi-save2 me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('formDirector');

        // ===== Volver por donde vine (lógica propia, sin depender del layout) =====
        document.getElementById('btnCancelar').addEventListener('click', function() {

            const rutaPorDefecto = '/home'; // a dónde ir si no hay historial seguro
            let referenciaSegura = false;

            try {
                const ref = new URL(document.referrer);
                // Solo es seguro volver si: viene de este mismo sitio y NO es esta misma página
                referenciaSegura = (ref.origin === window.location.origin) &&
                    (ref.pathname !== window.location.pathname);
            } catch (e) {
                // Sin referrer válido → usar ruta por defecto
            }

            if (referenciaSegura) {
                window.history.back();
            } else {
                window.location.href = rutaPorDefecto;
            }
        });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos incompletos',
                    text: 'Revise los campos marcados en rojo.',
                    confirmButtonColor: '#059669'
                });
                return;
            }

            const btn = document.getElementById('btnSubmit');
            const original = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';
            btn.disabled = true;

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form)
                });
                const data = await res.json();
                Swal.fire({
                    icon: data.icon || (data.success ? 'success' : 'error'),
                    title: data.success ? '¡Actualizado!' : 'Atención',
                    text: data.message,
                    confirmButtonColor: data.success ? '#198754' : '#059669'
                }).then(() => {
                    if (data.success) {
                        window.location.href = '/personal/director';
                    } else {
                        btn.innerHTML = original;
                        btn.disabled = false;
                    }
                });
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor.'
                });
                btn.innerHTML = original;
                btn.disabled = false;
            }
        });
    });
</script>