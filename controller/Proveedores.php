<?php
/*CONTROLADOR USUARIO */
require_once('../config/conexion.php');
require_once('../models/Proveedores.php');
require_once("curl.php");

$proveedores = new Proveedores();

switch ($_GET['op']) {

    /* SELECT PROVEEDORES REPORTE OBRAS */
    case 'combo':
        $datos = $proveedores->get_prov();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option value='' disabled selected>--Selecciona el Proveedor--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['prov_id'] . "'>" . ' - ' . $row['prov_descripcion']  . '</option>';
            }
            echo $html;
        }
        break;

    /* GUARDAR Y EDITAR */
    case 'guardaryeditar':
        $prov_id = $_POST['prov_id'];
        if (empty($prov_id) && $proveedores->provExiste($_POST['prov_nit'])) {
            echo json_encode(array('status' => 'error', 'message' => 'El Proveedor ya se encuentra registrado \n Por Favor,  Verifica los campos'));
        } else {
            if (empty($prov_id)) {
                $proveedores->insert_prov($_POST['prov_nit'], $_POST['prov_cod_siesa'], $_POST['prov_descripcion'], $_POST['prov_estado'], $_POST['prov_linea']);
            } else {
                $proveedores->update_prov($_POST['prov_id'], $_POST['prov_nit'], $_POST['prov_cod_siesa'], $_POST['prov_descripcion'], $_POST['prov_estado'], $_POST['prov_linea']);
            }
            echo json_encode(array('status' => 'success', 'message' => 'Datos guardados correctamente'));
        }
        break;

    /* LISTAR EMPLEADOS X ROL */
    case 'listar':
        $data = array();
        $data = $proveedores->get_prov();
        $formattedData = array();

        foreach ($data as $row) {
            $sub_array = array();
            $sub_array[] = $row['prov_id'];
            $sub_array[] = $row['prov_cod_siesa'];
            $sub_array[] = $row['prov_nit'];
            $sub_array[] = $row['prov_descripcion'];
            if ($row["prov_estado"] === "Activo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color:rgb(1, 167, 48); color: #fff; padding: 8px; border-radius: 5px; width: 200px;">ACTIVO</span></div>';
            } else if ($row["prov_estado"] === "Inactivo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color:rgb(136, 130, 131); color: #fff; padding: 8px; border-radius: 5px; width: 200px;">INACTIVO</span></div>';
            }
            $sub_array[] = $row['linea_nom'];
            $sub_array[] = '<div class="button-container text-center" >
                                        <button type="button" onClick="editar(' . $row['prov_id'] . ');" id="' . $row['prov_id'] . '" class="btn btn-warning btn-icon">
                                            <div><i class="fa fa-edit"></i></div>
                                        </button>
                                        <button type="button" onClick="eliminar(' . $row['prov_id'] . ');" id="' . $row['prov_id'] . '" class="btn btn-danger btn-icon">
                                            <div><i class="fa fa-trash"></i></div>
                                        </button>
                                    </div>';

            $formattedData[] = $sub_array;
        }
        $results = array(
            'sEcho' => 1,
            'iTotalRecords' => count($formattedData),
            'iTotalDisplayRecords' => count($formattedData),
            'aaData' => $formattedData
        );
        echo json_encode($results);
        break;

    /* ELIMINAR */
    case 'eliminar':
        $proveedores->delete_prov($_POST['prov_id']);
        break;

    /* MOSTRAR AL EDITAR */
    case 'mostrar':
        $datos = $proveedores->get_prov_id($_POST['prov_id']);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output['prov_id'] = $row['prov_id'];
                $output['prov_nit'] = $row['prov_nit'];
                $output['prov_descripcion'] = $row['prov_descripcion'];
                $output['prov_estado'] = $row['prov_estado'];
                $output['prov_linea'] = $row['prov_linea'];
            }
            echo json_encode($output);
        }
        break;

    case "consultaProveedorSiesa":
        header('Content-Type: application/json');

        // Validar parámetros
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1; // Página solicitada
        $tamPag = isset($_GET['tamPag']) ? (int)$_GET['tamPag'] : 100; // Número de registros por página
        $proveedor = isset($_GET['proveedor']) ? $_GET['proveedor'] : '';  // Obtener el parámetro 'proveedor'
        try {
            // Construir la URL de la solicitud a la API
            $url = 'idCompania=6026&descripcion=asfaltart_f202_proveedores_nombre';
            $url .= '&paginacion=' . urlencode("numPag=$pagina|tamPag=$tamPag");

            // Agregar parámetro de búsqueda del proveedor
            if (!empty($proveedor)) {
                $url .= '&parametros=' . urlencode("nombre=$proveedor");
            }
            // Realizar la solicitud a la API
            $response = CurlController::requestEstandar($url, "GET");

            // Verificar si la respuesta es válida
            if (!$response || !isset($response->detalle) || !isset($response->detalle->Datos)) {
                throw new Exception("Respuesta de API inválida o sin datos.");
            }

            // Procesar los datos de la API
            $data = [];
            foreach ($response->detalle->Datos as $row) {
                $data[] = [
                    $row->f202_rowid_tercero ?? '',
                    $row->f200_nit ?? '',
                    $row->f202_descripcion_sucursal ?? ''
                ];
            }

            // Obtener el total de registros y el total de páginas
            $totalRegistros = isset($response->detalle->total_registros) ? $response->detalle->total_registros : count($data);
            $totalPaginas = isset($response->detalle->total_páginas) ? $response->detalle->total_páginas : 1;

            // Devolver la respuesta en formato JSON
            echo json_encode([
                "success" => true,
                "iTotalRecords" => $totalRegistros,
                "iTotalDisplayRecords" => $totalRegistros,
                "totalPaginas" => $totalPaginas,
                "aaData" => $data
            ]);
        } catch (Exception $e) {
            // Manejo de errores: si ocurre una excepción, enviar la respuesta de error
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage(),
                "aaData" => []
            ]);
        }
        break;


    case 'consultarProveedores':
        $datos = $proveedores->get_prov();
        $data = array();
        foreach ($datos as $row) {

            $sub_array = array();
            $sub_array[] = $row["prov_nit"];
            $sub_array[] = $row["prov_descripcion"];
            $sub_array[] = $row["prov_cod_siesa"];
            $data[] = $sub_array;
        }


        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);

        break;

    case "guardarProveedorNuevo":

        $nit = $_POST["nit"];
        $descrip = $_POST["razon_social"];

        $resultado = $proveedores->insertarProvNuevo([
            'prov_nit' => $nit,
            'prov_descripcion' => $descrip,
        ]);

        echo json_encode(["success" => $resultado]);
        break;


    case 'comprasPorLinea':
        header('Content-Type: application/json');
        ob_start();

        try {
            $linea_id = $_POST['linea_id'] ?? null;
            $anio = $_POST['año'] ?? 2025;

            if (!$linea_id) {
                throw new Exception('ID de línea no proporcionado');
            }

            // Obtener proveedores con código SIESA
            $proveedor = $proveedores->get_by_linea_con_siesa($linea_id);
            $codigos_siesa = array_unique(array_map('intval', array_column($proveedor, 'prov_cod_siesa')));

            if (empty($codigos_siesa)) {
                throw new Exception('No hay proveedores con códigos SIESA');
            }

            $totales = [];
            $lineas_sumadas = [];

            $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-01-01'); // Default: inicio de año actual
            $fecha_fin = $_POST['fecha_fin'] ?? date('Y-12-31');

            foreach ($codigos_siesa as $proveedor_codigo) {
                $proveedor_codigo = (int) $proveedor_codigo;

                $pagina_actual = 1;
                $total_paginas_proveedor = 0;
                $total_registros_proveedor = 0;

                while (true) {
                    // Construir URL para esta página y proveedor
                    $url = "idCompania=6026";
                    $url .= "&descripcion=asfaltart_t350_docto_contable_entradas";
                    $url .= "&paginacion=numPag={$pagina_actual}|tamPag=100";
                    $url .= "&parametros=fechainicio={$fecha_inicio}|fechafin={$fecha_fin}|proveedor={$proveedor_codigo}";

                    //file_put_contents('debug_urls.log', "Página {$pagina_actual} | Proveedor {$proveedor_codigo} | URL: {$url}\n", FILE_APPEND);

                    $response = CurlController::requestEstandar($url, 'GET');

                    // Log: guardar la respuesta completa aunque sea inválida
                    //$log_filename = "debug_respuesta_pagina_{$anio}_proveedor_{$proveedor_codigo}.log";
                    /*file_put_contents(
                        $log_filename,
                        "Página {$pagina_actual}\n" . json_encode($response, JSON_PRETTY_PRINT) . "\n\n",
                        FILE_APPEND
                    );*/

                    if (!$response) {
                        //file_put_contents('debug_errores_api.log', "Error: Respuesta nula para proveedor {$proveedor_codigo} en página {$pagina_actual}\n", FILE_APPEND);
                        break; // Saltar a siguiente proveedor
                    }

                    if (!isset($response->detalle) || !isset($response->detalle->Datos)) {
                        //file_put_contents('debug_errores_api.log', "Error: Nodo Datos no encontrado para proveedor {$proveedor_codigo} en página {$pagina_actual}\n", FILE_APPEND);
                        break; // Saltar a siguiente proveedor
                    }

                    $datos = $response->detalle->Datos;
                    $registros_recibidos = count($datos);

                    /*file_put_contents(
                        'debug_total_datos.log',
                        "Página {$pagina_actual} | Proveedor {$proveedor_codigo} - Registros recibidos: {$registros_recibidos}\n",
                        FILE_APPEND
                    );*/

                    $total_paginas_proveedor++;
                    $total_registros_proveedor += $registros_recibidos;

                    foreach ($datos as $compra) {
                        $rowid_tercero = $compra->f350_rowid_tercero ?? null;
                        $tipo_doc_entrada = $compra->f350_id_tipo_docto ?? 'N/A';
                        $consec_doc_entrada = $compra->f350_consec_docto ?? 'N/A';
                        $vlr_neto = $compra->f451_vlr_neto ?? 0;
                        $rowid_compra = $compra->f451_rowid_docto ?? null;
                        $estado_doc = $compra->f054_id ?? null;
                        $centro_operacion = $compra->f350_id_co ?? null;

                        //file_put_contents('debug_pre_filtro_estado.log', "RowID: {$rowid_compra} | Estado: {$estado_doc}\n", FILE_APPEND);


                        // Excluir documentos con estado 0 o 2
                        if (in_array($estado_doc, [0, 2])) {
                            //file_put_contents('debug_documentos_excluidos.log', "EXCLUIDO: RowIDCompra {$rowid_compra} | Estado: {$estado_doc}\n", FILE_APPEND);
                            continue;
                        }

                        // Usar f451_rowid_docto como clave única
                        $clave_unica = $rowid_compra;

                        //file_put_contents('debug_pre_clave_unica.log', "Clave: {$clave_unica} | RowIDCompra: {$rowid_compra} | Valor: {$vlr_neto}\n", FILE_APPEND);


                        if (!isset($lineas_sumadas[$clave_unica])) {
                            $totales[$rowid_tercero] = ($totales[$rowid_tercero] ?? 0) + $vlr_neto;
                            $lineas_sumadas[$clave_unica] = true;

                            // Log de compras válidas
                            /*$log_linea = "ProveedorRowID: {$rowid_tercero} | CO: {$centro_operacion} | TipoDocEntrada: {$tipo_doc_entrada} | ConsecDocEntrada: {$consec_doc_entrada} | Valor: {$vlr_neto}\n";
                            file_put_contents('debug_compras_validas.log', $log_linea, FILE_APPEND);*/
                        }
                    }

                    // 🚨 Si la API devuelve menos de 100 registros, asumimos última página
                    if ($registros_recibidos < 100) {
                        break;
                    }

                    $pagina_actual++;
                }

                // Log resumen por proveedor
                /*file_put_contents(
                    'debug_resumen_proveedor.log',
                    "Proveedor {$proveedor_codigo} | Total páginas: {$total_paginas_proveedor} | Total registros: {$total_registros_proveedor}\n",
                    FILE_APPEND
                );*/
            }

            // Log final
            //file_put_contents('debug_totales_final.log', print_r($totales, true));

            // Preparar datos para la respuesta
            $labels = [];
            $valores = [];

            foreach ($proveedor as $prov) {
                $codigo = (int)$prov['prov_cod_siesa'];
                $labels[] = $prov['prov_descripcion'];
                $valores[] = $totales[$codigo] ?? 0;
            }

            ob_end_clean();
            echo json_encode([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'datasets' => [[
                        'label' => "Compras {$anio}",
                        'data' => $valores,
                        'backgroundColor' => array_map(function () {
                            return 'rgba(' . rand(50, 200) . ',' . rand(50, 200) . ',' . rand(50, 200) . ',0.7)';
                        }, $labels)
                    ]]
                ]
            ]);
        } catch (Exception $e) {
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ]);
        }
        break;




    case 'ordenesCompraRegistradas':
        header('Content-Type: application/json');

        // Parámetros de entrada
        $pagina = $_GET['pagina'] ?? 1;
        $tamPag = $_GET['tamPag'] ?? 100;
        $fechainicio = $_GET['fechainicio'] ?? '';
        $fechafin = $_GET['fechafin'] ?? '';

        // Obtener NITs de proveedores locales
        $proveedoresLocales = $proveedores->get_prov(); // método que obtenga todos los proveedores
        $nitsLocales = array_column($proveedoresLocales, 'prov_nit');

        // Construir la URL de la API de Siesa
        $url = 'idCompania=6026&descripcion=asfaltart_t421_ordenes_de_compra';
        $url .= '&paginacion=' . urlencode("numPag=$pagina|tamPag=$tamPag");

        if (!empty($fechainicio)) {
            $parametros = "fechainicio=$fechainicio";
            if (!empty($fechafin)) {
                $parametros .= "|fechafin=$fechafin";
            }
            $url .= '&parametros=' . urlencode($parametros);
        }
        try {
            $response = CurlController::requestEstandar($url, 'GET');

            // Verificar si la respuesta es válida
            if (!$response || !isset($response->detalle) || !isset($response->detalle->Datos)) {
                throw new Exception("Respuesta de API inválida o sin datos.");
            }

            // Procesar los datos de la API
            $data = [];
            foreach ($response->detalle->Datos as $row) {
                $data[] = [
                    $row->f420_rowid_tercero_prov ?? '',
                    $row->f120_descripcion ?? '',
                    $row->f421_vlr_neto ?? '',
                    $row->f420_fecha_ts_creacion ?? '',
                ];
            }

            // Obtener el total de registros y el total de páginas
            $totalRegistros = isset($response->detalle->total_registros) ? $response->detalle->total_registros : count($data);
            $totalPaginas = isset($response->detalle->total_páginas) ? $response->detalle->total_páginas : 1;

            // Devolver la respuesta en formato JSON
            echo json_encode([
                "success" => true,
                "iTotalRecords" => $totalRegistros,
                "iTotalDisplayRecords" => $totalRegistros,
                "totalPaginas" => $totalPaginas,
                "aaData" => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'aaData' => []
            ]);
        }
        break;
}
