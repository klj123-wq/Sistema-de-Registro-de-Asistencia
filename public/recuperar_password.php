<?php
// recuperar_password.php

use libs\CSRF;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRA - Recuperar Contraseña</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/bootstrap-icons.css">
    <style>
        :root {
            --color-principal: #0d6efd;
            --color-gris-fondo: #3c4146;
            --color-advertencia: #ffc107;
        }

        body, html {
            margin: 0; padding: 0; height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff; overflow-x: hidden;
        }

        .login-container { height: 100vh; }

        .panel-branding {
            background-color: var(--color-gris-fondo);
            color: #ffffff; padding: 4rem;
            display: flex; flex-direction: column;
            justify-content: space-between; height: 100vh;
            border-right: 4px solid var(--color-advertencia);
        }

        .panel-form {
            background-color: #ffffff; padding: 4rem;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            height: 100vh; position: relative;
        }

        .form-container { width: 100%; max-width: 400px; }

        .form-control-lg {
            border-radius: 8px; font-size: 0.95rem; padding: 0.6rem 0.9rem;
        }

        .form-control-lg:focus {
            border-color: var(--color-principal);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.12);
        }

        .footer-login {
            position: absolute; bottom: 20px; left: 0; width: 100%;
            text-align: center; color: #6c757d; font-size: 0.8rem;
        }

        @media (max-width: 767px) {
            .panel-branding {
                height: auto; padding: 2.5rem 1.5rem; gap: 2rem;
                border-right: none; border-bottom: 4px solid var(--color-advertencia);
            }
            .panel-form { height: auto; padding: 3rem 1.5rem; }
            .login-container { height: auto; }
            .footer-login { position: relative; bottom: 0; margin-top: 3rem; padding-bottom: 1rem; }
        }
    </style>
</head>

<body>

    <div class="container-fluid p-0 login-container">
        <div class="row g-0 h-100">

            <div class="col-12 col-md-6">
                <div class="panel-branding">
                    <div class="d-flex align-items-center">
                        <img src="/assets/logo_pc.png" alt="Logo ETC" width="60" height="60" class="me-3">
                        <div>
                            <h4 class="fw-bold mb-0 text-white">E.T.C.N "Pedro Curiel Ramírez"</h4>
                            <p class="text-light opacity-75 small mb-0">S.R.A. - Sistema de Registro Automatizado</p>
                        </div>
                    </div>

                    <div class="my-5 my-md-0">
                        <h1 class="fw-bold text-white mb-2" style="font-size: 2.3rem; letter-spacing: -0.5px;">Restaurar Acceso</h1>
                        <p class="text-light opacity-75 fs-6" style="max-width: 450px;">
                            Siga los pasos indicados en pantalla. Necesitará un token de seguridad autorizado para poder asignar una nueva contraseña.
                        </p>
                    </div>

                    <div class="w-100">
                        <a href="/" class="btn btn-outline-light w-100 py-2 fw-semibold rounded-3 border-opacity-50" style="font-size: 0.95rem;">
                            <i class="bi bi-arrow-left me-2"></i> REGRESAR AL LOGIN
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="panel-form">
                    <div class="form-container">

                        <div class="mb-4">
                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1.5 rounded-pill small fw-bold mb-2" style="color: #b18400 !important;">
                                <i class="bi bi-shield-lock-fill me-1"></i> Recuperación Segura
                            </span>
                            <h3 class="fw-bold text-dark mb-1">¿Olvidó su contraseña?</h3>
                            <p class="text-muted small" id="texto-instruccion">Paso 1: Identifique su cuenta de administrador.</p>
                        </div>

                        <form id="form-paso1" action="/recuperar_password/verificar" method="POST" autocomplete="off">
                            <input type="hidden" name="CSRF" value="<?php echo CSRF::generar() ?>">
                            
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary">Cédula o Nombre de Usuario</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0 px-3"><i class="bi bi-person-badge"></i></span>
                                    <input required type="text" name="identificador" class="form-control form-control-lg bg-light border-start-0" placeholder="Ej: 12345678 o admin">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary py-2.5 rounded-3 fw-bold w-100 shadow-sm">
                                <i class="bi bi-search me-2"></i> Verificar Cuenta
                            </button>
                        </form>

                        <form id="form-paso2" action="/recuperar_password/cambiar" method="POST" autocomplete="off" style="display: none;">
                            <input type="hidden" name="CSRF" value="<?php echo CSRF::generar() ?>">
                            <input type="hidden" name="id_usuario_encontrado" id="id_usuario_oculto">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Token de Seguridad</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0 px-3"><i class="bi bi-key-fill text-warning"></i></span>
                                    <input required type="text" name="token" class="form-control form-control-lg bg-light border-start-0 text-uppercase" placeholder="Ingrese el código token">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Nueva Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0 px-3"><i class="bi bi-lock"></i></span>
                                    <input required type="password" name="nueva_clave" class="form-control form-control-lg bg-light border-start-0" placeholder="Mínimo 8 caracteres">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0 px-3"><i class="bi bi-lock-fill"></i></span>
                                    <input required type="password" name="confirmar_clave" class="form-control form-control-lg bg-light border-start-0" placeholder="Repita la nueva contraseña">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success py-2.5 rounded-3 fw-bold w-100 shadow-sm">
                                <i class="bi bi-check-circle me-2"></i> Guardar Nueva Contraseña
                            </button>
                        </form>

                    </div>

                    <div class="footer-login">
                        © 2026 ETC "Pedro Curiel Ramírez" • Licencia CC 4.0 Internacional
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="/js/sweetalert2.all.min.js"></script>
    <script>
        const formPaso1 = document.getElementById('form-paso1');
        const formPaso2 = document.getElementById('form-paso2');
        const textoInstruccion = document.getElementById('texto-instruccion');
        const inputIdOculto = document.getElementById('id_usuario_oculto');

        // ==========================================
        // PROCESAR PASO 1 (Verificar Cédula/Usuario)
        // ==========================================
        formPaso1.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;

            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Buscando...';
            btn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Si el usuario existe, el Backend nos manda su ID y ocultamos el Paso 1
                    inputIdOculto.value = data.id_usuario; 
                    
                    formPaso1.style.display = 'none'; // Ocultar Paso 1
                    formPaso2.style.display = 'block'; // Mostrar Paso 2 (Token y Clave)
                    
                    textoInstruccion.innerHTML = '<span class="text-success fw-bold">Cuenta encontrada.</span> Ingrese su token y defina su nueva clave.';
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'Cuenta Localizada',
                        text: 'Por favor, ingrese el Token de Seguridad para proceder.',
                        confirmButtonColor: '#ffc107'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error de Red', text: 'Problemas al conectar con el servidor.' });
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });

        // ==========================================
        // PROCESAR PASO 2 (Validar Token y Cambiar Clave)
        // ==========================================
        formPaso2.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar que las contraseñas coincidan antes de enviar al servidor
            const clave = this.querySelector('input[name="nueva_clave"]').value;
            const confirma = this.querySelector('input[name="confirmar_clave"]').value;
            
            if(clave !== confirma) {
                Swal.fire({ icon: 'warning', title: 'Atención', text: 'Las contraseñas no coinciden.' });
                return;
            }

            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;

            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';
            btn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Contraseña Actualizada!',
                        text: 'Su contraseña ha sido cambiada exitosamente.',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        window.location.href = '/'; // Manda de vuelta al login
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Token Inválido', text: data.message });
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Hubo un problema al guardar la clave.' });
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>