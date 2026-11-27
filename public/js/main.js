/**
 * SISTEMA SRA - Pedro Curiel Ramírez
 */

// ==========================================
// 1. IDIOMA ESPAÑOL PARA DATATABLES
// ==========================================
const idiomaEspanol = {
    "processing": "Procesando...",
    "lengthMenu": "Ver _MENU_ registros",
    "zeroRecords": "No se encontraron coincidencias",
    "emptyTable": "No hay registros disponibles",
    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
    "search": "Buscar:",
    "paginate": {
        "first": "Primero",
        "last": "Último",
        "next": "Siguiente",
        "previous": "Anterior"
    }
};

// ==========================================
// 2. RELOJ EN TIEMPO REAL
// ==========================================
function initReloj() {
    function actualizar() {
        const ahora = new Date();
        const reloj = document.getElementById('reloj');
        const fecha = document.getElementById('fecha');
        if (reloj) reloj.textContent = ahora.toLocaleTimeString('es-VE', { hour12: true });
        if (fecha) fecha.textContent = ahora.toLocaleDateString('es-VE', { weekday: 'long', day: 'numeric', month: 'long' });
    }
    if (window.relojInterval) clearInterval(window.relojInterval);
    window.relojInterval = setInterval(actualizar, 1000);
    actualizar();
}

// ==========================================
// 3. INICIALIZAR TABLAS DE DATOS
// ==========================================
function initTablaPersonal() {
    const tabla = $('#tablaPersonal');
    if (tabla.length && !$.fn.DataTable.isDataTable('#tablaPersonal')) {
        tabla.DataTable({
            language: idiomaEspanol,
            pageLength: 5,
            responsive: true,
            order: [[1, "asc"]],
            dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row mt-3"<"col-md-5"i><"col-md-7"p>>'
        });
    }
}

function initTablaAsistenciaInicio() {
    const tabla = $('#tablaAsistenciaInicio');
    if (tabla.length && !$.fn.DataTable.isDataTable('#tablaAsistenciaInicio')) {
        tabla.DataTable({
            language: idiomaEspanol,
            pageLength: 5,
            lengthChange: false,
            order: [[0, "desc"]]
        });
    }
}

function initTablaAsistenciaAdmin() {
    const tabla = $('#tablaAsistenciaAdmin');
    if (tabla.length && !$.fn.DataTable.isDataTable('#tablaAsistenciaAdmin')) {
        tabla.DataTable({
            language: idiomaEspanol,
            pageLength: 10,
            responsive: true,
            order: [[2, "desc"]]
        });
    }
}

// ==========================================
// 4. CONFIRMACIÓN REUTILIZABLE
// ==========================================
function confirmarAccion(mensaje, callback) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}

// ==========================================
// 5. CONFIGURAR CIERRE DE SESIÓN
// ==========================================
function configurarCierreSesion() {
    const botonesSalir = document.querySelectorAll('a[href="/close"]');
    
    botonesSalir.forEach(function(boton) {
        if (boton.dataset.cierreConfigurado === 'true') return;
        boton.dataset.cierreConfigurado = 'true';
        
        boton.addEventListener('click', function(evento) {
            evento.preventDefault();
            evento.stopPropagation();
            
            Swal.fire({
                title: '¿Cerrar sesión?',
                text: 'Se cerrará tu sesión actual',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar'
            }).then((resultado) => {
                if (resultado.isConfirmed) {
                    window.location.href = '/close';
                }
            });
        });
    });
}

// ==========================================
// 6. TOGGLE SIDEBAR MÓVIL
// ==========================================
function configurarSidebarMovil() {
    const botonToggle = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    
    if (!botonToggle || !sidebar) return;
    
    botonToggle.addEventListener('click', function() {
        if (typeof bootstrap !== 'undefined') {
            const offcanvas = bootstrap.Offcanvas.getInstance(sidebar) || new bootstrap.Offcanvas(sidebar);
            offcanvas.toggle();
        }
    });
}

// ==========================================
// 7. REPORTE DETALLADO (para personal.php)
// ==========================================
function generarReporteDetallado(cedula, nombre, cargo) {
    document.getElementById('rep-nombre').innerText = nombre;
    document.getElementById('rep-cedula').innerText = cedula;
    document.getElementById('rep-cargo').innerText = cargo;
    document.getElementById('rep-mes-titulo').innerText = 'MAYO 2026';

    const cuerpoTabla = document.getElementById('cuerpo-reporte-dias');
    let html = '';
    let habiles = 0;

    for (let d = 1; d <= 31; d++) {
        const fecha = new Date(2026, 4, d);
        const diaSemana = fecha.getDay();
        
        if (diaSemana === 0 || diaSemana === 6) continue;
        habiles++;

        const feriado = (d === 1);
        const estado = feriado ? 'Feriado' : 'Presente';
        const badge = feriado ? 'bg-dark' : 'bg-success';
        const obs = feriado ? 'DÍA DEL TRABAJADOR' : 'S/N';
        const entrada = feriado ? '--:--' : '07:30 AM';
        const salida = feriado ? '--:--' : '12:15 PM';

        html += `<tr>
            <td>${fecha.toLocaleDateString('es-ES', { weekday: 'short' })} ${d.toString().padStart(2, '0')}</td>
            <td>${entrada}</td>
            <td>${salida}</td>
            <td>${obs}</td>
            <td><span class="badge ${badge}">${estado}</span></td>
        </tr>`;
    }

    cuerpoTabla.innerHTML = html;
    document.getElementById('tot-habiles').innerText = habiles;
    document.getElementById('tot-laborados').innerText = '0';
    document.getElementById('tot-nolaborables').innerText = '0';
    document.getElementById('tot-faltas').innerText = '0';
    document.getElementById('tot-justificados').innerText = '0';
    document.getElementById('tot-injustificados').innerText = '0';
    document.getElementById('tot-retardos').innerText = '0';
    document.getElementById('tot-permisos').innerText = '0';

    document.getElementById('vista-lista-personal').style.display = 'none';
    document.getElementById('vista-reporte-personal').style.display = 'block';
}

// ==========================================
// 8. BOTÓN DE VOLVER INTELIGENTE
// ==========================================
document.addEventListener('click', function(e) {
    // Detectamos si el clic fue en un botón de volver (o en el icono dentro de él)
    const btnVolver = e.target.closest('.btn-volver');
    
    if (btnVolver) {
        e.preventDefault(); // Evitamos que intente seguir un href (si es un tag <a>)
        
        // Verificamos si el navegador sabe de qué página venimos y si hay historial
        if (document.referrer !== "" && window.history.length > 1) {
            window.history.back(); // Regresa mágicamente a la vista anterior
        } else {
            // Si el historial está vacío (ej: pegó el link en una pestaña nueva)
            // se redirige a la ruta de rescate, o al inicio si no hay ruta definida.
            const rutaRescate = btnVolver.getAttribute('data-fallback') || '/';
            window.location.href = rutaRescate;
        }
    }
});

// ==========================================
// 9. ARRANQUE PRINCIPAL
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    initReloj();
    configurarSidebarMovil();
    configurarCierreSesion();
    
    if (document.getElementById('tablaPersonal')) initTablaPersonal();
    if (document.getElementById('tablaAsistenciaInicio')) initTablaAsistenciaInicio();
    if (document.getElementById('tablaAsistenciaAdmin')) initTablaAsistenciaAdmin();
    
    document.querySelectorAll('.data-table-base').forEach(function(tabla) {
        if (!$.fn.DataTable.isDataTable(tabla)) {
            $(tabla).DataTable({ language: idiomaEspanol });
        }
    });
});