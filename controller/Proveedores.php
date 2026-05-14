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


    case 'comprasPorLinea':
        header('Content-Type: application/json');
        ob_start();

        try {
            $linea_id = $_POST['linea_id'] ?? null;
            $anio     = $_POST['año'] ?? 2025;

            if (!$linea_id) {
                throw new Exception('ID de línea no proporcionado');
            }

            $proveedor     = $proveedores->get_by_linea_con_siesa($linea_id);
            $codigos_siesa = array_unique(array_map('intval', array_column($proveedor, 'prov_cod_siesa')));

            if (empty($codigos_siesa)) {
                throw new Exception('No hay proveedores con códigos SIESA');
            }

            $totales       = [];
            $detalle_items = [];

            $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-01-01');
            $fecha_fin    = $_POST['fecha_fin']    ?? date('Y-12-31');

            // Mapa de codigo => nombre proveedor para usarlo dentro del loop
            $mapa_proveedores = [];
            foreach ($proveedor as $prov) {
                $mapa_proveedores[(int)$prov['prov_cod_siesa']] = $prov['prov_descripcion'];
            }

            foreach ($codigos_siesa as $proveedor_codigo) {
                $proveedor_codigo = (int) $proveedor_codigo;
                $pagina_actual    = 1;

                while (true) {
                    $url  = "idCompania=6026";
                    $url .= "&descripcion=asfaltart_t350_docto_contable_entradas";
                    $url .= "&paginacion=numPag={$pagina_actual}|tamPag=100";
                    $url .= "&parametros=fechainicio={$fecha_inicio}|fechafin={$fecha_fin}|proveedor={$proveedor_codigo}";

                    $response = CurlController::requestEstandar($url, 'GET');

                    if (!$response || !isset($response->detalle->Datos)) {
                        break;
                    }

                    $datos               = $response->detalle->Datos;
                    $registros_recibidos = count($datos);

                    foreach ($datos as $compra) {
                        $rowid_tercero  = $compra->f350_rowid_tercero ?? null;
                        $estado_doc     = $compra->f054_id ?? null;
                        $vlr_neto_item  = (float)($compra->f470_vlr_neto  ?? 0);
                        $vlr_bruto_item = (float)($compra->f470_vlr_bruto ?? 0);
                        $cant           = (float)($compra->f470_cant_1    ?? 0);
                        $referencia     = $compra->f120_referencia  ?? 'SIN REF';
                        $descripcion    = $compra->f120_descripcion ?? 'SIN DESC';

                        // Excluir estados 0 y 2
                        if (in_array($estado_doc, [0, 2])) continue;

                        // Solo ítems con valor
                        if ($vlr_neto_item <= 0) continue;

                        // ✅ Acumular total por proveedor (para el gráfico)
                        $totales[$rowid_tercero] = ($totales[$rowid_tercero] ?? 0) + $vlr_neto_item;

                        // ✅ Acumular detalle agrupado por proveedor + ítem (para la tabla)
                        $key_item = $rowid_tercero . '_' . $referencia;
                        if (!isset($detalle_items[$key_item])) {
                            $detalle_items[$key_item] = [
                                'rowid_tercero' => $rowid_tercero,
                                'proveedor'     => $mapa_proveedores[$proveedor_codigo] ?? 'SIN NOMBRE',
                                'referencia'    => $referencia,
                                'descripcion'   => $descripcion,
                                'cantidad'      => 0,
                                'vlr_bruto'     => 0,
                                'vlr_neto'      => 0,
                            ];
                        }
                        $detalle_items[$key_item]['cantidad']  += $cant;
                        $detalle_items[$key_item]['vlr_bruto'] += $vlr_bruto_item;
                        $detalle_items[$key_item]['vlr_neto']  += $vlr_neto_item;
                    }

                    if ($registros_recibidos < 100) {
                        break;
                    }

                    $pagina_actual++;
                }
            }

            // Preparar datos para el gráfico
            $labels  = [];
            $valores = [];

            foreach ($proveedor as $prov) {
                $codigo    = (int)$prov['prov_cod_siesa'];
                $labels[]  = $prov['prov_descripcion'];
                $valores[] = round($totales[$codigo] ?? 0, 2);
            }

            ob_end_clean();
            echo json_encode([
                'success' => true,
                'data'    => [
                    'labels'   => $labels,
                    'datasets' => [[
                        'label'           => "Compras {$anio}",
                        'data'            => $valores,
                        'backgroundColor' => array_map(function () {
                            return 'rgba(' . rand(50, 200) . ',' . rand(50, 200) . ',' . rand(50, 200) . ',0.7)';
                        }, $labels)
                    ]],
                    'detalle' => array_values($detalle_items)
                ]
            ]);
        } catch (Exception $e) {
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => null
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