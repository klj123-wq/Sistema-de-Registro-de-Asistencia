<?php
/** @var array $data */
use libs\CSRF;
?>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h2 class="fw-bold text-dark">Registro de Asistencia</h2>
            <p class="text-muted">Gestione y cargue de forma manual las entradas y salidas del personal institucional</p>
        </div>
        <div>
            <a href="/asistencia" class="btn btn-light border fw-semibold text-secondary shadow-sm">
                <i class="bi bi-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 w-100">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-clipboard-check me-2"></i> Formulario de Asistencia Manual de <?= $data["nombre"]." ". $data["apellido"] ?>
            </h5>
        </div>
        
        <div class="card-body p-4">
            <form id="formCrearAsistencia" action="/asistencia/<?= $data["id"] ?>" method="POST">
                <input type="hidden" name="CSRF" value="<?php echo CSRF::generar() ?>">

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">
                            <i class="bi bi-calendar3 text-muted me-1"></i> Fecha
                        </label>
                        <input required type="date" class="form-control px-3" name="fecha" value="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">
                            <i class="bi bi-box-arrow-left text-success me-1"></i> Hora de Entrada
                        </label>
                        <input required type="time" class="form-control px-3" name="hora_entrada">
                    </div>
                    
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">
                            <i class="bi bi-box-arrow-left text-danger me-1"></i> Hora de Salida
                        </label>
                        <input type="time" class="form-control px-3" name="hora_salida">
                    </div>
                </div>

                <hr class="text-muted opacity-25 my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="/asistencia" class="btn btn-light border px-4 fw-semibold text-secondary">
                        Cancelar
                    </a>
                    <button type="submit" id="btnSubmitCrear" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="bi bi-check-circle me-2"></i> Registrar Asistencia
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
document.getElementById('formCrearAsistencia').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnSubmitCrear');
    const original = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';
    btn.disabled = true;
    
    try {
        const res = await fetch(this.action, { method: 'POST', body: new FormData(this) });
        const data = await res.json();
        
        Swal.fire({
            icon: data.icon || (data.success ? 'success' : 'error'),
            title: data.success ? 'Éxito' : 'Error',
            text: data.message,
            confirmButtonColor: data.success ? '#198754' : '#d33'
        }).then(() => {
            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            } else {
                btn.innerHTML = original;
                btn.disabled = false;
            }
        });
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo conectar con el servidor' });
        btn.innerHTML = original;
        btn.disabled = false;
    }
});
</script>