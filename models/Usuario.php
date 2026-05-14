<?php
/* CLASE USUARIO */
class Usuario extends Conectar
{

    // INICIO DE SESION
public function login() {
    $conectar = parent::conexion();

    if (isset($_POST["ingreso"])) {
        $usuario    = $_POST["usuario"];
        $contrasena = $_POST["password"];

        // Validar que los campos no estén vacíos
        if (empty($usuario) && empty($contrasena)) {
            header("Location:" . Conectar::ruta() . "index.php?m=2");
            exit();
        }

        // Consultar usuario con sus datos de empleado
        $sql = "SELECT u.*, e.*
                FROM usuarios u
                INNER JOIN empleados e ON e.empl_id = u.user_empleado
                WHERE u.user_usuario = ?
                AND u.user_password  = MD5(?)";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $usuario);
        $stmt->bindValue(2, $contrasena);
        $stmt->execute();
        $resultado = $stmt->fetch();

        if (is_array($resultado) && count($resultado) > 0) {

            // Guardar datos básicos del usuario en sesión
            $_SESSION["user_id"]        = $resultado["user_id"];
            $_SESSION["empl_id"]        = $resultado["empl_id"];
            $_SESSION["empl_documento"] = $resultado["empl_documento"];
            $_SESSION["empl_nom"]       = $resultado["empl_nom"];
            $_SESSION["empl_apellido"]  = $resultado["empl_apellido"];

            // Consultar todos los roles asignados al usuario
            $sqlRoles = "SELECT ur.ur_rol, r.rol_nom
                         FROM usuario_roles ur
                         INNER JOIN roles r ON r.rol_id = ur.ur_rol
                         WHERE ur.ur_user = ?";

            $stmtRoles = $conectar->prepare($sqlRoles);
            $stmtRoles->bindValue(1, $resultado["user_id"], PDO::PARAM_INT);
            $stmtRoles->execute();
            $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

            // Construir arrays de IDs y nombres de roles
            $idsRoles     = [];
            $nombresRoles = [];

            foreach ($roles as $rol) {
                $idsRoles[]     = $rol['ur_rol'];
                $nombresRoles[] = $rol['rol_nom'];
            }

            // Guardar roles en sesión
            $_SESSION["user_roles"]     = $idsRoles;     // [1, 2]
            $_SESSION["user_roles_nom"] = $nombresRoles; // ['Administrador', 'Gerente']

            // Verificar que el usuario tenga al menos un rol asignado
            if (empty($_SESSION["user_roles"])) {
                header("Location:" . Conectar::ruta() . "index.php?m=3");
                exit();
            }

            // Construir placeholders dinámicos según cantidad de roles
            $cantidadRoles = count($_SESSION["user_roles"]);
            $placeholders  = implode(',', array_fill(0, $cantidadRoles, '?'));

            // Consultar permisos activos — unión de todos los roles del usuario
            $sqlPermisos = "SELECT DISTINCT m.menu_identi
                            FROM permiso p
                            INNER JOIN menu m ON m.menu_id = p.permiso_menu
                            WHERE p.permiso_rol  IN ($placeholders)
                            AND p.permiso         = 'si'
                            AND p.permiso_estado  = 1
                            AND m.menu_estado     = 1";

            $stmtPermisos = $conectar->prepare($sqlPermisos);

            // Bind de cada rol como parámetro
            foreach ($_SESSION["user_roles"] as $indice => $rolId) {
                $stmtPermisos->bindValue($indice + 1, $rolId, PDO::PARAM_INT);
            }

            $stmtPermisos->execute();
            $permisos = $stmtPermisos->fetchAll(PDO::FETCH_ASSOC);

            // Construir array de permisos
            $listaPermisos = [];

            foreach ($permisos as $permiso) {
                $listaPermisos[] = $permiso['menu_identi'];
            }

            // Guardar permisos en sesión
            $_SESSION["user_permisos"] = $listaPermisos;

            // Redirigir al inicio
            header("Location:" . Conectar::ruta() . "view/inicio/inicio.php");
            exit();

        } else {
            // Credenciales incorrectas
            header("Location:" . Conectar::ruta() . "index.php?m=1");
            exit();
        }
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