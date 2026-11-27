<?php 
/** @var array $data */

use libs\CSRF;

?>

<div class="container-fluid py-5 px-4 fade-in">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4">
                
                <div class="card-header bg-white border-bottom-0 pt-5 pb-3 px-4 text-center">
                    <div class="d-inline-block bg-primary bg-opacity-10 p-3 rounded-circle mb-3 shadow-sm">
                        <i class="bi bi-clock-history text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">Edición de Asistencia</h3>
                    <p class="text-muted mt-1 small">Modificación manual de registros de jornada</p>
                </div>

                <div class="card-body p-4 p-md-5 pt-0">
                    <form id="formEditarAsistencia" class="needs-validation" action="/asistencia/<?= $data["id"] ?>/update" method="post" novalidate>
                     

                    <input type="hidden" name="CSRF" value="<?php echo CSRF::generar() ?>">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label text-muted fw-semibold">Fecha de Registro</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-date text-secondary"></i></span>
                                    <input required value="<?= $data["fecha"] ?>" type="date" name="fecha" class="form-control form-control-lg bg-light border-0">
                                    <div class="invalid-feedback">Debe especificar una fecha válida.</div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label text-muted fw-semibold">Hora de Entrada</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-box-arrow-in-right text-success"></i></span>
                                    <input required value="<?= $data["hora_entrada"] ?>" type="time" name="hora_entrada" class="form-control form-control-lg bg-light border-0">
                                    <div class="invalid-feedback">La hora de entrada es obligatoria.</div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label text-muted fw-semibold">Hora de Salida</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-box-arrow-left text-danger"></i></span>
                                    <input value="<?= $data["hora_salida"] ?>" type="time" name="hora_salida" class="form-control form-control-lg bg-light border-0">
                                </div>
                                <div class="form-text small mt-1">Opcional si el turno no ha concluido.</div>
                            </div>
                        </div>

                        <hr class="my-5 text-muted opacity-25">

                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-lg btn-outline-secondary rounded-pill px-4" onclick="history.back()">
                                <i class="bi bi-x-circle me-2"></i> Cancelar
                            </button>
                            <button type="submit" id="btnSubmitAsistencia" class="btn btn-lg btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                <i class="bi bi-save2-fill me-2"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fade-in { animation: fadeIn 0.4s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
document.getElementById('formEditarAsistencia').addEventListener('submit', async function(e) {
    e.preventDefault(); // Evitamos que la página se recargue
    
    const form = this;
    const btnSubmit = document.getElementById('btnSubmitAsistencia');

    // 1. Validación Front-end de Bootstrap
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return; // Detiene la ejecución si hay campos vacíos
    }

    // 2. Preparar datos y poner el botón en estado "Cargando..."
    const formData = new FormData(form);
    const originalBtnText = btnSubmit.innerHTML;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Actualizando...';
    btnSubmit.disabled = true;

    try {
        // 3. Enviar datos al controlador
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData
        });

        // 4. Leer la respuesta JSON (El array $this->success() o $this->error() de Dylan)
        const data = await response.json();

        // 5. Disparar el SweetAlert con la respuesta del backend
        Swal.fire({
            icon: data.icon || (data.success ? 'success' : 'error'),
            title: data.success ? '¡Actualizado!' : 'Atención',
            text: data.message,
            confirmButtonColor: data.success ? '#198754' : '#0d6efd'
        }).then(() => {
            if (data.success) {
                // Si guardó bien, regresamos a la pantalla de donde vino (Kardex o Lista)
                history.back(); 
            } else {
                // Si el backend devolvió error, liberamos el botón
                btnSubmit.innerHTML = originalBtnText;
                btnSubmit.disabled = false;
            }
        });

    } catch (error) {
        // En caso de que el servidor falle o se caiga el internet
        console.error("Error en Fetch:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo establecer comunicación con el servidor.',
            confirmButtonColor: '#d33'
        });
        btnSubmit.innerHTML = originalBtnText;
        btnSubmit.disabled = false;
    }
});
</script>