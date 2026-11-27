
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRA - Recuperar Contraseña</title>
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
            from { opacity: 0; transform: translateY(25px); }
            to   { opacity: 1; transform: translateY(0); }
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

        /* Medalla del logo (círculo blanco detrás, sin recortar) */
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

        /* Items informativos (panel izquierdo) */
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
            margin-top: 2rem;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .badge-seguridad {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
            font-weight: 600;
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

        .btn-recuperar {
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

        .btn-recuperar:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(16, 185, 129, 0.45);
            color: #fff;
        }

        .btn-recuperar img {
            filter: brightness(0) invert(1);
        }

        .link-volver {
            color: #059669;
            font-weight: 600;
            text-decoration: none;
        }

        .link-volver:hover {
            text-decoration: underline;
        }

        .login-footer {
            text-align: center;
            font-size: 0.72rem;
            color: #9ca3af;
            margin-top: 3rem;
        }

        /* ===== Móvil ===== */
        @media (max-width: 767px) {
            .tarjeta-login {
                flex-direction: column;
                min-height: auto;
                max-width: 440px;
            }

            .curva-divisor,
            .circulo-borde { display: none; }

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

            .logo-medalla { width: 80px; height: 80px; }
            .logo-medalla img { width: 60px; height: 60px; }

            .descripcion { display: none; }
            .item-info { display: none; }

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

            <!-- ===== Panel izquierdo ===== -->
            <div class="panel-bienvenida">
                <div class="circulo-interno circulo-interno-1"></div>
                <div class="circulo-interno circulo-interno-2"></div>

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
                    <strong>Recuperación de Cuenta.</strong><br>
                    Restablezca su contraseña de forma segura a través de su correo electrónico registrado.
                </div>

                

                <div class="item-info">
                    <div class="icono-circulo">
                        <img src="/bootstrap-icons/envelope-check-fill.svg" alt="" width="20" height="20">
                    </div>
                    <div>
                        <h6>Verificación por Correo</h6>
                        <small>Instrucciones en su bandeja</small>
                    </div>
                </div>
            </div>

            <!-- ===== Panel derecho ===== -->
            <div class="panel-formulario">

                
                <h3 class="fw-bold mb-1">¿Olvidó su contraseña?</h3>
                <p class="text-muted small mb-4">
                    Ingrese el correo asociado a su cuenta y le enviaremos las instrucciones de restablecimiento.
                </p>

                <form id="formRecovery" method="POST" action="/recuperar_password/verificar">
                    

                    <!-- Correo: icono a la derecha -->
                    <div class="mb-4">
                        <label for="correo" class="form-label small fw-semibold text-secondary">Correo Electrónico</label>
                        <div class="input-group">
                            <input
                                type="email"
                                class="form-control form-control-lg border-end-0"
                                id="correo"
                                name="correo"
                                placeholder="usuario@gmail.com"
                                autocomplete="off"
                                required>
                            <span class="input-group-text border-start-0 px-3 text-muted">
                                <img src="/bootstrap-icons/envelope.svg" alt="Correo" width="18" height="18">
                            </span>
                        </div>
                    </div>

                    <button type="submit" id="btnSubmit" class="btn btn-recuperar w-100 mt-2">
                        <img src="/bootstrap-icons/send-fill.svg" alt="" width="18" height="18">
                        Enviar Código
                    </button>

                    <div class="text-center mt-4">
                        <a href="/" class="link-volver small">
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
            const form = document.getElementById('formRecovery');
            const btnSubmit = document.getElementById('btnSubmit');
            const inputEmail = document.getElementById('correo');

            if (window.innerWidth > 991) {
                inputEmail.focus();
            }

            // Filtro de caracteres no válidos en correos
            inputEmail.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-Z0-9@._%+-]/g, '');
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const emailValue = inputEmail.value.trim();
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                if (!emailRegex.test(emailValue)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Correo no válido',
                        text: 'Por favor, introduce una dirección de correo electrónico con formato correcto (ejemplo@dominio.com).',
                        confirmButtonColor: '#10b981'
                    });
                    return;
                }

                const formData = new FormData(form);
                const originalBtnText = btnSubmit.innerHTML;

                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Enviando...';
                btnSubmit.disabled = true;

                fetch(form.action, {
    method: 'POST',
    body: formData,
    credentials: 'same-origin'
})
.then(async (response) => {
    const rawText = await response.text();
    try {
        return JSON.parse(rawText);
    } catch (err) {
        if (rawText.includes('__test')) {
            const match = rawText.match(/location\.href="([^"]+)"/);
            if (match && match[1]) {
                window.location.href = match[1];
            } else {
                window.location.reload();
            }
            return null;
        }
        throw err;
    }
})
.then(data => {
    if (!data) return;

    if (data.success) {
        Swal.fire({
            icon: 'success',
            title: 'Correo Enviado',
            text: data.message || 'Hemos enviado un correo con instrucciones para restablecer tu contraseña. Revisa tu bandeja de entrada.',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Entendido'
        }).then(() => {
            window.location.href = '/recuperar_password/verificar_correo';
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'No se pudo procesar',
            text: data.message || 'El correo ingresado no se encuentra registrado en nuestro sistema.',
            confirmButtonColor: '#0d6efd'
        });
        btnSubmit.innerHTML = originalBtnText;
        btnSubmit.disabled = false;
    }
})
.catch(error => {
    console.error("Error:", error);
    Swal.fire({
        icon: 'error',
        title: 'Error de servidor',
        text: 'Hubo un problema al contactar con el sistema. Intente más tarde.'
    });
    btnSubmit.innerHTML = originalBtnText;
    btnSubmit.disabled = false;
});
            });
        });
    </script>
</body>

</html>