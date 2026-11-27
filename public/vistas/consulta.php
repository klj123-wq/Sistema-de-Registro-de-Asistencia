<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRA - Consulta de Asistencias</title>
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
            border-radius: 10px;
            font-size: 0.95rem;
            padding: 0.7rem 0.95rem;
            background: #f8f9fa;
            border-color: #e9ecef;
        }

        .form-control-lg:focus,
        .form-select:focus {
            border-color: var(--verde-principal);
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.12);
            background: #ffffff;
        }

        .btn-consultar,
        .btn-pdf {
            background: linear-gradient(135deg, var(--verde-principal), #059669);
            border: none;
            color: #fff;
            font-weight: 700;
            border-radius: 10px;
            padding: 0.8rem 1.5rem;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .btn-consultar:hover:not(:disabled),
        .btn-pdf:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(16, 185, 129, 0.45);
            color: #fff;
        }

        .btn-consultar img,
        .btn-pdf img {
            filter: brightness(0) invert(1);
        }

        /* ===== Tarjeta del trabajador (paso 2) ===== */
        .tarjeta-trabajador {
            background: #f0fdf4;
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-left: 4px solid #059669;
            border-radius: 12px;
            padding: 0.9rem 1.1rem;
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
                    <strong>Consulta de Asistencias.</strong><br>
                    Verifique de forma rápida y segura los registros de asistencia del personal de la institución.
                </div>

                <div class="item-info">
                    <div class="icono-circulo">
                        <img src="/bootstrap-icons/search.svg" alt="" width="20" height="20">
                    </div>
                    <div>
                        <h6>Búsqueda por cédula</h6>
                        <small>Ingrese el número de identidad del trabajador</small>
                    </div>
                </div>

                <div class="item-info">
                    <div class="icono-circulo">
                        <img src="/bootstrap-icons/file-earmark-pdf.svg" alt="" width="20" height="20">
                    </div>
                    <div>
                        <h6>Reporte en PDF</h6>
                        <small>Genere un documento con los registros del periodo</small>
                    </div>
                </div>
            </div>

            <!-- ===== Panel derecho ===== -->
            <div class="panel-formulario">

                <h3 class="fw-bold mb-1">Consultar Asistencias</h3>
                <p class="text-muted small mb-4" id="subtitulo">Ingrese la cédula del trabajador para comenzar.</p>

                <!-- ========== PASO 1: Cédula ========== -->
                <form id="formCedula" autocomplete="off">
                    <label class="form-label small fw-semibold text-secondary mb-1">Cédula del trabajador</label>
                    <div class="d-flex gap-2 flex-column flex-sm-row mb-3">
                        <input required type="text" id="inputCedula" class="form-control form-control-lg"
                            placeholder="Ej: 12345678" maxlength="8"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8)">
                        <button type="submit" id="btnBuscar" class="btn btn-consultar">
                            <img src="/bootstrap-icons/search.svg" alt="" width="18" height="18">
                            Consultar
                        </button>
                    </div>
                </form>

                <!-- ========== PASO 2: Periodo + PDF (oculto hasta confirmar) ========== -->
                <div id="pasoPeriodo" style="display: none;" class="fade-in">

                    <!-- Datos del trabajador -->
                    <div class="tarjeta-trabajador mb-3">
                        <div class="fw-bold text-dark" id="resNombre">—</div>
                        <small class="text-muted">C.I: V-<span id="resCedula">—</span></small>
                    </div>

                    <!-- Mensaje cuando no hay asistencias -->
                    <div id="sinAsistencias" class="alert alert-light border text-center text-muted small" style="display: none;">
                        <i class="bi bi-inbox d-block mb-1" style="font-size: 1.5rem;"></i>
                        Este trabajador no tiene asistencias registradas.
                    </div>

                    <!-- Selectores de periodo -->
                    <div id="bloquePeriodo" style="display: none;">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary mb-1">Año</label>
                                <select id="selectAnio" class="form-select form-control-lg"></select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary mb-1">Mes</label>
                                <select id="selectMes" class="form-select form-control-lg"></select>
                            </div>
                        </div>

                        <button type="button" id="btnPdf" class="btn btn-pdf w-100">
                            <img src="/bootstrap-icons/file-earmark-pdf.svg" alt="" width="18" height="18">
                            Generar PDF del periodo
                        </button>
                    </div>

                    <div class="text-center mt-3">
                        <a href="#" id="linkOtraBusqueda" class="link-volver">
                            <img src="/bootstrap-icons/arrow-left.svg" alt="" width="14" height="14" style="vertical-align: -2px;">
                            Consultar otro trabajador
                        </a>
                    </div>
                </div>

                <div class="text-center mt-4" id="volverLogin">
                    <a href="/" class="link-volver">
                        <img src="/bootstrap-icons/arrow-left.svg" alt="" width="14" height="14" style="vertical-align: -2px;">
                        Volver al inicio de sesión
                    </a>
                </div>

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

            const NOMBRES_MESES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ];

            let trabajadorActual = null; // { cedula, nombre }
            let periodos = []; // [{anio: 2026, meses: [7, 8, 9]}]

            // ========== PASO 1: Buscar trabajador ==========
            document.getElementById('formCedula').addEventListener('submit', async function(e) {
                e.preventDefault();

                const cedula = document.getElementById('inputCedula').value.trim();
                const btn = document.getElementById('btnBuscar');

                if (cedula.length < 7) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cédula incompleta',
                        text: 'Ingrese una cédula válida (7-8 dígitos).',
                        confirmButtonColor: '#10b981'
                    });
                    return;
                }

                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Buscando...';
                btn.disabled = true;

                try {
                    const response = await fetch('/consulta/buscar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            cedula: cedula
                        })
                    });
                    const data = await response.json();

                    if (!data.success) {
                        ocultarPasoPeriodo();
                        Swal.fire({
                            icon: 'info',
                            title: 'Sin resultados',
                            text: data.message,
                            confirmButtonColor: '#10b981'
                        });
                        return;
                    }

                    // Guardar datos del trabajador
                    trabajadorActual = {
                        cedula: data.cedula,
                        nombre: data.nombre
                    };

                    // ✅ Periodos con asistencia (viene del backend)
                    periodos = data.periodos || [];

                    document.getElementById('resNombre').textContent = data.nombre;
                    document.getElementById('resCedula').textContent = Number(data.cedula).toLocaleString('es-VE');

                    // Mostrar paso 2
                    document.getElementById('formCedula').style.display = 'none';
                    document.getElementById('volverLogin').style.display = 'none';
                    document.getElementById('pasoPeriodo').style.display = 'block';
                    document.getElementById('subtitulo').textContent = 'Seleccione el periodo a consultar.';

                    if (periodos.length === 0) {
                        document.getElementById('sinAsistencias').style.display = 'block';
                        document.getElementById('bloquePeriodo').style.display = 'none';
                    } else {
                        document.getElementById('sinAsistencias').style.display = 'none';
                        document.getElementById('bloquePeriodo').style.display = 'block';
                        cargarSelectAnios();
                    }

                } catch (error) {
                    console.error("Error Fetch:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo comunicar con el servidor.',
                        confirmButtonColor: '#10b981'
                    });
                } finally {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            });

            // ========== Poblar selects según periodos disponibles ==========
            function cargarSelectAnios() {
                const selectAnio = document.getElementById('selectAnio');
                selectAnio.innerHTML = '';

                // Años ordenados descendente (más reciente primero)
                const anios = periodos.map(p => p.anio).sort((a, b) => b - a);
                anios.forEach(anio => {
                    selectAnio.innerHTML += `<option value="${anio}">${anio}</option>`;
                });

                cargarSelectMeses();
                selectAnio.onchange = cargarSelectMeses;
            }

            function cargarSelectMeses() {
                const anioSel = parseInt(document.getElementById('selectAnio').value);
                const periodo = periodos.find(p => p.anio === anioSel);
                const selectMes = document.getElementById('selectMes');
                selectMes.innerHTML = '';

                // Meses ordenados descendente (más reciente primero)
                const meses = periodo ? periodo.meses.slice().sort((a, b) => b - a) : [];
                meses.forEach(mes => {
                    selectMes.innerHTML += `<option value="${mes}">${NOMBRES_MESES[mes - 1]}</option>`;
                });
            }

            // ========== Generar PDF (POST en pestaña nueva) ==========
            document.getElementById('btnPdf').addEventListener('click', function() {
                if (!trabajadorActual) return;

                const mes = document.getElementById('selectMes').value;
                const anio = document.getElementById('selectAnio').value;

                // Formulario dinámico oculto → POST que abre en pestaña nueva
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/consulta/pdf';
                form.target = '_blank';

                const campos = {
                    cedula: trabajadorActual.cedula,
                    mes: mes,
                    anio: anio
                };

                for (const nombre in campos) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = nombre;
                    input.value = campos[nombre];
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
                form.remove();
            });

            // ========== Volver a buscar otro trabajador ==========
            document.getElementById('linkOtraBusqueda').addEventListener('click', function(e) {
                e.preventDefault();
                ocultarPasoPeriodo();
                document.getElementById('formCedula').style.display = 'block';
                document.getElementById('volverLogin').style.display = 'block';
                document.getElementById('subtitulo').textContent = 'Ingrese la cédula del trabajador para comenzar.';
                document.getElementById('inputCedula').value = '';
                document.getElementById('inputCedula').focus();
            });

            function ocultarPasoPeriodo() {
                document.getElementById('pasoPeriodo').style.display = 'none';
                document.getElementById('sinAsistencias').style.display = 'none';
                document.getElementById('bloquePeriodo').style.display = 'none';
                trabajadorActual = null;
                periodos = [];
            }
        });
    </script>
</body>

</html>