<?php
// vistas/personal/registrar.php

/** @var array $data */

use libs\CSRF;
?>

<div class="container-fluid py-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-0">Registro de Personal</h3>
            <p class="text-muted small mb-0">Complete la información para ingresar un nuevo trabajador.</p>
        </div>
        <div>
            <a href="/personal" class="btn btn-sm btn-light border fw-semibold text-secondary shadow-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Volver al Listado
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3" style="max-width: 1000px; margin: 0 auto;">
        <div class="card-body p-4 p-md-4 bg-white">
            <form id="formularioPersonal" class="needs-validation" action="/personal" method="post" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="CSRF" value="<?php echo CSRF::generar() ?>">

                <div class="row g-4">

                    <!-- ================= COLUMNA IZQUIERDA: FOTOGRAFÍA ================= -->
                    <div class="col-12 col-lg-4">
                        <div class="foto-lateral">
                            <h6 class="fw-bold text-success mb-3 border-bottom pb-2">Fotografía <span class="text-muted fw-normal small">(Requerida)</span></h6>

                            <!-- Círculo único: placeholder / video / preview -->
                            <div class="marco-foto mx-auto">
                                <div id="placeholderFoto" class="placeholder-foto"><i class="bi bi-person-fill"></i></div>
                                <video id="videoElement" autoplay playsinline muted style="display: none;"></video>
                                <img id="fotoPreview" class="d-none">
                                <img id="previewArchivo" class="d-none">
                                <canvas id="canvasElement" class="d-none"></canvas>
                            </div>

                            <input type="file" name="foto" id="foto_archivo" accept="image/*" class="d-none">
                            <input type="hidden" name="foto_base64" id="foto_base64">

                            <div class="d-grid gap-2 mt-3 mx-auto" style="max-width: 240px;">
                                <button type="button" id="btnIniciarCamara" class="btn btn-sm btn-success rounded-pill px-3"><i class="bi bi-camera me-1"></i> Activar Cámara</button>
                                <button type="button" id="btnCapturar" class="btn btn-sm btn-success rounded-pill px-3 d-none">Capturar Foto</button>
                                <button type="button" id="btnRetomar" class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-none">Retomar</button>
                                <button type="button" id="btnSubirArchivo" class="btn btn-sm btn-outline-success rounded-pill px-3"><i class="bi bi-folder2-open me-1"></i> Subir Archivo</button>
                            </div>

                            <p class="text-muted small text-center mt-3 mb-0" style="font-size: 0.72rem;">La foto se verá circular, igual que en el carnet.</p>
                        </div>
                    </div>

                    <!-- ================= COLUMNA DERECHA: CAMPOS ================= -->
                    <div class="col-12 col-lg-8">

                        <h6 class="fw-bold text-success mb-3 border-bottom pb-2">1. Datos Personales</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-4">
                                <label for="cedula" class="form-label small fw-bold text-secondary mb-1">Cédula de Identidad</label>
                                <input required type="text" class="form-control bg-light border-0" name="cedula" id="cedula" placeholder="12345678"
                                    maxlength="8" minlength="7"
                                    oninput="this.value = this.value.replace(/\s/g, '').replace(/[^0-9]/g, '').slice(0, 8)">
                                <div class="invalid-feedback" style="font-size: 0.75rem;">Solo números, entre 7 y 8 dígitos.</div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="nombre" class="form-label small fw-bold text-secondary mb-1">Nombre</label>
                                <input required type="text" class="form-control bg-light border-0" name="nombre" id="nombre" placeholder="Nombre"
                                    maxlength="12" minlength="2"
                                    oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '').slice(0, 12)">
                                <div class="invalid-feedback" style="font-size: 0.75rem;">Solo letras, sin espacios, entre 2 y 12 caracteres.</div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="apellido" class="form-label small fw-bold text-secondary mb-1">Apellido</label>
                                <input required type="text" class="form-control bg-light border-0" name="apellido" id="apellido" placeholder="Apellido"
                                    maxlength="12" minlength="2"
                                    oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '').slice(0, 12)">
                                <div class="invalid-feedback" style="font-size: 0.75rem;">Solo letras, sin espacios, entre 2 y 12 caracteres.</div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-success mb-3 border-bottom pb-2">2. Datos Laborales</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="select_cargo" class="form-label small fw-bold text-secondary mb-1">Función / Cargo</label>
                                <select required name="cargo" id="select_cargo" class="form-select bg-light border-0">
                                    <option value="" disabled selected hidden>Seleccione...</option>
                                    <option value="administrativo">Administrativo</option>
                                    <option value="docente">Docente</option>
                                    <option value="obrero">Obrero</option>
                                    <option value="cocinero">Cocinero</option>
                                </select>
                                <div class="invalid-feedback" style="font-size: 0.75rem;">Seleccione el cargo.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="select_turno" class="form-label small fw-bold text-secondary mb-1">Turno Asignado</label>
                                <select required name="turno" id="select_turno" class="form-select bg-light border-0" onchange="verificarMixto()" disabled>
                                    <option value="" disabled selected hidden>Esperando cargo...</option>
                                </select>
                                <div class="invalid-feedback" style="font-size: 0.75rem;">Asigne un turno.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="fecha_ingreso" class="form-label small fw-bold text-secondary mb-1">Fecha Ingreso</label>
                                <input required type="date" class="form-control bg-light border-0" name="fecha_ingreso" id="fecha_ingreso" max="<?= date('Y-m-d') ?>">
                                <div class="invalid-feedback" style="font-size: 0.75rem;">Fecha requerida (máximo hoy).</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="cod_cargo" class="form-label small fw-bold text-secondary mb-1">Código de Cargo</label>
                                <input required type="text" class="form-control bg-light border-0" name="cod_cargo" id="cod_cargo" placeholder="1234AB"
                                    maxlength="6" minlength="6" pattern="\d{4}[A-Z]{2}" title="4 números seguidos de 2 letras"
                                    oninput="this.value = formatearCodCargo(this.value)">
                                <div class="invalid-feedback" style="font-size: 0.75rem;">Debe ser 4 números seguidos de 2 letras (ej: 1234AB).</div>
                            </div>
                        </div>

                        <div id="contenedorTurnoMixto" style="display: none;" class="mt-3 fade-in">
                            <div class="p-3 bg-white border rounded-3 shadow-sm" style="border-left: 4px solid #059669 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-range text-success me-2"></i>Configuración de Turno Mixto</h6>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label for="turno_nomina" class="form-label small fw-bold text-secondary mb-1">Turno para Nómina</label>
                                        <select name="turno_nomina" id="turno_nomina" class="form-select bg-light border-0" disabled>
                                            <option value="" disabled selected hidden>Seleccione turno principal...</option>
                                        </select>
                                        <div class="invalid-feedback" style="font-size: 0.75rem;">Requerido para trabajador mixto.</div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <span class="form-label small fw-bold text-secondary mb-1 d-block">Distribución Semanal</span>
                                        <div id="dias_mixto_container" class="border rounded-3 overflow-hidden bg-white"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="text-muted opacity-10 my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/personal" class="btn btn-light border px-4 fw-semibold text-secondary rounded-3">Cancelar</a>
                            <button type="submit" id="btnSubmit" class="btn btn-success px-4 fw-bold shadow-sm rounded-3">
                                <i class="bi bi-save2 me-1"></i> Guardar
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* ===== Columna de foto (sticky en escritorio) ===== */
    .foto-lateral {
        position: sticky;
        top: 1rem;
    }

    /* Círculo único: placeholder / video / preview */
    .marco-foto {
        width: 220px;
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.35), 0 8px 20px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .marco-foto video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .marco-foto img:not(.d-none) {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .placeholder-foto {
        color: #cbd5e1;
        font-size: 4.5rem;
        line-height: 1;
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .was-validated .form-control:invalid,
    .was-validated .form-select:invalid {
        border-color: #dc3545;
        background-image: none;
    }

    /* ===== Responsive ===== */

    @media (max-width: 991px) {
        .foto-lateral {
            position: static;
            text-align: center;
            border-bottom: 1px solid #eef0f2;
            padding-bottom: 1.5rem;
            margin-bottom: 0.5rem;
        }
    }

    @media (max-width: 480px) {
        form .d-flex.justify-content-end.gap-2 {
            flex-direction: column;
        }

        form .d-flex.justify-content-end.gap-2 .btn {
            width: 100%;
        }
    }

    @media (max-width: 340px) {
        .marco-foto {
            width: 100%;
        }
    }

    /* Radios de días mixtos: seleccionado en verde */
    .btn-check:checked+.btn-outline-success {
        background-color: #198754;
        border-color: #198754;
        color: #fff;
    }

    /* Hover verde suave en filas de días mixtos */
    .hover-row {
        transition: background-color 0.2s ease;
    }

    .hover-row:hover {
        background-color: #f0fdf4 !important;
    }
</style>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
    // ===== Formateador estricto del código de cargo: 4 números + 2 letras =====
    function formatearCodCargo(valor) {
        valor = valor.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
        let numeros = '',
            letras = '';
        for (const ch of valor) {
            if (/[0-9]/.test(ch) && numeros.length < 4) numeros += ch;
            else if (/[A-Z]/.test(ch) && numeros.length === 4 && letras.length < 2) letras += ch;
        }
        return numeros + letras;
    }

    document.addEventListener('DOMContentLoaded', function() {
        let turnosCargados = [];

        // ===== 1. Turnos según cargo =====
        document.getElementById('select_cargo').addEventListener('change', async function() {
            const cargo = this.value;
            const selectTurno = document.getElementById('select_turno');
            const contenedorMixto = document.getElementById('contenedorTurnoMixto');
            const formData = new FormData();
            formData.append('cargo', cargo);

            try {
                const response = await fetch('/personal/get_turnos', {
                    method: 'POST',
                    body: formData
                });
                let turnos = await response.json();

                const turnosUnicos = [];
                const nombresVistos = new Set();
                turnos.forEach(turno => {
                    const nombreLimpio = turno.nombre.toLowerCase().trim();
                    if (!nombresVistos.has(nombreLimpio)) {
                        nombresVistos.add(nombreLimpio);
                        turnosUnicos.push(turno);
                    }
                });
                turnos = turnosUnicos;

                if (turnos.length === 0) {
                    selectTurno.innerHTML = '<option value="" disabled selected hidden>Sin turnos disponibles</option>';
                    selectTurno.disabled = true;
                    contenedorMixto.style.display = 'none';
                    Swal.fire({
                        title: 'Cargo sin turnos',
                        text: 'Este cargo no tiene turnos configurados.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ir a crear Turno',
                        confirmButtonColor: '#059669'
                    }).then((result) => {
                        if (result.isConfirmed) window.location.href = '/turnos/registrar';
                    });
                    return;
                }

                turnosCargados = turnos;
                selectTurno.innerHTML = '<option value="" disabled selected hidden>Seleccione...</option>';
                turnos.forEach(turno => {
                    selectTurno.innerHTML += `<option value="${turno.id}">${turno.nombre.charAt(0).toUpperCase() + turno.nombre.slice(1)}</option>`;
                });
                if (turnos.length >= 2) selectTurno.innerHTML += '<option value="mixto">Mixto (Configurar)</option>';
                selectTurno.disabled = false;
                contenedorMixto.style.display = 'none';
                verificarMixto();
            } catch (error) {
                console.error("Error al cargar los turnos:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los turnos. Intente de nuevo.',
                    confirmButtonColor: '#059669'
                });
            }
        });

        // ===== 2. Turno Mixto =====
        window.verificarMixto = function() {
            const select = document.getElementById('select_turno');
            const contenedorMixto = document.getElementById('contenedorTurnoMixto');
            const containerDias = document.getElementById('dias_mixto_container');
            const turnoNomina = document.getElementById('turno_nomina');

            if (select.value === 'mixto') {
                turnoNomina.innerHTML = '<option value="" disabled selected hidden>Seleccione turno principal...</option>';
                turnosCargados.forEach(turno => {
                    turnoNomina.innerHTML += `<option value="${turno.id}">${turno.nombre.charAt(0).toUpperCase() + turno.nombre.slice(1)}</option>`;
                });
                turnoNomina.disabled = false;
                turnoNomina.required = true;

                let htmlDias = '';
                const dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                dias.forEach((dia, index) => {
                    const numDia = index + 1;
                    const borderBottom = (index < 4) ? 'border-bottom' : '';
                    htmlDias += `<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between p-2 ${borderBottom} hover-row"><span class="small fw-semibold text-dark mb-2 mb-sm-0" style="min-width: 80px;">${dia}</span><div class="d-flex flex-wrap gap-1">`;
                    turnosCargados.forEach(turno => {
                        const idInput = `turno_${turno.id}_dia_${numDia}`;
                        htmlDias += `<input type="radio" class="btn-check" name="dia[${numDia}]" id="${idInput}" value="${turno.id}" required><label class="btn btn-outline-success btn-sm rounded-pill px-3" for="${idInput}" style="font-size: 0.8rem;">${turno.nombre.charAt(0).toUpperCase() + turno.nombre.slice(1)}</label>`;
                    });
                    htmlDias += `</div></div>`;
                });
                containerDias.innerHTML = htmlDias;
                contenedorMixto.style.display = 'block';
            } else {
                turnoNomina.disabled = true;
                turnoNomina.required = false;
                turnoNomina.value = "";
                containerDias.querySelectorAll('input[type="radio"]').forEach(r => {
                    r.required = false;
                    r.disabled = true;
                });
                contenedorMixto.style.display = 'none';
                containerDias.innerHTML = '';
            }
        }

        // ===== 3. Foto (círculo único, dos fuentes excluyentes) =====
        const video = document.getElementById('videoElement');
        const canvas = document.getElementById('canvasElement');
        const fotoPreview = document.getElementById('fotoPreview');
        const previewArchivo = document.getElementById('previewArchivo');
        const placeholder = document.getElementById('placeholderFoto');
        const fotoBase64 = document.getElementById('foto_base64');
        const inputArchivo = document.getElementById('foto_archivo');
        const btnIniciar = document.getElementById('btnIniciarCamara');
        const btnCapturar = document.getElementById('btnCapturar');
        const btnRetomar = document.getElementById('btnRetomar');
        const btnSubir = document.getElementById('btnSubirArchivo');
        let streamActivo = null;

        function detenerCamara() {
            if (streamActivo) {
                streamActivo.getTracks().forEach(track => track.stop());
                streamActivo = null;
            }
            video.style.display = 'none';
        }

        // Máquina de estados: solo un botón visible a la vez
        function estadoCamara(estado) {
            btnIniciar.classList.toggle('d-none', estado !== 'iniciar');
            btnCapturar.classList.toggle('d-none', estado !== 'capturar');
            btnRetomar.classList.toggle('d-none', estado !== 'retomar');
            btnSubir.classList.toggle('d-none', estado === 'capturar');
        }

        function mostrarEnCirculo(elemento) {
            // 'placeholder' | 'video' | 'captura' | 'archivo'
            placeholder.classList.toggle('d-none', elemento !== 'placeholder');
            video.style.display = (elemento === 'video') ? 'block' : 'none';
            fotoPreview.classList.toggle('d-none', elemento !== 'captura');
            previewArchivo.classList.toggle('d-none', elemento !== 'archivo');
        }

        btnIniciar.addEventListener('click', async () => {
            try {
                detenerCamara();

                // 1. UI primero: mostrar el círculo y el botón de capturar de inmediato
                video.muted = true;
                mostrarEnCirculo('video');
                estadoCamara('capturar');

                // 2. Pedir el stream y reproducir
                streamActivo = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user'
                    }
                });
                video.srcObject = streamActivo;
                video.play().catch(function(e) {
                    console.warn('play() interrumpido:', e);
                });

                // 3. Limpiar la fuente contraria (defensivo)
                if (fotoBase64) fotoBase64.value = '';
                if (inputArchivo) inputArchivo.value = '';
            } catch (err) {
                console.error('Error de cámara:', err);
                mostrarEnCirculo('placeholder');
                estadoCamara('iniciar');
                Swal.fire('Sin acceso', 'No se pudo acceder a la cámara.', 'error');
            }
        });

        btnCapturar.addEventListener('click', () => {
            const size = 640;
            canvas.width = size;
            canvas.height = size;
            const ctx = canvas.getContext('2d');
            const vw = video.videoWidth,
                vh = video.videoHeight;
            const minDim = Math.min(vw, vh);
            ctx.drawImage(video, (vw - minDim) / 2, (vh - minDim) / 2, minDim, minDim, 0, 0, size, size);
            const imageDataUrl = canvas.toDataURL('image/jpeg', 0.8);
            fotoBase64.value = imageDataUrl;
            inputArchivo.value = '';
            detenerCamara();
            fotoPreview.src = imageDataUrl;
            mostrarEnCirculo('captura');
            estadoCamara('retomar');
        });

        btnRetomar.addEventListener('click', () => {
            fotoBase64.value = '';
            btnIniciar.click();
        });

        btnSubir.addEventListener('click', () => {
            detenerCamara();
            inputArchivo.click();
        });

        inputArchivo.addEventListener('change', function() {
            fotoBase64.value = '';
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewArchivo.src = e.target.result;
                    mostrarEnCirculo('archivo');
                };
                reader.readAsDataURL(this.files[0]);
            }
            estadoCamara('iniciar');
        });

        // ===== 4. Envío del formulario =====
        document.getElementById('formularioPersonal').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            const btnSubmit = document.getElementById('btnSubmit');

            if (document.getElementById('select_turno').value !== 'mixto') {
                document.querySelectorAll('#dias_mixto_container input[type="radio"]').forEach(r => r.required = false);
                document.getElementById('turno_nomina').required = false;
            }

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulario Incompleto',
                    text: 'Revise los campos en rojo.'
                });
                return;
            }

            // Fuente única: debe existir una de las dos
            if (!fotoBase64.value && !inputArchivo.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Falta la fotografía',
                    text: 'Tome una foto con la cámara o suba un archivo.'
                });
                mostrarEnCirculo('placeholder');
                estadoCamara('iniciar');
                return;
            }

            detenerCamara();

            const formData = new FormData(form);
            if (fotoBase64.value) {
                formData.delete('foto'); // gana la captura
            } else {
                formData.delete('foto_base64'); // gana el archivo
            }

            const originalBtnText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';
            btnSubmit.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? '¡Registro Exitoso!' : 'Atención',
                    text: data.message,
                    showCancelButton: data.success,
                    confirmButtonText: data.success ? 'Ir a la lista' : 'Aceptar',
                    cancelButtonText: 'Registrar otro',
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (data.success) {
                        if (result.isConfirmed) {
                            window.location.href = '/personal';
                        } else {
                            window.location.reload();
                        }
                    } else {
                        btnSubmit.innerHTML = originalBtnText;
                        btnSubmit.disabled = false;
                    }
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo conectar al servidor'
                });
                btnSubmit.innerHTML = originalBtnText;
                btnSubmit.disabled = false;
            }
        });
    });
</script>