<?php

/** Escáner de asistencia — tema claro + asistencias recientes */
?>

<style>
    :root {
        --verde: #10b981;
        --verde-oscuro: #059669;
        --verde-claro: #34d399;
        --texto: #1e293b;
    }

    /* ===== Escenario cámara ===== */
    .kiosco-stage {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.07);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .kiosco-hud {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.1rem 1.6rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .hud-etiqueta {
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .hud-reloj {
        text-align: right;
    }

    #reloj-digital {
        color: var(--verde-oscuro);
        font-weight: 800;
        font-size: 1.5rem;
        line-height: 1;
        letter-spacing: 1px;
        font-variant-numeric: tabular-nums;
    }

    #fecha-digital {
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 0.3rem;
    }

    .kiosco-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1.5rem 2.2rem;
    }

    .camera-wrapper {
        position: relative;
        width: 100%;
        max-width: 300px;
        aspect-ratio: 1 / 1;
        background-color: #111417;
        border-radius: 20px;
        overflow: hidden;
        border: 3px solid #e2e8f0;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .camera-wrapper.exito {
        border-color: var(--verde);
        box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.18), 0 0 35px rgba(16, 185, 129, 0.35);
    }

    .camera-wrapper.error {
        border-color: #ef4444;
        box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.15), 0 0 35px rgba(239, 68, 68, 0.3);
    }

    #webcam-preview,
    #webcam-preview video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .qr-target-box {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 66%;
        height: 66%;
        pointer-events: none;
        z-index: 5;
    }

    .qr-target-box::before,
    .qr-target-box::after,
    .qr-target-box-inner::before,
    .qr-target-box-inner::after {
        content: '';
        position: absolute;
        width: 26px;
        height: 26px;
        border: 4px solid var(--verde-claro);
        filter: drop-shadow(0 0 6px rgba(52, 211, 153, 0.5));
    }

    .qr-target-box::before {
        top: 0;
        left: 0;
        border-right: none;
        border-bottom: none;
    }

    .qr-target-box::after {
        top: 0;
        right: 0;
        border-left: none;
        border-bottom: none;
    }

    .qr-target-box-inner {
        position: absolute;
        width: 100%;
        height: 100%;
    }

    .qr-target-box-inner::before {
        bottom: 0;
        left: 0;
        border-right: none;
        border-top: none;
    }

    .qr-target-box-inner::after {
        bottom: 0;
        right: 0;
        border-left: none;
        border-top: none;
    }

    .laser-line {
        position: absolute;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, transparent, var(--verde-claro), transparent);
        top: 0;
        animation: scanning 2.5s infinite linear;
    }

    @keyframes scanning {
        0% {
            top: 0%;
        }

        50% {
            top: 100%;
        }

        100% {
            top: 0%;
        }
    }

    .pantalla-carga {
        position: absolute;
        inset: 0;
        background: rgba(17, 20, 23, 0.92);
        z-index: 20;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }

    .pantalla-carga.oculto {
        opacity: 0;
        visibility: hidden;
    }

    .instruccion-texto {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--texto);
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: color 0.3s ease;
        min-height: 2.2em;
    }

    /* ===== Panel de asistencias recientes ===== */
    .panel-recientes {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.07);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .panel-recientes .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .panel-recientes .panel-titulo {
        color: var(--texto);
        font-size: 0.82rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
    }

    .panel-recientes .panel-refresh {
        color: #94a3b8;
        font-size: 0.72rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .lista-recientes {
        flex-grow: 1;
        overflow-y: auto;
        padding: 0.6rem 0.8rem;
    }

    .item-reciente {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.65rem 0.6rem;
        border-radius: 12px;
        transition: background-color 0.2s ease;
    }

    .item-reciente:hover {
        background-color: #f8fafc;
    }

    .item-reciente.item-nuevo {
        animation: entradaItem 0.5s ease;
        background-color: #f0fdf4;
    }

    @keyframes entradaItem {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .avatar-reciente {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.10);
        color: var(--verde-oscuro);
        font-weight: 800;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .item-info {
        flex-grow: 1;
        min-width: 0;
    }

    .item-nombre {
        font-weight: 700;
        color: var(--texto);
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-detalle {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 600;
    }

    .badge-tipo {
        font-size: 0.68rem;
        font-weight: 800;
        padding: 0.3rem 0.6rem;
        border-radius: 50rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        flex-shrink: 0;
    }

    .badge-entrada {
        background: rgba(16, 185, 129, 0.12);
        color: var(--verde-oscuro);
    }

    .badge-salida {
        background: rgba(100, 116, 139, 0.12);
        color: #475569;
    }

    .recientes-vacio {
        text-align: center;
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 3rem 1rem;
    }

    /* Aviso de sonido */
    .aviso-sonido {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 50;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        padding: 0.6rem 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: opacity 0.4s ease;
    }

    .aviso-sonido.oculto {
        opacity: 0;
        pointer-events: none;
    }

    @media (max-width: 991px) {
        .kiosco-body {
            padding: 1.5rem 1rem 1.8rem;
        }

        .lista-recientes {
            max-height: 320px;
        }
    }

    @media (max-width: 576px) {
        .kiosco-hud {
            flex-direction: column;
            gap: 0.6rem;
            text-align: center;
        }

        .hud-reloj {
            text-align: center;
        }

        #reloj-digital {
            font-size: 1.35rem;
        }
    }
</style>

<div class="container-fluid py-4">

    <div class="row g-4 justify-content-center">

        <!-- ===== Escáner ===== -->
        <div class="col-12 col-lg-7">
            <div class="kiosco-stage">
                <div class="kiosco-hud">
                    <span class="hud-etiqueta">Escáner de Asistencia</span>
                    <div class="hud-reloj">
                        <div id="reloj-digital">00:00 AM</div>
                        <div id="fecha-digital">—</div>
                    </div>
                </div>

                <div class="kiosco-body">
                    <div class="camera-wrapper" id="camera-wrapper">
                        <div id="pantalla-carga" class="pantalla-carga">
                            <div class="spinner-border" style="width: 3rem; height: 3rem; color: #34d399;" role="status"></div>
                            <p class="mt-3 fw-semibold text-white-50 mb-0" style="font-size: 0.85rem;">Iniciando cámara...</p>
                        </div>

                        <div class="qr-target-box">
                            <div class="qr-target-box-inner"></div>
                            <div class="laser-line"></div>
                        </div>
                        <div id="webcam-preview"></div>
                    </div>

                    <div class="instruccion-texto" id="mensaje-estado">
                        Por favor, escanee su código QR
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Asistencias recientes ===== -->
        <div class="col-12 col-lg-5">
            <div class="panel-recientes">
                <div class="panel-header">
                    <span class="panel-titulo"><i class="bi bi-clock-history me-2 text-success"></i>Últimos Registros</span>
                    <span class="panel-refresh"><i class="bi bi-arrow-repeat"></i>Hoy</span>
                </div>

                <div class="lista-recientes" id="lista-recientes">
                    <?php if (empty($recientes)): ?>
                        <div class="recientes-vacio" id="recientes-vacio">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Sin registros aún hoy
                        </div>
                    <?php else: ?>
                        <?php foreach ($recientes as $r):
                            $nombreR = trim(($r['nombre'] ?? '') . ' ' . ($r['apellido'] ?? ''));
                            $iniciales = strtoupper(substr($r['nombre'] ?? '?', 0, 1) . substr($r['apellido'] ?? '', 0, 1));
                            // Tipo: salida si ya marcó salida, si no entrada
                            $esSalida = !empty($r['hora_salida']);
                            $hora = date("h:i A", strtotime($esSalida ? $r['hora_salida'] : $r['hora_entrada']));
                        ?>
                            <div class="item-reciente">
                                <div class="avatar-reciente"><?= $iniciales ?></div>
                                <div class="item-info">
                                    <div class="item-nombre"><?= htmlspecialchars($nombreR) ?></div>
                                    <div class="item-detalle"><?= $hora ?></div>
                                </div>
                                <span class="badge-tipo <?= $esSalida ? 'badge-salida' : 'badge-entrada' ?>">
                                    <?= $esSalida ? 'Salida' : 'Entrada' ?>
                                </span>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="aviso-sonido" class="aviso-sonido oculto">
    <i class="bi bi-volume-mute-fill text-success"></i> Toque la pantalla para activar el sonido
</div>

<script src="/js/html5-qrcode.min.js"></script>
<script>
    // ===== AUDIO =====
    let audioCtx = null;

    function getAudio() {
        if (!audioCtx) {
            try {
                audioCtx = new(window.AudioContext || window.webkitAudioContext)();
            } catch (e) {
                return null;
            }
        }
        if (audioCtx.state === 'suspended') audioCtx.resume();
        return audioCtx;
    }

    function beep(tono = 880, duracion = 0.15, exito = true) {
        const ctx = getAudio();
        if (!ctx) return;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = exito ? 'sine' : 'square';
        osc.frequency.value = tono;
        gain.gain.setValueAtTime(0.12, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duracion);
        osc.start();
        osc.stop(ctx.currentTime + duracion);
    }

    document.addEventListener('DOMContentLoaded', function() {
        cargarRecientes();
        /* setInterval(function() {
            if (!document.hidden) cargarRecientes(); 
        }, 30000); */
    });

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = getAudio();
        const aviso = document.getElementById('aviso-sonido');
        if (ctx && ctx.state === 'suspended') {
            aviso.classList.remove('oculto');
            const desbloquear = () => {
                ctx.resume().then(() => aviso.classList.add('oculto'));
                document.removeEventListener('click', desbloquear);
                document.removeEventListener('touchstart', desbloquear);
            };
            document.addEventListener('click', desbloquear);
            document.addEventListener('touchstart', desbloquear);
        }
    });

    // ===== RELOJ (HH:MM AM) =====
    function actualizarReloj() {
        const ahora = new Date();
        let horas = ahora.getHours();
        const minutos = ahora.getMinutes();
        const ampm = horas >= 12 ? 'PM' : 'AM';

        horas = horas % 12 || 12;
        const minStr = minutos < 10 ? '0' + minutos : minutos;

        document.getElementById('reloj-digital').textContent = `${horas}:${minStr} ${ampm}`;
        document.getElementById('fecha-digital').textContent = ahora.toLocaleDateString('es-VE', {
            weekday: 'short',
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }
    actualizarReloj();
    setInterval(actualizarReloj, 1000);

    // =========================================================
    // ASISTENCIAS RECIENTES — formato idéntico al PHP + sincronización
    // =========================================================

    // "07:32:00" -> "07:32 AM" (mismo formato que date("h:i A") de PHP)
    function formatearHora(fechaHora) {
        if (!fechaHora) return '';
        const partes = String(fechaHora).split(':');
        let h = parseInt(partes[0], 10);
        const m = partes[1] || '00';
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return (h < 10 ? '0' : '') + h + ':' + m + ' ' + ampm;
    }

    function itemRecienteHTML(nombre, hora, esSalida, esNuevo) {
        const partes = nombre.trim().split(/\s+/);
        const iniciales = ((partes[0] ? partes[0][0] : '?') + (partes[1] ? partes[1][0] : '')).toUpperCase();
        return '' +
            '<div class="item-reciente' + (esNuevo ? ' item-nuevo' : '') + '">' +
            '<div class="avatar-reciente">' + iniciales + '</div>' +
            '<div class="item-info">' +
            '<div class="item-nombre">' + nombre + '</div>' +
            '<div class="item-detalle">' + hora + '</div>' +
            '</div>' +
            '<span class="badge-tipo ' + (esSalida ? 'badge-salida' : 'badge-entrada') + '">' +
            (esSalida ? 'Salida' : 'Entrada') +
            '</span>' +
            '</div>';
    }

    // Dibuja toda la lista (desde el servidor, formato único)
    function renderizarRecientes(lista) {
        const cont = document.getElementById('lista-recientes');
        if (!cont) return;
        cont.innerHTML = '';

        if (!lista || lista.length === 0) {
            cont.innerHTML = '<div class="recientes-vacio" id="recientes-vacio">' +
                '<i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>' +
                'Sin registros aún hoy</div>';
            return;
        }

        lista.forEach(function(r) {
            const nombre = (r.nombre + ' ' + (r.apellido || '')).trim();
            const esSalida = !!r.hora_salida;
            const hora = formatearHora(esSalida ? r.hora_salida : r.hora_entrada);
            cont.insertAdjacentHTML('beforeend', itemRecienteHTML(nombre, hora, esSalida, false));
        });
    }

    // Pide los recientes al servidor (todos los escáneres ven lo mismo)
    async function cargarRecientes() {
        try {
            const res = await fetch('/asistencia/recientes-hoy');
            if (!res.ok) return;
            const data = await res.json();
            renderizarRecientes(data);
        } catch (e) {
            /* sin conexión, deja lo que hay */
        }
    }

    // ===== ESCÁNER =====
    document.addEventListener('DOMContentLoaded', function() {
        const campoTexto = document.getElementById('mensaje-estado');
        const pantallaCarga = document.getElementById('pantalla-carga');
        const cameraWrapper = document.getElementById('camera-wrapper');
        const html5QrCode = new Html5Qrcode("webcam-preview");
        const tiempoInicio = Date.now();

        let ultimoCodigo = null;
        let ultimoTiempo = 0;
        const BLOQUEO_SEGUNDOS = 10;

        function ocultarCarga() {
            const transcurrido = Date.now() - tiempoInicio;
            setTimeout(() => pantallaCarga.classList.add('oculto'), Math.max(0, 1200 - transcurrido));
        }

        function flashCamara(esExito) {
            cameraWrapper.classList.remove('exito', 'error');
            void cameraWrapper.offsetWidth;
            cameraWrapper.classList.add(esExito ? 'exito' : 'error');
            setTimeout(() => cameraWrapper.classList.remove('exito', 'error'), 2500);
        }

        const qrCodeSuccessCallback = async (decodedText) => {
            const ahora = Date.now();
            if (decodedText === ultimoCodigo && (ahora - ultimoTiempo) < BLOQUEO_SEGUNDOS * 1000) {
                return;
            }
            ultimoCodigo = decodedText;
            ultimoTiempo = ahora;

            await html5QrCode.pause();
            campoTexto.style.color = "#d97706";
            campoTexto.innerText = "PROCESANDO INFORMACIÓN...";

            try {
                const response = await fetch('/asistencia/procesar/qr', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: decodedText
                    })
                });
                const data = await response.json();

                if (data.success) {
                    beep(880, 0.15, true);
                    setTimeout(() => beep(1175, 0.2, true), 120);
                    flashCamara(true);
                    campoTexto.style.color = "#059669";
                    campoTexto.innerHTML = `¡REGISTRO EXITOSO!<br><span style="font-size:1rem; font-weight:600; color:#64748b;">${data.empleado}</span>`;
                    // ✅ Recargar recientes desde el servidor: formato correcto Y sincronizado
                    cargarRecientes();
                } else {
                    beep(220, 0.3, false);
                    flashCamara(false);
                    campoTexto.style.color = "#dc2626";
                    campoTexto.innerText = data.message.toUpperCase();
                }
            } catch (error) {
                console.error("Error:", error);
                flashCamara(false);
                campoTexto.style.color = "#dc2626";
                campoTexto.innerText = "ERROR DE CONEXIÓN";
            }

            setTimeout(async () => {
                campoTexto.style.color = "#1e293b";
                campoTexto.innerText = "POR FAVOR, ESCANEE SU CÓDIGO QR";
                await html5QrCode.resume();
            }, 4000);
        };

        html5QrCode.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                },
                qrCodeSuccessCallback
            ).then(ocultarCarga)
            .catch(err => {
                console.error("Error al iniciar cámara:", err);
                campoTexto.style.color = "#dc2626";
                campoTexto.innerText = "ERROR: NO SE PUDO ACCEDER A LA CÁMARA";
                ocultarCarga();
            });
    });
</script>