<?php
/* CLASE LINEA */
class Linea extends Conectar {

    /* INSERTAR */
    public function insert_linea($linea_nom) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO linea (linea_nom) VALUES (?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $linea_nom);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* VERIFICAR SI EXISTE EN LA BD */
    public function lineaExiste($linea_nom) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS count FROM linea WHERE linea_nom = ?   ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $linea_nom);

        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result["count"] > 0;
    }


    /* ACTUALIZAR */
    public function update_linea($linea_id, $linea_nom) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE linea SET linea_nom = ? WHERE linea_id = ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $linea_nom);
        $sql->bindValue(2, $linea_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    /* ELIMINAR */
    public function delete_linea($linea_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "DELETE FROM linea WHERE linea_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $linea_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* SELECT */
    public function get_linea() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM linea";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR LOS ROLES */
    public function listar_linea() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM linea";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR DATOS AL EDITAR */
    public function get_linea_id($linea_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM linea WHERE linea_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $linea_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR LOS ROLES */
    public function listar_linea_proveedores() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                linea_id, 
                linea_nom, 
                prov_id, 
                prov_nit, 
                prov_descripcion, 
                prov_cod_siesa
            FROM linea 
            INNER JOIN proveedores  ON prov_linea = linea_id
            ORDER BY linea_nom, prov_descripcion";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}