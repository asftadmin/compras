let tabla;

function init() {
  $("#usuarios_form").on("submit", function (e) {
    guardaryeditar(e);
    console.log("click");
  });

  $("#clave_form").on("submit", function (e) {
    editarclave(e);
  });
}
/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevouser", function () {
  $("#user_id").val("");
  $("#mdltitulo").html("Nuevo Registro");
  $("#usuarios_form")[0].reset();
  $("#user_password").show();
  $('label[for="user_password"]').show();

  // Asegúrate de que el icono de mostrar/ocultar contraseña esté visible
  $("#show-password").show();

  $("#modalusuario").modal("show");
});

$.post("../../controller/Empleado.php?op=comboEmpleados", function (data, status) {
  $("#user_empleado").html(data);
});

function guardaryeditar(e) {
  e.preventDefault();
  let formData = new FormData($("#usuarios_form")[0]);

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
      url: "../../controller/Usuario.php?op=guardaryeditarUsuario",
      type: "POST",
      data: formData,
      dataType: "json",
      contentType: false,
      processData: false,
      success: function (data) {
        if (data.status.trim().toLowerCase() === "error") {
          Swal.fire({
            title: "Error",
            text: data.message,
            icon: "error",
            customClass: { confirmButton: "btn btn-danger" }
          });
          return;
        }

        $("#usuarios_form")[0].reset();
        $("#modalusuario").modal("hide");
        $("#user_data").DataTable().ajax.reload();
        Swal.fire({
          title: "Correcto",
          text: data.message,
          icon: "success",
          customClass: { confirmButton: "btn btn-success" }
        });
      },
      error: function () {
        Swal.fire({
          title: "Error",
          text: "No se pudieron guardar los datos. Intenta nuevamente.",
          icon: "error"
        });
      }
    });
  });
}

$(document).ready(function () {
  tabla = $("#user_data")
    .dataTable({
      aProcessing: true,
      aServerSide: true,
      dom: "Bfrtip",
      searching: true,
      lengthChange: false,
      colReorder: true,
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
      ajax: {
        url: "../../controller/Usuario.php?op=listarUsuario",
        type: "post",
        dataType: "json",
        data: tabla,
        error: function (e) {
          console.log(e.responseText);
        },
      },

      bDestroy: true,
      responsive: true,
      bInfo: true,
      iDisplayLength: 7,
      autoWidth: false,
      language: {
        sProcessing: "Procesando...",
        sLengthMenu: "Mostrar _MENU_ registros",
        sZeroRecords: "No se encontraron resultados",
        sEmptyTable: "Ningún dato disponible en esta tabla",
        sInfo: "Mostrando un total de _TOTAL_ registros",
        sInfoEmpty: "Mostrando un total de 0 registros",
        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
        sInfoPostFix: "",
        sSearch: "Buscar:",
        sUrl: "",
        sInfoThousands: ",",
        sLoadingRecords: "Cargando...",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior",
        },
        oAria: {
          sSortAscending:
            ": Activar para ordenar la columna de manera ascendente",
          sSortDescending:
            ": Activar para ordenar la columna de manera descendente",
        },
      },
    })
    .DataTable();
});

function editarclave(e) {
  e.preventDefault();
  let formData = new FormData($("#clave_form")[0]);
  formData.append("user_id1", $("#user_id1").val());
  // formData.append("user_contrasena", $("#user_contrasena1").val());
  /*URL DEL CONTROLADOR OP INSERT - ROL */

  var newPassword = $("#user_password").val();
  // Define una expresión regular para validar la contraseña
  var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/;

  if (!passwordRegex.test(newPassword)) {
    swal({
      title: "Error",
      text: "La contraseña debe tener al menos 6 caracteres y contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.",
      type: "error",
      confirmButtonClass: "btn-danger",
    });
  } else {
    // Si la contraseña es válida, mostrar un cuadro de confirmación
    swal(
      {
        title: "Confirmar Contraseña",
        text: "¿Estás seguro de cambiar la contraseña?",
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
            url: "../../controller/Usuario.php?op=editarClave",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (datos) {
              var data = JSON.parse(datos);
              $("#modalclave").modal("hide");
              $("#user_data").DataTable().ajax.reload();
              swal({
                title: "Correcto",
                text: data.message,
                type: "success",
                confirmButtonClass: "btn-success",
              });
            },
          });
        }
      }
    );
  }
}

function editar(user_id) {
  $("#mdltitulo").html("Editar Registro");
  $("#modalusuario").modal("hide");
  $("#user_password").hide();
  $('label[for="user_password"]').hide();

  $("#show-password").hide();

  $.post(
    "../../controller/usuario.php?op=mostrarUsuario",
    { user_id: user_id },
    function (data) {
      data = JSON.parse(data);
      console.log(data.user_id);
      $("#user_id").val(data.user_id);
      $("#user_usuario").val(data.user_usuario);
      $("#user_empleado").val(data.user_empleado);
    }
  );

  $("#modalusuario").modal("show");
}

function clave(user_id) {
  $("#mdltitulo1").html("Cambiar Contraseña");

  $.post(
    "../../controller/usuario.php?op=mostrarClave",
    { user_id: user_id },
    function (data) {
      data = JSON.parse(data);
      $("#user_id1").val(data.user_id);
      var nombre = data.empl_nom;
      var apellidos = data.empl_apellido;
      var nombreCompleto = nombre + " " + apellidos;
      $("#user_nombre1").val(nombreCompleto);
      $("#user_contrasena1").val("");
    }
  );
  $("#modalclave").modal("show");
}

function eliminar(user_id) {
  swal(
    {
      title: "Eliminar Usuario",
      text: "Estas seguro de eliminar el usuario?",
      type: "error",
      showCancelButton: true,
      confirmButtonClass: "btn-danger",
      confirmButtonText: "Si",
      cancelButtonText: "No",
      closeOnConfirm: false,
    },

    function (isConfirm) {
      if (isConfirm) {
        $.post(
          "../../controller/usuario.php?op=eliminarUsuario",
          { user_id: user_id },
          function (data) {}
        );
        /*Recargamos el data table*/
        $("#user_data").DataTable().ajax.reload();

        swal({
          title: "Eliminar Usuario",
          text: "Registro Eliminado",
          type: "success",
          confirmButtonClass: "btn btn-success",
        });
      } else {
        swal({
          title: "Eliminar Usuario",
          text: "No se elimino el registro",
          type: "error",
          confirmButtonClass: "btn btn-danger",
        });
      }
    }
  );
}

init();
