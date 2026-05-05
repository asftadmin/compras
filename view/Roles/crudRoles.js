let tabla;

function init() {
  $("#roles_form").on("submit", function (e) {
    guardaryeditar(e);
  });
}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevorol", function () {
  $("#rol_id").val("");
  $("#mdltitulo").html("Nuevo Rol");
  $("#roles_form")[0].reset();
  $("#modalrol").modal("show");
});

function guardaryeditar(e) {
  e.preventDefault();
  let formData = new FormData($("#roles_form")[0]);

  /*URL DEL CONTROLADOR OP INSERT - ROL */

  $.ajax({
    url: "../../controller/Rol.php?op=guardaryeditarRol",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      console.log(datos);
      var data = JSON.parse(datos);
      $("#roles_form")[0].reset();
      $("#modalrol").modal("hide");
      $("#rol_data").DataTable().ajax.reload();

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
    },
  });
}
$(document).ready(function () {
  tabla = $("#rol_data")
    .dataTable({
      aProcessing: true,
      aServerSide: true,
      dom: "Bfrtip",
      searching: true,
      lengthChange: false,
      colReorder: true,
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
      ajax: {
        url: "../../controller/Rol.php?op=listarRol",
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

function editar(rol_id) {
  $("#mdltitulo").html("Editar Rol");

  $.post(
    "../../controller/Rol.php?op=mostrarRol",
    { rol_id: rol_id },
    function (data) {
      data = JSON.parse(data);
      $("#rol_id").val(data.rol_id);
      $("#rol_nom").val(data.rol_nom);
    }
  );

  $("#modalrol").modal("show");
}

function eliminar(rol_id) {
  swal(
    {
      title: "Eliminar Rol",
      text: "Estas seguro de eliminar el rol?",
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
          "../../controller/Rol.php?op=eliminarRol",
          { rol_id: rol_id },
          function (data) {}
        );
        /*Recargamos el data table*/
        $("#rol_data").DataTable().ajax.reload();

        swal({
          title: "Eliminar Rol",
          text: "Registro Eliminado",
          type: "success",
          confirmButtonClass: "btn btn-success",
        });
      } else {
        swal({
          title: "Eliminar Rol",
          text: "No se elimino el registro",
          type: "error",
          confirmButtonClass: "btn btn-danger",
        });
      }
    }
  );
}

// PERMISOS DE LOS ROLES
function permisos(rol_id) {
  $("#mdltitulo2").html("Asignacion de Permisos");

  tabla = $("#permisos_data")
    .dataTable({
      aProcessing: true,
      aServerSide: true,
      dom: "Bfrtip",
      searching: true,
      lengthChange: false,
      colReorder: true,
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
      ajax: {
        url: "../../controller/Menu.php?op=listarxrol",
        type: "post",
        dataType: "json",
        data: { rol_id: rol_id },
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

  $("#modalPermisos").modal("show");
}

function habilitar(permiso_id) {
  $.post(
    "../../controller/Menu.php?op=habilitar",
    { permiso_id: permiso_id },
    function (data) {
      $("#permisos_data").DataTable().ajax.reload();
    }
  );
}

function deshabilitar(permiso_id) {
  $.post(
    "../../controller/Menu.php?op=deshabilitar",
    { permiso_id: permiso_id },
    function (data) {
      $("#permisos_data").DataTable().ajax.reload();
    }
  );
}


init();
