<?php
/* CLASE USUARIO */
class Proveedores extends Conectar
{
    /* INSERTAR */
    public function insert_prov($prov_nit,$prov_cod_siesa, $prov_descripcion, $prov_estado,$prov_linea)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO proveedores (prov_nit,prov_cod_siesa, prov_descripcion, prov_estado,prov_linea) VALUES ( ?, ?, ?, ?, ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $prov_nit);
        $sql->bindValue(2, $prov_cod_siesa);
        $sql->bindValue(3, $prov_descripcion);
        $sql->bindValue(4, $prov_estado);
        $sql->bindValue(5, $prov_linea);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* VERIFICAR SI EL USUARIO EXISTE EN LA BD */
    public function provExiste($prov_nit)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS count FROM proveedores WHERE prov_nit = ?  ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $prov_nit);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result["count"] > 0;
    }

    /* ACTUALIZAR */
    public function update_prov($prov_id, $prov_nit, $prov_cod_siesa,$prov_descripcion, $prov_estado, $prov_linea)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE proveedores SET prov_nit = ?,
            prov_cod_siesa = ?,
            prov_descripcion = ?,
            prov_estado = ?,
            prov_linea = ?
            WHERE prov_id = ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $prov_nit);
        $sql->bindValue(2, $prov_cod_siesa);
        $sql->bindValue(3, $prov_descripcion);
        $sql->bindValue(4, $prov_estado);
        $sql->bindValue(5, $prov_linea);
        $sql->bindValue(6, $prov_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    /* ELIMINAR */
    public function delete_prov($prov_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "DELETE FROM proveedores WHERE prov_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $prov_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR TODOS LOS PROVEEDORES */
    public function get_prov()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *, CASE
            WHEN prov_estado = 0 THEN 'Inactivo'
            WHEN prov_estado = 1 THEN 'Activo'
            ELSE NULL 
            END AS prov_estado FROM proveedores LEFT JOIN linea ON linea.linea_id = proveedores.prov_linea ";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR DATOS AL EDITAR */
    public function get_prov_id($prov_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM proveedores LEFT JOIN linea ON linea.linea_id = proveedores.prov_linea WHERE prov_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $prov_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* FUNCION PARA INSERTAR EMPELADO DE SIESA */
    public function insertarProvNuevo($data)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO proveedores (prov_nit, prov_descripcion, prov_estado) VALUES (:nit, :descrip, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindParam(":nit", $data['prov_nit']);
        $sql->bindParam(":descrip", $data['prov_descripcion']);

        return $sql->execute();
    }

    public function get_by_linea_con_siesa($lineaId) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT p.prov_id, p.prov_nit, p.prov_descripcion, p.prov_cod_siesa, 
                       l.linea_nom
                FROM proveedores p
                JOIN linea l ON p.prov_linea = l.linea_id
                WHERE p.prov_linea = ?";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $lineaId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
