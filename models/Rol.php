<?php
    /* CLASE ROL */
    class Rol extends Conectar {

        /* INSERTAR */
        public function insert_rol($rol_nom){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "INSERT INTO roles (rol_nom) VALUES (?);";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $rol_nom); 
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* VERIFICAR SI EL USUARIO EXISTE EN LA BD */
        public function rolExiste($rol_nom ) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) AS count FROM roles WHERE rol_nom = ?   ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $rol_nom);
            
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        }

    
        /* ACTUALIZAR */
        public function update_rol($rol_id, $rol_nom){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE roles SET rol_nom = ? WHERE rol_id = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $rol_nom);  
            $sql->bindValue(2, $rol_id); 
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        
        /* ELIMINAR */
        public function delete_rol($rol_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "DELETE FROM roles WHERE rol_id=?";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $rol_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* SELECT ROLES */
        public function get_rol(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM roles";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }  

        /* LISTAR LOS ROLES */
        public function listar_rol(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM roles";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* MOSTRAR DATOS AL EDITAR */
        public function get_rol_id($rol_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM roles WHERE rol_id=?";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $rol_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* DAR ACCESO AQUELLAS VISTAS QUE TENGA PERMISO */
        /* public function validacion_acceso($user_id, $menu_identi){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * 
                    FROM menu INNER JOIN permiso ON menu.menu_id = permiso.permiso_menu 
                    INNER JOIN roles ON permiso.permiso_rol = roles.rol_id 
                    INNER JOIN usuarios ON roles.rol_id = usuarios.user_rol_usuario 
                    WHERE user_id =? and permiso ='Si' and menu_identi =?";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $user_id);
            $sql -> bindValue(2, $menu_identi);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        } */

    }
?>