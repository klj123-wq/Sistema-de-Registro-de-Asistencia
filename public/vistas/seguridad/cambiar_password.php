<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRA - Configurar Contraseña</title>
    <link rel="shortcut icon" href="/assets/logo_pc.png" type="image/png">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/sweetalert2.min.css">

    <style>
        :root {
            --verde-oscuro: #065f46;
            --verde-principal: #10b981;
            --verde-claro: #34d399;
            --gris-institucion: #3c4146;
            --gris-oscuro: #2b3035;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ededed;
            overflow-x: hidden;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            z-index: 1;
        }

        .tarjeta-login {
            width: 100%;
            max-width: 960px;
            min-height: 580px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.3);
            display: flex;
            overflow: hidden;
            animation: aparecer 0.6s ease;
        }

        @keyframes aparecer {
            from {
                opacity: 0;
                transform: translateY(25px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== Panel izquierdo ===== */
        .panel-bienvenida {
            flex: 0 0 48%;
            background: linear-gradient(160deg, var(--gris-institucion) 0%, var(--gris-oscuro) 100%);
            color: #ffffff;
            padding: 4.5rem 4rem 3rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            position: relative;
            overflow: visible;
            z-index: 1;
        }

        .curva-divisor {
            position: absolute;
            top: 0;
            right: -1px;
            height: 100%;
            width: 90px;
            z-index: 2;
        }

        .logo-bloque {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 0;
            position: relative;
            z-index: 4;
        }

        .logo-medalla {
            position: relative;
            width: 96px;
            height: 96px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logo-medalla::before {
            content: '';
            position: absolute;
            inset: 0;
            background: #ffffff;
            border-radius: 50%;
            box-shadow:
                0 0 0 5px rgba(52, 211, 153, 0.40),
                0 0 0 9px rgba(52, 211, 153, 0.12),
                0 10px 24px rgba(0, 0, 0, 0.35);
        }

        .logo-medalla img {
            position: relative;
            z-index: 1;
            margin-top: 10%;
            width: 72px;
            height: 72px;
            object-fit: contain;
        }

        .sistema-nombre {
            font-size: 0.8rem;
            opacity: 0.8;
            letter-spacing: 0.5px;
        }

        .descripcion {
            font-size: 0.92rem;
            line-height: 1.7;
            opacity: 0.9;
            max-width: 320px;
            margin-top: 7.5rem;
            position: relative;
            z-index: 4;
        }

        .item-info {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-top: 1.25rem;
            opacity: 0.85;
            position: relative;
            z-index: 4;
        }

        .item-info .icono-circulo {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(52, 211, 153, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .item-info h6 {
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .item-info small {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        /* ===== Panel derecho ===== */
        .panel-formulario {
            flex: 1;
            padding: 3rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
            position: relative;
        }

        .form-control-lg {
            border-radius: 10px 0 0 10px;
            font-size: 0.95rem;
            padding: 0.7rem 0.95rem;
            background: #f8f9fa;
            border-color: #e9ecef;
        }

        .input-group-text {
            background: #f8f9fa;
            border-color: #e9ecef;
            border-radius: 0 10px 10px 0;
        }

        .form-control-lg:focus {
            border-color: var(--verde-principal);
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.12);
            background: #ffffff;
        }

        .btn-toggle-ojo {
            cursor: pointer;
        }

        .btn-toggle-ojo:hover img {
            filter: invert(48%) sepia(79%) saturate(440%) hue-rotate(112deg) brightness(95%) contrast(90%);
        }

        /* Caja de requisitos: FIJA debajo del input (ya no flota ni tapa nada) */
        .caja-requisitos {
            margin-top: 0.75rem;
            padding: 0.85rem 1rem;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            font-size: 0.8rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.35rem 1rem;
        }

        .req-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .req-item img {
            flex-shrink: 0;
        }

        .req-ok {
            color: #059669;
        }

        .req-fail {
            color: #dc3545;
        }

        #password-match {
            min-height: 1.2em;
        }

        .btn-guardar {
            background: linear-gradient(135deg, var(--verde-principal), #059669);
            border: none;
            color: #fff;
            font-weight: 700;
            border-radius: 10px;
            padding: 0.8rem;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-guardar:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(16, 185, 129, 0.45);
            color: #fff;
        }

        .link-volver {
            color: #6c757d;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .link-volver:hover {
            color: #059669;
            text-decoration: underline;
        }

        .login-footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 0.72rem;
            color: #9ca3af;
        }

        /* ===== Móvil ===== */
        @media (max-width: 767px) {
            .tarjeta-login {
                flex-direction: column;
                min-height: auto;
                max-width: 440px;
            }

            .curva-divisor,
            .circulo-borde {
                display: none;
            }

            .panel-bienvenida {
                flex: none;
                padding: 2.5rem 1.8rem 3rem;
                align-items: center;
                text-align: center;
                justify-content: center;
                border-radius: 0 0 45% 45% / 0 0 8% 8%;
            }

            .logo-bloque {
                margin-bottom: 1.5rem;
                flex-direction: column;
                text-align: center;
            }

            .logo-medalla {
                width: 80px;
                height: 80px;
            }

            .logo-medalla img {
                width: 60px;
                height: 60px;
            }

            .descripcion {
                display: none;
            }

            .item-info {
                display: none;
            }

            .panel-formulario {
                padding: 2.5rem 1.8rem 2rem;
            }

            .caja-requisitos {
                grid-template-columns: 1fr;
            }

            .login-footer {
                position: static;
                margin-top: 2.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="tarjeta-login">

            <!-- ===== Panel izquierdo ===== -->
            <div class="panel-bienvenida">
                <svg class="curva-divisor" viewBox="0 0 100 600" preserveAspectRatio="none">
                    <path d="M100,0 C30,150 30,450 100,600 Z" fill="#ffffff" />
                </svg>
                <div class="circulo-borde"></div>

                <div class="logo-bloque">
                    <div class="logo-medalla">
                        <img src="/assets/logo_pc.png" alt="Logo ETC">
                    </div>
                    <div>
                        <div class="fw-bold">E.T.C.N "Pedro Curiel Ramírez"</div>
                        <div class="sistema-nombre">S.R.A. - Sistema de Registro de Asistencia</div>
                    </div>
                </div>

                <div class="descripcion">
                    <strong>Configuración de Acceso.</strong><br>
                    Establezca una nueva contraseña segura para proteger su cuenta.
                </div>

                <div class="item-info">
                    <div class="icono-circulo">
                        <img src="/bootstrap-icons/shield-lock-fill.svg" alt="" width="20" height="20">
                    </div>
                    <div>
                        <h6>Contraseña segura</h6>
                        <small>Combinación de letras, números y signos</small>
                    </div>
                </div>

                <div class="item-info">
                    <div class="icono-circulo">
                        <img src="/bootstrap-icons/check2-circle.svg" alt="" width="20" height="20">
                    </div>
                    <div>
                        <h6>Verificación en vivo</h6>
                        <small>Confirme que ambas contraseñas coincidan</small>
                    </div>
                </div>
            </div>

            <!-- ===== Panel derecho ===== -->
            <div class="panel-formulario">

                <h3 class="fw-bold mb-1">Establecer Contraseña</h3>
                <p class="text-muted small mb-4">Cree una nueva contraseña segura para su cuenta.</p>

                <form id="formNewPass" action="/recuperar_password/cambiar" method="POST" novalidate>

                    <!-- Nueva contraseña -->
                    <div class="mb-3">
                        <label for="inputPassword" class="form-label small fw-semibold text-secondary">Nueva Contraseña</label>
                        <div class="input-group">
                            <input required type="password" class="form-control form-control-lg border-end-0"
                                name="pass"
                                id="inputPassword"
                                placeholder="••••••••"
                                oninput="this.value = this.value.replace(/[^a-zA-Z0-9@#$_\-*]/g, '')"
                                maxlength="20">
                            <button class="input-group-text border-start-0 px-3 text-muted btn-toggle-ojo" type="button" id="togglePass1">
                                <img src="/bootstrap-icons/eye.svg" alt="" width="18" height="18" id="iconoOjo1">
                            </button>
                        </div>

                        <!-- Requisitos: SIEMPRE visible, debajo del input, sin tapar nada -->
                        <div class="caja-requisitos">
                            <div class="req-item req-fail" id="req-letters">
                                <img src="/bootstrap-icons/x-lg.svg" alt="" width="12" height="12" class="icono-req">
                                Mayúsculas y minúsculas
                            </div>
                            <div class="req-item req-fail" id="req-num">
                                <img src="/bootstrap-icons/x-lg.svg" alt="" width="12" height="12" class="icono-req">
                                Al menos un número
                            </div>
                            <div class="req-item req-fail" id="req-spec">
                                <img src="/bootstrap-icons/x-lg.svg" alt="" width="12" height="12" class="icono-req">
                                Signos: @ # $ _ - *
                            </div>
                            <div class="req-item req-fail" id="req-long">
                                <img src="/bootstrap-icons/x-lg.svg" alt="" width="12" height="12" class="icono-req">
                                Más de 15 caracteres
                            </div>
                        </div>
                    </div>

                    <!-- Confirmar contraseña -->
                    <div class="mb-4">
                        <label for="inputConfirmPassword" class="form-label small fw-semibold text-secondary">Confirmar Contraseña</label>
                        <div class="input-group">
                            <input required type="password" class="form-control form-control-lg border-end-0"
                                name="verify" id="inputConfirmPassword" placeholder="••••••••"
                                maxlength="20"
                                oninput="this.value = this.value.replace(/[^a-zA-Z0-9@#$_\-*]/g, '')">
                            <button class="input-group-text border-start-0 px-3 text-muted btn-toggle-ojo" type="button" id="togglePass2">
                                <img src="/bootstrap-icons/eye.svg" alt="" width="18" height="18" id="iconoOjo2">
                            </button>
                        </div>
                        <div id="password-match" class="small mt-1 fw-bold"></div>
                    </div>

                    <button type="submit" id="btnSubmit" class="btn btn-guardar w-100">
                        Guardar Contraseña
                    </button>

                    <div class="text-center mt-3">
                        <a href="/" class="link-volver">
                            <img src="/bootstrap-icons/arrow-left.svg" alt="" width="14" height="14" style="vertical-align: -2px;">
                            Volver al inicio de sesión
                        </a>
                    </div>
                </form>

                <div class="login-footer">
                    &copy; 2026 <strong>Hidalgo D.</strong> <strong>Medina D.</strong> <strong>Montilva O.</strong> <strong>Nelo E.</strong> <br>
                    Licencia <a href="https://www.gnu.org/licenses/agpl-3.0.html"
                        target="_blank"
                        rel="license noopener noreferrer">GNU AGPLv3</a> &bull;
                    <a href="https://github.com/xDaikirix/Sistema-de-Registro-de-Asistencia"
                        target="_blank"
                        rel="noopener noreferrer"
                        title="Código fuente correspondiente (AGPLv3, sección 13)">Código fuente</a>
                    <br>
                    
                </div>
            </div>

        </div>
    </div>

    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="/js/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formNewPass');
            const passInput = document.getElementById('inputPassword');
            const confirmInput = document.getElementById('inputConfirmPassword');
            const matchDiv = document.getElementById('password-match');

            const regexMayus = /[A-Z]/;
            const regexMinus = /[a-z]/;
            const regexNum = /[0-9]/;
            const regexSigno = /[@#$_\-*]/;
            const regexSoloPermitidos = /^[a-zA-Z0-9@#$_\-*]+$/;

            // ===== Toggles de ojo (con SVG locales) =====
            function crearToggle(btnId, iconId, inputId) {
                const btn = document.getElementById(btnId);
                const icono = document.getElementById(iconId);
                const input = document.getElementById(inputId);
                btn.addEventListener('click', function() {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icono.src = '/bootstrap-icons/eye-slash.svg';
                    } else {
                        input.type = 'password';
                        icono.src = '/bootstrap-icons/eye.svg';
                    }
                });
            }
            crearToggle('togglePass1', 'iconoOjo1', 'inputPassword');
            crearToggle('togglePass2', 'iconoOjo2', 'inputConfirmPassword');

            // ===== Validación en tiempo real (con íconos check/x) =====
            passInput.addEventListener('input', function() {
                const val = this.value;

                const checks = {
                    'req-letters': (regexMayus.test(val) && regexMinus.test(val)),
                    'req-num': regexNum.test(val),
                    'req-spec': regexSigno.test(val),
                    'req-long': val.length > 15
                };

                for (const id in checks) {
                    const el = document.getElementById(id);
                    const img = el.querySelector('.icono-req');
                    if (checks[id]) {
                        el.classList.replace('req-fail', 'req-ok');
                        img.src = '/bootstrap-icons/check-lg.svg';
                    } else {
                        el.classList.replace('req-ok', 'req-fail');
                        img.src = '/bootstrap-icons/x-lg.svg';
                    }
                }
                validarCoincidencia();
            });

            confirmInput.addEventListener('input', validarCoincidencia);

            function validarCoincidencia() {
                if (confirmInput.value === "") {
                    matchDiv.textContent = "";
                    return;
                }
                if (passInput.value === confirmInput.value) {
                    matchDiv.textContent = "✓ Las contraseñas coinciden";
                    matchDiv.className = "text-success small mt-1 fw-bold";
                } else {
                    matchDiv.textContent = "✕ Las contraseñas no coinciden";
                    matchDiv.className = "text-danger small mt-1 fw-bold";
                }
            }

            // ===== Envío =====
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const passValida = (regexMayus.test(passInput.value) &&
                    regexMinus.test(passInput.value) &&
                    regexNum.test(passInput.value) &&
                    regexSigno.test(passInput.value) &&
                    regexSoloPermitidos.test(passInput.value));

                const coinciden = (passInput.value === confirmInput.value && passInput.value !== "");

                if (!passValida || !coinciden) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Verifique los campos',
                        text: 'Asegúrese de cumplir con los requisitos y que las contraseñas coincidan.',
                        confirmButtonColor: '#10b981'
                    });
                    return;
                }

                const btnSubmit = document.getElementById('btnSubmit');
                const originalContent = btnSubmit.innerHTML;
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message || 'Contraseña actualizada correctamente.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = data.redirect || '/';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Ocurrió un error al guardar.'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de servidor',
                        text: 'No se pudo conectar con el servidor. Intente de nuevo.'
                    });
                } finally {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalContent;
                }
            });
        });
    </script>
</body>

</html>