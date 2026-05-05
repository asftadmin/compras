let tabla;

function init(){
    $("#modalproveedor").on("submit", function(e){
        guardaryeditar(e);
    console.log("click");
    });
}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevoprov", function() {
    $('#prov_id').val('');
    $('#mdltitulo').html('Nuevo Proveedor');
    $('#proveedor_form')[0].reset();

    $('#modalproveedor').modal('show');
});
$.post("../../controller/Linea.php?op=combo",function(data, status){
    $('#prov_linea').html(data);
});

function guardaryeditar(e){
    e.preventDefault();
    let formData = new FormData($("#proveedor_form")[0]);

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
        url: "../../controller/Proveedores.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            var data = JSON.parse(datos);
            $('#proveedor_form')[0].reset();
            $('#modalproveedor').modal('hide');
            $('#proveedor_data').DataTable().ajax.reload();

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

    tabla=$('#proveedor_data').dataTable({
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
            url: '../../controller/Proveedores.php?op=listar',
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
function editar(prov_id){
        $('#mdltitulo').html('Editar Registro');

        $.post("../../controller/Proveedores.php?op=mostrar", { prov_id : prov_id}, function(data) { 
            data = JSON.parse(data);
            $('#prov_id').val(data.prov_id);
            $('#prov_nit').val(data.prov_nit);
            $('#prov_cod_siesa').val(data.prov_cod_siesa);
            $('#prov_descripcion').val(data.prov_descripcion);
            $('#prov_estado').val(data.prov_estado);
            $('#prov_linea').val(data.prov_linea);

        }); 

        $('#modalproveedor').modal('show');
}

function eliminar(prov_id){
        swal({
            title:"Eliminar Proveedor",
            text: "Estas seguro de eliminar el proveedor?",
            type: "error",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm:false,
        },
        
        function(isConfirm){
            if(isConfirm){
        
            $.post("../../controller/Proveedores.php?op=eliminar", { prov_id : prov_id}, function(data) { 

            }); 
            /*Recargamos el data table*/
            $('#proveedor_data').DataTable().ajax.reload();

            swal({
                title: "Eliminar Proveedor",
                text: "Registro Eliminado",
                type: "success",
                confirmButtonClass: "btn btn-success"
            });

            }else{
                swal({
                    title: "Eliminar Proveedor",
                    text: "No se elimino el registro",
                    type: "error",
                    confirmButtonClass: "btn btn-danger"
                });
            }
        });

}
    

init();