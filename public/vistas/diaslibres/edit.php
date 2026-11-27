<?php
/** @var array $data */
use libs\CSRF;

// Lógica de días recurrentes: si se repite cada año, mostramos las fechas
// del año actual para editarlas cómodamente
if (!$data["ano"]) {
    $ano_actual = date("Y");
    $fecha = explode("-", $data["fecha_inicio"]);
    $fecha_fin = explode("-", $data["fecha_fin"]);

    $data["fecha_inicio"] = $ano_actual . "-" . $fecha[1] . "-" . $fecha[2];
    $data["fecha_fin"] = $ano_actual . "-" . $fecha_fin[1] . "-" . $fecha_fin[2];
}
?>

<style>
    /* Tarjetas de recurrencia: selección en verde */
    .opcion-recurrencia {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .opcion-recurrencia:has(input:checked) {
        background-color: #f0fdf4 !important;
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }

    /* Botones a ancho completo en móvil */
    @media (max-width: 480px) {
        form .d-flex.justify-content-end.gap-2 {
            flex-direction: column;
        }
        form .d-flex.justify-content-end.gap-2 .btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid pt-1 pb-3">
    <div class="text-center mb-3">
        <h3 class="fw-bold text-dark mb-1">Editar Día Libre</h3>
        <p class="text-muted small mb-0">Modifique los detalles del evento o feriado seleccionado.</p>
    </div>

    <div class="card shadow-sm border-0 rounded-3" style="max-width: 800px; margin: 0 auto;">
        <div class="card-body p-4 p-md-4 bg-white">
            <form id="formDiaLibre" action="/diaslibres/<?= $data["id"] ?>" method="post" novalidate>
                <input type="hidden" name="CSRF" value="<?php echo CSRF::generar() ?>">

                <h6 class="fw-bold text-success mb-3 border-bottom pb-2">Datos del Día Libre</h6>

                <div class="mb-3">
                    <label for="nombre" class="form-label small fw-bold text-secondary mb-1">Descripción del Evento</label>
                    <input required type="text" class="form-control bg-light border-0" name="nombre" id="nombre"
                           value="<?= htmlspecialchars($data["nombre"]) ?>" placeholder="Ej: Día del Trabajador, Navidad..." minlength="5" maxlength="150">
                    <div class="invalid-feedback" style="font-size: 0.75rem;">La descripción debe tener entre 5 y 150 caracteres.</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="fecha_inicio" class="form-label small fw-bold text-secondary mb-1">Fecha de Inicio</label>
                        <input required type="date" class="form-control bg-light border-0" name="fecha_inicio" id="fecha_inicio"
                               value="<?= htmlspecialchars($data["fecha_inicio"]) ?>">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Seleccione una fecha válida.</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="fecha_fin" class="form-label small fw-bold text-secondary mb-1">Fecha de Fin</label>
                        <input required type="date" class="form-control bg-light border-0" name="fecha_fin" id="fecha_fin"
                               value="<?= htmlspecialchars($data["fecha_fin"]) ?>">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">No puede ser anterior a la fecha de inicio.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary mb-2">¿Se repite cada año?</label>
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <label class="flex-fill d-flex align-items-center gap-2 bg-white rounded-3 px-3 py-2 border opcion-recurrencia" for="repiteSi">
                            <input class="form-check-input mt-0" type="radio" name="ano" value="repitesi" id="repiteSi" <?php if (!$data["ano"]) echo "checked" ?> required>
                            <i class="bi bi-arrow-repeat text-success"></i>
                            <span class="small fw-medium">Sí, repetir</span>
                        </label>
                        <label class="flex-fill d-flex align-items-center gap-2 bg-white rounded-3 px-3 py-2 border opcion-recurrencia" for="repiteNo">
                            <input class="form-check-input mt-0" type="radio" name="ano" value="repiteno" id="repiteNo" <?php if ($data["ano"]) echo "checked" ?> required>
                            <i class="bi bi-calendar-x text-danger"></i>
                            <span class="small fw-medium">No, solo este año</span>
                        </label>
                    </div>
                </div>

                <hr class="text-muted opacity-10 my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="/diaslibres" class="btn btn-light border px-4 fw-semibold text-secondary rounded-3">Cancelar</a>
                    <button type="submit" id="btnSubmit" class="btn btn-success px-4 fw-bold shadow-sm rounded-3">
                        <i class="bi bi-save2 me-1"></i> Actualizar Día Libre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const form = document.getElementById('formDiaLibre');
    const inputNombre = document.getElementById('nombre');
    const inputInicio = document.getElementById('fecha_inicio');
    const inputFin = document.getElementById('fecha_fin');

    // El fin no puede ser anterior al inicio
    inputFin.min = inputInicio.value;
    inputInicio.addEventListener('change', function() {
        inputFin.min = this.value;
        if (inputFin.value && inputFin.value < this.value) {
            inputFin.value = '';
        }
    });

    // Validación en vivo del nombre
    inputNombre.addEventListener('input', function() {
        const ok = this.value.trim().length >= 5;
        this.classList.toggle('is-valid', ok);
        this.classList.toggle('is-invalid', !ok && this.value.length > 0);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const nombre = inputNombre.value.trim();

        if (nombre.length < 5) {
            Swal.fire({ icon: 'warning', title: 'Descripción corta', text: 'Mínimo 5 caracteres.', confirmButtonColor: '#059669' });
            inputNombre.focus();
            return;
        }
        if (!inputInicio.value || !inputFin.value) {
            Swal.fire({ icon: 'warning', title: 'Fechas incompletas', text: 'Seleccione ambas fechas.', confirmButtonColor: '#059669' });
            return;
        }
        if (inputFin.value < inputInicio.value) {
            Swal.fire({ icon: 'warning', title: 'Horario inválido', text: 'La fecha de fin no puede ser anterior a la de inicio.', confirmButtonColor: '#059669' });
            inputFin.focus();
            return;
        }

        const btn = document.getElementById('btnSubmit');
        const original = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';
        btn.disabled = true;

        fetch(form.action, { method: 'POST', body: new FormData(form) })
            .then(r => r.json())
            .then(data => {
                Swal.fire({
                    icon: data.icon || (data.success ? 'success' : 'error'),
                    title: data.success ? '¡Día Libre Actualizado!' : 'Atención',
                    text: data.message,
                    confirmButtonColor: data.success ? '#198754' : '#059669'
                }).then(() => {
                    if (data.success) {
                        window.location.href = '/diaslibres';
                    } else {
                        btn.innerHTML = original;
                        btn.disabled = false;
                    }
                });
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar al servidor.' });
                btn.innerHTML = original;
                btn.disabled = false;
            });
    });
});
</script>