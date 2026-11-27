<?php
// vistas/perfil/index.php

/**
 * @var array $usuario  // ['nombre'=>..., 'apellido'=>..., 'cedula'=>..., 'correo'=>...]
 */

use libs\CSRF;

$inicial = strtoupper(substr($usuario["nombre"] ?? 'U', 0, 1));
?>

<style>
    /* ===== Avatar dentro de la tarjeta ===== */
    .perfil-avatar {
        position: relative;
        width: 110px;
        height: 110px;
        margin: 0 auto;
    }

    .perfil-avatar::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        box-shadow:
            0 0 0 4px rgba(52, 211, 153, 0.35),
            0 0 0 8px rgba(52, 211, 153, 0.10),
            0 10px 24px rgba(0, 0, 0, 0.20);
    }

    .perfil-avatar span {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.75rem;
        font-weight: 800;
        color: #ffffff;
        z-index: 1;
    }

    /* ===== Modo lectura: inputs con estilo plano ===== */
    .campo-perfil[readonly] {
        background-color: transparent !important;
        border: none !important;
        padding-left: 0;
        font-weight: 600;
        color: #1f2937;
        cursor: default;
    }

    .campo-perfil[readonly]:focus {
        box-shadow: none;
        outline: none;
    }

    .campo-perfil {
        transition: background-color 0.3s ease, padding 0.3s ease;
    }

    /* ===== Botón editar flotante ===== */
    .btn-editar-modo {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        color: #ffffff;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.45);
        transition: all 0.25s ease;
    }

    .btn-editar-modo:hover {
        transform: scale(1.12);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.6);
        color: #ffffff;
    }

    /* ===== Botón toggle contraseña ===== */
    .btn-toggle-password {
        background: #f0fdf4;
        border: 1px solid rgba(16, 185, 129, 0.30);
        color: #059669;
        font-weight: 600;
        transition: all 0.25s ease;
    }

    .btn-toggle-password:hover {
        background: #10b981;
        border-color: #10b981;
        color: #ffffff;
    }

    /* ===== Requisitos en vivo: colores e íconos ===== */
    #cajaRequisitos .req-ok {
        color: #059669;
    }

    #cajaRequisitos .req-fail {
        color: #dc3545;
    }

    /* ===== Caja de requisitos flotante (overlay animado) ===== */
    #cajaRequisitos {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 20;
        margin-top: 0.35rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        opacity: 0;
        transform: translateY(-8px);
        pointer-events: none;
        transition: opacity 0.25s ease, transform 0.25s ease;
    }

    #cajaRequisitos.visible {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .was-validated .form-control:invalid {
        border-color: #dc3545;
        background-image: none;
    }

    @media (max-width: 480px) {
        #formularioPerfil .d-flex.justify-content-end.gap-2 {
            flex-direction: column;
        }

        #formularioPerfil .d-flex.justify-content-end.gap-2 .btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid py-3">
    <div class="animate__animated animate__fadeIn">

        <div class="card shadow-sm border-0 rounded-3" style="max-width: 800px; margin: 0 auto;">
            <div class="card-body p-4 p-md-4 bg-white position-relative">

                <!-- ===== Botón Editar (esquina superior) ===== -->
                <button type="button" id="btnEditar" class="btn-editar-modo position-absolute" title="Editar perfil"
                    style="top: 1.25rem; right: 1.25rem; z-index: 10;">
                    <i class="bi bi-pencil-fill"></i>
                </button>

                <!-- ===== Avatar dentro de la tarjeta ===== -->
                <div class="text-center mb-4">
                    <div class="perfil-avatar mb-3">
                        <span><?= htmlspecialchars($inicial) ?></span>
                    </div>
                    <h5 class="fw-bold text-dark mb-0">Mi Perfil</h5>
                </div>

                <form id="formularioPerfil" class="needs-validation" action="/usuarios/perfil_actualizar" method="post" autocomplete="off" novalidate>
                    <input type="hidden" name="CSRF" value="<?php echo CSRF::generar() ?>">

                    <!-- ========== DATOS PERSONALES ========== -->
                    <h6 class="fw-bold text-success mb-3 border-bottom pb-2">Datos Personales</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label for="inputNombre" class="form-label small fw-bold text-secondary mb-1">Nombre</label>
                            <input type="text" class="form-control bg-light border-0 campo-perfil" name="nombre" id="inputNombre"
                                value="<?= htmlspecialchars($usuario["nombre"] ?? '') ?>"
                                placeholder="Nombre" maxlength="15" minlength="2" readonly
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '').slice(0, 15)">
                            <div class="invalid-feedback" style="font-size: 0.75rem;">Solo letras, entre 2 y 15 caracteres.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputApellido" class="form-label small fw-bold text-secondary mb-1">Apellido</label>
                            <input type="text" class="form-control bg-light border-0 campo-perfil" name="apellido" id="inputApellido"
                                value="<?= htmlspecialchars($usuario["apellido"] ?? '') ?>"
                                placeholder="Apellido" maxlength="15" minlength="2" readonly
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '').slice(0, 15)">
                            <div class="invalid-feedback" style="font-size: 0.75rem;">Solo letras, entre 2 y 15 caracteres.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputCedula" class="form-label small fw-bold text-secondary mb-1">Cédula de Identidad</label>
                            <input type="text" class="form-control bg-light border-0 campo-perfil" name="cedula" id="inputCedula"
                                value="<?= htmlspecialchars($usuario["cedula"] ?? '') ?>"
                                placeholder="12345678" maxlength="8" minlength="7" readonly
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8)">
                            <div class="invalid-feedback" style="font-size: 0.75rem;">Solo números, entre 7 y 8 dígitos.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputCorreo" class="form-label small fw-bold text-secondary mb-1">Correo Electrónico</label>
                            <input type="email" class="form-control bg-light border-0 campo-perfil" name="correo" id="inputCorreo"
                                value="<?= htmlspecialchars($usuario["correo"] ?? '') ?>"
                                placeholder="usuario@gmail.com" maxlength="50" readonly
                                oninput="this.value = this.value.replace(/[^a-zA-Z0-9.@_%+-]/g, '')">
                            <div class="invalid-feedback" style="font-size: 0.75rem;">Ingrese un correo válido.</div>
                        </div>
                    </div>

                    <!-- ========== SEGURIDAD (solo modo edición) ========== -->
                    <div id="bloqueSeguridad" style="display: none;" class="fade-in">
                        <h6 class="fw-bold text-success mb-3 border-bottom pb-2">Seguridad</h6>

                        <div class="mb-4">
                            <button type="button" id="btnTogglePassword" class="btn btn-sm btn-toggle-password rounded-pill px-3">
                                <i class="bi bi-shield-lock me-1"></i> Cambiar contraseña
                            </button>
                        </div>

                        <div id="seccionPassword" style="display: none;" class="fade-in">
                            <div class="row g-3 mb-4">
                                <!-- Contraseña Actual: ancho completo -->
                                <div class="col-12 col-md-6">
                                    <label for="inputPasswordActual" class="form-label small fw-bold text-secondary mb-1">Contraseña Actual</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control bg-light border-0" name="password_actual" id="inputPasswordActual"
                                            placeholder="Su contraseña actual" maxlength="20"
                                            autocomplete="current-password">
                                        <button type="button" class="btn btn-outline-secondary border-0 btn-toggle-eye" data-target="inputPasswordActual" title="Mostrar / ocultar">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" style="font-size: 0.75rem;">Ingrese su contraseña actual.</div>
                                </div>

                                <!-- Nueva Contraseña: 2 columnas + caja flotante -->
                                <div class="col-12 col-md-6 position-relative">
                                    <label for="inputPasswordNueva" class="form-label small fw-bold text-secondary mb-1">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control bg-light border-0" name="password_nueva" id="inputPasswordNueva"
                                            placeholder="Mínimo 16 caracteres" maxlength="20"
                                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9@#$_\-*]/g, '')"
                                            autocomplete="new-password">
                                        <button type="button" class="btn btn-outline-secondary border-0 btn-toggle-eye" data-target="inputPasswordNueva" title="Mostrar / ocultar">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>

                                    <!-- ✅ Caja de requisitos flotante -->
                                    <div id="cajaRequisitos" class="mt-2 p-3 bg-white rounded-3 border" style="display: none;">
                                        <div class="row g-1" style="font-size: 0.72rem;">
                                            <div id="req-letters" class="col-6 req-fail"><i class="bi bi-x-circle-fill me-1"></i> Mayúsculas y minúsculas</div>
                                            <div id="req-num" class="col-6 req-fail"><i class="bi bi-x-circle-fill me-1"></i> Al menos un número</div>
                                            <div id="req-spec" class="col-6 req-fail"><i class="bi bi-x-circle-fill me-1"></i> Signos: @ # $ _ - *</div>
                                            <div id="req-long" class="col-6 req-fail"><i class="bi bi-x-circle-fill me-1"></i> Más de 15 caracteres</div>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" style="font-size: 0.75rem;">La contraseña debe cumplir los 4 requisitos.</div>
                                </div>

                                <!-- Confirmar: 2 columnas -->
                                <div class="col-12 col-md-6">
                                    <label for="inputPasswordConfirm" class="form-label small fw-bold text-secondary mb-1">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control bg-light border-0" name="password_confirm" id="inputPasswordConfirm"
                                            placeholder="Repita la contraseña" maxlength="20"
                                            autocomplete="new-password">
                                        <button type="button" class="btn btn-outline-secondary border-0 btn-toggle-eye" data-target="inputPasswordConfirm" title="Mostrar / ocultar">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div id="password-match" class="mt-1" style="font-size: 0.80rem; font-weight: 600;"></div>
                                </div>
                            </div>


                        </div>
                    </div>

                    <!-- ===== Verificación de identidad (solo modo edición) ===== -->
                    <div id="bloqueVerificacion" style="display: none;" class="fade-in mb-3">
                        <div class="p-3 rounded-3 bg-light border" style="border-left: 4px solid #059669 !important;">
                            <label for="inputPasswordVerificacion" class="form-label small fw-bold text-secondary mb-1">
                                <i class="bi bi-shield-check me-1 text-success"></i>Confirme su contraseña para aplicar los cambios
                            </label>
                            <div class="input-group" style="max-width: 380px;">
                                <input type="password" class="form-control bg-white border-0" name="password_verificacion" id="inputPasswordVerificacion"
                                    placeholder="Su contraseña actual" maxlength="20"
                                    autocomplete="current-password">
                                <button type="button" class="btn btn-outline-secondary border-0 btn-toggle-eye" data-target="inputPasswordVerificacion" title="Mostrar / ocultar">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" style="font-size: 0.75rem;">Ingrese su contraseña para confirmar.</div>
                        </div>
                    </div>

                    <hr class="text-muted opacity-10 my-4">

                    <!-- ===== Botones ===== -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" id="btnCancelar" class="btn btn-light border px-4 fw-semibold text-secondary rounded-3 d-none">Cancelar</button>
                        <button type="submit" id="btnSubmit" class="btn btn-success px-4 fw-bold shadow-sm rounded-3 d-none">
                            <i class="bi bi-save2 me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('formularioPerfil');
        const btnEditar = document.getElementById('btnEditar');
        const btnSubmit = document.getElementById('btnSubmit');
        const btnCancelar = document.getElementById('btnCancelar');
        const bloqueSeguridad = document.getElementById('bloqueSeguridad');
        const bloqueVerificacion = document.getElementById('bloqueVerificacion');
        const btnTogglePassword = document.getElementById('btnTogglePassword');
        const seccionPassword = document.getElementById('seccionPassword');
        const campos = document.querySelectorAll('.campo-perfil');
        const inputVerificacion = document.getElementById('inputPasswordVerificacion');
        const camposPassword = [
            document.getElementById('inputPasswordActual'),
            document.getElementById('inputPasswordNueva'),
            document.getElementById('inputPasswordConfirm')
        ];

        // ===== Toggle mostrar/ocultar contraseñas =====
        document.querySelectorAll('.btn-toggle-eye').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.target);
                const icono = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icono.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icono.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });

        // ===== Helpers de la caja de requisitos flotante =====
        const inputNueva = document.getElementById('inputPasswordNueva');
        const inputConfirm = document.getElementById('inputPasswordConfirm');
        const matchDiv = document.getElementById('password-match');
        const cajaRequisitos = document.getElementById('cajaRequisitos');

        function mostrarCajaRequisitos() {
            cajaRequisitos.style.display = 'block';
            requestAnimationFrame(() => cajaRequisitos.classList.add('visible'));
        }

        function ocultarCajaRequisitos() {
            cajaRequisitos.classList.remove('visible');
            setTimeout(() => {
                if (!cajaRequisitos.classList.contains('visible')) {
                    cajaRequisitos.style.display = 'none';
                }
            }, 250);
        }

        function resetCajaRequisitos() {
            cajaRequisitos.classList.remove('visible');
            cajaRequisitos.style.display = 'none';
            ['req-letters', 'req-num', 'req-spec', 'req-long'].forEach(id => {
                const el = document.getElementById(id);
                el.classList.remove('req-ok');
                el.classList.add('req-fail');
                const icono = el.querySelector('i');
                icono.classList.remove('bi-check-circle-fill');
                icono.classList.add('bi-x-circle-fill');
            });
        }

        // ===== Activar modo edición =====
        btnEditar.addEventListener('click', function() {
            campos.forEach(c => {
                c.readOnly = false;
                c.required = true;
            });
            bloqueSeguridad.style.display = 'block';
            bloqueVerificacion.style.display = 'block';
            inputVerificacion.required = true;
            btnEditar.classList.add('d-none');
            btnSubmit.classList.remove('d-none');
            btnCancelar.classList.remove('d-none');
            document.getElementById('inputNombre').focus();
        });

        // ===== Cancelar edición =====
        btnCancelar.addEventListener('click', function() {
            form.reset();
            form.classList.remove('was-validated');

            campos.forEach(c => {
                c.readOnly = true;
                c.required = false;
            });

            camposPassword.forEach(c => {
                c.value = '';
                c.required = false;
                c.setCustomValidity('');
            });

            inputVerificacion.value = '';
            inputVerificacion.required = false;
            inputVerificacion.setCustomValidity('');
            bloqueVerificacion.style.display = 'none';

            matchDiv.innerHTML = '';
            resetCajaRequisitos();

            seccionPassword.style.display = 'none';
            btnTogglePassword.classList.remove('activo');
            btnTogglePassword.innerHTML = '<i class="bi bi-shield-lock me-1"></i> Cambiar contraseña';

            bloqueSeguridad.style.display = 'none';
            btnEditar.classList.remove('d-none');
            btnSubmit.classList.add('d-none');
            btnCancelar.classList.add('d-none');
        });

        // ===== Toggle sección "Cambiar contraseña" =====
        btnTogglePassword.addEventListener('click', function() {
            const visible = seccionPassword.style.display !== 'none';
            seccionPassword.style.display = visible ? 'none' : 'block';
            this.classList.toggle('activo', !visible);
            this.innerHTML = visible ?
                '<i class="bi bi-shield-lock me-1"></i> Cambiar contraseña' :
                '<i class="bi bi-x-lg me-1"></i> Cancelar cambio';
            if (visible) {
                camposPassword.forEach(c => {
                    c.value = '';
                    c.required = false;
                    c.setCustomValidity('');
                });
                matchDiv.innerHTML = '';
                ocultarCajaRequisitos();
            }
        });

        // ===== Requisitos en vivo =====
        const regexMayus = /[A-Z]/;
        const regexMinus = /[a-z]/;
        const regexNum = /[0-9]/;
        const regexSigno = /[@#$_\-*]/;
        const regexSoloPermitidos = /^[a-zA-Z0-9@#$_\-*]+$/;

        function updateReq(id, ok) {
            const el = document.getElementById(id);
            el.classList.toggle('req-ok', ok);
            el.classList.toggle('req-fail', !ok);
            const icono = el.querySelector('i');
            icono.classList.toggle('bi-check-circle-fill', ok);
            icono.classList.toggle('bi-x-circle-fill', !ok);
        }

        function passEsValida() {
            const v = inputNueva.value;
            return regexMayus.test(v) && regexMinus.test(v) && regexNum.test(v) &&
                regexSigno.test(v) && regexSoloPermitidos.test(v) && v.length > 15;
        }

        function verificarCoincidencia() {
            if (!inputConfirm.value) {
                matchDiv.innerHTML = '';
                inputConfirm.setCustomValidity('');
                return;
            }
            const coinciden = inputConfirm.value === inputNueva.value;
            matchDiv.innerHTML = coinciden ?
                '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Coinciden</span>' :
                '<span class="text-danger"><i class="bi bi-x-circle-fill"></i> No coinciden</span>';
            inputConfirm.setCustomValidity(coinciden ? '' : 'no_coinciden');
        }

        inputNueva.addEventListener('focus', mostrarCajaRequisitos);
        inputNueva.addEventListener('blur', () => {
            if (!inputNueva.value) ocultarCajaRequisitos();
        });

        inputNueva.addEventListener('input', () => {
            const v = inputNueva.value;
            updateReq('req-letters', regexMayus.test(v) && regexMinus.test(v));
            updateReq('req-num', regexNum.test(v));
            updateReq('req-spec', regexSigno.test(v));
            updateReq('req-long', v.length > 15);
            verificarCoincidencia();
        });

        inputConfirm.addEventListener('input', verificarCoincidencia);

        // ===== Envío del formulario =====
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            inputVerificacion.required = true;

            const cambiarPassword = seccionPassword.style.display !== 'none';
            camposPassword.forEach(c => c.required = cambiarPassword);

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulario Incompleto',
                    text: 'Revise los campos marcados en rojo.',
                    confirmButtonColor: '#059669'
                });
                return;
            }

            if (cambiarPassword && !passEsValida()) {
                mostrarCajaRequisitos();
                Swal.fire({
                    icon: 'warning',
                    title: 'Contraseña no válida',
                    text: 'La nueva contraseña debe cumplir los 4 requisitos indicados.',
                    confirmButtonColor: '#059669'
                });
                return;
            }

            const formData = new FormData(form);

            if (!cambiarPassword) {
                formData.delete('password_actual');
                formData.delete('password_nueva');
                formData.delete('password_confirm');
            }

            const originalBtnText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Guardando...';
            btnSubmit.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                Swal.fire({
                    icon: data.icon || (data.success ? 'success' : 'error'),
                    title: data.success ? '¡Perfil Actualizado!' : 'Atención',
                    text: data.message,
                    confirmButtonColor: data.success ? '#198754' : '#059669'
                }).then(() => {
                    if (data.success) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            // Volver a modo lectura
                            campos.forEach(c => {
                                c.readOnly = true;
                                c.required = false;
                            });

                            camposPassword.forEach(c => {
                                c.value = '';
                                c.required = false;
                                c.setCustomValidity('');
                            });

                            inputVerificacion.value = '';
                            inputVerificacion.required = false;
                            inputVerificacion.setCustomValidity('');
                            bloqueVerificacion.style.display = 'none';

                            matchDiv.innerHTML = '';
                            resetCajaRequisitos();

                            seccionPassword.style.display = 'none';
                            btnTogglePassword.classList.remove('activo');
                            btnTogglePassword.innerHTML = '<i class="bi bi-shield-lock me-1"></i> Cambiar contraseña';

                            bloqueSeguridad.style.display = 'none';
                            btnEditar.classList.remove('d-none');
                            btnSubmit.classList.add('d-none');
                            btnCancelar.classList.add('d-none');
                            form.classList.remove('was-validated');
                        }
                    } else {
                        btnSubmit.innerHTML = originalBtnText;
                        btnSubmit.disabled = false;
                    }
                });
            } catch (error) {
                console.error("Error Fetch:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Red',
                    text: 'No se pudo comunicar con el servidor.',
                    confirmButtonColor: '#059669'
                });
                btnSubmit.innerHTML = originalBtnText;
                btnSubmit.disabled = false;
            }
        });
    });
</script>