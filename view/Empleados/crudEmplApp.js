let tabla;

function init(){
    $("#Emplapp_form").on("submit", function(e){
        guardaryeditar(e);
    console.log("click");
    });
}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevoemplapp", function() {
    $('#empl_id').val('');
    $('#mdltitulo').html('Nuevo Empleado');
    $('#Emplapp_form')[0].reset();

    $('#modalemplapp').modal('show');
});

$.post("../../controller/Rol.php?op=comboRol",function(data, status){
    $('#empl_rol').html(data);
});

function guardaryeditar(e){
    e.preventDefault();
    let formData = new FormData($("#Emplapp_form")[0]);

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
        url: "../../controller/Empleado.php?op=guardaryeditarEmpleado",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            var data = JSON.parse(datos);
            $('#Emplapp_form')[0].reset();
            $('#modalemplapp').modal('hide');
            
            $('#app_data').DataTable().ajax.reload();

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

    tabla=$('#app_data').dataTable({
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
            url: '../../controller/Empleado.php?op=listarEmpleados',
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
function editar(empl_id){
        $('#mdltitulo').html('Editar Registro');
        $('#lbestado').html('Estado: *');
        $('#empl_estado').html('<select class="form-control select2bs4" id="empl_estado" name="empl_estado"><option value="1" id="activo">Activo</option><option value="0" id="inactivo">Inactivo</option></select>');

        $.post("../../controller/Empleado.php?op=mostrar", { empl_id : empl_id}, function(data) { 
            data = JSON.parse(data);
            $('#empl_id').val(data.empl_id);
            $('#empl_nom').val(data.empl_nom);
            $('#empl_apellido').val(data.empl_apellido);
            $('#empl_documento').val(data.empl_documento);
            $('#empl_telefono').val(data.empl_telefono);
            $('#empl_email').val(data.empl_email);
            $('#empl_direccion').val(data.empl_direccion);
            if (data.empl_estado === 1) {
                $('#activo').prop('selected', true);
            } else {
                $('#inactivo').prop('selected', true);
            }
            $('#empl_rol').val(data.empl_rol);
        }); 

        $('#modalemplapp').modal('show');
}

function clave(user_id){

    $('#mdltitulo1').html('Cambiar Contraseña');

    $.post("../../controller/usuario.php?op=mostrarClave", { user_id : user_id}, function(data) { 
        data = JSON.parse(data);
        $('#user_id1').val(data.user_id);
        var nombre = data.user_nombre;
        var apellidos = data.user_apellidos;
        var nombreCompleto = nombre+' ' +apellidos;
        $('#user_nombre1').val(nombreCompleto);
        $('#user_contrasena1').val('');
    
    }); 
    $('#modalclave').modal('show');
}

function eliminar(user_id){
        swal({
            title:"Eliminar Usuario",
            text: "Estas seguro de eliminar el usuario?",
            type: "error",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm:false,
        },
        
        function(isConfirm){
            if(isConfirm){
        
            $.post("../../controller/usuario.php?op=eliminarUsuario", { user_id : user_id}, function(data) { 

            }); 
            /*Recargamos el data table*/
            $('#user_data').DataTable().ajax.reload();

            swal({
                title: "Eliminar Usuario",
                text: "Registro Eliminado",
                type: "success",
                confirmButtonClass: "btn btn-success"
            });

            }else{
                swal({
                    title: "Eliminar Usuario",
                    text: "No se elimino el registro",
                    type: "error",
                    confirmButtonClass: "btn btn-danger"
                });
            }
        });

}
    

init();