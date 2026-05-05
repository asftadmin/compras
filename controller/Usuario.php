<?php
/*CONTROLADOR USUARIO */
require_once('../config/conexion.php');
require_once('../models/Usuario.php');
require_once("curl.php");

$usuario = new Usuario();

switch ($_GET['op']) {

    /* GUARDAR Y EDITAR */
    case 'guardaryeditarUsuario':
        $user_id = $_POST['user_id'];
        if (empty($user_id) && $usuario->userExiste($_POST['user_empleado'])) {
            echo json_encode(array('status' => 'error', 'message' => 'El Usuario ya se encuentra registrado \n Por Favor,  Verifica los campos'));
        } else {
            if (empty($user_id)) {
                $usuario->insert_user($_POST['user_usuario'],$_POST['user_password'],$_POST['user_empleado']);
            } else {
                $usuario->update_user($_POST['user_id'], $_POST['user_usuario'],$_POST['user_empleado']);
            }
            echo json_encode(array('status' => 'success', 'message' => 'Datos guardados correctamente'));
        }
        break;

    /* LISTAR USUARIOS X ROL */
    case 'listarUsuario':
        $data = array();
        $data = $usuario->get_user();
        $formattedData = array();

        foreach ($data as $row) {
            $sub_array = array();
            $sub_array[] = $row['user_id'];
            $sub_array[] = $row['empleado'];
            $sub_array[] = $row['user_usuario'];
            $sub_array[] = $row['rol_nom'];

            $sub_array[] = '<div class="button-container text-center" >
                                        <button type="button" onClick="editar(' . $row['user_id'] . ');" id="' . $row['user_id'] . '" class="btn btn-warning btn-icon">
                                            <div><i class="fa fa-edit"></i></div>
                                        </button>
                                        <button type="button" onClick="eliminar(' . $row['user_id'] . ');" id="' . $row['user_id'] . '" class="btn btn-danger btn-icon">
                                            <div><i class="fa fa-trash"></i></div>
                                        </button>
                                        <button type="button" onClick="clave(' . $row['user_id'] . ');" id="' . $row['user_id'] . '" class="btn btn-info btn-icon">
                                            <div><i class="fa fa-lock"></i></div>
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
    case 'eliminarUsuario':
        $usuario->delete_user($_POST['user_id']);
        break;

    /* MOSTRAR AL EDITAR */
    case 'mostrarUsuario':
        $datos = $usuario->get_user_id($_POST['user_id']);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output['user_id'] = $row['user_id'];
                $output['user_usuario'] = $row['user_usuario'];
                $output['user_empleado'] = $row['user_empleado'];  
            }
            echo json_encode($output);
        }
        break;

    /* EDITAR CLAVE USUARIO */
    case 'editarClave':
        $user_id = $_POST['user_id1'];

        if (!empty($user_id)) {
            $usuario->update_user_pass($_POST['user_id1'], $_POST['user_password']);
            echo json_encode(array('status' => 'success', 'message' => 'Contraseña cambiada correctamente'));
        }
        break;

    case 'mostrarClave':
        $datos = $usuario->get_user_id($_POST['user_id']);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output['user_id'] = $row['user_id'];
                $output['empl_nom'] = $row['empl_nom'];
                $output['empl_apellido'] = $row['empl_apellido'];
                $output['user_password'] = $row['user_password'];
            }
            echo json_encode($output);
        }
        break;

    /*  ACTUALIZAR CONTRASEÑA CORREO */
    case 'Password':
        $usuario->update_user_pass($_POST['user_id'], $_POST['user_password']);
        break;

    /* ENVIAR CORREO ELECTRONICO */
    /* case 'Email':
        $datos = $usuario->get_user_email($_POST['user_email']);
        if (is_array($datos) == true && count($datos) > 0) {
            echo 'Existe';
        } else {
            echo 'Error';
        }

        break; */
}
?>