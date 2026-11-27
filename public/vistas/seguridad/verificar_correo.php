<?php
// 1. Calculamos los segundos restantes basándonos en el timestamp del servidor
$timestamp_expiracion = isset($_SESSION["token_tiempo"]) ? (int)$_SESSION["token_tiempo"] : 0;
$segundos_restantes = max(0, $timestamp_expiracion - time());
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRA - Verificar Código</title>
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

        /* Input del código */
        .input-code {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 8px;
            text-align: center;
            padding: 0.8rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .input-code:focus {
            border-color: var(--verde-principal);
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.12);
            outline: none;
            background-color: #ffffff;
        }

        .input-code::placeholder {
            letter-spacing: 2px;
            font-size: 1.2rem;
            font-weight: normal;
            color: #cbd5e1;
        }

        .input-code:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-validar {
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

        .btn-validar:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(16, 185, 129, 0.45);
            color: #fff;
        }

        .btn-validar img {
            filter: brightness(0) invert(1);
        }

        .btn-reenviar {
            color: #059669;
            font-weight: 600;
            text-decoration: none;
            background: none;
            border: none;
            font-size: 0.85rem;
        }

        .btn-reenviar:hover:not(:disabled) {
            text-decoration: underline;
        }

        .btn-reenviar:disabled {
            color: #9ca3af;
            cursor: not-allowed;
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
                    <strong>Verificación de Seguridad.</strong><br>
                    Ingrese el código de 6 dígitos enviado a su correo para continuar con el restablecimiento.
                </div>

                <div class="item-info">
                    <div class="icono-circulo">
                        <img src="/bootstrap-icons/patch-check-fill.svg" alt="" width="20" height="20">
                    </div>
                    <div>
                        <h6>Código de un solo uso</h6>
                        <small>Válido por tiempo limitado</small>
                    </div>
                </div>

                <div class="item-info">
                    <div class="icono-circulo">
                        <img src="/bootstrap-icons/shield-lock-fill.svg" alt="" width="20" height="20">
                    </div>
                    <div>
                        <h6>Conexión encriptada</h6>
                        <small>Tus datos viajan seguros</small>
                    </div>
                </div>
            </div>

            <!-- ===== Panel derecho ===== -->
            <div class="panel-formulario">

                <h3 class="fw-bold mb-1 text-center">Verifique su correo</h3>
                <p class="text-muted small mb-4 text-center">
                    Hemos enviado un código de verificación de 6 dígitos a su correo electrónico.
                </p>

                <form id="formVerifyCode" method="POST" action="/recuperar_password/verificar_codigo">


                    <div class="mb-4">
                        <label for="codigo" class="form-label small fw-semibold text-secondary d-block text-center">Código de Verificación</label>
                        <div class="d-flex justify-content-center">
                            <input
                                type="text"
                                class="form-control input-code"
                                style="max-width: 320px;"
                                id="codigo"
                                name="codigo"
                                placeholder="000000"
                                maxlength="6"
                                autocomplete="off"
                                required>
                        </div>

                        <div class="form-text text-center mt-3" style="font-size: 0.95rem;">
                            Tiempo restante: <span id="contador-tiempo" class="fw-bold" style="color:#059669;">--:--</span>
                        </div>
                    </div>

                    <div class="d-flex flex-column align-items-center gap-3 mt-2">
                        <button type="submit" id="btnSubmit" class="btn btn-validar w-100" style="max-width: 280px;">
                            <img src="/bootstrap-icons/shield-check.svg" alt="" width="18" height="18">
                            Validar Código
                        </button>

                        <button type="button" id="btnResend" class="btn-reenviar" disabled>
                            <img src="/bootstrap-icons/arrow-clockwise.svg" alt="" width="14" height="14" style="vertical-align: -2px;">
                            Volver a solicitar código
                        </button>

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
            const form = document.getElementById('formVerifyCode');
            const btnSubmit = document.getElementById('btnSubmit');
            const inputCodigo = document.getElementById('codigo');
            const btnResend = document.getElementById('btnResend');
            const spanContador = document.getElementById('contador-tiempo');

            let segundosIniciales = <?php echo $segundos_restantes; ?>;
            const fechaExpiracion = Date.now() + (segundosIniciales * 1000);
            let intervaloContador;

            if (window.innerWidth > 991 && segundosIniciales > 0) {
                inputCodigo.focus();
            }

            function actualizarContador() {
                const ahora = Date.now();
                let tiempoRestante = Math.max(0, Math.floor((fechaExpiracion - ahora) / 1000));

                if (tiempoRestante <= 0) {
                    clearInterval(intervaloContador);
                    spanContador.innerHTML = '00:00 (Expirado)';
                    spanContador.style.color = '#dc3545';

                    inputCodigo.disabled = true;
                    btnSubmit.disabled = true;
                    btnResend.disabled = false;
                    return;
                }

                let minutos = Math.floor(tiempoRestante / 60);
                let segundos = tiempoRestante % 60;
                minutos = minutos < 10 ? '0' + minutos : minutos;
                segundos = segundos < 10 ? '0' + segundos : segundos;

                spanContador.innerHTML = minutos + ':' + segundos;

                if (tiempoRestante <= 60) {
                    spanContador.style.color = '#ffc107'; // Amarillo de alerta
                }
            }

            actualizarContador();
            if (segundosIniciales > 0) {
                intervaloContador = setInterval(actualizarContador, 1000);
            }

            btnResend.addEventListener('click', function() {
                btnResend.disabled = true;
                btnResend.innerText = "Enviando...";

                fetch('/reenviar_correo', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Código reenviado',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                            btnResend.disabled = false;
                            btnResend.innerText = "Volver a solicitar código";
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                        btnResend.disabled = false;
                        btnResend.innerText = "Volver a solicitar código";
                    });
            });

            inputCodigo.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const ahora = Date.now();
                let tiempoRestante = Math.floor((fechaExpiracion - ahora) / 1000);
                if (tiempoRestante <= 0) return;

                const codigoVal = inputCodigo.value.trim();
                if (codigoVal.length !== 6) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Código incompleto',
                        text: 'El código debe tener 6 caracteres.',
                        confirmButtonColor: '#10b981'
                    });
                    return;
                }

                const formData = new FormData(form);
                const originalBtnText = btnSubmit.innerHTML;

                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Validando...';
                btnSubmit.disabled = true;

                fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            clearInterval(intervaloContador);
                            Swal.fire({
                                icon: 'success',
                                title: 'Código verificado',
                                text: 'Redirigiendo...',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = '/recuperar_password/cambiar';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Código Inválido',
                                text: data.message
                            });
                            btnSubmit.innerHTML = originalBtnText;
                            btnSubmit.disabled = false;
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión.'
                        });
                        btnSubmit.innerHTML = originalBtnText;
                        btnSubmit.disabled = false;
                    });
            });
        });
    </script>
</body>

</html>