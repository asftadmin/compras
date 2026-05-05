let tabla;

let proveedoresPorLinea = [];

let chartComprasLinea = null;

function init() {

};

$(function () {
    const inicioAno = moment().startOf('year');
    const finAno = moment().endOf('year');

    $('#rangoFechas').daterangepicker({
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



$(document).ready(function () {
    cargarLineasYProveedores();

    $('#btn-filtrar-ordenes').on('click', function () {
        const lineaId = $('#linea').val();
        if (!lineaId) {
            alert('Por favor seleccione una línea');
            return;
        }

        const lineaSeleccionada = proveedoresPorLinea.find(l => l.linea_id == lineaId);
        if (!lineaSeleccionada) {
            alert('Línea no encontrada');
            return;
        }

        // Verificar que la propiedad proveedores existe y tiene datos
        if (!lineaSeleccionada.proveedores || !Array.isArray(lineaSeleccionada.proveedores)) {
            alert('No se encontraron proveedores para esta línea');
            return;
        }

        obtenerComprasPorLinea(lineaSeleccionada);
    });


});

function cargarLineasYProveedores() {
    $.ajax({
        url: '../../controller/Linea.php?op=consultarLinea',
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Procesar los datos para agrupar por línea
                proveedoresPorLinea = response.aaData;
                console.log(proveedoresPorLinea);
                llenarSelectLineas();
            } else {
                console.error('Error al cargar líneas y proveedores');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error en la solicitud AJAX:', error);
        }
    });
}

function llenarSelectLineas() {
    const $selectLinea = $('#linea');
    $selectLinea.empty().append('<option value="">Seleccione una línea</option>');

    // Iterar sobre las líneas recibidas
    proveedoresPorLinea.forEach(linea => {
        // Verificar que la línea tenga las propiedades esperadas
        if (linea.linea_id && linea.linea_nombre) {
            $selectLinea.append(
                `<option value="${linea.linea_id}">${linea.linea_nombre}</option>`
            );
        } else {
            console.warn('Objeto de línea no tiene la estructura esperada:', linea);
        }
    });

    // Opcional: Activar select2 si lo estás usando
    if ($.fn.select2) {
        $selectLinea.select2();
    }
}

function obtenerComprasPorLinea(linea) {
    console.log('Línea enviada al servidor:', linea);
    // 1. Corregir nombre de propiedad (de 'proveedores' a 'proveedores')
    if (!linea || !linea.proveedores || !Array.isArray(linea.proveedores)) {
        console.error('Datos incorrectos:', linea);
        alert('Error: Datos de proveedores no disponibles');
        return;
    }

    // 2. Obtener códigos SIESA (asegurar que son strings)
    const codigosSiesa = linea.proveedores.map(p => p.prov_cod_siesa?.toString()).filter(Boolean);

    if (codigosSiesa.length === 0) {
        alert('No hay códigos SIESA válidos');
        return;
    }

    //Obetner rango de fechas
    const rangoFechas = $('#rangoFechas').data('daterangepicker');
    const fechaInicio = rangoFechas.startDate.format('YYYY-MM-DD');
    const fechaFin = rangoFechas.endDate.format('YYYY-MM-DD');

    // 3. Configurar UI
    $('#btn-filtrar-ordenes').html('<i class="fas fa-spinner fa-spin"></i> Cargando...');

    // 4. Petición AJAX (MANTIENO tu estructura original)
    $.ajax({
        url: '../../controller/Proveedores.php?op=comprasPorLinea',
        type: 'POST',
        dataType: 'json',
        data: {
            linea_id: linea.linea_id,
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        },
        success: function (response) {
            $('#btn-filtrar-ordenes').html('Filtrar');

            if (!response || !response.success || !response.data) {
                alert(response?.message || 'Respuesta inválida');
                return;
            }

            // Gráfico
            if (response.data.labels?.length > 0) {
                generarGraficoCompras(response.data);
            }

            // Tabla DataTable
            if (response.data.detalle?.length > 0) {
                generarTablaItems(response.data.detalle);
            } else {
                $('#contenedor-tabla-items').html(
                    '<div class="alert alert-warning mt-3">No hay detalle de ítems disponible.</div>'
                );
            }
        },
        error: function (xhr) {
            $('#btn-filtrar-ordenes').html('Filtrar');
            console.error('Error:', xhr.responseText);
            alert('Error al conectar con el servidor');
        }
    });
}

let chartCompras = null; // para destruir el gráfico anterior si existe

function generarGraficoCompras(data) {
    console.log('Generando gráfico con datos:', data);

    const mensajeInicial = document.getElementById("mensajeInicial");
    const canvas = document.getElementById("graficoCompras");
    const chartContainer = canvas.parentElement;

    // 🚨 Validar si hay datos
    if (!data || !data.labels || data.labels.length === 0) {
        console.warn('No hay datos para mostrar en el gráfico');
        if (chartCompras !== null) {
            chartCompras.destroy();
            chartCompras = null;
        }

        // Mostrar mensaje y ocultar canvas
        chartContainer.style.height = "150px";
        canvas.style.display = "none";
        mensajeInicial.style.display = "block";
        mensajeInicial.textContent = "No hay compras disponibles para la línea seleccionada.";
        return;
    }

    // ✅ Hay datos: ajustar altura y mostrar canvas
    chartContainer.style.height = "360px"; // altura fija para el contenedor
    mensajeInicial.style.display = "none";
    canvas.style.display = "block";

    const ctx = canvas.getContext('2d');

    // ⚡ Escalar valores en millones y redondear a 2 decimales
    const scaledDataset = data.datasets.map(ds => {
        return {
            ...ds,
            data: ds.data.map(val => (val / 1_000_000).toFixed(2))
        };
    });

    if (chartCompras !== null) {
        chartCompras.destroy();
    }

    // 📊 Crear gráfico con etiquetas de datos
    chartCompras = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: scaledDataset
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Compras por proveedor (en millones)',
                    padding: {
                        top: 20,   // espacio arriba
                        bottom: 30 // espacio abajo
                    },
                    font: {
                        size: 16
                    }
                },
                legend: {
                    display: false // Oculta la leyenda
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.parsed.y + ' M';
                        }
                    }
                },
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    color: '#000',
                    formatter: function (value) {
                        return value + ' M';
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxRotation: 0, // ✅ Sin inclinación
                        minRotation: 0,
                        align: 'center', // ✅ Centradas horizontalmente
                        callback: function (value, index, ticks) {
                            // Opcional: recorta texto muy largo
                            let label = this.getLabelForValue(value);
                            return label.length > 20 ? label.substr(0, 20) + '…' : label;
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return value + ' M';
                        }
                    }
                }
            }
        },
        plugins: [ChartDataLabels] // habilitar etiquetas de datos
    });
}

// Variable global para destruir la tabla si ya existe
let tablaItemsDT = null;

function generarTablaItems(detalle) {
    const formatPeso = valor => new Intl.NumberFormat('es-CO', {
        style: 'currency', currency: 'COP', minimumFractionDigits: 0
    }).format(valor);

    // Destruir DataTable anterior si existe
    if (tablaItemsDT) {
        tablaItemsDT.destroy();
        tablaItemsDT = null;
    }

    // Calcular totales generales
    let totalCantidad = 0;
    let totalBruto = 0;
    let totalNeto = 0;

    detalle.forEach(item => {
        totalCantidad += item.cantidad;
        totalBruto += item.vlr_bruto;
        totalNeto += item.vlr_neto;
    });

    // Construir HTML de la tabla
    let filas = '';
    detalle.forEach(item => {
        filas += `
            <tr>
                <td>${item.proveedor}</td>
                <td>${item.referencia}</td>
                <td>${item.descripcion}</td>
                <td class="text-right">${item.cantidad.toLocaleString('es-CO')}</td>
                <td class="text-right">${formatPeso(item.vlr_bruto)}</td>
                <td class="text-right">${formatPeso(item.vlr_neto)}</td>
            </tr>
        `;
    });

    const html = `
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-boxes"></i> Detalle de Ítems por Proveedor</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaItemsDetalle" class="table table-bordered table-hover table-sm w-100">
                        <thead class="thead-dark">
                            <tr>
                                <th>Proveedor</th>
                                <th>Referencia</th>
                                <th>Descripción</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">Vlr Bruto</th>
                                <th class="text-right">Vlr Neto</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${filas}
                        </tbody>
                        <tfoot class="table-dark font-weight-bold">
                            <tr>
                                <td colspan="3">TOTAL GENERAL</td>
                                <td class="text-right">${totalCantidad.toLocaleString('es-CO')}</td>
                                <td class="text-right">${formatPeso(totalBruto)}</td>
                                <td class="text-right">${formatPeso(totalNeto)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    `;

    $('#contenedor-tabla-items').html(html);

    // Inicializar DataTable
    tablaItemsDT = $('#tablaItemsDetalle').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
        order: [[0, 'asc'], [5, 'desc']], // ordenar por proveedor y neto desc
        dom: '<"row mb-2"<"col-sm-6"B><"col-sm-6"f>>rtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Compras por Línea',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column) {
                            // Limpiar formato de moneda para Excel
                            return data.replace(/[$\s.]/g, '').replace(',', '.');
                        }
                    }
                }
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info btn-sm',
                title: 'Compras por Línea',
                fieldSeparator: ';',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Compras por Línea',
                orientation: 'landscape',
                pageSize: 'LETTER',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-secondary btn-sm',
                title: 'Compras por Línea',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columnas',
                className: 'btn btn-warning btn-sm'
            }
        ],
        columnDefs: [
            { targets: [3, 4, 5], className: 'text-right' }, // alinear números a la derecha
            { targets: [2], width: '30%' }                   // descripción más ancha
        ],
        initComplete: function () {
            // Agrupar visualmente por proveedor con color alterno
            let proveedorActual = '';
            let colorClass = 'bg-light';

            this.api().rows().every(function () {
                const data = this.data();
                const proveedor = $(data[0]).text ? $(data[0]).text() : data[0];

                if (proveedor !== proveedorActual) {
                    proveedorActual = proveedor;
                    colorClass = colorClass === 'bg-light' ? '' : 'bg-light';
                }
                $(this.node()).addClass(colorClass);
            });
        }
    });
}






/* function cargarOrdenesCompra() {
    const fechainicio = $('#filtro-fechainicio').val();
    const fechafin = $('#filtro-fechafin').val();

    $('#ordenes_compra_table').DataTable({
        destroy: true,
        processing: true,
        ajax: {
            url: '../../controller/Proveedores.php?op=ordenesCompraRegistradas',
            type: 'GET',
            data: {
                fechainicio: fechainicio,
                fechafin: fechafin
            },
            dataSrc: function (json) {
                console.log(json);
                return json.aaData || [];
            }
        },
        columns: [
            { title: 'ID PROVEEDOR', data: 0 },
            { title: 'DESCRIPCION', data: 1 },
            { title: 'TOTAL', data: 2 },
            { title: 'FECHA', data: 3 },
        ],
        language: {
            sProcessing: "Procesando...",
            sZeroRecords: "No se encontraron órdenes de compra",
            sEmptyTable: "Ningún dato disponible",
            sSearch: "Buscar:",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            }
        }
    });
} */

init();
