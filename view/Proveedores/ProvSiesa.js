function init() {}

$(document).on("click", "#btn-filtrar", function () {
  cargarProveedorAPI();
});

function cargarProveedorAPI() {
  const proveedor = $("#filtro-nombre").val(); // Obtener el valor del filtro de nombre

  $("#proveedores_data_siesa").DataTable({
    destroy: true, // Eliminar la tabla existente antes de crear una nueva
    processing: true,
    serverSide: false, // Deshabilitar la paginación en el servidor (en caso de que lo necesites)
    ajax: {
      url: "../../controller/Proveedores.php?op=consultaProveedorSiesa",
      type: "GET",
      data: {
        proveedor: proveedor, // Pasar el filtro al backend
      },
      dataSrc: function (json) {
        console.log("Respuesta de la API:", json); // Verificar la respuesta en la consola
        return json.aaData || []; // Retornar los datos, asegúrate de que 'aaData' esté presente en la respuesta
      },
    },
    columns: [
      { data: 0, title: "CODIGO" },
      { data: 1, title: "NIT" },
      { data: 2, title: "RAZON SOCIAL" },
      {
        data: 3,
        title: "Estado",
        render: function (data, type, row) {
          if (data === "registrado") {
            return '<span class="badge bg-success">Registrado</span>';
          } else {
            return '<span class="badge bg-warning text-dark">Nuevo</span>';
          }
        },
      },
      {
        data: null,
        title: "Acciones",
        className: "text-center",
        orderable: false,
        render: function (data, type, row) {
          // Mostrar botón solo si no está registrado
          if (row[3] !== "registrado") {
            return `
                            <button class="btn btn-info btn-sm btn-guardar-proveedores" data-nit="${row[1]}">
                                <i class="fa fa-save"></i>
                            </button>
                        `;
          } else {
            return ""; // No mostrar botón si ya está registrado
          }
        },
      },
    ],
    order: [[1, "asc"]], // Ordenar por la segunda columna
    iDisplayLength: 10, // Número de registros por página
    language: {
      sProcessing: "Procesando...",
      sLengthMenu: "",
      sZeroRecords: "No se encontraron resultados",
      sEmptyTable: "Ningún dato disponible en esta tabla",
      sInfo: "Mostrando un total de TOTAL registros",
      sInfoEmpty: "Mostrando un total de 0 registros",
      sInfoFiltered: "(filtrado de un total de MAX registros)",
      sSearch: "Buscar:",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior",
      },
    },
  });

  // Vaciar el input cuando se complete la carga de datos
  $("#proveedores_data_siesa").on("xhr.dt", function () {
    $("#filtro-nombre").val("");
  });
}

$(document).on("click", ".btn-guardar-proveedores", function () {
  const fila = $(this).closest("tr");
  const table = $("#proveedores_data_siesa").DataTable();
  const datos = table.row(fila).data();

  const payload = {
    nit: datos[0],
    razon_social: datos[1],
  };

  $.ajax({
    url: "../../controller/Proveedores.php?op=guardarProveedorNuevo",
    type: "POST",
    data: payload,
    success: function (response) {
      swal({
        title: "Correcto",
        text: "Proveedor registrado exitosamente.",
        type: "success",
        confirmButtonClass: "btn-success",
      });

      // Actualizar fila con nuevo estado
      datos[2] = "registrado"; // Cambiar estado
      table.row(fila).data(datos).invalidate().draw(false);
    },
    error: function () {
      swal({
        title: "Error",
        text: "No se pudo guardar el Proveedor",
        type: "error",
        confirmButtonClass: "btn-danger",
      });
    },
  });
});



init();
