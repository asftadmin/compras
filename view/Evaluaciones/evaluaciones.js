let tabla;

function init(){
    $("#modalEvaluacion").on("submit", function(e){
        guardaryeditar(e);
    console.log("click");
    });
}

// FUNCION PARA FORMATO DE INPUT FECHA
$(function () {
    const inicioAno = moment().startOf('year');
    const finAno = moment().endOf('year');

    $('#rangoFechasEva').daterangepicker({
        startDate: inicioAno,
        endDate: finAno,
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            customRangeLabel: "Personalizado",
            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                         'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            firstDay: 1
        },
        ranges: {
            'Año actual': [inicioAno, finAno],
            'Año pasado': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            'Mes actual': [moment().startOf('month'), moment().endOf('month')],
            'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
});

$(document).on("click", "#btn-add-evaluacion", function() {
    $('#prov_id').val('');
    $('#mdltitulo').html('Nueva Evaluacion');
    $('#evaluacion_form')[0].reset();

    $('#modalEvaluacion').modal('show');
});


//FUNCION GUARDAR EVALUACION
function guardaryeditar(e) {
    e.preventDefault();

    // Capturamos los valores desde el formulario
    let rangoFechasEva = $("#rangoFechasEva").val().trim();
    let eval_nombre = $("#eval_nombre").val().trim();

    // Validación: Campos vacíos
    if (rangoFechasEva === "" || eval_nombre === "") {
        Swal.fire({
            title: "Campos incompletos",
            text: "Por favor completa todos los campos antes de continuar.",
            icon: "warning",
            confirmButtonText: "Entendido",
            confirmButtonColor: "#3085d6"
        });
        return false;
    }

    // Creamos el FormData con los datos del formulario
    let formData = new FormData($("#evaluacion_form")[0]);

    // Confirmación antes de enviar
    Swal.fire({
        title: "¿Confirmar datos?",
        text: "¿Deseas guardar la evaluación con el rango seleccionado?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, guardar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33"
    }).then((result) => {
        if (result.isConfirmed) {
            // Envío AJAX al controlador PHP
            $.ajax({
                url: "../../controller/Evaluacion.php?op=guardaryeditar",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (datos) {
                    try {
                        let data = JSON.parse(datos);

                        // Reset y recarga de vista
                        $('#evaluacion_form')[0].reset();
                        $('#modalEvaluacion').modal('hide');
                        $('#evaluacion_data').DataTable().ajax.reload();

                        if (data.status.trim().toLowerCase() === "error") {
                            Swal.fire({
                                title: "Error",
                                text: data.message,
                                icon: "error",
                                confirmButtonColor: "#d33"
                            });
                        } else {
                            Swal.fire({
                                title: "Correcto",
                                text: data.message,
                                icon: "success",
                                confirmButtonColor: "#28a745"
                            });
                        }
                    } catch (e) {
                        Swal.fire({
                            title: "Error de formato",
                            text: "La respuesta del servidor no tiene el formato esperado.",
                            icon: "error"
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "Error de conexión",
                        text: "No se pudo enviar la solicitud. Verifica tu conexión o intenta más tarde.",
                        icon: "error",
                        confirmButtonText: "Cerrar"
                    });
                }
            });
        }
    });
}

// FUNCION MOSTRAR DATA TABLA

$(document).ready(function(){

    tabla=$('#evaluacion_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": true,
        lengthChange: false,
        colReorder: true,
        "ajax":{
            url: '../../controller/Evaluacion.php?op=listarEvaluaciones',
            type : "post",
            dataType : "json",	
            data: tabla,			    		
            error: function(e){
                console.log(e.responseText);	
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo":true,
        "iDisplayLength": 7,
        "autoWidth": false,
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }     
    }).DataTable(); 
});


function ver(codi_eval) {
    window.location.href = BASE_URL +'/view/Evaluaciones/detalle_evaluaciones.php?id='+codi_eval; 
}

function enviar(codi_eval) {
    window.location.href = BASE_URL +'/view/Evaluaciones/enviar_evaluaciones.php?id='+codi_eval; 
}


init();