<?php
/*CONTROLADOR USUARIO */
require_once('../config/conexion.php');
require_once('../models/Evaluacion.php');
require_once("curl.php");
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$evaluaciones = new Evaluacion();

switch ($_GET['op']) {


    case "guardaryeditar":

        // --- Validar que existan los campos esperados ---
        if (!isset($_POST["rangoFechasEva"]) || !isset($_POST["eval_nombre"])) {
            echo json_encode([
                "status" => "error",
                "message" => "Faltan datos requeridos en la solicitud."
            ]);
            exit;
        }

        // --- Sanitizar y asignar variables ---
        $rangoFechasEva = trim($_POST["rangoFechasEva"]);
        $eval_nombre = trim($_POST["eval_nombre"]);

        // --- Validaciones adicionales ---
        if ($rangoFechasEva === "" || $eval_nombre === "") {
            echo json_encode([
                "status" => "error",
                "message" => "Los campos no pueden estar vacíos."
            ]);
            exit;
        }

        $fechas = explode(" - ", $rangoFechasEva);

        $fecha_inicio = trim($fechas[0]);
        $fecha_fin = trim($fechas[1]);

        // Validar formato de fechas (opcional)
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha_inicio) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha_fin)) {
            echo json_encode([
                "status" => "error",
                "message" => "Las fechas deben tener el formato YYYY-MM-DD."
            ]);
            exit;
        }

        // --- Procesar y guardar en el modelo ---
        $resultado = $evaluaciones->insert_evaluacion($eval_nombre, $fecha_inicio, $fecha_fin);

        // --- Evaluar resultado ---
        if ($resultado) {
            echo json_encode([
                "status" => "success",
                "message" => "Evaluación registrada correctamente."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo registrar la evaluación. Verifica los datos."
            ]);
        }

        break;

    case "listarEvaluaciones":
        $data = array();
        $data = $evaluaciones->get_evaluacion();
        $formattedData = array();

        foreach ($data as $row) {
            $sub_array = array();
            $sub_array[] = $row['codi_eval'];
            $sub_array[] = $row['nomb_eval'];
            $rango_fechas = $row['fecha_inicio_eval'] . " al " . $row['fecha_fin_eval'];
            $sub_array[] = $rango_fechas;
            $sub_array[] = '<div class="button-container text-center" >
                                <button type="button" onClick="ver(' . $row['codi_eval'] . ');" id="' . $row['codi_eval'] . '" class="btn btn-success btn-icon">
                                    <div><i class="fas fa-sync-alt"></i></div>
                                </button>
                                <button type="button" onClick="enviar(' . $row['codi_eval'] . ');" id="' . $row['codi_eval'] . '" class="btn btn-dark btn-icon">
                                    <div><i class="fas fa-paper-plane"></i></i></div>
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

    case "detalleEvaluacion":


        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            exit;
        }

        $evaluacionID = $_POST['id'];

        // Obtiene el detalle
        $detalle = $evaluaciones->get_detalle_eval($evaluacionID);

        if ($detalle === false) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al consultar la base de datos'
            ]);
            exit;
        }

        if (empty($detalle)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron detalles para el ticket ID: ' . $evaluacionID
            ]);
            exit;
        }

        // Respuesta con detalle y fechas
        echo json_encode([
            'status' => 'success',
            'data' => $detalle,
        ]);
        exit;
        break;


    case "consultaEvaluacionSiesa":

        $data = [];
        $fechainicio = $_GET['fechainicio'] ?? date('Y-m-d');
        $fechafin = $_GET['fechafin'] ?? date('Y-m-d');

        $consultas = [
            'asfaltart_EvaluacionServicioUnificada',
            'asfaltart_EvaluacionProductosUnificada'
        ];

        foreach ($consultas as $consulta) {
            $url = 'idCompania=6026';
            $url .= '&descripcion=' . $consulta;
            //$url .= '&paginacion=' . urlencode("numPag=1|tamPag=100");
            $url .= '&parametros=' . urlencode("fechainicio=$fechainicio|fechafin=$fechafin");
            $method = "GET";
            $response = CurlController::requestEstandar($url, $method);

            if (
                isset($response->codigo) &&
                $response->codigo == 0 &&
                isset($response->detalle->Table) &&
                is_array($response->detalle->Table)
            ) {
                foreach ($response->detalle->Table as $row) {
                    if (is_object($row)) {
                        $sub_array = [
                            'nit_proveedor' => $row->NIT ?? '',
                            'razon_social' => $row->proveedor_razon_soc ?? '',
                            'email' => $row->EMAIL ?? '',
                            'p1' => $row->e001_porcentaje ?? '',
                            'p2' => $row->e002_porcentaje ?? '',
                            'p3' => $row->e003_porcentaje ?? '',
                            'p4' => $row->e004_porcentaje ?? '',
                            'p5' => $row->e005_porcentaje ?? '',
                            'p6' => $row->e006_porcentaje ?? '',
                            'p7' => $row->e007_porcentaje ?? '',
                            'p8' => $row->e008_porcentaje ?? '',
                            'p9' => $row->e009_porcentaje ?? '',
                            'promedio' => $row->porcentaje_promedio ?? '',
                            'tipo' => ($consulta === 'asfaltart_EvaluacionServicioUnificada') ? 'Servicio' : 'Producto'
                        ];
                        $data[] = $sub_array;
                    }
                }
            }
        }



        $filtrados = [];
        foreach ($data as $item) {
            if (!$evaluaciones->existe_detalle_evaluacion(
                $item['nit_proveedor'],
                $item['tipo'],
                $fechainicio,
                $fechafin
            )) {
                $filtrados[] = array_values($item);
            }
        }

        echo json_encode([
            "sEcho" => 1,
            "iTotalRecords" => count($filtrados),
            "iTotalDisplayRecords" => count($filtrados),
            "aaData" => $filtrados
        ]);
        break;

    case "guardarDetalleEvaluacion":

        if (!isset($_POST['registros'])) {
            echo json_encode([
                "status" => "error",
                "message" => "No se recibieron datos."
            ]);
            exit;
        }

        // Decodificar correctamente el JSON
        $registros = json_decode($_POST['registros'], true);

        if (!is_array($registros)) {
            echo json_encode([
                "status" => "error",
                "message" => "Formato de datos inválido (no es JSON)."
            ]);
            exit;
        }

        $guardados = 0;

        foreach ($registros as $row) {

            // 🔒 Asegurar que cada elemento sea array (algunos pueden llegar como string)
            if (!is_array($row)) {
                $row = json_decode($row, true);
            }

            $resultado = $evaluaciones->insert_detalle_evaluacion($row);
            if ($resultado) $guardados++;
        }

        echo json_encode([
            "status" => "success",
            "message" => "Se guardaron {$guardados} registros correctamente."
        ]);
        break;

    case "listarEnvios":

        $codi_eval = $_GET['codi_eval'] ?? NULL;

        $data = array();
        $data = $evaluaciones->get_envios($codi_eval);
        $formattedData = array();

        foreach ($data as $row) {
            $sub_array = array();
            $sub_array[] = $row['nit_proveedor'];
            $sub_array[] = $row['razon_social'];
            $sub_array[] = $row['email_eval'];
            $sub_array[] = $row['porcentaje_promedio'];
            $estado = $row['estado_texto'];
            $badge = '';

            switch ($estado) {
                case 'Enviado':
                    $badge = '<span class="badge bg-success">Enviado</span>';
                    break;
                case 'Sin enviar':
                    $badge = '<span class="badge bg-secondary">Sin enviar</span>';
                    break;
                default:
                    $badge = '<span class="badge bg-dark">Desconocido</span>';
                    break;
            }

            $sub_array[] = '<div class="text-center">' . $badge . '</div>';
            $sub_array[] = '<div class="button-container text-center" >
                                <button type="button" onClick="verEval(' . $row['codi_eval'] . ', \'' . $row['nit_proveedor'] . '\')" class="btn btn-danger btn-icon">
                                    <div><i class="fas fa-file-pdf"></i></div>
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

    case 'enviarCorreos':

        $codi_eval = $_POST['codi_eval'];
        $proveedores = json_decode($_POST['proveedores'], true);

        if (empty($proveedores)) {
            echo json_encode(['status' => 'error', 'message' => 'No hay proveedores seleccionados.']);
            exit;
        }

        // --- Configuración SMTP Office 365 ---
        $smtp_user = 'jefedecompras@asfaltart.com';
        $smtp_pass = 'Jcomp+2021';
        $smtp_host = 'smtp.office365.com';
        $smtp_port = 587;

        $total = count($proveedores);
        $enviados = 0;

        // --- Cargar plantilla HTML ---
        $templatePath = '../public/ReporteEvaluaciones.html';
        if (!file_exists($templatePath)) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontró la plantilla HTML.']);
            exit;
        }

        $templateContent = file_get_contents($templatePath);
        $templateContent = mb_convert_encoding($templateContent, 'UTF-8', 'auto');

        // --- Directorio temporal absoluto ---
        $tempDir = realpath(__DIR__ . '/../public/temp');
        if (!$tempDir) {
            $tempDir = __DIR__ . '/../public/temp';
            if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
        }

        // --- Envío en lotes de 5 ---
        $chunks = array_chunk($proveedores, 5);

        foreach ($chunks as $lote) {
            foreach ($lote as $prov) {

                if (empty($prov['email'])) continue;

                $emails = explode(';', $prov['email']);
                $emailPrincipal = trim($emails[0]);

                $mail = new PHPMailer(true);

                try {
                    // --- SMTP ---
                    $mail->isSMTP();
                    $mail->Host = $smtp_host;
                    $mail->SMTPAuth = true;
                    $mail->Username = $smtp_user;
                    $mail->Password = $smtp_pass;
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = $smtp_port;

                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = 'base64';

                    $mail->setFrom($smtp_user, 'ASFALTART Evaluaciones');
                    $mail->addAddress($emailPrincipal, $prov['razon_social']);

                    // --- Enlace PDF (por si no se adjunta) ---
                    $link_pdf = "http://181.204.219.154:3396/compras/view/PDF/ReporteEvaluacion.php?id1={$codi_eval}&id2={$prov['nit']}";

                    // --- Generar PDF temporal ---
                    $rutaPDF = $tempDir . "/evaluacion_{$prov['nit']}.pdf";
                    $link_gen = "http://181.204.219.154:3396/compras/view/PDF/ReporteEvaluacion.php?id1={$codi_eval}&id2={$prov['nit']}&savepath=" . urlencode($rutaPDF);

                    // Desactivar verificación SSL en localhost
                    $context = stream_context_create([
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false,
                        ]
                    ]);

                    // Ejecutar y generar PDF
                    @file_get_contents($link_gen, false, $context);

                    // --- Cuerpo del correo HTML ---
                    $body = str_replace(
                        [
                            '<b style="font-family: \'Consolas\', Arial;font-size:14px; font-weight: normal; color: rgb(0, 0, 0);">xlink</b>',
                            'Estimado(a):'
                        ],
                        [
                            "<a href='{$link_pdf}' target='_blank' style='color:#009ba9;font-weight:bold;'>Ver Evaluación</a>",
                            "Estimado(a): <strong>" . htmlspecialchars($prov['razon_social'], ENT_QUOTES, 'UTF-8') . "</strong>"
                        ],
                        $templateContent
                    );

                    $mail->isHTML(true);
                    $mail->Subject = 'Evaluación de Desempeño de Proveedores - ASFALTART S.A.S';
                    $mail->Body = $body;

                    // �?Adjuntar PDF solo si existe y tiene tamaño válido
                    if (file_exists($rutaPDF) && filesize($rutaPDF) > 1000) {
                        $mail->addAttachment($rutaPDF, "Evaluacion_{$prov['nit']}.pdf");
                    } else {
                        error_log("PDF no encontrado o vacío para NIT {$prov['nit']}");
                    }

                    if ($mail->send()) {
                        $enviados++;
                        $evaluaciones->actualizarEstadoDetalle($codi_eval, $prov['nit'], 2);
                    }

                    if (file_exists($rutaPDF)) unlink($rutaPDF);
                } catch (Exception $e) {
                    error_log("Error al enviar correo a {$emailPrincipal}: {$mail->ErrorInfo}");
                }
            }

            sleep(5);
        }

        echo json_encode([
            'status' => 'success',
            'message' => "Se enviaron {$enviados} correos correctamente de un total de {$total}."
        ]);


        break;
}