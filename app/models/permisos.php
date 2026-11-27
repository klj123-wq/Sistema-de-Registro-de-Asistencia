<?php

namespace app\models;



class permisos extends model
{

    protected $table = "permiso";

    public function verify($ini, $fin, $id)
    {

        $sql = "SELECT * FROM {$this->table} WHERE fecha_fin >= ? AND fecha_inicio <= ? AND id_persona = ? AND activo = 1";
        $data = [$ini, $fin, $id];
        return $this->query($sql, $data);
    }

    public function encontrar_permisos($fecha_inicio, $fecha_fin, $id)
    {

        $sql = "SELECT * FROM {$this->table} 
        WHERE (id_persona = ?) 
        AND (fecha_inicio >= ? AND fecha_inicio < ?
        OR fecha_fin >= ? AND fecha_fin < ?)  ";
        return $this->query($sql, [$id, $fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin])->get();
    }

    public function permiso_general($fecha, $id_persona)
    {

        $data = [$fecha, $fecha, $id_persona];

        $sql = "SELECT * FROM {$this->table} 
        WHERE fecha_inicio <= ? AND fecha_fin >= ? AND {$this->table}.id_persona = ?";

        return $this->query($sql, $data)->firts();
    }

    public function permisos_de_hoy()
    {

        $sql = "SELECT per.*, pl.*, p.id_persona, p.descripcion FROM {$this->table} AS p
        INNER JOIN persona AS per ON p.id_persona = per.id
        INNER JOIN personal AS pl ON per.id = pl.id
        WHERE fecha_inicio <= CURRENT_DATE AND fecha_fin >= CURRENT_DATE";
        return $this->query($sql)->get();
    }

    public function all_permisos_mes($fecha_ini, $fecha_fin)
    {

        $data = [$fecha_ini, $fecha_fin, $fecha_ini, $fecha_fin];

        $sql = "SELECT COUNT(id) AS permisos FROM {$this->table} 
        WHERE (fecha_inicio >= ? AND fecha_inicio < ?)
        OR (fecha_fin >= ? AND fecha_fin < ?) ";

        return $this->query($sql, $data)->get();
    }
}
