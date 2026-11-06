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

    Swal.fire({
        title: "Confirmar Datos",
        text: "¿Deseas guardar cambios?",
        icon: "info",
        showCancelButton: true,
        customClass: { confirmButton: "btn btn-primary", cancelButton: "btn btn-secondary" },
        confirmButtonText: "Sí",
        cancelButtonText: "No"
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: "../../controller/Empleado.php?op=guardaryeditarEmpleado",
            type: "POST",
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.status.trim().toLowerCase() === "error") {
                    Swal.fire({
                        title: "Error",
                        text: data.message,
                        icon: "error",
                        customClass: { confirmButton: "btn btn-danger" }
                    });
                    return;
                }

                $('#Emplapp_form')[0].reset();
                $('#modalemplapp').modal('hide');
                $('#app_data').DataTable().ajax.reload();
                Swal.fire({
                    title: "Correcto",
                    text: data.message,
                    icon: "success",
                    customClass: { confirmButton: "btn btn-success" }
                });
            },
            error: function() {
                Swal.fire({
                    title: "Error",
                    text: "No se pudieron guardar los datos. Intenta nuevamente.",
                    icon: "error"
                });
            }
        });
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

function eliminar(empl_id){
    Swal.fire({
        title: "Eliminar Empleado",
        text: "¿Estás seguro de eliminar el empleado?",
        icon: "warning",
        showCancelButton: true,
        customClass: { confirmButton: "btn btn-danger", cancelButton: "btn btn-secondary" },
        confirmButtonText: "Sí",
        cancelButtonText: "No"
    }).then(function(result) {
        if (!result.isConfirmed) {
            return;
        }

        $.post("../../controller/Empleado.php?op=eliminarEmpleado", { empl_id: empl_id })
            .done(function() {
                $('#app_data').DataTable().ajax.reload();
                Swal.fire({
                    title: "Eliminar Empleado",
                    text: "Registro eliminado",
                    icon: "success",
                    customClass: { confirmButton: "btn btn-success" }
                });
            })
            .fail(function() {
                Swal.fire({
                    title: "Error",
                    text: "No se pudo eliminar el empleado. Intenta nuevamente.",
                    icon: "error"
                });
            });
    });
}

init();
