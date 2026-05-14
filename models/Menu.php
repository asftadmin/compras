<?php
class Menu extends Conectar
 {

    public function get_menu_por_roles($roles) {
        $conectar = parent::conexion();
        parent::set_names();

        if (empty($roles)) return [];

        $cantidadRoles = count($roles);
        $placeholders  = implode(',', array_fill(0, $cantidadRoles, '?'));

        $sql = "SELECT DISTINCT
                    m.menu_id,
                    m.menu_nom,
                    m.menu_ruta,
                    m.menu_icono,
                    m.menu_identi,
                    m.menu_grupo,
                    m.menu_padre
                FROM menu m
                INNER JOIN permiso p ON p.permiso_menu = m.menu_id
                WHERE p.permiso_rol  IN ($placeholders)
                AND p.permiso         = 'si'
                AND p.permiso_estado  = 1
                AND m.menu_estado     = 1
                ORDER BY m.menu_grupo, m.menu_padre NULLS FIRST, m.menu_id ASC";

        $stmt = $conectar->prepare($sql);

        foreach ($roles as $indice => $rol) {
            $stmt->bindValue($indice + 1, $rol, PDO::PARAM_INT);
        }

        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // construir jerarquía
        $jerarquia = [];

        foreach ($items as $item) {
            $grupo = $item['menu_grupo'];

            if (is_null($item['menu_padre'])) {
                // es padre
                $jerarquia[$grupo][$item['menu_id']] = array_merge($item, ['hijos' => []]);
            } else {
                // es hijo — agregarlo al padre correspondiente
                $padre = $item['menu_padre'];
                if (isset($jerarquia[$grupo][$padre])) {
                    $jerarquia[$grupo][$padre]['hijos'][] = $item;
                }
            }
        }

        return $jerarquia;
    }
}
?>