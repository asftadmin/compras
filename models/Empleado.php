<?php
/* CLASE USUARIO */
class Empleado extends Conectar
{

    /* INSERTAR */
    public function insert_empl($empl_nom, $empl_apellido, $empl_documento, $empl_telefono, $empl_email, $empl_direccion, $empl_rol)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO empleados (empl_nom, empl_apellido, empl_documento, empl_telefono, empl_email, empl_direccion, empl_estado, empl_rol) VALUES ( ?, ?, ?, ?, ?, ?, 1,?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $empl_nom);
        $sql->bindValue(2, $empl_apellido);
        $sql->bindValue(3, $empl_documento);
        $sql->bindValue(4, $empl_telefono);
        $sql->bindValue(5, $empl_email);
        $sql->bindValue(6, $empl_direccion);
        $sql->bindValue(7, $empl_rol);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* VERIFICAR SI EL USUARIO EXISTE EN LA BD */
    public function emplExiste($empl_documento)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS count FROM empleados WHERE empl_documento = ?  ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $empl_documento);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result["count"] > 0;
    }

    /* ACTUALIZAR */
    public function update_empl($empl_id, $empl_nom, $empl_apellido, $empl_documento, $empl_telefono, $empl_email, $empl_direccion, $empl_estado, $empl_rol)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE empleados SET empl_nom = ?,
            empl_apellido = ?,
            empl_documento = ?,
            empl_telefono = ?,
            empl_email = ?,
            empl_direccion = ?,
            empl_estado = ?,
            empl_rol =?
            WHERE empl_id = ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $empl_nom);
        $sql->bindValue(2, $empl_apellido);
        $sql->bindValue(3, $empl_documento);
        $sql->bindValue(4, $empl_telefono);
        $sql->bindValue(5, $empl_email);
        $sql->bindValue(6, $empl_direccion);
        $sql->bindValue(7, $empl_estado);
        $sql->bindValue(8, $empl_rol);
        $sql->bindValue(9, $empl_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    /* ELIMINAR */
    public function delete_empl($empl_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "DELETE FROM empleados WHERE empl_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $empl_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR TODOS LOS ROLES */
    public function get_empl()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *, CASE
            WHEN empl_estado = 0 THEN 'Inactivo'
            WHEN empl_estado = 1 THEN 'Activo'
            ELSE NULL 
            END AS empl_estado FROM empleados
            LEFT JOIN roles r ON empleados.empl_rol= r.rol_id";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR DATOS AL EDITAR */
    public function get_empl_id($empl_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM empleados WHERE empl_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $empl_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* ACTUALIZAR LA CONTRASEÑA */
    public function update_empl_pass($user_id, $user_contrasena)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE usuarios SET user_contrasena = MD5(?) WHERE user_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_contrasena);
        $sql->bindValue(2, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* FUNCION PARA INSERTAR EMPELADO DE SIESA */
    public function insertarEmplNuevo($data)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO empleados (empl_nom, empl_apellido, empl_documento, empl_telefono, empl_email, empl_direccion,empl_estado) VALUES (:nom, :apellido, :doc, :tel, :email, :dir, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindParam(":nom", $data['empl_nom']);
        $sql->bindParam(":apellido", $data['empl_apellido']);
        $sql->bindParam(":doc", $data['empl_documento']);
        $sql->bindParam(":tel", $data['empl_telefono']);
        $sql->bindParam(":email", $data['empl_email']);
        $sql->bindParam(":dir", $data['empl_direccion']);


        return $sql->execute();
    }
}
