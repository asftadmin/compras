let lineasDashboard = [];
let evaluacionesDashboard = [];
let proveedoresEvaluacionDashboard = [];
let graficoComprasAnios = null;
let graficoEvaluacionesProveedores = null;

$(document).ready(function () {
    configurarSelectoresDashboard();
    cargarLineasDashboard();
    cargarEvaluacionesDashboard();

    $("#btn_consultar_compras").on("click", function () {
        consultarComprasComparativo();
    });

    $("#btn_consultar_evaluaciones").on("click", function () {
        consultarComparativoEvaluaciones();
    });

    $('a[data-toggle="pill"]').on("shown.bs.tab", function () {
        if (graficoComprasAnios) {
            graficoComprasAnios.resize();
        }

        if (graficoEvaluacionesProveedores) {
            graficoEvaluacionesProveedores.resize();
        }
    });
});

function configurarSelectoresDashboard() {
    const $proveedoresEvaluacion = $("#proveedores_evaluacion_dashboard");

    $proveedoresEvaluacion.select2({
        width: "100%",
        placeholder: "Seleccione proveedores",
        allowClear: true
    });
    const $linea = $("#linea_dashboard");
    const $anios = $("#anios_comparativo");
    const $evaluacionUno = $("#evaluacion_uno_dashboard");
    const $evaluacionDos = $("#evaluacion_dos_dashboard");

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

        $evaluacionUno.select2({
            width: "100%",
            placeholder: "Seleccione una evaluacion"
        });

        $evaluacionDos.select2({
            width: "100%",
            placeholder: "Seleccione una evaluacion"
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

function cargarEvaluacionesDashboard() {
    $.ajax({
        url: "../../controller/Evaluacion.php?op=listarEvaluacionesDashboard",
        type: "POST",
        dataType: "json",
        success: function (response) {
            if (!response || !response.success || !Array.isArray(response.aaData)) {
                mostrarAlerta("Error", response?.message || "No fue posible cargar las evaluaciones.", "error");
                return;
            }

            evaluacionesDashboard = response.aaData;
            llenarSelectEvaluacionesDashboard();
        },
        error: function () {
            mostrarAlerta("Error", "No fue posible conectar con el servidor para cargar las evaluaciones.", "error");
        }
    });
}

function llenarSelectEvaluacionesDashboard() {
    const $evaluacionUno = $("#evaluacion_uno_dashboard");
    const $evaluacionDos = $("#evaluacion_dos_dashboard");

    $evaluacionUno.empty().append('<option value="">Seleccione una evaluacion</option>');
    $evaluacionDos.empty().append('<option value="">Seleccione una evaluacion</option>');

    evaluacionesDashboard.forEach(function (evaluacion) {
        if (evaluacion.codi_eval && evaluacion.label) {
            const option = `<option value="${evaluacion.codi_eval}">${escaparHtml(evaluacion.label)}</option>`;
            $evaluacionUno.append(option);
            $evaluacionDos.append(option);
        }
    });

    if (evaluacionesDashboard.length >= 2) {
        $evaluacionUno.val(evaluacionesDashboard[1].codi_eval);
        $evaluacionDos.val(evaluacionesDashboard[0].codi_eval);
    }

    $evaluacionUno.trigger("change");
    $evaluacionDos.trigger("change");
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

function llenarSelectProveedoresEvaluacion(filas) {
    const $select = $("#proveedores_evaluacion_dashboard");
    const seleccionados = $select.val() || [];

    $select.empty();

    filas.forEach(function (fila) {
        if (fila.nit_proveedor && fila.razon_social) {
            $select.append(
                `<option value="${escaparHtml(fila.nit_proveedor)}">${escaparHtml(fila.razon_social)} - ${escaparHtml(fila.nit_proveedor)}</option>`
            );
        }
    });

    $select.val(seleccionados).trigger("change");
}

function consultarComparativoEvaluaciones() {
    const evaluacionUno = $("#evaluacion_uno_dashboard").val();
    const evaluacionDos = $("#evaluacion_dos_dashboard").val();
    const proveedores = $("#proveedores_evaluacion_dashboard").val() || [];

    if (!evaluacionUno || !evaluacionDos) {
        mostrarAlerta("Seleccione evaluaciones", "Debe seleccionar dos evaluaciones para comparar.", "warning");
        return;
    }

    if (evaluacionUno === evaluacionDos) {
        mostrarAlerta("Seleccione evaluaciones diferentes", "La evaluacion base y la comparativa no pueden ser la misma.", "warning");
        return;
    }

    setEstadoCargaEvaluaciones(true);
    mostrarMensajeEvaluaciones('<i class="fas fa-spinner fa-spin mr-1"></i> Consultando evaluaciones...');

    $.ajax({
        url: "../../controller/Evaluacion.php?op=compararEvaluacionesDashboard",
        type: "POST",
        dataType: "json",
        data: {
            evaluacion_uno: evaluacionUno,
            evaluacion_dos: evaluacionDos,
            proveedores: proveedores
        },
        success: function (response) {
            if (!response || !response.success) {
                limpiarDashboardEvaluaciones();
                mostrarMensajeEvaluaciones(response?.message || "No fue posible comparar las evaluaciones.");
                mostrarAlerta("Error", response?.message || "No fue posible comparar las evaluaciones.", "error");
                return;
            }

            renderDashboardEvaluaciones(response.data);
        },
        error: function (xhr) {
            limpiarDashboardEvaluaciones();
            mostrarMensajeEvaluaciones("No fue posible conectar con el servidor.");
            console.error(xhr.responseText);
            mostrarAlerta("Error", "No fue posible conectar con el servidor.", "error");
        },
        complete: function () {
            setEstadoCargaEvaluaciones(false);
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

function renderDashboardEvaluaciones(data) {
    if (!data || !Array.isArray(data.labels) || !Array.isArray(data.datasets) || !Array.isArray(data.tabla)) {
        limpiarDashboardEvaluaciones();
        mostrarMensajeEvaluaciones("No se recibieron datos para comparar.");
        return;
    }

    if (data.tabla.length === 0) {
        limpiarDashboardEvaluaciones();
        mostrarMensajeEvaluaciones("No hay proveedores con calificacion para comparar.");
        return;
    }

    llenarSelectProveedoresEvaluacion(data.tabla);

    renderIndicadoresEvaluaciones(data);
    mostrarGraficoEvaluaciones();
    generarGraficoEvaluaciones(data);
    renderTablaEvaluaciones(data);
}

function renderIndicadoresEvaluaciones(data) {
    const $contenedor = $("#indicadores_evaluaciones");
    const resumen = data.resumen || {};
    const evaluaciones = data.evaluaciones || [];
    const evalUno = evaluaciones[0] || {};
    const evalDos = evaluaciones[1] || {};
    const diferencia = resumen.diferencia;

    let claseDiferencia = "text-muted";
    if (Number(diferencia) > 0) claseDiferencia = "text-success";
    if (Number(diferencia) < 0) claseDiferencia = "text-danger";

    const html = `
        <div class="col-sm-6 col-xl-3">
            <div class="card dashboard-total-card">
                <div class="card-body">
                    <div class="total-label">${escaparHtml(evalUno.anio || "Base")}</div>
                    <div class="total-value">${formatearPorcentaje(resumen.promedio_uno)}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card dashboard-total-card">
                <div class="card-body">
                    <div class="total-label">${escaparHtml(evalDos.anio || "Comparativa")}</div>
                    <div class="total-value">${formatearPorcentaje(resumen.promedio_dos)}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card dashboard-total-card">
                <div class="card-body">
                    <div class="total-label">Variacion</div>
                    <div class="total-value ${claseDiferencia}">${formatearDiferenciaPorcentaje(diferencia)}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card dashboard-total-card">
                <div class="card-body">
                    <div class="total-label">Proveedores</div>
                    <div class="total-value">${Number(resumen.proveedores || 0).toLocaleString("es-CO")}</div>
                </div>
            </div>
        </div>
    `;

    $contenedor.html(html);
}

function generarGraficoEvaluaciones(data) {
    const canvas = document.getElementById("grafico_evaluaciones_proveedores");
    const ctx = canvas.getContext("2d");
    const anchoMinimo = Math.max(920, data.labels.length * Math.max(95, data.datasets.length * 42));

    $("#grafico_evaluaciones_box").css("min-width", `${anchoMinimo}px`);

    const datasets = data.datasets.map(function (dataset) {
        return {
            ...dataset,
            maxBarThickness: 42
        };
    });

    if (graficoEvaluacionesProveedores) {
        graficoEvaluacionesProveedores.destroy();
    }

    const plugins = typeof ChartDataLabels !== "undefined" ? [ChartDataLabels] : [];

    graficoEvaluacionesProveedores = new Chart(ctx, {
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
                            return `${context.dataset.label}: ${formatearPorcentaje(context.raw)}`;
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
                        return value !== null && value !== undefined ? formatearPorcentaje(value) : "";
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
                    suggestedMax: 100,
                    ticks: {
                        callback: function (value) {
                            return `${value}%`;
                        }
                    },
                    title: {
                        display: true,
                        text: "Calificacion"
                    }
                }
            }
        },
        plugins: plugins
    });
}

function renderTablaEvaluaciones(data) {
    const evaluaciones = data.evaluaciones || [];
    const evalUno = evaluaciones[0] || {};
    const evalDos = evaluaciones[1] || {};
    const filas = data.tabla || [];

    $("#th_eval_uno").text(evalUno.nomb_eval || "Evaluacion base");
    $("#th_eval_dos").text(evalDos.nomb_eval || "Evaluacion comparativa");

    let html = "";

    filas.forEach(function (fila) {
        const diferencia = fila.diferencia;
        let badgeClass = "badge-secondary";
        if (Number(diferencia) > 0) badgeClass = "badge-success";
        if (Number(diferencia) < 0) badgeClass = "badge-danger";

        html += `
            <tr>
                <td>${escaparHtml(fila.nit_proveedor)}</td>
                <td>${escaparHtml(fila.razon_social)}</td>
                <td class="text-right">${formatearPorcentaje(fila.valor_uno)}</td>
                <td class="text-right">${formatearPorcentaje(fila.valor_dos)}</td>
                <td class="text-right">
                    <span class="badge ${badgeClass}">${formatearDiferenciaPorcentaje(diferencia)}</span>
                </td>
            </tr>
        `;
    });

    $("#tabla_comparativo_evaluaciones").html(html);
    $("#contenedor_tabla_evaluaciones").removeClass("d-none");
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

function setEstadoCargaEvaluaciones(cargando) {
    const $boton = $("#btn_consultar_evaluaciones");

    if (cargando) {
        $boton
            .prop("disabled", true)
            .html('<i class="fas fa-spinner fa-spin mr-1"></i> Comparando');
        return;
    }

    $boton
        .prop("disabled", false)
        .html('<i class="fas fa-search mr-1"></i> Comparar');
}

function mostrarMensajeCompras(mensaje) {
    limpiarGraficoCompras();
    $("#mensaje_compras_inicial").removeClass("d-none").html(mensaje);
}

function mostrarMensajeEvaluaciones(mensaje) {
    limpiarDashboardEvaluaciones();
    $("#mensaje_evaluaciones_inicial").removeClass("d-none").html(mensaje);
}

function mostrarGraficoCompras() {
    $("#mensaje_compras_inicial").addClass("d-none").empty();
    $("#contenedor_grafico_compras").removeClass("d-none");
}

function mostrarGraficoEvaluaciones() {
    $("#mensaje_evaluaciones_inicial").addClass("d-none").empty();
    $("#contenedor_grafico_evaluaciones").removeClass("d-none");
}

function limpiarGraficoCompras() {
    $("#contenedor_grafico_compras").addClass("d-none");

    if (graficoComprasAnios) {
        graficoComprasAnios.destroy();
        graficoComprasAnios = null;
    }
}

function limpiarDashboardEvaluaciones() {
    $("#contenedor_grafico_evaluaciones").addClass("d-none");
    $("#contenedor_tabla_evaluaciones").addClass("d-none");
    $("#tabla_comparativo_evaluaciones").empty();
    $("#indicadores_evaluaciones").empty();

    if (graficoEvaluacionesProveedores) {
        graficoEvaluacionesProveedores.destroy();
        graficoEvaluacionesProveedores = null;
    }
}

function formatearPesosMillones(valorPesos) {
    return formatearMillonesNumero(Number(valorPesos || 0) / 1000000);
}

function formatearPorcentaje(valor) {
    if (valor === null || valor === undefined || valor === "") {
        return "Sin dato";
    }

    const numero = Number(valor);

    if (Number.isNaN(numero)) {
        return "Sin dato";
    }

    return `${numero.toLocaleString("es-CO", {
        minimumFractionDigits: 1,
        maximumFractionDigits: 2
    })}%`;
}

function formatearDiferenciaPorcentaje(valor) {
    if (valor === null || valor === undefined || valor === "") {
        return "Sin dato";
    }

    const numero = Number(valor);

    if (Number.isNaN(numero)) {
        return "Sin dato";
    }

    const signo = numero > 0 ? "+" : "";

    return `${signo}${numero.toLocaleString("es-CO", {
        minimumFractionDigits: 1,
        maximumFractionDigits: 2
    })}%`;
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

function escaparHtml(valor) {
    return String(valor ?? "").replace(/[&<>"']/g, function (caracter) {
        return {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        }[caracter];
    });
}
