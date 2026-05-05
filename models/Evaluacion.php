<?php
/* CLASE USUARIO */
class Evaluacion extends Conectar {

    public function insert_evaluacion($nombre_eval, $fecha_inicio, $fecha_fin) {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO evaluacion (nomb_eval,fecha_inicio_eval,fecha_fin_eval) VALUES (?, ?, ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $nombre_eval);
        $sql->bindValue(2, $fecha_inicio);
        $sql->bindValue(3, $fecha_fin);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    /* LISTAR TODOS LOS PROVEEDORES */
    public function get_evaluacion() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM evaluacion";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_detalle_eval($codi_eval) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM evaluacion WHERE codi_eval = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codi_eval);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function insert_detalle_evaluacion($data) {
        $conectar = parent::conexion();
        parent::set_names();

        if (!is_array($data)) {
            $data = json_decode($data, true);
        }

        $sql = "INSERT INTO evaluacion_detalle (
                codi_eval, nit_proveedor, razon_social,
                p1_porcentaje, p2_porcentaje, p3_porcentaje,
                p4_porcentaje, p5_porcentaje, p6_porcentaje,
                p7_porcentaje, p8_porcentaje, p9_porcentaje,
                porcentaje_promedio, tipo_evaluacion, fecha_inicio_deta,
                fecha_fin_deta, email_eval
            ) VALUES (
                :codi_eval, :nit_proveedor, :razon_social,
                :p1_porcentaje, :p2_porcentaje, :p3_porcentaje,
                :p4_porcentaje, :p5_porcentaje, :p6_porcentaje,
                :p7_porcentaje, :p8_porcentaje, :p9_porcentaje,
                :porcentaje_promedio, :tipo_evaluacion, :fecha_inicio,
                :fecha_fin, :email_eval
            )";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(':codi_eval', $data['codi_eval'], PDO::PARAM_INT);
        $sql->bindValue(':nit_proveedor', $data['nit_proveedor']);
        $sql->bindValue(':razon_social', $data['razon_social']);
        $sql->bindValue(':p1_porcentaje', $data['p1_porcentaje']);
        $sql->bindValue(':p2_porcentaje', $data['p2_porcentaje']);
        $sql->bindValue(':p3_porcentaje', $data['p3_porcentaje']);
        $sql->bindValue(':p4_porcentaje', $data['p4_porcentaje']);
        $sql->bindValue(':p5_porcentaje', $data['p5_porcentaje']);
        $sql->bindValue(':p6_porcentaje', $data['p6_porcentaje']);
        $sql->bindValue(':p7_porcentaje', $data['p7_porcentaje']);
        $sql->bindValue(':p8_porcentaje', $data['p8_porcentaje']);
        $sql->bindValue(':p9_porcentaje', $data['p9_porcentaje']);
        $sql->bindValue(':porcentaje_promedio', $data['porcentaje_promedio']);
        $sql->bindValue(':tipo_evaluacion', $data['tipo_evaluacion']);
        $sql->bindValue(':fecha_inicio', $data['fecha_inicio_deta']);
        $sql->bindValue(':fecha_fin', $data['fecha_fin_deta']);
        $sql->bindValue(':email_eval', $data['email_eval']);

        return $sql->execute();
    }

    public function existe_detalle_evaluacion($nit, $tipo, $fecha_inicio, $fecha_fin) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT COUNT(*) 
            FROM evaluacion_detalle
            WHERE nit_proveedor = :nit_proveedor
              AND tipo_evaluacion = :tipo_evaluacion
              AND fecha_inicio_deta = :fecha_inicio
              AND fecha_fin_deta = :fecha_fin";

        $stmt = $conectar->prepare($sql);

        $stmt->bindValue(':nit_proveedor', $nit, PDO::PARAM_STR);
        $stmt->bindValue(':tipo_evaluacion', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_fin', $fecha_fin, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function get_envios($codi_eval) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT DISTINCT 
                    codi_eval,
                    nit_proveedor,
                    razon_social,
                    email_eval,
                    CASE 
                        WHEN estado_deta = 1 THEN 'Sin enviar'
                        WHEN estado_deta = 2 THEN 'Enviado'
                        ELSE NULL
                    END AS estado_texto,
					ROUND(AVG(porcentaje_promedio), 2) AS porcentaje_promedio
                FROM evaluacion_detalle
                WHERE codi_eval = ?
				GROUP BY codi_eval, nit_proveedor, razon_social, email_eval, estado_texto
                ORDER BY nit_proveedor ASC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codi_eval, PDO::PARAM_INT);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_evaluacion_id($codigo, $nit) {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                    nit_proveedor,
                    razon_social,
                    ROUND(AVG(p1_porcentaje), 2) AS prom_p1,
                    ROUND(AVG(p2_porcentaje), 2) AS prom_p2,
                    ROUND(AVG(p3_porcentaje), 2) AS prom_p3,
                    ROUND(AVG(p4_porcentaje), 2) AS prom_p4,
                    ROUND(AVG(p5_porcentaje), 2) AS prom_p5,
                    ROUND(AVG(p6_porcentaje), 2) AS prom_p6,
                    ROUND(AVG(p7_porcentaje), 2) AS prom_p7,
                    ROUND(AVG(p8_porcentaje), 2) AS prom_p8,
                    ROUND(AVG(p9_porcentaje), 2) AS prom_p9,
                    ROUND(AVG(porcentaje_promedio), 2) AS porcentaje_promedio
                FROM evaluacion_detalle
                WHERE codi_eval = ?
                AND nit_proveedor = ?
                GROUP BY nit_proveedor, razon_social
                ORDER BY nit_proveedor ASC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codigo, PDO::PARAM_INT);
        $sql->bindValue(2, $nit, PDO::PARAM_STR);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function actualizarEstadoDetalle($codi_eval, $nit_proveedor, $estado) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE evaluacion_detalle 
            SET estado_deta = :estado
            WHERE codi_eval = :codi_eval AND nit_proveedor = :nit";
        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
        $stmt->bindParam(':codi_eval', $codi_eval, PDO::PARAM_INT);
        $stmt->bindParam(':nit', $nit_proveedor, PDO::PARAM_STR);
        return $stmt->execute();
    }
}