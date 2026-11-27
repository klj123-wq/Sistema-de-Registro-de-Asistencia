// $(document).ready(function() {
//     // Configuración global para todas las tablas
//     $.extend(true, $.fn.dataTable.defaults, {
//         language: {
//             "processing": "Procesando...",
//             "lengthMenu": "Mostrar _MENU_ registros",
//             "zeroRecords": "No se encontraron resultados",
//             "emptyTable": "Ningún dato disponible en esta tabla",
//             "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
//             "infoEmpty": "Mostrando 0 a 0 de 0 registros",
//             "search": "Buscar:",
//             "paginate": {
//                 "first": "Primero",
//                 "last": "Último",
//                 "next": "Siguiente",
//                 "previous": "Anterior"
//             }
//         },
//         responsive: true,
//         pageLength: 10,
//         // Eliminamos la "B" y los botones para evitar que busque scripts externos
//         dom: '<"row"<"col-md-6"f><"col-md-6">>rt<"row"<"col-md-6"i><"col-md-6"p>>'
//     });

//     // Inicialización automática
//     $('.data-table-base').DataTable();
// });