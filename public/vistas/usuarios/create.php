<?php

/**
 * @var array $roles
 */

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
        <h3 class="fw-bold text-dark mb-1">Registrar Usuario</h3>
        <p class="text-muted small mb-0">Ingrese los datos para crear una cuenta de acceso al sistema.</p>
    </div>

    <div class="card shadow-sm border-0 rounded-3" style="max-width: 900px; margin: 0 auto;">
        <div class="card-body p-4 p-md-4 bg-white">
            <form id="formularioUsuario" class="needs-validation" action="/usuario" method="post" autocomplete="off" novalidate>
                <input type="hidden" name="CSRF" value="<?php echo CSRF::generar() ?>">

                <!-- ========== 1. DATOS DEL TRABAJADOR ========== -->
                <h6 class="fw-bold text-success mb-3 border-bottom pb-2">1. Datos del Trabajador</h6>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="inputCedula" class="form-label small fw-bold text-secondary mb-1">Cédula del Trabajador</label>
                        <input required type="text" class="form-control bg-light border-0" name="cedula" id="inputCedula"
                            placeholder="Ej: 12345678" maxlength="8" pattern="^[0-9]{7,8}$"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8)">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Ingrese una cédula válida (7-8 dígitos).</div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="inputNombre" class="form-label small fw-bold text-secondary mb-1">Nombre</label>
                        <input required type="text" class="form-control bg-light border-0" name="nombre" id="inputNombre"
                            placeholder="Nombre" maxlength="15" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$"
                            oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '')">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Solo letras, sin espacios ni números (máx. 15).</div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="inputApellido" class="form-label small fw-bold text-secondary mb-1">Apellido</label>
                        <input required type="text" class="form-control bg-light border-0" name="apellido" id="inputApellido"
                            placeholder="Apellido" maxlength="15" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$"
                            oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '')">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Solo letras, sin espacios ni números (máx. 15).</div>
                    </div>
                </div>

                <!-- ========== 2. ACCESO AL SISTEMA ========== -->
                <h6 class="fw-bold text-success mb-3 border-bottom pb-2">2. Acceso al Sistema</h6>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="inputEmail" class="form-label small fw-bold text-secondary mb-1">Correo Electrónico</label>
                        <input required type="email" class="form-control bg-light border-0" name="correo" id="inputEmail"
                            placeholder="usuario@gmail.com" maxlength="50"
                            pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9.@_%+-]/g, '')">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Ingrese un correo válido.</div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="inputPassword" class="form-label small fw-bold text-secondary mb-1">Contraseña</label>
                        <div class="input-group">
                            <input required type="password" class="form-control bg-light border-0" name="password" id="inputPassword"
                                placeholder="Mínimo 16 caracteres" minlength="16" maxlength="20"
                                pattern="[a-zA-Z0-9@#$_\-*]{16,20}"
                                autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary border-0" id="togglePassword" title="Mostrar / ocultar">
                                <i class="bi bi-eye" id="iconoOjo"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" style="font-size: 0.75rem;">De 16 a 20 caracteres (letras, números y @ # $ _ - *).</div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="inputRol" class="form-label small fw-bold text-secondary mb-1">Rol</label>
                        <select required name="rol" id="inputRol" class="form-select bg-light border-0">
                            <option value="" selected disabled hidden>Seleccione una opción</option>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= $rol["id"] ?>"><?= htmlspecialchars(ucfirst($rol["rol"])) ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Seleccione un rol.</div>
                    </div>
                </div>

                <hr class="text-muted opacity-10 my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="/configuraciones" class="btn btn-light border px-4 fw-semibold text-secondary rounded-3">Cancelar</a>
                    <button type="submit" id="btnSubmit" class="btn btn-success px-4 fw-bold shadow-sm rounded-3">
                        <i class="bi bi-save2 me-1"></i> Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const cedulaInput = document.getElementById('inputCedula');
        const nombreInput = document.getElementById('inputNombre');
        const apellidoInput = document.getElementById('inputApellido');

        // ==========================================
        // BÚSQUEDA DE TRABAJADOR POR CÉDULA
        // ==========================================
        cedulaInput.addEventListener('change', async function() {
            const cedula = this.value.trim();

            if (cedula === '') {
                limpiarYDesbloquearCampos();
                return;
            }

            try {
                const response = await fetch('/usuario/buscar-trabajador', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        cedula: cedula
                    })
                });

                if (response.ok) {
                    const data = await response.json();

                    if (data.existe) {
                        nombreInput.value = data.nombre;
                        apellidoInput.value = data.apellido;
                        nombreInput.readOnly = true;
                        apellidoInput.readOnly = true;
                        nombreInput.classList.add('bg-light');
                        apellidoInput.classList.add('bg-light');
                    } else {
                        limpiarYDesbloquearCampos();
                        Swal.fire({
                            icon: 'info',
                            title: 'Trabajador no encontrado',
                            text: 'No existe personal registrado con esa cédula. Verifique la cedula o Registrelo como uno nuevo',
                            confirmButtonColor: '#059669'
                        });
                    }
                }
            } catch (error) {
                console.error("Error al consultar la cédula:", error);
                limpiarYDesbloquearCampos();
            }
        });

        function limpiarYDesbloquearCampos() {
            nombreInput.value = '';
            apellidoInput.value = '';
            nombreInput.readOnly = false;
            apellidoInput.readOnly = false;
        }

        // ==========================================
        // TOGGLE MOSTRAR/OCULTAR CONTRASEÑA
        // ==========================================
        const togglePassword = document.getElementById('togglePassword');
        const inputPassword = document.getElementById('inputPassword');
        const iconoOjo = document.getElementById('iconoOjo');

        togglePassword.addEventListener('click', function() {
            if (inputPassword.type === 'password') {
                inputPassword.type = 'text';
                iconoOjo.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                inputPassword.type = 'password';
                iconoOjo.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        // ==========================================
        // ENVÍO DEL FORMULARIO (reactivado)
        // ==========================================
        const form = document.getElementById('formularioUsuario');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btnSubmit = document.getElementById('btnSubmit');

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulario Incompleto',
                    text: 'Revise los campos en rojo antes de continuar.',
                    confirmButtonColor: '#059669'
                });
                return;
            }

            const formData = new FormData(form);
            const originalBtnText = btnSubmit.innerHTML;

            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Creando...';
            btnSubmit.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                Swal.fire({
                    icon: data.icon || (data.success ? 'success' : 'error'),
                    title: data.success ? '¡Usuario Creado!' : 'Atención',
                    text: data.message,
                    showCancelButton: data.success,
                    confirmButtonText: data.success ? 'Ir a lista de usuarios' : 'Entendido',
                    cancelButtonText: 'Crear otro',
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (data.success) {
                        if (result.isConfirmed) {
                            window.location.href = '/configuraciones';
                        } else {
                            form.reset();
                            form.classList.remove('was-validated');
                            limpiarYDesbloquearCampos();
                            btnSubmit.innerHTML = originalBtnText;
                            btnSubmit.disabled = false;
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
                    text: 'No se pudo recibir una respuesta del servidor.'
                });
                btnSubmit.innerHTML = originalBtnText;
                btnSubmit.disabled = false;
            }
        });
    });
</script>