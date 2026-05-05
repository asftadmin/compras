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



$(document).ready(function() {
    cargarLineasYProveedores();

    $('#btn-filtrar-ordenes').on('click', function() {
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
        success: function(response) {
            if (response.success) {
                // Procesar los datos para agrupar por línea
                proveedoresPorLinea = response.aaData;
                console.log(proveedoresPorLinea);
                llenarSelectLineas();
            } else {
                console.error('Error al cargar líneas y proveedores');
            }
        },
        error: function(xhr, status, error) {
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
        success: function(response) {
            $('#btn-filtrar-ordenes').html('Filtrar');
            
            // Validación básica de respuesta
            if (!response || !response.success || !response.data) {
                alert(response?.message || 'Respuesta inválida');
                return;
            }
            
            // Mostrar gráfico si hay datos
            if (response.data.labels?.length > 0) {
                console.log(response.data);
                generarGraficoCompras(response.data);
            } else {
                alert('No hay compras en 2025 para esta línea');
            }
        },
        error: function(xhr) {
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
                        label: function(context) {
                            return context.parsed.y + ' M';
                        }
                    }
                },
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    color: '#000',
                    formatter: function(value) {
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
                        callback: function(value, index, ticks) {
                            // Opcional: recorta texto muy largo
                            let label = this.getLabelForValue(value);
                            return label.length > 20 ? label.substr(0, 20) + '…' : label;
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' M';
                        }
                    }
                }
            }
        },
        plugins: [ChartDataLabels] // habilitar etiquetas de datos
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
