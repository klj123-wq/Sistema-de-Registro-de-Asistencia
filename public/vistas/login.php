<!DOCTYPE html>
    
    
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRA - Control de Asistencia</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <style>
        :root {
            --verde-oscuro: #065f46;
            --verde-principal: #10b981;
            --verde-claro: #34d399;
            --gris-institucion: #3c4146;
            /* Gris del tema original */
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

        /* Círculos decorativos del fondo */
        .deco-fondo {
            position: fixed;
            border-radius: 50%;
            background: rgba(52, 211, 153, 0.10);
            /* Verde suave */
            pointer-events: none;
        }



        /* Contenedor centrado */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            z-index: 1;
        }

        /* Tarjeta ancha con los dos paneles */
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

        /* ===== PANEL IZQUIERDO (verde, bienvenida) ===== */
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

        /* Curva blanca divisoria (SVG) */
        .curva-divisor {
            position: absolute;
            top: 0;
            right: -1px;
            height: 100%;
            width: 90px;
            z-index: 2;
        }

        /* Círculo decorativo que cruza la curva (como en tu referencia) */


        /* Círculos decorativos internos del panel verde */
        .circulo-interno {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }



        .panel-bienvenida .logo-bloque {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 0;
            position: relative;
            z-index: 4;
        }

        /* Medalla: círculo blanco DETRÁS del logo (sin recortar la imagen) */
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
                /* Anillo verde */
                0 0 0 9px rgba(52, 211, 153, 0.12),
                /* Halo */
                0 10px 24px rgba(0, 0, 0, 0.35);
            /* Sombra */
        }

        .logo-medalla img {
            position: relative;
            margin-top: 10%;
            z-index: 1;
            width: 72px;
            height: 72px;
            object-fit: contain;
            /* El logo completo, sin cortes */
        }

        .panel-bienvenida .descripcion {
            font-size: 0.92rem;
            line-height: 1.7;
            opacity: 0.9;
            max-width: 320px;
            margin-top: 7.5rem;
            position: relative;
            z-index: 4;
        }

        .panel-bienvenida .descripcion {
            font-size: 0.92rem;
            line-height: 1.7;
            opacity: 0.9;
            max-width: 320px;
            position: relative;
            z-index: 4;
        }

        .panel-bienvenida .sistema-nombre {
            font-size: 0.8rem;
            opacity: 0.8;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 4;
        }

        /* ===== PANEL DERECHO (formulario) ===== */
        .panel-formulario {
            flex: 1;
            padding: 3rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .panel-formulario h3 {
            font-weight: 800;
            color: #1f2937;
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

        #togglePassword {
            cursor: pointer;
        }

        #togglePassword:hover img {
            filter: invert(48%) sepia(79%) saturate(440%) hue-rotate(112deg) brightness(95%) contrast(90%);
        }

        .btn-ingresar {
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

        .btn-ingresar:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(16, 185, 129, 0.45);
            color: #fff;
        }

        .btn-ingresar img {
            filter: brightness(0) invert(1);
        }

        .link-recuperar {
            color: #059669;
            font-weight: 600;
            text-decoration: none;
        }

        .link-recuperar:hover {
            text-decoration: underline;
        }

        .login-footer {
            text-align: center;
            font-size: 0.72rem;
            color: #9ca3af;
            margin-top: 2rem;
        }

        /* ===== Enlace de consulta pública (panel izquierdo) ===== */
        .btn-consulta {
            margin-top: auto;
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.7rem 1.4rem;
            border-radius: 50rem;
            border: 1.5px solid rgba(255, 255, 255, 0.45);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(4px);
            transition: all 0.3s ease;
            position: relative;
            z-index: 4;
        }

        .btn-consulta img {
            filter: brightness(0) invert(1);
        }

        .btn-consulta:hover {
            background: #10b981;
            border-color: #10b981;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            color: #ffffff;
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
                border-radius: 0 0 45% 45% / 0 0 8% 8%;
            }

            .panel-bienvenida .logo-bloque {
                margin-bottom: 1.5rem;
                flex-direction: column;
                text-align: center;
            }

            .panel-bienvenida .descripcion {
                display: none;
                /* Se oculta en móvil para compactar */
            }

            .panel-formulario {
                padding: 2.5rem 1.8rem 2rem;
            }
        }
    </style>
</head>

<body>

    <div class="deco-fondo deco-fondo-1"></div>
    <div class="deco-fondo deco-fondo-2"></div>

    <div class="login-wrapper">
        <div class="tarjeta-login">

            <!-- ===== Panel izquierdo: Bienvenida ===== -->
            <div class="panel-bienvenida">
                <div class="circulo-interno circulo-interno-1"></div>
                <div class="circulo-interno circulo-interno-2"></div>

                <!-- Curva divisoria -->
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


                <p class="descripcion">
                    <strong>Control de Asistencia.</strong><br>
                    Optimización y seguimiento del registro diario para el personal
                    Docente, Administrativo y Obrero.
                </p>

                <a href="/consulta" class="btn-consulta">
                    <img src="/bootstrap-icons/calendar2-check.svg" alt="" width="18" height="18">
                    Consultar mis Asistencias
                </a>
            </div>

            <!-- ===== Panel derecho: Formulario ===== -->
            <div class="panel-formulario">

                <h3 class="mb-1">Iniciar Sesión</h3>
                <p class="text-muted small mb-4">Ingrese sus credenciales para gestionar el sistema</p>

                <form action="/login" method="POST" autocomplete="off">

                    <!-- Correo: icono a la derecha -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary" for="inputCorreo">Correo</label>
                        <div class="input-group">
                            <input required type="email" name="usuario" id="inputCorreo"
                                class="form-control form-control-lg border-end-0"
                                placeholder="Ingrese su correo">
                            <span class="input-group-text border-start-0 px-3 text-muted">
                                <img src="/bootstrap-icons/envelope.svg" alt="Correo" width="18" height="18">
                            </span>
                        </div>
                    </div>

                    <!-- Contraseña: ojo a la derecha -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary" for="passwordField">Contraseña</label>
                        <div class="input-group">
                            <input required type="password" name="password" id="passwordField"
                                class="form-control form-control-lg border-end-0"
                                placeholder="Ingrese su contraseña">
                            <button type="button" id="togglePassword"
                                class="input-group-text border-start-0 px-3 text-muted"
                                title="Mostrar / ocultar contraseña">
                                <img src="/bootstrap-icons/eye.svg" alt="Ver contraseña" width="18" height="18" id="iconoOjo">
                            </button>
                        </div>
                        <div class="text-end mt-2">
                            <a href="/recuperar_password" class="link-recuperar small">¿Olvidó su contraseña?</a>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-ingresar w-100 mt-3" id="btnSubmit">
                        <img src="/bootstrap-icons/box-arrow-in-right.svg" alt="" width="18" height="18">
                        INGRESAR
                    </button>

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

    <script src="/js/sweetalert2.all.min.js"></script>
    <script>
        // Toggle ojo: intercambia los SVG locales
        document.getElementById('togglePassword').addEventListener('click', function() {
            const field = document.getElementById('passwordField');
            const icono = document.getElementById('iconoOjo');

            if (field.type === 'password') {
                field.type = 'text';
                icono.src = '/bootstrap-icons/eye-slash.svg';
                this.title = 'Ocultar contraseña';
            } else {
                field.type = 'password';
                icono.src = '/bootstrap-icons/eye.svg';
                this.title = 'Mostrar contraseña';
            }
        });

        // Envío del formulario (igual que antes)
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmit');
            const originalText = btn.innerHTML;

            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Ingresando...';
            btn.disabled = true;

            fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Mostramos el mensaje de éxito y redirigimos al cerrarlo (o al terminar el timer)
                        Swal.fire({
                            icon: 'success',
                            title: '¡Acceso exitoso!',
                            text: data.message || 'Bienvenido al sistema',
                            showConfirmButton: false,
                            timer: 2000, // Si no pulsa nada, redirige solo a los 2 segundos
                            timerProgressBar: false
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Acceso denegado',
                            text: data.message
                        });
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor'
                    });
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        });
    </script>
</body>

</html>