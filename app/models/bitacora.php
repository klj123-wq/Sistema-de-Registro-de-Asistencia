<?php

namespace app\models;

class bitacora extends model
{

    protected $table = "bitacora";


    // Todas las entradas de bitácora con quién hizo la acción y quién fue afectado
    public function todas_con_usuario()
    {
        $sql = "SELECT b.*, u.correo AS usuario,
                   per_afectado.nombre AS afectado_nombre,
                   per_afectado.apellido AS afectado_apellido,
                   per_afectado.cedula AS afectado_cedula
            FROM {$this->table} AS b
            LEFT JOIN usuario AS u ON b.id_user = u.id
            LEFT JOIN persona AS per_afectado ON b.id_persona = per_afectado.id
            ORDER BY b.fecha DESC, b.hora DESC";

        return $this->query($sql)->get();
    }
}
