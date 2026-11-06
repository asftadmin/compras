let tabla;

function init() {

};

$('#filtro-fechas').daterangepicker({
    locale: {
        format: 'YYYY-MM-DD',
        applyLabel: 'Aplicar',
        cancelLabel: 'Cancelar',
        customRangeLabel: 'Rango personalizado',
        daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
    },
    startDate: '2017-01-01',
    endDate: moment()
});

$(document).on('click', '#btn-filtrar-fechas', function () {
    $.ajax({
        url: '../../controller/Empleado.php?op=consultarEmpleados',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            const empleadosBD = response.aaData.map(row => row[0].toString().trim());
            cargarEmpleadosAPI(empleadosBD);
        }
    });
});

function cargarEmpleadosAPI(empleadosBD) {
    const fechas = $('#filtro-fechas').val().split(' - ');
    const fechainicio = fechas[0];
    const fechafin = fechas[1];

    $('#empleados_data_siesa').DataTable({
        destroy: true,
        ajax: {
            url: '../../controller/Empleado.php?op=consultaEmpleadoSiesa',
            type: 'GET',
            data: {
                fechainicio,
                fechafin
            },
            dataSrc: function (json) {
                return json.aaData.map(row => {
                    const documento = row[0].toString().trim();
                    const existe = empleadosBD.includes(documento);
                    const estado = existe
                        ? '<span class="badge bg-success">Registrado</span>'
                        : '<span class="badge bg-danger">Nuevo</span>';
                    const accionesHTML = existe
                        ? '' // No mostrar botones si ya está registrado
                        : `<div class="text-center">
                        <button class="btn btn-info btn-sm btn-guardar-empleado" data-documento="${documento}">
                            <i class="fa fa-save"></i>
                        </button>
                        </div>`;
                    row.splice(9, 0, estado);
                    row.splice(10, 1, accionesHTML);
                    return row;
                });
            }
        },
        order: [[1, 'asc']],
        iDisplayLength: 5,
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
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

//Boton guardar empleado
$(document).on('click', '.btn-guardar-empleado', function () {
    const documento = $(this).data('documento');

    // Buscar la fila completa en la tabla
    const fila = $(this).closest('tr');
    const datos = $('#empleados_data_siesa').DataTable().row(fila).data();

    // Aquí puedes extraer los campos que necesitas
    const payload = {
        nombre: datos[1],
        apellido1: datos[2],
        apellido2: datos[3],
        documento: datos[0],       // f200_id
        telefono: datos[8],   
        email: datos[7],
        direccion: datos[6]
    };

    $.ajax({
        url: '../../controller/Empleado.php?op=guardarEmpleadoNuevo',
        type: 'POST',
        data: payload,
        dataType: 'json',
        success: function (response) {
            if (!response || response.success !== true) {
                Swal.fire({
                    title: "Error",
                    text: "No se pudo guardar el empleado",
                    icon: "error",
                    customClass: { confirmButton: "btn btn-danger" }
                });
                return;
            }

            Swal.fire({
                title: "Correcto",
                text: "Empleado Registrado",
                icon: "success",
                customClass: { confirmButton: "btn btn-success" }
            });
            const table = $('#empleados_data_siesa').DataTable();
            const rowIndex = table.row(fila).index();

            datos[9] = '<span class="badge bg-success">Registrado</span>';
            datos[10] = ''; // Quitar el botón

            table.row(rowIndex).data(datos).invalidate().draw(false);
        },
        error: function () {
            Swal.fire({
                title: "Error",
                text: "No se pudo guardar el empleado",
                icon: "error",
                customClass: { confirmButton: "btn btn-danger" }
            });
        }
    });
});




init();
