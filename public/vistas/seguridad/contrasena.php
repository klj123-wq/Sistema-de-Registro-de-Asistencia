<?php

use libs\CSRF;
?>

<style>
    /* Requisitos en vivo: íconos y colores */
    #cajaRequisitos .req-ok {
        color: #059669;
    }

    #cajaRequisitos .req-fail {
        color: #dc3545;
    }
</style>

<div class="container-fluid pt-1 pb-3">
    <div class="text-center mb-3">
        <h3 class="fw-bold text-dark mb-1">Cambiar Contraseña</h3>
        <p class="text-muted small mb-0">Por seguridad, ingrese su contraseña actual antes de crear la nueva.</p>
    </div>

    <div class="card shadow-sm border-0 rounded-3" style="max-width: 460px; margin: 0 auto;">
        <div class="card-body p-4 p-md-4 bg-white">
            <form id="form-paso2" action="/usuario/cambiar_password_perfil" method="POST" novalidate>
                <input type="hidden" name="CSRF" value="<?php echo \libs\CSRF::generar() ?>">

                <!-- Contraseña actual -->
                <div class="mb-3">
                    <label for="inputAnterior" class="form-label small fw-bold text-secondary mb-1">Contraseña Actual</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0 px-3"><i class="bi bi-shield-lock"></i></span>
                        <input required type="password" class="form-control bg-light border-start-0 border-end-0" name="anterior" id="inputAnterior"
                            placeholder="Ingrese su contraseña actual" maxlength="20" autocomplete="current-password">
                        <button type="button" class="btn btn-outline-secondary bg-light border-start-0 toggle-password" tabindex="-1"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <hr class="text-muted opacity-10 my-4">

                <!-- Nueva contraseña (mismas reglas que la recuperación desde el login) -->
                <div class="mb-3">
                    <label for="inputPassword" class="form-label small fw-bold text-secondary mb-1">Nueva Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0 px-3"><i class="bi bi-key"></i></span>
                        <input required type="password" class="form-control bg-light border-start-0 border-end-0" name="pass" id="inputPassword"
                            placeholder="Más de 15 caracteres" maxlength="20"
                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9@#$_\-*]/g, '')"
                            autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary bg-light border-start-0 toggle-password" tabindex="-1"><i class="bi bi-eye"></i></button>
                    </div>

                    <!-- Los MISMOS 5 requisitos que en la recuperación -->
                    <div id="cajaRequisitos" class="mt-2 p-3 bg-light rounded-3 border" style="display: none;">
                        <div class="row g-1" style="font-size: 0.72rem;">
                            <div id="req-letters" class="col-6 req-fail"><i class="bi bi-x-circle-fill me-1"></i> Mayúsculas y minúsculas</div>
                            <div id="req-num" class="col-6 req-fail"><i class="bi bi-x-circle-fill me-1"></i> Al menos un número</div>
                            <div id="req-spec" class="col-6 req-fail"><i class="bi bi-x-circle-fill me-1"></i> Signos: @ # $ _ - *</div>
                            <div id="req-long" class="col-6 req-fail"><i class="bi bi-x-circle-fill me-1"></i> Más de 15 caracteres</div>
                        </div>
                    </div>
                </div>

                <!-- Confirmar -->
                <div class="mb-4">
                    <label for="inputConfirmPassword" class="form-label small fw-bold text-secondary mb-1">Confirmar Nueva Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0 px-3"><i class="bi bi-check2-all"></i></span>
                        <input required type="password" class="form-control bg-light border-start-0 border-end-0" name="verify" id="inputConfirmPassword"
                            placeholder="Repita la contraseña" maxlength="20" autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary bg-light border-start-0 toggle-password" tabindex="-1"><i class="bi bi-eye"></i></button>
                    </div>
                    <div id="password-match" class="mt-1" style="font-size: 0.80rem; font-weight: 600;"></div>
                </div>

                <button type="submit" id="btnPaso2" class="btn btn-success w-100 py-2 fw-semibold rounded-3">
                    <i class="bi bi-save me-2"></i> Guardar Nueva Contraseña
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="/home" class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Cancelar
                </a>
            </div>
        </div>
    </div>
</div>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Toggle mostrar/ocultar contraseña
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const icon = this.querySelector('i');
                input.type = input.type === "password" ? "text" : "password";
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        });

        const passInput = document.getElementById('inputPassword');
        const confirmInput = document.getElementById('inputConfirmPassword');
        const matchDiv = document.getElementById('password-match');
        const cajaRequisitos = document.getElementById('cajaRequisitos');

        // ===== Mismos regex que la recuperación =====
        const regexMayus = /[A-Z]/;
        const regexMinus = /[a-z]/;
        const regexNum = /[0-9]/;
        const regexSigno = /[@#$_\-*]/;
        const regexSoloPermitidos = /^[a-zA-Z0-9@#$_\-*]+$/;



        // La caja aparece al escribir en "Nueva Contraseña" y se ocurre al salir si está vacía
        passInput.addEventListener('focus', () => cajaRequisitos.style.display = 'block');
        passInput.addEventListener('blur', () => {
            if (!passInput.value) cajaRequisitos.style.display = 'none';
        });

        passInput.addEventListener('input', () => {
            const v = passInput.value;

            updateReq('req-letters', regexMayus.test(v) && regexMinus.test(v));
            updateReq('req-num', regexNum.test(v));
            updateReq('req-spec', regexSigno.test(v));
            updateReq('req-long', v.length > 15);

            verificarCoincidencia();
        });
        confirmInput.addEventListener('input', verificarCoincidencia);

        function updateReq(id, ok) {
            const el = document.getElementById(id);
            el.classList.toggle('req-ok', ok);
            el.classList.toggle('req-fail', !ok);
            const icono = el.querySelector('i');
            icono.classList.toggle('bi-check-circle-fill', ok);
            icono.classList.toggle('bi-x-circle-fill', !ok);
        }

        function verificarCoincidencia() {
            if (!confirmInput.value) {
                matchDiv.innerHTML = '';
                return;
            }
            matchDiv.innerHTML = confirmInput.value === passInput.value ?
                '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Coinciden</span>' :
                '<span class="text-danger"><i class="bi bi-x-circle-fill"></i> No coinciden</span>';
        }

        function passEsValida() {
            const v = passInput.value;
            return regexMayus.test(v) && regexMinus.test(v) && regexNum.test(v) &&
                regexSigno.test(v) && regexSoloPermitidos.test(v) && v.length > 15;
        }

        // Envío
        document.getElementById('form-paso2').addEventListener('submit', function(e) {
            e.preventDefault();
            const anterior = document.getElementById('inputAnterior').value;

            if (!anterior) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Falta contraseña actual',
                    text: 'Ingrese su contraseña actual para continuar.',
                    confirmButtonColor: '#059669'
                });
                return;
            }
            if (!passEsValida()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Contraseña no válida',
                    text: 'La nueva contraseña debe cumplir los 5 requisitos.',
                    confirmButtonColor: '#059669'
                });
                return;
            }
            if (passInput.value !== confirmInput.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No coinciden',
                    text: 'Las contraseñas nuevas no coinciden.',
                    confirmButtonColor: '#059669'
                });
                return;
            }

            const btn = document.getElementById('btnPaso2');
            const original = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';
            btn.disabled = true;

            fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(r => r.json())
                .then(data => {
                    Swal.fire({
                        icon: data.icon || (data.success ? 'success' : 'error'),
                        title: data.success ? '¡Contraseña Actualizada!' : 'Atención',
                        text: data.message,
                        confirmButtonColor: data.success ? '#198754' : '#059669'
                    }).then(() => {
                        if (data.success) {
                            window.location.href = '/home';
                        } else {
                            btn.innerHTML = original;
                            btn.disabled = false;
                        }
                    });
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor.'
                    });
                    btn.innerHTML = original;
                    btn.disabled = false;
                });
        });
    });
</script>