<?php
if (!isset($_SESSION["usuario"]) && empty($_SESSION["usuario"])) {
    http_response_code(401);

    $ruta_vista = $_SERVER['DOCUMENT_ROOT'] . '/vistas/error/401.php';

    if (file_exists($ruta_vista)) {
        require_once $ruta_vista;
    } else {
        echo "<h1 style='text-align:center; margin-top:50px; font-family:sans-serif;'>401 - P&aacute;gina no encontrada</h1>";
        echo "<p style='text-align:center; color: red;'>No tienes permiso para acceder a este apartado</b></p>";
    }
    die;
}

use libs\authenticator\GoogleAuthenticator;

$ga = new GoogleAuthenticator;
$secret = $ga->generateSecret();
$usuario_encoded = rawurlencode($_SESSION["usuario"][0]["nombre"] ?? 'Usuario');
$nombre_sistema_encoded = rawurlencode("Spedro");
$otpauth_url = "otpauth://totp/{$nombre_sistema_encoded}:{$usuario_encoded}?secret={$secret}&issuer={$nombre_sistema_encoded}";

ob_start();
QRcode::png($otpauth_url, null, QR_ECLEVEL_L, 4, 2);

$qr_uncodified = ob_get_clean();
$qr = base64_encode($qr_uncodified);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRA - Vincular Autenticador</title>
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

        /* Pasos (items informativos) */
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
            padding: 2.5rem 3.5rem;
            /* era 3rem → recorta 8px arriba y abajo */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            gap: 0.9rem;
            /* aire uniforme; reemplaza los mb-* sueltos */
            background: #ffffff;
            position: relative;
        }

        /* Caja del QR */
        .qr-box {
            background-color: #ffffff;
            padding: 0.75rem;
            /* era 1rem → recorta 8px total */
            border-radius: 16px;
            border: 2px dashed #dee2e6;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: border-color 0.3s ease, transform 0.3s ease;
        }

        .qr-box:hover {
            border-color: var(--verde-principal);
            transform: translateY(-2px);
        }

        /* ===== Clave manual (compacta) ===== */
        .clave-manual-texto {
            font-family: monospace;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: var(--verde-oscuro);
            user-select: all;
        }

        /* ===== Botón copiar ===== */
        .btn-copiar {
            background: #ffffff;
            border: 1.5px solid var(--verde-principal);
            border-radius: 8px;
            padding: 0.3rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--verde-principal);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .btn-copiar img {
            filter: none;
        }

        .btn-copiar:hover {
            background: var(--verde-principal);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }

        .btn-copiar:hover img {
            filter: brightness(0) invert(1);
        }

        .btn-copiar.copiado {
            background: #059669;
            border-color: #059669;
            color: #ffffff;
        }

        .btn-copiar.copiado img {
            filter: brightness(0) invert(1);
        }

        .qr-image {
            width: 150px;
            height: 150px;
            object-fit: contain;
        }

        /* Input del código */
        .input-code {
            font-size: 2.2rem;
            letter-spacing: 0.8rem;
            text-align: center;
            font-weight: 700;
            color: var(--verde-oscuro);
            background-color: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.6rem 0 0.6rem 0.8rem;
            /* compensa letter-spacing */
            transition: all 0.3s ease;
            font-family: monospace;
            width: 100%;
            max-width: 300px;
            appearance: textfield;
            -moz-appearance: textfield;
        }

        .input-code:focus {
            border-color: var(--verde-principal);
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.15);
            outline: none;
            background-color: #ffffff;
        }

        .input-code::-webkit-outer-spin-button,
        .input-code::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
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

        .btn-vincular {
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

        .btn-vincular:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(16, 185, 129, 0.45);
            color: #fff;
        }

        .btn-vincular img {
            filter: brightness(0) invert(1);
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

            .qr-image {
                width: 150px;
                height: 150px;
            }

            .input-code {
                font-size: 1.8rem;
                letter-spacing: 0.5rem;
            }

            .login-footer {
                position: static;
                margin-top: 1rem;
                /* era 2.5rem */
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
                    <strong>Vinculación de Dispositivo.</strong><br>
                    Asocie su cuenta a la app Google Authenticator para agregar una capa extra de seguridad a su acceso.
                </div>

                <div class="item-info">
                    <div class="icono-circulo">
                        <img src="/bootstrap-icons/1-circle-fill.svg" alt="" width="20" height="20">
                    </div>
                    <div>
                        <h6>Paso 1: Obtener código</h6>
                        <small>Escanee el QR con la app Authenticator o ingrese la clave manual</small>
                    </div>
                </div>

                <div class="item-info">
                    <div class="icono-circulo">
                        <img src="/bootstrap-icons/2-circle-fill.svg" alt="" width="20" height="20">
                    </div>
                    <div>
                        <h6>Paso 2: Confirmar</h6>
                        <small>Ingrese el código de 6 dígitos generado</small>
                    </div>
                </div>
            </div>

            <!-- ===== Panel derecho ===== -->
            <div class="panel-formulario">

                <h3 class="fw-bold mb-0">Vincular Dispositivo</h3>
                <p class="text-muted small mb-1">Escanee el QR o copie la clave en su app Authenticator.</p>

                <div class="mb-0">
                    <div class="qr-box mb-1">
                        <img src="data:image/png;base64,<?= $qr ?>" alt="Código QR 2FA" class="qr-image">
                    </div>
                    <p class="mt-2 mb-1 d-flex align-items-center justify-content-center gap-1">
                        <span class="clave-manual-texto" style="font-size: 0.9rem;"><?= chunk_split($secret, 4, ' ') ?></span>
                        <button type="button" class="btn-copiar btn-copiar-mini" title="Copiar clave" onclick="copiarClave()">
                            <img src="/bootstrap-icons/clipboard.svg" alt="" width="12" height="12">
                        </button>
                    </p>


                </div>

                <form id="formAuthenticator" method="POST" action="/authenticator" class="w-100">
                    <input type="hidden" name="secret" value="<?= $secret ?>">

                    <label for="codigo_2fa" class="form-label small fw-semibold text-secondary d-block mb-2">Ingrese el código de 6 dígitos generado:</label>

                    <div class="mb-4 d-flex justify-content-center">
                        <input
                            required
                            type="number"
                            class="form-control input-code"
                            id="codigo_2fa"
                            name="codigo_2fa"
                            maxlength="6"
                            placeholder="••••••"
                            autocomplete="off"
                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                    </div>

                    <button type="submit" id="btnSubmit" class="btn btn-vincular w-100" style="max-width: 280px;">
                        <img src="/bootstrap-icons/link-45deg.svg" alt="" width="18" height="18">
                        Verificar Código
                    </button>

                    <div class="text-center mt-3 mb-2">
                        <a href="/" class="link-volver">
                            <img src="/bootstrap-icons/arrow-left.svg" alt="" width="14" height="14" style="vertical-align: -2px;">
                            Volver al inicio
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
        document.getElementById('formAuthenticator').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btnSubmit = document.getElementById('btnSubmit');
            const originalText = btnSubmit.innerHTML;
            const inputCodigo = document.getElementById('codigo_2fa').value;

            if (inputCodigo.length !== 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Código incompleto',
                    text: 'El código de seguridad debe tener exactamente 6 dígitos.',
                    confirmButtonColor: '#10b981'
                });
                return;
            }

            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Verificando...';
            btnSubmit.disabled = true;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this)
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: data.icon || 'success',
                        title: '¡Verificado!',
                        text: data.message || 'Dispositivo vinculado correctamente.',
                        confirmButtonColor: '#10b981',
                        confirmButtonText: 'Salir e iniciar sesión'
                    }).then(() => {
                        if (data.redirect && data.redirect !== "") {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = '/login';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: data.icon || 'error',
                        title: 'Error de validación',
                        text: data.message,
                        confirmButtonColor: '#0d6efd'
                    });
                    btnSubmit.innerHTML = originalText;
                    btnSubmit.disabled = false;
                }

            } catch (error) {
                console.error("Error en Fetch:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de servidor',
                    text: 'No se pudo comunicar con el sistema. Intente de nuevo.'
                });
                btnSubmit.innerHTML = originalText;
                btnSubmit.disabled = false;
            }
        });

        function copiarClave() {
            const clave = "<?= $secret ?>";
            const btn = document.querySelector('.btn-copiar-mini');

            const exito = () => {
                btn.classList.add('copiado');
                const original = btn.innerHTML;
                btn.innerHTML = '<img src="/bootstrap-icons/check-lg.svg" alt="" width="12" height="12">';
                setTimeout(() => {
                    btn.innerHTML = original;
                    btn.classList.remove('copiado');
                }, 2000);
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(clave).then(exito).catch(() => fallbackCopiar(clave, exito));
            } else {
                fallbackCopiar(clave, exito);
            }
        }

        function fallbackCopiar(texto, callback) {
            const temp = document.createElement('textarea');
            temp.value = texto;
            temp.style.position = 'absolute';
            temp.style.left = '-9999px';
            document.body.appendChild(temp);
            temp.select();
            try {
                document.execCommand('copy');
                callback();
            } catch (e) {
                Swal.fire({
                    icon: 'info',
                    title: 'Clave manual',
                    text: 'Tu navegador no permite copiar automático. Selecciona y copia manualmente: ' + texto,
                    confirmButtonColor: '#10b981'
                });
            }
            document.body.removeChild(temp);
        }
    </script>
</body>

</html>