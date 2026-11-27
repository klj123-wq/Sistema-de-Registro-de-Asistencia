<?php

namespace app\models;

class asistencia extends model
{

    protected $table = "asistencia";


    public function verify($id, $fecha)
    {

        $data = [$id, $fecha];

        $sql =
            "SELECT asis.id FROM {$this->table} AS asis
        INNER JOIN personal ON asis.id_persona = personal.id 
        WHERE asis.id_persona = ? AND asis.fecha = ?";

        return $this->query($sql, $data)->firts();
    }


    public function encontrar_asistencia($fecha1, $fecha2, $id)
    {

        $sql = "SELECT * FROM {$this->table} WHERE fecha >= ? AND fecha < ? AND {$this->table}.id_persona = ?";

        return $this->query($sql, [$fecha1, $fecha2, $id])->get();
    }

    public function asistencia_general($fecha, $id_persona)
    {

        $data = [$fecha, $id_persona];

        $sql = "SELECT estado_entrada FROM {$this->table} 
        WHERE fecha = ? AND {$this->table}.id_persona = ?";

        return $this->query($sql, $data)->firts();
    }
    public function asistencias_hoy()
    {

        $sql = "SELECT * FROM {$this->table} as a
        INNER JOIN persona AS p ON a.id_persona = p.id
        WHERE fecha = CURRENT_DATE
        ORDER BY
        GREATEST(a.hora_salida, a.hora_entrada) DESC,
        a.id DESC
        LIMIT 8";
        return $this->query($sql)->get();
    }

    public function all_asistencias()
    {

        $sql = "SELECT estado_entrada FROM {$this->table}";

        return $this->query($sql)->get();
    }

    public function periodos_con_asistencia($id_persona)
    {
        $sql = "SELECT DISTINCT YEAR(fecha) AS anio, MONTH(fecha) AS mes
            FROM {$this->table}
            WHERE id_persona = ?
            ORDER BY anio DESC, mes DESC";

        return $this->query($sql, [$id_persona])->get();
    }
}
