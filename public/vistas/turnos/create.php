<?php

use libs\CSRF;

?>

<style>
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
        <h3 class="fw-bold text-dark mb-1">Registrar Turno</h3>
        <p class="text-muted small mb-0">Complete los campos para crear una nueva configuración de horario.</p>
    </div>

    <div class="card shadow-sm border-0 rounded-3" style="max-width: 640px; margin: 0 auto;">
        <div class="card-body p-4 p-md-4 bg-white">
            <form id="formNuevoTurno" action="/turnos" method="post" novalidate>
                <input type="hidden" name="CSRF" value="<?php echo CSRF::generar() ?>">

                <h6 class="fw-bold text-success mb-3 border-bottom pb-2">Datos del Turno</h6>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="nombre" class="form-label small fw-bold text-secondary mb-1">Nombre del Turno</label>
                        <select required name="nombre" id="nombre" class="form-select bg-light border-0">
                            <option value="" disabled selected hidden>Seleccione una opción</option>
                            <option value="mañana">Mañana</option>
                            <option value="tarde">Tarde</option>
                        </select>
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Seleccione el turno.</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="cargo" class="form-label small fw-bold text-secondary mb-1">Cargo Asignado</label>
                        <select required name="cargo" id="cargo" class="form-select bg-light border-0">
                            <option value="" disabled selected hidden>Seleccione una opción</option>
                            <option value="docente">Docente</option>
                            <option value="obrero">Obrero</option>
                            <option value="administrativo">Administrativo</option>
                            <option value="cocinero">Cocinero</option>
                        </select>
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Seleccione el cargo.</div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="hora_entrada" class="form-label small fw-bold text-secondary mb-1">Hora de Inicio</label>
                        <input required type="time" name="hora_entrada" id="hora_entrada" class="form-control bg-light border-0">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Hora requerida.</div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="hora_salida" class="form-label small fw-bold text-secondary mb-1">Hora de Finalización</label>
                        <input required type="time" name="hora_salida" id="hora_salida" class="form-control bg-light border-0">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Debe ser mayor que la hora de inicio.</div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="minutos_tolerancia" class="form-label small fw-bold text-secondary mb-1">Minutos de Tolerancia</label>
                        <input required type="number" name="minutos_tolerancia" id="minutos_tolerancia" class="form-control bg-light border-0"
                            placeholder="Ej. 15" min="0" max="60"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2)">
                        <div class="invalid-feedback" style="font-size: 0.75rem;">Entre 0 y 60 minutos.</div>
                    </div>
                </div>

                <hr class="text-muted opacity-10 my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="/turnos" class="btn btn-light border px-4 fw-semibold text-secondary rounded-3">Cancelar</a>
                    <button type="submit" id="btnSubmit" class="btn btn-success px-4 fw-bold shadow-sm rounded-3">
                        <i class="bi bi-plus-circle me-1"></i> Registrar Turno
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const formTurno = document.getElementById('formNuevoTurno');
        const btnSubmit = document.getElementById('btnSubmit');

        formTurno.addEventListener('submit', function(e) {
            e.preventDefault();

            // ✅ Validación 1: campos requeridos
            if (!formTurno.checkValidity()) {
                formTurno.classList.add('was-validated');
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulario incompleto',
                    text: 'Revise los campos marcados en rojo.',
                    confirmButtonColor: '#059669'
                });
                return;
            }

            // ✅ Validación 2: la hora de salida debe ser MAYOR que la de entrada
            const hEntrada = formTurno.hora_entrada.value;
            const hSalida = formTurno.hora_salida.value;
            if (hEntrada && hSalida && hSalida <= hEntrada) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Horario inválido',
                    text: 'La hora de finalización debe ser mayor que la hora de inicio.',
                    confirmButtonColor: '#059669'
                });
                document.getElementById('hora_salida').focus();
                return;
            }

            const formData = new FormData(formTurno);
            const originalBtnText = btnSubmit.innerHTML;

            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Guardando...';
            btnSubmit.disabled = true;

            fetch(formTurno.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        icon: data.icon || (data.success ? 'success' : 'error'),
                        title: data.success ? '¡Excelente!' : 'Atención',
                        text: data.message,
                        showCancelButton: data.success,
                        confirmButtonText: data.success ? 'Ir a lista de turnos' : 'Entendido',
                        cancelButtonText: 'Registrar otro',
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (data.success) {
                            if (result.isConfirmed) {
                                window.location.href = '/turnos';
                            } else {
                                formTurno.reset();
                                formTurno.classList.remove('was-validated');
                                btnSubmit.innerHTML = originalBtnText;
                                btnSubmit.disabled = false;
                            }
                        } else {
                            btnSubmit.innerHTML = originalBtnText;
                            btnSubmit.disabled = false;
                        }
                    });
                })
                .catch(error => {
                    console.error("Error en Fetch:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo comunicar con el servidor.'
                    });
                    btnSubmit.innerHTML = originalBtnText;
                    btnSubmit.disabled = false;
                });
        });
    });
</script>