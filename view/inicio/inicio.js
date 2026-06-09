let lineasDashboard = [];
let graficoComprasAnios = null;

$(document).ready(function () {
    configurarSelectoresDashboard();
    cargarLineasDashboard();

    $("#btn_consultar_compras").on("click", function () {
        consultarComprasComparativo();
    });
});

function configurarSelectoresDashboard() {
    const $linea = $("#linea_dashboard");
    const $anios = $("#anios_comparativo");

    if ($.fn.select2) {
        $linea.select2({
            width: "100%",
            placeholder: "Seleccione una linea"
        });

        $anios.select2({
            width: "100%",
            placeholder: "Seleccione hasta 3 anios",
            maximumSelectionLength: 3
        });

        $anios.on("select2:selecting", function (e) {
            const seleccionados = $(this).val() || [];

            if (seleccionados.length >= 3) {
                e.preventDefault();
                mostrarAlerta("Limite alcanzado", "Solo puede comparar hasta tres anios.", "warning");
            }
        });
    }

    $anios.on("change", function () {
        const seleccionados = $(this).val() || [];

        if (seleccionados.length > 3) {
            $(this).val(seleccionados.slice(0, 3)).trigger("change");
            mostrarAlerta("Limite alcanzado", "Solo puede comparar hasta tres anios.", "warning");
        }
    });
}

function cargarLineasDashboard() {
    $.ajax({
        url: "../../controller/Linea.php?op=consultarLinea",
        type: "POST",
        dataType: "json",
        success: function (response) {
            if (!response || !response.success || !Array.isArray(response.aaData)) {
                mostrarAlerta("Error", "No fue posible cargar las lineas.", "error");
                return;
            }

            lineasDashboard = response.aaData;
            llenarSelectLineasDashboard();
        },
        error: function () {
            mostrarAlerta("Error", "No fue posible conectar con el servidor para cargar las lineas.", "error");
        }
    });
}

function llenarSelectLineasDashboard() {
    const $selectLinea = $("#linea_dashboard");
    $selectLinea.empty().append('<option value="">Seleccione una linea</option>');

    lineasDashboard.forEach(function (linea) {
        if (linea.linea_id && linea.linea_nombre) {
            $selectLinea.append(
                `<option value="${linea.linea_id}">${linea.linea_nombre}</option>`
            );
        }
    });

    $selectLinea.trigger("change");
}

function consultarComprasComparativo() {
    const lineaId = $("#linea_dashboard").val();
    const anios = obtenerAniosSeleccionados();

    if (!lineaId) {
        mostrarAlerta("Seleccione una linea", "Debe seleccionar una linea para consultar.", "warning");
        return;
    }

    if (anios.length === 0) {
        mostrarAlerta("Seleccione anios", "Debe seleccionar al menos un anio.", "warning");
        return;
    }

    if (anios.length > 3) {
        mostrarAlerta("Limite alcanzado", "Solo puede comparar hasta tres anios.", "warning");
        return;
    }

    setEstadoCarga(true);
    mostrarMensajeCompras('<i class="fas fa-spinner fa-spin mr-1"></i> Consultando compras...');

    $.ajax({
        url: "../../controller/Proveedores.php?op=comprasComparativoAnios",
        type: "POST",
        dataType: "json",
        data: {
            linea_id: lineaId,
            anios: anios
        },
        success: function (response) {
            if (!response || !response.success) {
                limpiarGraficoCompras();
                mostrarMensajeCompras(response?.message || "No fue posible consultar las compras.");
                mostrarAlerta("Error", response?.message || "No fue posible consultar las compras.", "error");
                return;
            }

            renderDashboardCompras(response.data);
        },
        error: function (xhr) {
            limpiarGraficoCompras();
            mostrarMensajeCompras("No fue posible conectar con el servidor.");
            console.error(xhr.responseText);
            mostrarAlerta("Error", "No fue posible conectar con el servidor.", "error");
        },
        complete: function () {
            setEstadoCarga(false);
        }
    });
}

function obtenerAniosSeleccionados() {
    const seleccionados = $("#anios_comparativo").val() || [];

    return seleccionados
        .map(function (anio) {
            return parseInt(anio, 10);
        })
        .filter(function (anio) {
            return !Number.isNaN(anio);
        });
}

function renderDashboardCompras(data) {
    if (!data || !Array.isArray(data.labels) || !Array.isArray(data.datasets)) {
        limpiarGraficoCompras();
        mostrarMensajeCompras("No se recibieron datos para graficar.");
        return;
    }

    renderIndicadoresCompras(data);

    const totalGeneral = data.datasets.reduce(function (totalDataset, dataset) {
        return totalDataset + dataset.data.reduce(function (total, valor) {
            return total + Number(valor || 0);
        }, 0);
    }, 0);

    if (totalGeneral <= 0) {
        limpiarGraficoCompras();
        mostrarMensajeCompras("No hay compras registradas para la linea y anios seleccionados.");
        return;
    }

    mostrarGraficoCompras();
    generarGraficoComprasAnios(data);
}

function renderIndicadoresCompras(data) {
    const $contenedor = $("#indicadores_compras");
    const totales = data.totales_anio || {};
    const anios = Array.isArray(data.anios) ? data.anios : Object.keys(totales);

    let html = "";

    anios.forEach(function (anio) {
        const total = Number(totales[anio] || 0);

        html += `
            <div class="col-sm-6 col-xl-4">
                <div class="card dashboard-total-card">
                    <div class="card-body">
                        <div class="total-label">Total ${anio}</div>
                        <div class="total-value">${formatearPesosMillones(total)}</div>
                    </div>
                </div>
            </div>
        `;
    });

    $contenedor.html(html);
}

function generarGraficoComprasAnios(data) {
    const canvas = document.getElementById("grafico_compras_anios");
    const ctx = canvas.getContext("2d");
    const anchoMinimo = Math.max(920, data.labels.length * Math.max(95, data.datasets.length * 42));

    $("#grafico_compras_box").css("min-width", `${anchoMinimo}px`);

    const datasets = data.datasets.map(function (dataset) {
        return {
            ...dataset,
            data: dataset.data.map(function (valor) {
                return Number((Number(valor || 0) / 1000000).toFixed(2));
            }),
            maxBarThickness: 46
        };
    });

    if (graficoComprasAnios) {
        graficoComprasAnios.destroy();
    }

    const plugins = typeof ChartDataLabels !== "undefined" ? [ChartDataLabels] : [];

    graficoComprasAnios = new Chart(ctx, {
        type: "bar",
        data: {
            labels: data.labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: "index",
                intersect: false
            },
            plugins: {
                legend: {
                    position: "top"
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `${context.dataset.label}: ${formatearMillonesNumero(context.raw)}`;
                        }
                    }
                },
                datalabels: {
                    anchor: "end",
                    align: "end",
                    clamp: true,
                    color: "#212529",
                    font: {
                        size: 10,
                        weight: "600"
                    },
                    formatter: function (value) {
                        return value > 0 ? formatearMillonesNumero(value) : "";
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 35,
                        minRotation: 0,
                        callback: function (value) {
                            const label = this.getLabelForValue(value);
                            return label.length > 22 ? `${label.substring(0, 22)}...` : label;
                        }
                    },
                    title: {
                        display: true,
                        text: "Proveedores"
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return formatearMillonesNumero(value);
                        }
                    },
                    title: {
                        display: true,
                        text: "Millones de pesos"
                    }
                }
            }
        },
        plugins: plugins
    });
}

function setEstadoCarga(cargando) {
    const $boton = $("#btn_consultar_compras");

    if (cargando) {
        $boton
            .prop("disabled", true)
            .html('<i class="fas fa-spinner fa-spin mr-1"></i> Consultando');
        return;
    }

    $boton
        .prop("disabled", false)
        .html('<i class="fas fa-search mr-1"></i> Consultar');
}

function mostrarMensajeCompras(mensaje) {
    limpiarGraficoCompras();
    $("#mensaje_compras_inicial").removeClass("d-none").html(mensaje);
}

function mostrarGraficoCompras() {
    $("#mensaje_compras_inicial").addClass("d-none").empty();
    $("#contenedor_grafico_compras").removeClass("d-none");
}

function limpiarGraficoCompras() {
    $("#contenedor_grafico_compras").addClass("d-none");

    if (graficoComprasAnios) {
        graficoComprasAnios.destroy();
        graficoComprasAnios = null;
    }
}

function formatearPesosMillones(valorPesos) {
    return formatearMillonesNumero(Number(valorPesos || 0) / 1000000);
}

function formatearMillonesNumero(valorMillones) {
    const valor = Number(valorMillones || 0);
    const decimales = Math.abs(valor) >= 100 ? 0 : 1;

    return `$${valor.toLocaleString("es-CO", {
        minimumFractionDigits: decimales,
        maximumFractionDigits: decimales
    })} M`;
}

function mostrarAlerta(titulo, texto, icono) {
    if (typeof Swal !== "undefined") {
        Swal.fire(titulo, texto, icono || "info");
        return;
    }

    alert(`${titulo}\n${texto}`);
}
