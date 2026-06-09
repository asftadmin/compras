function init() {
    if ($('#ev_enviar_data').length) {
        cargarProveedoresxEvalu();
    }
}



// Función para obtener el parámetro de la URL
var getURLParameter = function (sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1));
    var sURLVariables = sPageURL.split('&');
    var sParameterName;
    for (var i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}

//Adquirir datos para el detalle ticket
$(document).ready(function () {
    if (!$('#fecha_inicio').length || !$('#fecha_fin').length) {
        return;
    }

    var evaluacion_id = getURLParameter('id');

    if (!evaluacion_id) {
        alert('No se encontró el ID del ticket en la URL');
        return;
    }

    $.ajax({
        url: "../../controller/Evaluacion.php?op=detalleEvaluacion",
        method: "POST",
        data: { id: evaluacion_id },
        dataType: "json", // Esperamos una respuesta JSON
        success: function (data) {
            console.log("Respuesta completa:", data);

            if (data.status === 'success') {
                const evaluacion = data.data[0];
                $('#fecha_inicio').val(evaluacion.fecha_inicio_eval);
                $('#fecha_fin').val(evaluacion.fecha_fin_eval);
            } else {
                alert(data.message || 'Error desconocido');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud:", status, error);
            alert("Error al cargar los detalles del ticket");
        }
    });

});


function cargarEvaluacionesAPI() {
    if (!$('#ev_siesa_data').length) {
        return;
    }

    const fechainicio = $('#fecha_inicio').val();
    const fechafin = $('#fecha_fin').val();

    if (!fechainicio || !fechafin) {
        Swal.fire('Atencion', 'Primero consulte las fechas de la evaluacion.', 'warning');
        return;
    }

    // Mostrar SweetAlert con spinner
    Swal.fire({
        title: 'Consultando datos...',
        html: 'Por favor espere mientras se cargan las evaluaciones desde SIESA y se comparan con la base de datos.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Timer opcional de seguridad (por si tarda mucho)
    const timeout = setTimeout(() => {
        Swal.update({
            title: 'Esto está tardando más de lo habitual...',
            html: 'Verifique su conexión o espere unos segundos más.'
        });
    }, 35000); // 10 segundos


    const tablaSiesa = $('#ev_siesa_data');

    // Evita acumular eventos cuando el usuario consulta varias veces.
    tablaSiesa
        .off('xhr.dt.cargarEvaluaciones error.dt.cargarEvaluaciones')
        .on('xhr.dt.cargarEvaluaciones', function () {
            clearTimeout(timeout);
            Swal.close();
        })
        .on('error.dt.cargarEvaluaciones', function () {
            clearTimeout(timeout);
            Swal.fire('Error', 'Ocurrio un problema al cargar los datos.', 'error');
        });

    tablaSiesa.DataTable({
        destroy: true,
        ajax: {
            url: '../../controller/Evaluacion.php?op=consultaEvaluacionSiesa',
            type: 'GET',
            data: {
                fechainicio,
                fechafin
            },
            dataSrc: function (json) {
                if (!json.aaData || !Array.isArray(json.aaData)) {
                    console.error("Respuesta inválida del servidor:", json);
                    return [];
                }

                // ✅ Aseguramos que cada fila sea un array y agregamos las fechas
                return json.aaData.map(row => {
                    if (!Array.isArray(row)) row = Object.values(row);
                    row[14] = fechainicio;
                    row[15] = fechafin;
                    return row;
                });
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                render: function () {
                    return '<input type="checkbox" class="row-check">';
                }
            },
            { data: 0, title: "NIT" },
            { data: 1, title: "Razón Social" },
            { data: 2, title: "Email" },
            { data: 3, title: "P1" },
            { data: 4, title: "P2" },
            { data: 5, title: "P3" },
            { data: 6, title: "P4" },
            { data: 7, title: "P5" },
            { data: 8, title: "P6" },
            { data: 9, title: "P7" },
            { data: 10, title: "P8" },
            { data: 11, title: "P9" },
            { data: 12, title: "Promedio" },
            { data: 13, title: "Tipo" },
            { data: 14, visible: false, title: "Fecha Inicio" },
            { data: 15, visible: false, title: "Fecha Fin" }
        ],
        order: [[1, 'asc']],
        responsive: true,
        scrollX: true,
        autoWidth: false,
        iDisplayLength: 10,
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron pendientes por importar",
            "sEmptyTable": "No hay evaluaciones pendientes por importar",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });

}

$(document).on('click', '#btn-add-buscar', function () {
    cargarEvaluacionesAPI();
});

//GUARDAR LOS DATOS DE LA EVALUACION EN LA TABLA DETALLE EVALUACION

// Checkbox maestro (seleccionar / deseleccionar todos)
$(document).on('change', '#selectAll', function () {
    const isChecked = $(this).prop('checked');
    $('#ev_siesa_data tbody .row-check').prop('checked', isChecked);
});

// Guardar seleccionados
$(document).on('click', '#btnGuardarEvaSiesa', function () {
    if (!$.fn.dataTable.isDataTable('#ev_siesa_data')) {
        Swal.fire('Atencion', 'Primero consulte las evaluaciones pendientes.', 'warning');
        return;
    }

    const table = $('#ev_siesa_data').DataTable();
    const codi_eval = getURLParameter('id');
    let seleccionados = [];

    table.rows().every(function () {
        const fila = this.node();
        const check = $(fila).find('.row-check');
        if (check.is(':checked')) {
            const datos = this.data();
            seleccionados.push({
                codi_eval: codi_eval,
                nit_proveedor: datos[0],
                razon_social: datos[1],
                email_eval: datos[2],
                p1_porcentaje: datos[3],
                p2_porcentaje: datos[4],
                p3_porcentaje: datos[5],
                p4_porcentaje: datos[6],
                p5_porcentaje: datos[7],
                p6_porcentaje: datos[8],
                p7_porcentaje: datos[9],
                p8_porcentaje: datos[10],
                p9_porcentaje: datos[11],
                porcentaje_promedio: datos[12],
                tipo_evaluacion: datos[13],
                fecha_inicio_deta: datos[14],
                fecha_fin_deta: datos[15]

            });
        }
    });

    if (seleccionados.length === 0) {
        Swal.fire('Atención', 'Debe seleccionar al menos un registro para guardar.', 'warning');
        return;
    }

    // Confirmación antes de guardar
    Swal.fire({
        title: "¿Guardar registros seleccionados?",
        text: `Se guardarán ${seleccionados.length} registros.`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, guardar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#d33"
    }).then((result) => {
        if (result.isConfirmed) {

            Swal.fire({
                title: 'Guardando evaluaciones...',
                html: 'Por favor espere mientras se registran los datos seleccionados.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });



            // Enviar al backend en un solo lote
            $.ajax({
                url: '../../controller/Evaluacion.php?op=guardarDetalleEvaluacion',
                type: 'POST',
                data: { registros: JSON.stringify(seleccionados) },
                success: function (response) {
                    let res;
                    try {
                        res = JSON.parse(response);
                    } catch (e) {
                        Swal.fire('Error', 'Respuesta del servidor inválida.', 'error');
                        return;
                    }

                    Swal.close();

                    if (res.status === 'success') {
                        Swal.fire({
                            title: 'Guardado',
                            text: res.message,
                            icon: 'success',
                            timer: 1000,
                            showConfirmButton: false
                        });

                        $('#selectAll').prop('checked', false);
                        $('#ev_siesa_data tbody .row-check').prop('checked', false);

                        // Recarga pendientes por importar. Los guardados ya no deben aparecer.
                        setTimeout(() => {
                            table.ajax.reload(function () {
                                $('#selectAll').prop('checked', false);
                            }, false);
                        }, 500);

                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: res.message || 'No se pudo guardar la evaluación.',
                            icon: 'error'
                        });
                    }
                },
                error: function () {
                    Swal.close();
                    Swal.fire('Error', 'No se pudo enviar la solicitud al servidor.', 'error');
                }
            });
        }
    });
});

$('#btnVolver').click(function () {
    // Verificar si viene de tickets.php
    if (document.referrer.indexOf('tickets.php') !== -1) {
        history.back(); // Regresa a la página anterior
    } else {
        window.location.href = 'evaluaciones.php'; // Redirige por defecto
    }
});

function cargarProveedoresxEvalu() {
    if (!$('#ev_enviar_data').length) {
        return;
    }

    const codi_eval = getURLParameter('id');


    if (!codi_eval) {
        Swal.fire('Error', 'No se ha especificado la evaluación.', 'error');
        return;
    }
    // 🔹 Inicializar DataTable
    const table = $('#ev_enviar_data').DataTable({
        destroy: true,
        dom: "Bfrtip",
        ajax: {
            url: '../../controller/Evaluacion.php?op=listarEnvios',
            type: 'GET',
            data: { codi_eval }, // Enviar el id de evaluación al backend
            dataSrc: function (json) {
                if (!json.aaData) {
                    Swal.fire('Advertencia', 'No se encontraron proveedores para esta evaluación.', 'warning');
                    return [];
                }
                return json.aaData;
            },
            error: function () {
                Swal.fire('Error', 'Ocurrió un problema al obtener los datos.', 'error');
            }
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                title: 'Reporte de Evaluaciones Proveedores',
                className: 'btn btn-success btn-sm',
                // Exportar TODAS las columnas (índices 0 a 11)
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5], // Todas las columnas
                    modifier: {
                        page: 'all' // Todas las páginas
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                title: 'Reporte de Evaluaciones Proveedores',
                className: 'btn btn-danger btn-sm',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    exportOptions: {
                        columns: ':visible',
                        modifier: { page: 'all' }
                    }
                },
                customize: function (doc) {
                    // Configuración básica
                    doc.defaultStyle.fontSize = 7;
                    doc.styles.tableHeader.fontSize = 9;
                    doc.styles.title.fontSize = 14;
                    doc.pageMargins = [20, 60, 20, 40];

                    // Ajustar ancho de columnas
                    var colCount = doc.content[1].table.body[0].length;
                    var colWidths = [];

                    for (var i = 0; i < colCount; i++) {
                        colWidths.push('auto');
                    }

                    doc.content[1].table.widths = colWidths;

                    // Estilo para encabezados
                    doc.content[1].table.body[0].forEach(function (cell) {
                        cell.fillColor = '#2c3e50';
                        cell.color = '#ffffff';
                        cell.bold = true;
                        cell.alignment = 'center';
                    });

                    // Pie de página
                    doc['footer'] = function (currentPage, pageCount) {
                        return {
                            text: 'Página ' + currentPage.toString() + ' de ' + pageCount,
                            alignment: 'center',
                            fontSize: 8,
                            margin: [0, 10, 0, 0]
                        };
                    };
                },
                filename: 'Proveedores_' + new Date().toISOString().slice(0, 10) + '.pdf'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                title: 'Reporte de Evaluaciones Proveedores',
                className: 'btn btn-info btn-sm',
                // Exportar TODAS las columnas
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5],
                    modifier: {
                        page: 'all'
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                title: '',
                className: 'btn btn-warning btn-sm',
                // Exportar TODAS las columnas
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5],
                    modifier: {
                        page: 'all'
                    }
                },
                customize: function (win) {
                    $(win.document.body).css('font-size', '9pt');

                    // Crear título personalizado
                    var title = '<div style="text-align:center; margin-bottom:20px;">' +
                        '<h3>Reporte de Evaluaciones Proveedores</h3>' +
                        '<p>Generado: ' + new Date().toLocaleDateString() + ' ' + new Date().toLocaleTimeString() + '</p>' +
                        '</div>';

                    $(win.document.body).prepend(title);

                    // Asegurar que todas las columnas sean visibles
                    $(win.document.body).find('table thead th').show();
                    $(win.document.body).find('table tbody td').show();

                    // Ajustar ancho de tabla
                    $(win.document.body).find('table').css('width', '100%');

                    // Estilo para mejor visualización
                    $(win.document.body).find('tr:nth-child(odd)').css('background-color', '#f9f9f9');
                    $(win.document.body).find('tr:nth-child(even)').css('background-color', '#ffffff');

                    // Agregar pie de página
                    var pageCount = win.document.querySelectorAll('.dataTables_scrollBody table tr').length > 0 ? '1' : '';
                    var footer = '<div style="text-align:center; margin-top:30px; font-size:8pt; color:#666;">' +
                        'Página ' + pageCount +
                        '</div>';

                    $(win.document.body).append(footer);
                }
            },
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copiar',
                title: 'Reporte de Ausentismo',
                className: 'btn btn-secondary btn-sm',
                // Exportar TODAS las columnas
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5],
                    modifier: {
                        page: 'all'
                    }
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columnas',
                className: 'btn btn-dark btn-sm',
                columns: ':gt(0)', // Todas excepto la primera (ITEM)
                columnText: function (dt, idx, title) {
                    return (idx + 1) + ': ' + title;
                },
                // Botones adicionales para colvis
                postfixButtons: [
                    {
                        text: 'Mostrar todas',
                        action: function (e, dt, button, config) {
                            dt.columns().visible(true);
                            this.active(false);
                        }
                    },
                    {
                        text: 'Ocultar todas',
                        action: function (e, dt, button, config) {
                            dt.columns(':gt(0)').visible(false); // Ocultar todas excepto ITEM
                            this.active(false);
                        }
                    },
                    'colvisRestore'
                ]
            }
        ],
        columns: [
            {
                data: null,
                orderable: false,
                render: function () {
                    return '<input type="checkbox" class="row-check">';
                }
            },
            { data: 0, title: "NIT" },
            { data: 1, title: "RAZON SOCIAL" },
            {
                data: 2, title: "EMAIL",
                render: function (data, type, row) {
                    if (!data) return '';
                    // Divide los correos separados por ";" y crea una lista con <br>
                    const correos = data.split(';').map(correo => correo.trim()).filter(c => c !== '');
                    return correos.join('<br>');
                }
            },
            { data: 3, title: "CALIFICACION" },
            { data: 4, title: "ESTADO" },
            { data: 5, title: "ACCIONES" }
        ],
        order: [[2, 'asc']],
        responsive: true,
        autoWidth: false,
        scrollX: true,
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Sin proveedores asociados",
            sInfo: "Mostrando _TOTAL_ registros",
            sInfoEmpty: "Mostrando 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar:",
            sLoadingRecords: "Cargando...",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            }
        }
    });

    $(window).off('resize.evEnviar').on('resize.evEnviar', function () {
        table.columns.adjust().responsive.recalc();
    });


}

//Funcion ver evaluacion
function verEval(id1, id2) {
    var url = BASE_URL + '/view/PDF/ReporteEvaluacion.php?id1=' + id1 + '&id2=' + id2;
    window.open(url, '_blank');
}

//==================================================
//Boton enviar evaluaciones proveedores seleccionados
//==================================================

$('#btnEnviar').on('click', function () {
    const proveedoresSeleccionados = [];

    // Recorremos las filas seleccionadas del DataTable
    $('#ev_enviar_data tbody input.row-check:checked').each(function () {
        const fila = $(this).closest('tr');
        const data = $('#ev_enviar_data').DataTable().row(fila).data();

        const proveedor = {
            nit: data[0],
            razon_social: data[1],
            email: data[2].split(';')[0].trim() // Solo el primer correo
        };
        proveedoresSeleccionados.push(proveedor);
    });

    if (proveedoresSeleccionados.length === 0) {
        Swal.fire('Advertencia', 'Debe seleccionar al menos un proveedor.', 'warning');
        return;
    }

    const codi_eval = getURLParameter('id');

    Swal.fire({
        title: 'Enviando correos...',
        text: 'Por favor espere mientras se envían las evaluaciones.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    $.ajax({
        url: '../../controller/Evaluacion.php?op=enviarCorreos',
        type: 'POST',
        data: {
            codi_eval: codi_eval,
            proveedores: JSON.stringify(proveedoresSeleccionados)
        },
        success: function (response) {
            Swal.close();
            const res = JSON.parse(response);
            if (res.status === 'success') {
                Swal.fire('Correcto', res.message, 'success');
                $('#ev_enviar_data').DataTable().ajax.reload();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: function () {
            Swal.close();
            Swal.fire('Error', 'No se pudo completar el envío.', 'error');
        }
    });
});



init();

