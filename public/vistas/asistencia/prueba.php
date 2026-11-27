<?php
/** @var array $data */
/** @var string $id */

$meses = [
   1   =>  "enero", 2 => "febrero", 3 => "marzo", 4 => "abril",
   5   =>  "mayo", 6 => "junio", 7 => "julio", 8 => "agosto",
   9   =>  "septiembre", 10 => "octubre", 11 => "noviembre", 12 => "diciembre"
];
?>

<div class="container-fluid">
    <h3 class="fw-bold mb-4">Asistencias del personal</h3>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-check2-circle me-2 text-success"></i>Asistencias Recientes
                </h5>
                
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a class="btn btn-primary shadow-sm" href="/asistencia/<?= $id ?>/registrar">
                        <i class="bi bi-person-plus-fill me-1"></i> Registrar asistencia
                    </a>

                    <form action="/reportes/<?= $id ?>/especifico" method="post" target="_blank" class="d-flex align-items-center gap-2">
                        <select name="mes" class="form-select form-select-sm" style="width: auto;">
                            <?php foreach ($meses as $value => $mes): ?>
                                <option value="<?= $value ?>" <?= ($value == date("m")) ? "selected" : ""?>><?= ucfirst($mes) ?></option>
                            <?php endforeach ?>
                        </select>
                        <select name="ano" class="form-select form-select-sm" style="width: auto;">
                            <?php for ($i=0; $i < 3; $i++):
                                $anos = date("Y", strtotime(date("Y"). "- {$i} years")); ?>
                                <option value="<?= $anos ?>"><?= $anos ?></option>
                            <?php endfor ?>
                        </select>
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-printer me-1"></i> Imprimir
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive contenedor-tabla">
                <table id="tablaAsistencias" class="table table-hover align-middle w-100 table-oculta-inicial">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Hora de entrada</th>
                            <th>Hora de salida</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $dato): ?>
                        <tr>
                            <td><?= date("d-m-y", strtotime($dato["fecha"])) ?></td>
                            <td><?= (!empty($dato["hora_entrada"])) ? date("h:i a",strtotime($dato["hora_entrada"])) : ""?></td>
                            <td><?= (!empty($dato["hora_salida"])) ? date("h:i a",strtotime($dato["hora_salida"])) : ""?></td>
                            <td><?= $dato["estado_entrada"]?></td>
                            <td><?= $dato["estado_salida"]?></td>
                            <td>
                                <a href="/asistencia/<?= $dato["id"] ?>/edit" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarAsistencia(<?= $dato['id'] ?>)"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .contenedor-tabla {
        min-height: 300px;
        position: relative;
    }
    .contenedor-tabla::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: #fff;
        z-index: 5;
    }
    .contenedor-tabla.cargado::before {
        display: none;
    }
    .table-oculta-inicial {
        opacity: 0;
    }
    .table-visible {
        opacity: 1 !important;
        transition: opacity 0.4s ease;
    }
</style>

<script src="/js/sweetalert2.all.min.js"></script>
<script>
// Inicializar DataTable con anti-FOUC
$(document).ready(function() {
    $('#tablaAsistencias').DataTable({
        "language": {
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "pageLength": 10,
        "responsive": true,
        "order": [[0, "desc"]],
        "dom": '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
        "initComplete": function() {
            $('.contenedor-tabla').addClass('cargado');
            $('#tablaAsistencias').addClass('table-visible');
        }
    });
});

// Fallback anti-FOUC
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.querySelector('.contenedor-tabla').classList.add('cargado');
        document.getElementById('tablaAsistencias').classList.add('table-visible');
    }, 2000);
});

async function eliminarAsistencia(id) {
    const confirm = await Swal.fire({
        title: '¿Eliminar asistencia?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    });
    
    if (!confirm.isConfirmed) return;
    
    const formData = new FormData();
    formData.append('id_user', '<?= $_SESSION["user_id"] ?? 1 ?>');
    
    try {
        const res = await fetch('/asistencia/'+ id +'/delete', { method: 'POST', body: formData });
        const data = await res.json();
        
        Swal.fire({
            icon: data.icon || (data.success ? 'success' : 'error'),
            title: data.success ? 'Eliminado' : 'Error',
            text: data.message,
            confirmButtonColor: data.success ? '#198754' : '#d33'
        }).then(() => {
            if (data.success) location.reload();
        });
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo conectar con el servidor' });
    }
}
</script>