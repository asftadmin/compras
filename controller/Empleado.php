<?php
/*CONTROLADOR USUARIO */
require_once('../config/conexion.php');
require_once('../models/Empleado.php');
require_once("curl.php");

$empleado = new Empleado();

switch ($_GET['op']) {

    /* SELECT USUARIOS REPORTE OBRAS */
    case 'comboEmpleados':
        $datos = $empleado->get_empl();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option value='' disabled selected>--Selecciona el Empleado--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['empl_id'] . "'>" . $row['empl_documento'] . ' - '. $row['empl_nom'] . ' ' . $row['empl_apellido'] . '</option>';
            }
            echo $html;
        }
        break;

    /* GUARDAR Y EDITAR */
    case 'guardaryeditarEmpleado':
        $empl_id = $_POST['empl_id'];
        if (empty($empl_id) && $empleado->emplExiste($_POST['empl_documento'])) {
            echo json_encode(array('status' => 'error', 'message' => 'El Empleado ya se encuentra registrado \n Por Favor,  Verifica los campos'));
        } else {
            if (empty($empl_id)) {
                $empleado->insert_empl($_POST['empl_nom'], $_POST['empl_apellido'], $_POST['empl_documento'], $_POST['empl_telefono'], $_POST['empl_email'], $_POST['empl_direccion'], $_POST['empl_rol']);
            } else {
                $empleado->update_empl($_POST['empl_id'], $_POST['empl_nom'], $_POST['empl_apellido'], $_POST['empl_documento'], $_POST['empl_telefono'], $_POST['empl_email'], $_POST['empl_direccion'],$_POST['empl_estado'],$_POST['empl_rol']);
            }
            echo json_encode(array('status' => 'success', 'message' => 'Datos guardados correctamente'));
        }
        break;

    /* LISTAR EMPLEADOS X ROL */
    case 'listarEmpleados':
        $data = array();
        $data = $empleado->get_empl();
        $formattedData = array();

        foreach ($data as $row) {
            $sub_array = array();
            $sub_array[] = $row['empl_id'];
            $sub_array[] = $row['empl_nom'];
            $sub_array[] = $row['empl_apellido'];
            $sub_array[] = $row['empl_documento'];
            $sub_array[] = $row['empl_telefono'];
            $sub_array[] = $row['empl_email'];
            $sub_array[] = $row['empl_direccion'];
            if ($row["empl_estado"] === "Activo") {
                    $sub_array[] = '<div style="text-align: center;"><span style="background-color:rgb(74, 177, 208); color: #fff; padding: 8px; border-radius: 5px; width: 200px;">ACTIVO</span></div>';
                } else if ($row["empl_estado"] === "Inactivo") {
                    $sub_array[] = '<div style="text-align: center;"><span style="background-color:rgb(136, 130, 131); color: #fff; padding: 8px; border-radius: 5px; width: 200px;">INACTIVO</span></div>';
                } 
            $sub_array[] = $row['rol_nom'];
            $sub_array[] = '<div class="button-container" style="display: inline-block; text-align:center;" >
                                        <button type="button" onClick="editar(' . $row['empl_id'] . ');" id="' . $row['empl_id'] . '" class="btn btn-warning btn-icon">
                                            <div><i class="fa fa-edit"></i></div>
                                        </button>
                                        <button type="button" onClick="eliminar(' . $row['empl_id'] . ');" id="' . $row['empl_id'] . '" class="btn btn-danger btn-icon">
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
    case 'eliminarEmpleado':
        $empleado->delete_empl($_POST['empl_id']);
        break;

    /* MOSTRAR AL EDITAR */
    case 'mostrar':
        $datos = $empleado->get_empl_id($_POST['empl_id']);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output['empl_id'] = $row['empl_id'];
                $output['empl_nom'] = $row['empl_nom'];
                $output['empl_apellido'] = $row['empl_apellido'];
                $output['empl_documento'] = $row['empl_documento'];
                $output['empl_telefono'] = $row['empl_telefono'];
                $output['empl_email'] = $row['empl_email'];
                $output['empl_direccion'] = $row['empl_direccion'];
                $output['empl_estado'] = $row['empl_estado'];
                $output['empl_rol'] = $row['empl_rol'];
            }
            echo json_encode($output);
        }
        break;

    case "consultaEmpleadoSiesa":
        $data = array();
        $pagina = 1;
        $tamPag = 100;
        $totalPaginas = 1; // valor inicial, luego se actualiza en la primera iteración
        $fechainicio = $_GET['fechainicio'] ?? '2017-01-01';
        $fechafin = $_GET['fechafin'] ?? date('Y-m-d');

        do {
            $url = 'idCompania=6026';
            $url .= '&descripcion=asfaltart_CONSULTA_EMPLEADOS';
            $url .= '&paginacion=' . urlencode("numPag=$pagina|tamPag=$tamPag");
            $url .= '&parametros=' . urlencode("fechainicio=$fechainicio|fechafin=$fechafin");

            $method = "GET";
            $response = CurlController::requestEstandar($url, $method);

            if (isset($response->detalle->Datos) && is_array($response->detalle->Datos)) {
                if ($pagina === 1 && isset($response->detalle->total_páginas)) {
                    $totalPaginas = $response->detalle->total_páginas;
                }

                foreach ($response->detalle->Datos as $row) {
                    if (is_object($row)) {
                        $sub_array = array();
                        $sub_array[] = $row->f200_id ?? '';
                        $sub_array[] = $row->f200_nombres ?? '';
                        $sub_array[] = $row->f200_apellido1 ?? '';
                        $sub_array[] = $row->f200_apellido2 ?? '';
                        $sub_array[] = $row->c0540_fecha_ingreso ?? '';
                        $sub_array[] = $row->c0540_fecha_nacimiento ?? '';
                        $sub_array[] = $row->f015_direccion1 ?? '';
                        $sub_array[] = $row->f015_email ?? '';
                        $sub_array[] = $row->f015_celular ?? '';

                        $data[] = $sub_array;
                    }
                }
            }

            $pagina++; // avanzar a la siguiente página

        } while ($pagina <= $totalPaginas);

        // Enviar la respuesta final
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);
        break;
    case 'consultarEmpleados':

        $datos = $empleado->get_empl();

        $data = array();
        foreach ($datos as $row) {

            $sub_array = array();
            $sub_array[] = $row["empl_documento"];
            $sub_array[] = $row["empl_nom"];
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

    case "guardarEmpleadoNuevo":

        $documento = $_POST["documento"];
        $nombre = $_POST["nombre"];
        $apellido1 = $_POST["apellido1"];
        $apellido2 = $_POST["apellido2"];
        $apellidos = $apellido1.' '.$apellido2;
        $email = $_POST["email"];
        $telefono = $_POST["telefono"];
        $direccion = $_POST["direccion"];

        $resultado = $empleado->insertarEmplNuevo([
            'empl_nom' => $nombre,
            'empl_apellido' => $apellidos,
            'empl_documento' => $documento,
            'empl_telefono' => $telefono,
            'empl_email' => $email,
            'empl_direccion' => $direccion,
        ]);

        echo json_encode(["success" => $resultado]);


        break;
}
?>