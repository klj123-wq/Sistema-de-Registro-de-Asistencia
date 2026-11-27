<?php

use libs\CSRF;

/** @var array $data */
?>

<div class="container-fluid pt-1 pb-3">
    <div class="text-center mb-3">
        <h3 class="fw-bold text-dark mb-1">Actualizar Permiso</h3>
        <p class="text-muted small mb-0">Modifique los datos del permiso. Los campos con <span class="text-danger">*</span> son requeridos.</p>
    </div>

    <div class="card shadow-sm border-0 rounded-3" style="max-width: 800px; margin: 0 auto;">
        <div class="card-body p-4 p-md-4 bg-white">
            <form action="/permisos/<?= $data["id"] ?>" method="post" id="formEditarPermiso" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="CSRF" value="<?php echo CSRF::generar() ?>">
                <input type="hidden" name="id_persona" value="<?= htmlspecialchars($data['id_persona'] ?? '') ?>">

                <!-- ========== 1. DATOS DEL PERMISO ========== -->
                <h6 class="fw-bold text-success mb-3 border-bottom pb-2">1. Datos del Permiso</h6>

                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label for="descripcion" class="form-label small fw-bold text-secondary mb-1">Motivo del Permiso</label>
                        <input required type="text" class="form-control bg-light border-0" name="descripcion" id="descripcion" maxlength="100"
                            value="<?= htmlspecialchars($data['descripcion'] ?? '') ?>"
                            placeholder="Ej. Reposo médico, diligencia personal...">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Ingrese el motivo (mínimo 3 caracteres).</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="fecha_inicio" class="form-label small fw-bold text-secondary mb-1">Fecha de Inicio</label>
                        <input required value="<?= htmlspecialchars($data['fecha_inicio'] ?? '') ?>" type="date" class="form-control bg-light border-0" name="fecha_inicio" id="fecha_inicio">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Seleccione la fecha de inicio.</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="fecha_fin" class="form-label small fw-bold text-secondary mb-1">Fecha de Fin</label>
                        <input required value="<?= htmlspecialchars($data['fecha_fin'] ?? '') ?>" type="date" class="form-control bg-light border-0" name="fecha_fin" id="fecha_fin">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">No puede ser anterior a la fecha de inicio.</div>
                    </div>
                </div>

                <!-- ========== 2. EVIDENCIA (OPCIONAL) ========== -->
                <h6 class="fw-bold text-success mb-3 border-bottom pb-2">2. Evidencia <span class="text-muted fw-normal small">(Opcional)</span></h6>

                <div class="bg-light p-3 rounded-3 border-0">
                    <!-- Evidencia actual (si existe): imágenes se ven como las fotos de perfil -->
                    <?php if (!empty($data['evidencia'])):
                        $es_imagen = preg_match('/\.(jpe?g|png)$/i', $data['evidencia']);
                        $evidencia_src = 'imagenes.php?imagen=' . urlencode($data['evidencia']);
                    ?>
                        <?php if ($es_imagen): ?>
                            <div class="text-center mb-3">
                                <p class="small fw-bold text-secondary mb-1">Evidencia actual:</p>
                                <img src="<?= $evidencia_src ?>" alt="Evidencia actual" class="rounded-3 shadow-sm" style="width: min(320px, 85vw);">
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <a href="<?= htmlspecialchars($data['evidencia']) ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> Ver evidencia actual (PDF)
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <ul class="nav nav-pills mb-3 gap-2" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="btn btn-sm btn-outline-success active rounded-pill px-4" id="pills-archivo-tab" data-bs-toggle="pill" data-bs-target="#pills-archivo" type="button" role="tab">
                                <i class="bi bi-cloud-upload me-1"></i> Subir Archivo
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="btn btn-sm btn-outline-success rounded-pill px-4" id="pills-camara-tab" data-bs-toggle="pill" data-bs-target="#pills-camara" type="button" role="tab">
                                <i class="bi bi-camera me-1"></i> Tomar Foto
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <!-- TAB 1: ARCHIVO -->
                        <div class="tab-pane fade show active" id="pills-archivo" role="tabpanel">
                            <label for="evidencia_archivo" class="form-label small fw-bold text-secondary mb-1">Archivo (Máx. 5MB | JPG, PNG, PDF)</label>
                            <input type="file" class="form-control bg-white" name="evidencia_archivo" id="evidencia_archivo" accept=".jpg, .jpeg, .png, .pdf">
                            <div class="invalid-feedback" style="font-size: 0.75rem;">El archivo supera el límite de 5MB.</div>
                        </div>

                        <!-- TAB 2: CÁMARA -->
                        <div class="tab-pane fade" id="pills-camara" role="tabpanel">
                            <div class="text-center">
                                <div id="camaraContenedor" class="mb-3 position-relative overflow-hidden rounded-3 bg-dark mx-auto" style="display: none; width: min(320px, 85vw); aspect-ratio: 3 / 4;">
                                    <video id="videoElement" autoplay playsinline muted style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;"></video>
                                    <canvas id="canvasElement" class="d-none"></canvas>
                                </div>

                                <img id="fotoPreview" class="rounded-3 shadow-sm mb-3 d-none mx-auto" style="width: min(320px, 85vw);">

                                <input type="hidden" name="foto_base64" id="foto_base64">

                                <button type="button" id="btnIniciarCamara" class="btn btn-sm btn-success rounded-pill px-3"><i class="bi bi-camera me-1"></i> Activar Cámara</button>
                                <button type="button" id="btnCapturar" class="btn btn-sm btn-success rounded-pill px-3 d-none">Capturar Foto</button>
                                <button type="button" id="btnRetomar" class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-none">Retomar</button>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted small text-center mt-3 mb-0" style="font-size: 0.72rem;">
                        Si no adjunta una nueva evidencia, se conservará la actual.
                    </p>
                </div>

                <hr class="text-muted opacity-10 my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="/permisos/<?= $data['id_persona'] ?>" class="btn btn-light border px-4 fw-semibold text-secondary rounded-3">Cancelar</a>
                    <button type="submit" id="btnSubmit" class="btn btn-success px-4 fw-bold shadow-sm rounded-3">
                        <i class="bi bi-save2 me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('formEditarPermiso');
        const inputDesc = document.getElementById('descripcion');
        const inputInicio = document.getElementById('fecha_inicio');
        const inputFin = document.getElementById('fecha_fin');
        const inputArchivo = document.getElementById('evidencia_archivo');

        // ===== 1. VALIDACIONES EN TIEMPO REAL =====
        inputDesc.addEventListener('input', function() {
            const ok = this.value.trim().length >= 3;
            this.classList.toggle('is-valid', ok);
            this.classList.toggle('is-invalid', !ok);
        });

        function validarFechas() {
            let esValido = true;

            const iniOk = inputInicio.value !== '';
            inputInicio.classList.toggle('is-valid', iniOk);
            inputInicio.classList.toggle('is-invalid', !iniOk);
            if (!iniOk) esValido = false;

            let finOk = inputFin.value !== '';
            if (finOk && iniOk && inputFin.value < inputInicio.value) finOk = false;
            inputFin.classList.toggle('is-valid', finOk);
            inputFin.classList.toggle('is-invalid', !finOk);
            if (!finOk) esValido = false;

            return esValido;
        }

        inputInicio.addEventListener('change', function() {
            inputFin.min = this.value;
            validarFechas();
        });
        inputFin.addEventListener('change', validarFechas);

        inputArchivo.addEventListener('change', function() {
            if (this.files.length > 0) {
                const sizeInMB = this.files[0].size / (1024 * 1024);
                if (sizeInMB > 5) {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    this.value = '';
                    Swal.fire({
                        icon: 'error',
                        title: 'Archivo muy pesado',
                        text: 'El archivo supera el límite de 5 MB.',
                        confirmButtonColor: '#059669'
                    });
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    fotoBase64.value = '';
                    fotoPreview.classList.add('d-none');
                }
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });

        // ===== 2. CÁMARA (documento vertical 3:4, fuente única) =====
        const video = document.getElementById('videoElement');
        const canvas = document.getElementById('canvasElement');
        const camaraContenedor = document.getElementById('camaraContenedor');
        const fotoPreview = document.getElementById('fotoPreview');
        const fotoBase64 = document.getElementById('foto_base64');
        const btnIniciar = document.getElementById('btnIniciarCamara');
        const btnCapturar = document.getElementById('btnCapturar');
        const btnRetomar = document.getElementById('btnRetomar');
        let streamActivo = null;

        function detenerCamara() {
            if (streamActivo) {
                streamActivo.getTracks().forEach(track => track.stop());
                streamActivo = null;
            }
            video.style.display = 'none';
        }

        function estadoCamara(estado) {
            btnIniciar.classList.toggle('d-none', estado !== 'iniciar');
            btnCapturar.classList.toggle('d-none', estado !== 'capturar');
            btnRetomar.classList.toggle('d-none', estado !== 'retomar');
        }

        btnIniciar.addEventListener('click', async () => {
            try {
                detenerCamara();
                video.muted = true;
                camaraContenedor.style.display = 'block';
                video.style.display = 'block';
                fotoPreview.classList.add('d-none');
                estadoCamara('capturar');

                streamActivo = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment'
                    }
                });
                video.srcObject = streamActivo;
                video.play().catch(function(e) {
                    console.warn('play() interrumpido:', e);
                });

                fotoBase64.value = '';
                inputArchivo.value = '';
            } catch (err) {
                console.error('Error de cámara:', err);
                camaraContenedor.style.display = 'none';
                estadoCamara('iniciar');
                Swal.fire('Sin acceso', 'No se pudo acceder a la cámara.', 'error');
            }
        });

        btnCapturar.addEventListener('click', () => {
            // Recorte vertical 3:4 centrado (formato documento)
            const vw = video.videoWidth,
                vh = video.videoHeight;
            const ratio = 3 / 4;
            let cropW, cropH, sx, sy;
            if (vw / vh > ratio) {
                cropH = vh;
                cropW = vh * ratio;
                sx = (vw - cropW) / 2;
                sy = 0;
            } else {
                cropW = vw;
                cropH = vw / ratio;
                sx = 0;
                sy = (vh - cropH) / 2;
            }

            canvas.width = 720;
            canvas.height = 960;
            const context = canvas.getContext('2d');
            context.drawImage(video, sx, sy, cropW, cropH, 0, 0, 720, 960);

            const imageDataUrl = canvas.toDataURL('image/jpeg', 0.8);
            fotoBase64.value = imageDataUrl;
            inputArchivo.value = '';

            fotoPreview.src = imageDataUrl;
            fotoPreview.classList.remove('d-none');
            detenerCamara();
            camaraContenedor.style.display = 'none';
            estadoCamara('retomar');
        });

        btnRetomar.addEventListener('click', () => {
            fotoBase64.value = '';
            btnIniciar.click();
        });

        document.getElementById('pills-archivo-tab').addEventListener('shown.bs.tab', () => {
            detenerCamara();
            camaraContenedor.style.display = 'none';
            if (fotoBase64.value) {
                fotoPreview.classList.remove('d-none');
                estadoCamara('retomar');
            } else {
                fotoPreview.classList.add('d-none');
                estadoCamara('iniciar');
            }
        });

        document.getElementById('pills-camara-tab').addEventListener('shown.bs.tab', () => {
            if (fotoBase64.value) {
                fotoPreview.classList.remove('d-none');
                estadoCamara('retomar');
            } else {
                estadoCamara('iniciar');
            }
        });

        // ===== 3. ENVÍO CON FETCH (ya no miente sobre el resultado) =====
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            inputDesc.dispatchEvent(new Event('input'));
            validarFechas();

            if (form.querySelectorAll('.is-invalid').length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos incompletos',
                    text: 'Revise los campos marcados en rojo antes de guardar.',
                    confirmButtonColor: '#059669'
                });
                return;
            }

            detenerCamara();

            const btnSubmit = document.getElementById('btnSubmit');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';
            btnSubmit.disabled = true;

            const formData = new FormData(this);
            // Solo viaja la evidencia nueva si el usuario eligió una
            if (fotoBase64.value) {
                formData.delete('evidencia_archivo');
            } else if (inputArchivo.value) {
                formData.delete('foto_base64');
            } else {
                formData.delete('evidencia_archivo');
                formData.delete('foto_base64');
            }

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                Swal.fire({
                    icon: data.icon || (data.success ? 'success' : 'error'),
                    title: data.success ? '¡Permiso Actualizado!' : 'Atención',
                    text: data.message,
                    confirmButtonColor: data.success ? '#198754' : '#059669'
                }).then(() => {
                    if (data.success) {
                        window.location.href = `/permisos/<?= $data['id_persona'] ?>`;
                    } else {
                        btnSubmit.innerHTML = originalText;
                        btnSubmit.disabled = false;
                    }
                });
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo procesar la solicitud con el servidor.'
                });
                btnSubmit.innerHTML = originalText;
                btnSubmit.disabled = false;
            }
        });
    });
</script>