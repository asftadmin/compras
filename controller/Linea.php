<?php
/* CONTROLADOR LINEA */
require_once("../config/conexion.php");
require_once("../models/Linea.php");

$linea = new Linea();

switch ($_GET["op"]) {

    /* GUARDAR Y EDITAR */
    case 'guardaryeditar':
        $linea_id = $_POST["linea_id"];
        if (empty($linea_id) && $linea->lineaExiste($_POST["linea_nom"])) {
            echo json_encode(array("status" => "error", "message" => "La linea de producto ya se encuentra registrada \n Por Favor,  Verifica los campos"));
        } else {
            if (empty($linea_id)) {
                $linea->insert_linea($_POST["linea_nom"],);
            } else {
                $linea->update_linea($_POST["linea_id"], $_POST["linea_nom"]);
            }
            echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
        }
        break;

    /* SELECT - TRAEMOS LOS ROLES DE LA BD  */
    case 'combo':
        $datos = $linea->get_linea();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option disabled selected>--Selecciona una Linea de Produccion--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['linea_id'] . "'>" . $row['linea_nom'] . "</option>";
            }
            echo $html;
        }
        break;

    /* LISTAR LOS ROLES */
    case 'listar':
        $datoslista = $linea->get_linea();
        $data = array();
        foreach ($datoslista as $row) {
            $sub_array = array();
            $sub_array[] = $row["linea_id"];
            $sub_array[] = $row["linea_nom"];


            $sub_array[] = '<div class="button-container text-center" >
                    <button type="button" onClick="editar(' . $row["linea_id"] . ');" id="' . $row["linea_id"] . '" class="btn btn-warning btn-icon " >
                        <div><i class="fa fa-edit"></i></div>
                    </button>
                    <button type="button" onClick="eliminar(' . $row["linea_id"] . ');" id="' . $row["linea_id"] . '" class="btn btn-danger btn-icon" >
                        <div><i class="fa fa-trash"></i></div>
                    </button>
                </div>';
            $data[] = $sub_array;
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;


    /* ELIMINAR */
    case 'eliminar':
        $linea->delete_linea($_POST["linea_id"]);
        break;

    /* MOSTRAR ROLES AL EDITAR */
    case 'mostrarRol':
        $datos = $linea->get_linea_id($_POST["linea_id"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["linea_id"] = $row['linea_id'];
                $output["linea_nom"] = $row['linea_nom'];
            }
            echo json_encode($output);
        }
        break;
    case 'consultarLinea':
        $datos = $linea->listar_linea_proveedores();
        $lineas = array();

        // Agrupar proveedores por línea
        foreach ($datos as $row) {
            $lineaId = $row["linea_id"];

            if (!isset($lineas[$lineaId])) {
                $lineas[$lineaId] = array(
                    "linea_id" => $lineaId,
                    "linea_nombre" => $row["linea_nom"], // Asegúrate de incluir este campo en tu SQL
                    "proveedores" => array()
                );
            }

            $lineas[$lineaId]["proveedores"][] = array(
                "prov_id" => $row["prov_id"],
                "prov_nit" => $row["prov_nit"],
                "prov_descripcion" => $row["prov_descripcion"],
                "prov_cod_siesa" => $row["prov_cod_siesa"]
            );
        }

        // Convertir a formato adecuado para DataTables si es necesario
        $aaData = array_values($lineas);

        $resultado = array(
            "success" => true,
            "aaData" => $aaData,
            "iTotalRecords" => count($aaData),
            "iTotalDisplayRecords" => count($aaData)
        );

        echo json_encode($resultado);
        break;
}