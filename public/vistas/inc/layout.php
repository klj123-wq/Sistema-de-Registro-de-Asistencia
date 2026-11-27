<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRA - Pedro Curiel Ramírez</title>

    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">

    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="/css/dashbord.css">

    <style>
        .tuerca-giratoria {
            transition: transform 0.4s ease;
            display: inline-block;
        }

        .nav-link:hover .tuerca-giratoria {
            transform: rotate(90deg);
            color: #34d399 !important;
        }

        .bg-danger-hover:hover {
            background-color: #f8d7da !important;
            color: #dc3545 !important;
        }

        .dropdown-item {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu>.dropdown-menu {
            top: 0;
            right: 100%;
            left: auto;
            margin-top: -6px;
            display: none;
        }

        .dropdown-submenu:hover>.dropdown-menu {
            display: block;
        }

        .dropdown-submenu>a::after {
            display: none;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand navbar-dark bg-dark px-3 shadow-sm" style="height: 56px;">
        <div class="container-fluid" style="flex-wrap: nowrap;">
            <button class="btn btn-dark d-md-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand d-flex align-items-center" href="/home" style="min-width: 0;">
                <img src="/assets/logo_pc.png" alt="Logo" width="40" height="40" class="d-inline-block align-text-top me-2 flex-shrink-0">
                <span class="fw-bold text-truncate d-none d-sm-inline">E.T.C.N "Pedro Curiel Ramírez"</span>
            </a>

            <div class="ms-auto d-flex align-items-center">
                <span class="text-white d-none d-md-block me-3"><strong><?= htmlspecialchars($_SESSION["nombre"] ?? 'Usuario') ?></strong></span>

                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-white opacity-75" href="#" id="navbarConfigDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear-fill tuerca-giratoria d-inline-block" style="font-size: 1.4rem;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="navbarConfigDropdown" style="min-width: 250px; border-radius: 8px; z-index: 2000;">

                        <?php if (!empty($_SESSION["permisos"]["usuarios"])) : ?>
                            <li>
                                <h6 class="dropdown-header text-uppercase fw-bold text-success">Administración</h6>
                            </li>
                            <li><a class="dropdown-item py-2" href="/configuraciones"><i class="bi bi-people-fill text-muted me-2"></i> Gestionar Usuarios</a></li>
                            <li><a class="dropdown-item py-2" href="/usuario/roles"><i class="bi bi-shield-lock-fill text-muted me-2"></i> Permisos por Rol</a></li>
                            <li><a class="dropdown-item py-2" href="/bitacora"><i class="bi bi-journal-text text-muted me-2"></i> Bitácora</a></li>
                            <li>
                                <hr class="dropdown-divider opacity-10">
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION["permisos"]["general"]) || !empty($_SESSION["permisos"]["director"])) : ?>
                            <li>
                                <h6 class="dropdown-header text-uppercase fw-bold text-success">Configuración</h6>
                            </li>
                            <?php if (!empty($_SESSION["permisos"]["general"])) : ?>
                                <li><a class="dropdown-item py-2" href="/turnos"><i class="bi bi-clock-history text-muted me-2"></i> Turnos</a></li>
                                <li><a class="dropdown-item py-2" href="/diaslibres"><i class="bi bi-calendar-x text-muted me-2"></i> Días Libres</a></li>
                            <?php endif; ?>
                            <?php if (!empty($_SESSION["permisos"]["director"])) : ?>
                                <li><a class="dropdown-item py-2" href="/personal/director"><i class="bi bi-briefcase-fill text-muted me-2"></i> Datos del Director</a></li>
                            <?php endif; ?>
                            <li>
                                <hr class="dropdown-divider opacity-10">
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION["permisos"]["respaldo"])) : ?>
                            <li>
                                <h6 class="dropdown-header text-uppercase fw-bold text-success">Sistema</h6>
                            </li>
                            <li><a class="dropdown-item py-2" href="/sistema/respaldo"><i class="bi bi-database-down text-muted me-2"></i> Respaldar Información</a></li>
                            <li>
                                <hr class="dropdown-divider opacity-10">
                            </li>
                        <?php endif; ?>

                        <li>
                            <h6 class="dropdown-header text-uppercase fw-bold text-success">Mi Cuenta</h6>
                        </li>
                        <li><a class="dropdown-item py-2" href="/usuarios/perfil"><i class="bi bi-person-circle text-muted me-2"></i> Mi Perfil</a></li>
                        <li>
                            <hr class="dropdown-divider opacity-10">
                        </li>
                        <li><a id="btnSalirNavbar" class="dropdown-item py-2 text-danger fw-semibold bg-danger-hover" href="/close"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="wrapper d-flex" style="height: calc(100vh - 56px); overflow: hidden;">

        <?php include 'sidebar.php'; ?>

        <div class="main-container d-flex flex-column flex-grow-1" style="overflow: hidden;">

            <main class="content-area p-4 flex-grow-1" style="overflow-y: auto; background-color: #f8f9fa;">

                <?php
                /*
                // ===== BOTÓN VOLVER (comentado temporalmente) =====
                $ruta_actual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                if ($ruta_actual !== '/' && $ruta_actual !== '/home' && $ruta_actual !== '/inicio'):
                ?>
                    <div class="mb-2 d-print-none">
                        <button type="button" class="btn btn-sm btn-white border fw-semibold text-secondary shadow-sm rounded-pill px-3 btn-volver hover-bg-gray" data-fallback="/home" style="transition: all 0.2s ease;">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </button>
                    </div>
                <?php endif; */
                ?>
                <?php
                if (isset($vista_interna)) {
                    include 'vistas/' . $vista_interna . '.php';
                }
                ?>
            </main>

            <?php include 'footer.php'; ?>

        </div>
    </div>

    <script src="/js/jquery.min.js"></script>
    <script src="/js/jquery.dataTables.min.js"></script>
    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="/js/datatables-config.js"></script>
    <script src="/js/dataTables.bootstrap5.min.js"></script>
    <script src="/js/main.js"></script>
    <script src="/js/sweetalert2.all.min.js"></script>

    <script>
        window.addEventListener('load', function() {

            // ===== 1. CERRAR SESIÓN =====
            // ===== 1. CERRAR SESIÓN =====
            const btnSalirNavbar = document.getElementById('btnSalirNavbar');
            if (btnSalirNavbar) {
                btnSalirNavbar.addEventListener('click', function(e) {
                    e.preventDefault();

                    // ✅ Cerrar el menú de la tuerca antes de mostrar el Swal
                    const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('navbarConfigDropdown'));
                    if (dropdown) dropdown.hide();

                    Swal.fire({
                        title: '¿Cerrar sesión?',
                        text: 'Se cerrará tu sesión actual',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, salir',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) window.location.href = '/close';
                    });
                });
            }

            // ===== 2. CONTROL DE INACTIVIDAD (15 minutos) =====
            let tiempoInactividad;

            function reiniciarTiempo() {
                clearTimeout(tiempoInactividad);
                tiempoInactividad = setTimeout(mostrarAlertaInactividad, 900000);
            }

            function mostrarAlertaInactividad() {
                Swal.fire({
                    title: '¿Sigues ahí?',
                    text: 'Tu sesión está a punto de cerrarse por inactividad.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: '<i class="bi bi-hand-thumbs-up-fill me-1"></i> Sí, aquí estoy',
                    cancelButtonText: 'Cerrar sesión',
                    timer: 30000,
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        reiniciarTiempo();
                    } else {
                        window.location.href = '/close';
                    }
                });
            }

            if (window.location.pathname !== '/') {
                document.addEventListener('mousemove', reiniciarTiempo);
                document.addEventListener('keypress', reiniciarTiempo);
                document.addEventListener('scroll', reiniciarTiempo);
                document.addEventListener('click', reiniciarTiempo);
                reiniciarTiempo();
            }
        });


        // ===== 3. BOTÓN VOLVER (lógica propia: vuelve por donde viniste, sin salir de la sesión) =====
        document.addEventListener('click', function(e) {
            const btnVolver = e.target.closest('.btn-volver');
            if (!btnVolver) return;
            e.preventDefault();

            // ✅ Guard GLOBAL: aunque este handler esté duplicado en el archivo,
            // el segundo intento ve la bandera y no ejecuta nada
            if (window.__volverEnCurso === true) return;
            window.__volverEnCurso = true;

            // Marcar y bloquear visualmente
            btnVolver.dataset.enEspera = '1';
            btnVolver.disabled = true;

            // Liberar después de 1s por si la navegación no ocurrió
            // (si la página cambia, esto ya no importa — se descarga)
            setTimeout(() => {
                btnVolver.dataset.enEspera = '0';
                btnVolver.disabled = false;
            }, 1000);

            const rutaPorDefecto = btnVolver.getAttribute('data-fallback') || '/home';
            const rutaActual = window.location.pathname;

            // Rutas de las que NUNCA se puede "volver" (sacarían de la sesión)
            const rutasProhibidas = ['/', '/verify', '/login', '/close', '/recuperar_password', '/recuperar', '/authenticator'];

            let referenciaSegura = false;

            try {
                const ref = new URL(document.referrer);
                referenciaSegura =
                    ref.origin === window.location.origin && // misma app (nunca salir del dominio)
                    ref.pathname !== rutaActual && // no es esta misma página
                    !rutasProhibidas.includes(ref.pathname); // no es login/cierre/recuperación
            } catch (err) {
                // Sin referrer válido → ruta por defecto
            }

            if (referenciaSegura) {
                window.history.back();
            } else {
                window.location.href = rutaPorDefecto;
            }
        });
    </script>
</body>

</html>