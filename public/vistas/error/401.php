<?php 
http_response_code(401);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No autenticado | Control de Asistencia</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="/css/bootstrap.min.css">

    <style>
        :root {
            --verde-oscuro: #065f46;
            --verde-principal: #10b981;
            --verde-claro: #34d399;
            --gris-institucion: #3c4146;
            --gris-oscuro: #2b3035;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ededed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .error-container {
            max-width: 460px;
            width: 100%;
            text-align: center;
            animation: aparecer 0.6s ease;
        }

        @keyframes aparecer {
            from { opacity: 0; transform: translateY(25px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .tarjeta-error {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.35);
            padding: 3rem 2.5rem 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .tarjeta-error::before {
            content: '';
            display: block;
            height: 6px;
            background: linear-gradient(90deg, var(--verde-oscuro), var(--verde-claro));
            position: absolute;
            top: 0; left: 0; right: 0;
        }

        .error-code {
            font-size: 6.5rem;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .icono-error {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.10);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .titulo-error {
            font-weight: 800;
            color: #1f2937;
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
        }

        .mensaje-error {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn-inicio {
            background: linear-gradient(135deg, var(--verde-principal), #059669);
            border: none;
            color: #fff;
            font-weight: 700;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-inicio:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(16, 185, 129, 0.45);
            color: #fff;
        }

        .btn-inicio img {
            filter: brightness(0) invert(1);
        }

        .btn-regresar {
            border: 2px solid #e5e7eb;
            color: #6b7280;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            background: #ffffff;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-regresar:hover {
            border-color: var(--verde-principal);
            color: var(--verde-oscuro);
            background: #f0fdf4;
        }

        .error-footer {
            margin-top: 1.5rem;
            color: #9ca3af;
            font-size: 0.75rem;
        }

        .tarjeta-error .d-flex {
            align-items: center;
        }

        @media (max-width: 480px) {
            .tarjeta-error { padding: 2.5rem 1.5rem 2rem; }
            .error-code { font-size: 4.5rem; }
            .btn-inicio, .btn-regresar {
                padding: 0.55rem 1.1rem;
                font-size: 0.88rem;
                border-radius: 8px;
            }
            .btn-inicio img, .btn-regresar img { width: 14px; height: 14px; }
        }
    </style>
</head>

<body>

    <div class="error-container">
        <div class="tarjeta-error">

            <h1 class="error-code">401</h1>

            <div class="icono-error">
                <img src="/bootstrap-icons/shield-lock-fill.svg" alt="" width="28" height="28">
            </div>

            <h3 class="titulo-error">Acceso no Autorizado</h3>
            <p class="mensaje-error">
                No tienes acceso a esta página. Debes iniciar sesión o proporcionar credenciales válidas para continuar.
            </p>

            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                <button onclick="history.back()" class="btn btn-regresar">
                    <img src="/bootstrap-icons/arrow-left.svg" alt="" width="16" height="16">
                    Regresar
                </button>
                <?php if (empty($_SESSION["user_id"])): ?>
                    <a href="/" class="btn btn-inicio">
                        <img src="/bootstrap-icons/box-arrow-in-right.svg" alt="" width="16" height="16">
                        Iniciar Sesión
                    </a>
                <?php endif ?>
            </div>

        </div>

        <div class="error-footer">
            &copy; <?= date('Y') ?> E.T.C.N "Pedro Curiel Ramírez" • Licencia CC 4.0 Internacional
        </div>
    </div>

</body>
</html>