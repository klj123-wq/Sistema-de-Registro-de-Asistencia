<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-database-down me-2 text-primary"></i>Respaldo de Información
            </h3>
            <p class="text-muted small mb-0">Descargue una copia de seguridad de la base de datos del sistema</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4 text-center">
            <div class="mb-4">
                <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
                    <i class="bi bi-shield-check text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold text-dark">Generar copia de seguridad</h5>
                <p class="text-muted small">El archivo SQL generado contendrá todos los registros actuales del sistema. Guárdelo en un lugar seguro.</p>
            </div>
            
            <button type="button" id="btnRespaldo" class="btn btn-success px-4 py-2 fw-semibold rounded-3 shadow-sm">
                <i class="bi bi-cloud-download me-2"></i> Descargar Respaldo Ahora
            </button>
        </div>
    </div>
</div>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
document.getElementById('btnRespaldo').addEventListener('click', async function() {
    const btn = this;
    const originalText = btn.innerHTML;
    
    // Deshabilitar botón para evitar clics múltiples
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Generando...';

    try {
        // Petición GET a la ruta
        const response = await fetch('/sistema/crear_respaldo', {
            method: 'GET'
        });
        
        const data = await response.json();

        Swal.fire({
            icon: data.success ? 'success' : 'error',
            title: data.success ? '¡Respaldo Exitoso!' : 'Atención',
            text: data.message,
            confirmButtonColor: '#198754'
        }).then((result) => {
            // Si quieres redirigir o recargar al terminar, puedes hacerlo aquí
            // if (data.success) { window.location.reload(); }
        });

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo conectar con el servidor para generar el respaldo.',
            confirmButtonColor: '#dc3545'
        });
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});
</script>