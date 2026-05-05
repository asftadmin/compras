<?php 
    /* CONTROLADOR ROL */
    require_once ("../config/conexion.php");
    require_once("../models/Rol.php");

    $rol = new Rol();

    switch ($_GET["op"]) {

         /* GUARDAR Y EDITAR */
        case 'guardaryeditarRol':
            $rol_id=$_POST["rol_id"];
            if(empty($rol_id) && $rol->rolExiste($_POST["rol_nom"] )){
                echo json_encode(array("status" => "error", "message" => "El Rol ya se encuentra registrado \n Por Favor,  Verifica los campos"));
            }else{
                if(empty($rol_id)){
                    $rol->insert_rol($_POST["rol_nom"], );
                }else{
                    $rol->update_rol($_POST["rol_id"],$_POST["rol_nom"] );
                }
                echo json_encode(array("status" => "success", "message" => "Datos guardados correctamente"));
            }
            break;

        /* SELECT - TRAEMOS LOS ROLES DE LA BD  */
        case 'comboRol':
            $datos = $rol->get_rol();
            if (is_array($datos)==true and count($datos)>0) {
                $html = "<option disabled selected>--Selecciona un Rol--</option>";
                foreach ($datos as $row) {
                    $html.="<option value='".$row['rol_id']."'>".$row['rol_nom']."</option>";
                }
                echo $html;
            }
            break;
        
        /* LISTAR LOS ROLES */
        case 'listarRol':
            $datoslista = $rol->get_rol();
            $data = Array();
                foreach ($datoslista as $row) {
                    $sub_array = array();
                    $sub_array [] = $row["rol_id"];
                    $sub_array [] = $row["rol_nom"];
                    

                    $sub_array [] = '<div class="button-container text-center" >
                    <button type="button" onClick="editar('.$row["rol_id"].');" id="'.$row["rol_id"].'" class="btn btn-warning btn-icon " >
                        <div><i class="fa fa-edit"></i></div>
                    </button>
                    <button type="button" onClick="eliminar('.$row["rol_id"].');" id="'.$row["rol_id"].'" class="btn btn-danger btn-icon" >
                        <div><i class="fa fa-trash"></i></div>
                    </button>
                    <button type="button" onClick="permisos('.$row["rol_id"].');" id="'.$row["rol_id"].'" class="btn btn-info btn-icon" >
                        <div><i class="fa fa-cogs fa-award"></i></div>
                    </button>
                </div>';
                $data[] = $sub_array;
                        
                }
                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data);
                echo json_encode($results);
            break;


            /* ELIMINAR */
            case 'eliminarRol':
                $rol->delete_rol($_POST["rol_id"]);
            break;

            /* MOSTRAR ROLES AL EDITAR */
        case 'mostrarRol':
            $datos=$rol->get_rol_id($_POST["rol_id"]);
                if(is_array($datos)==true && count($datos)>0){
                    foreach($datos as $row){
                        $output["rol_id"] = $row['rol_id'];
                        $output["rol_nom"] = $row['rol_nom'];
                    }
                    echo json_encode($output);
                }
            break;


    }

    
?>