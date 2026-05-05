let tabla;

function init(){
    $("#modallinea").on("submit", function(e){
        guardaryeditar(e);
    console.log("click");
    });
}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevalinea", function() {
    $('#linea_id').val('');
    $('#mdltitulo').html('Nueva Linea de Produccion');
    $('#linea_form')[0].reset();

    $('#modallinea').modal('show');
});

function guardaryeditar(e){
    e.preventDefault();
    let formData = new FormData($("#linea_form")[0]);

    /* URL DEL CONTROLADOR OP INSERT - ROL */
    swal({
        title: "Confirmar Datos",
        text: "¿Deseas guardar Cambios?",
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-primary",
        confirmButtonText: "Sí",
        cancelButtonText: "No",
        closeOnConfirm: false,
        
    },
    function (isConfirm) {
        if (isConfirm) {
    $.ajax({
        url: "../../controller/Linea.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            var data = JSON.parse(datos);
            $('#linea_form')[0].reset();
            $('#modallinea').modal('hide');
            $('#linea_data').DataTable().ajax.reload();

            if (data.status.trim().toLowerCase() === "error") {
                swal({
                    title: "Error",
                    text: data.message,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                });
            } else {
                swal({
                    title: "Correcto",
                    text: data.message,
                    type: "success",
                    confirmButtonClass: "btn-success",
                });
            }
        }
        });
    }
    });
}

$(document).ready(function(){

    tabla=$('#linea_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [		          
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
                ],
        "ajax":{
            url: '../../controller/Linea.php?op=listar',
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
function editar(linea_id){
        $('#mdltitulo').html('Editar Registro');

        $.post("../../controller/Linea.php?op=mostrar", { linea_id : linea_id}, function(data) { 
            data = JSON.parse(data);
            $('#linea_id').val(data.linea_id);
            $('#linea_nom').val(data.linea_nom);
        }); 

        $('#modallinea').modal('show');
}

function eliminar(linea_id){
        swal({
            title:"Eliminar Linea de Produccion",
            text: "Estas seguro de eliminar la linea de produccion?",
            type: "error",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm:false,
        },
        
        function(isConfirm){
            if(isConfirm){
        
            $.post("../../controller/Linea.php?op=eliminar", { linea_id : linea_id}, function(data) { 

            }); 
            /*Recargamos el data table*/
            $('#linea_data').DataTable().ajax.reload();

            swal({
                title: "Eliminar Linea de Produccion",
                text: "Registro Eliminado",
                type: "success",
                confirmButtonClass: "btn btn-success"
            });

            }else{
                swal({
                    title: "Eliminar Linea de Produccion",
                    text: "No se elimino el registro",
                    type: "error",
                    confirmButtonClass: "btn btn-danger"
                });
            }
        });

}
    

init();