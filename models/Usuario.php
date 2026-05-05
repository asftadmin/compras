<?php
/* CLASE USUARIO */
class Usuario extends Conectar
{

    // INICIO DE SESION
    public function login()
    {

        $conectar = parent::conexion();

        // Verifica si se envió el formulario de ingreso
        if (isset($_POST["ingreso"])) {
            // Obtiene los valores del formulario
            $usuario = $_POST["usuario"];
            $password = $_POST["password"];


            if (empty($usuario) && empty($password)) {
                // Redirige a la página de inicio con mensaje de error (m=2)
                header("Location:" . conectar::ruta() . "index.php?m=2");
                exit();
            } else {
                // Consulta SQL para verificar las credenciales en la base de datos
                $sql = "SELECT * FROM usuarios INNER JOIN empleados ON empleados.empl_id = usuarios.user_empleado WHERE user_usuario = ? AND user_password = MD5(?) ";
                $stmt = $conectar->prepare($sql);
                $stmt->bindValue(1, $usuario);
                $stmt->bindValue(2, $password);    // Primera ocurrencia de user_contrasena

                $stmt->execute();

                // Obtiene el resultado de la consulta
                $resultado = $stmt->fetch();

                // Si se encontraron registros con las credenciales proporcionadas
                if (is_array($resultado) && count($resultado) > 0) {
                    // Almacena información del usuario en variables de sesión
                    $_SESSION["user_id"] = $resultado["user_id"];
                    $_SESSION["empl_id"] = $resultado["empl_id"];
                    $_SESSION["empl_documento"] = $resultado["empl_documento"];
                    $_SESSION["empl_nom"] = $resultado["empl_nom"];
                    $_SESSION["empl_apellido"] = $resultado["empl_apellido"];

                    // Redirige al inicio después del inicio de sesión exitoso
                    header("Location:" . Conectar::ruta() . "view/inicio/inicio.php");
                    exit();
                } else {
                    // Redirige a la página de inicio con mensaje de error (m=1)
                    header("Location:" . Conectar::ruta() . "index.php?m=1");
                    exit();
                }
            }
            //siguiente rol                     
        }
    }

    /* CLASE USUARIOS */

    /* INSERTAR */
    public function insert_user($user_usuario, $user_password, $user_empleado)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO usuarios ( user_usuario, user_password, user_empleado) VALUES ( ?,MD5(?), ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_usuario);
        $sql->bindValue(2, $user_password);
        $sql->bindValue(3, $user_empleado);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* VERIFICAR SI EL USUARIO EXISTE EN LA BD */
    public function userExiste($user_empleado)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS count FROM usuarios WHERE user_empleado = ?  ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_empleado);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result["count"] > 0;
    }

    /* ACTUALIZAR */
    public function update_user($user_id, $user_usuario, $user_empleado)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE usuarios SET 
            user_usuario = ?,
            user_empleado = ? 
            WHERE user_id = ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_usuario);
        $sql->bindValue(2, $user_empleado);
        $sql->bindValue(3, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    /* ELIMINAR */
    public function delete_user($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "DELETE FROM usuarios WHERE user_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR TODOS LOS ROLES */
    public function get_user()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *, CONCAT(empleados.empl_nom, ' ', empleados.empl_apellido) AS empleado FROM usuarios
            INNER JOIN empleados ON empleados.empl_id = usuarios.user_empleado
            INNER JOIN roles ON roles.rol_id = empleados.empl_rol";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR DATOS AL EDITAR */
    public function get_user_id($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM usuarios LEFT JOIN empleados ON empleados.empl_id = usuarios.user_empleado WHERE user_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* ACTUALIZAR LA CONTRASEÑA */
    public function update_user_pass($user_id, $user_password)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE usuarios SET user_password = MD5(?) WHERE user_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_password);
        $sql->bindValue(2, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /*  RECUPERAR LA CONTRASEÑA POR MEDIO DEL CORREO ELECTRONICO */
    /* public function get_user_cambiar_pass($user_email)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE usuarios SET user_password = CONCAT(SUBSTRING(MD5(RANDOM()::text) FROM 1 FOR 3), LPAD(FLOOR(RANDOM() * 1000)::text, 3, '0')) 
            WHERE user_email= ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_email);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    } */

    /* FUNCION PARA ENCRIPTAR LA NUEVA CONTRASEÑA */
    public function get_user_encriptar_nuevo_pass($user_id, $user_password)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE usuarios SET user_password = MD5(?)
            WHERE user_id= ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_password);
        $sql->bindValue(2, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    
    
}
?>