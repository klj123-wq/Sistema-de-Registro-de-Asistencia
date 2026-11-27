<?php

namespace app\models;

class personal extends model
{

    protected $table = "personal";


    public function all()
    {
        # SELECT * FROM table 

        $sql = "SELECT * FROM {$this->table}
        INNER JOIN persona ON $this->table.id = persona.id
        WHERE {$this->table}.activo = 1";
        return $this->query($sql)->get();
    }


    public function turno_personal()
    {

        // Se agregó pl.foto a la consulta
        $sql = "SELECT pl.qr, pl.id, pl.id_turno, t.nombre AS nombre_turno, p.nombre, p.apellido, p.cedula, t.cargo, pl.foto FROM personal AS pl
        INNER JOIN persona AS p ON p.id = pl.id
        LEFT JOIN turnos AS t ON pl.id_turno = t.id
        WHERE pl.activo = 1 AND pl.id_turno IS NOT NULL AND t.nombre IS NOT NULL";

        return $this->query($sql)->get();
    }

    public function turno_dia_personal()
    {

        // Se agregó pl.foto a la consulta
        $sql = "SELECT pl.qr, pl.id, pl.id_turno, t.nombre AS nombre_turno, p.nombre, p.apellido, p.cedula, t.cargo, pl.foto FROM personal AS pl 
        INNER JOIN persona AS p ON p.id = pl.id
        INNER JOIN turno_dia AS td ON pl.id = td.id_personal
        INNER JOIN turnos AS t ON td.id_turno = t.id
        WHERE td.dia = 1 AND pl.activo = 1 AND pl.id_turno IS NULL";

        return $this->query($sql)->get();
    }

    public function turno_personal_id($id)
    {


        // Se agregó pl.foto a la consulta
        $sql = "SELECT pl.id, pl.fecha_ingreso, pl.cod_cargo, p.nombre, p.apellido, p.cedula, t.cargo, pl.foto FROM personal AS pl
        INNER JOIN persona AS p ON p.id = pl.id
        LEFT JOIN turnos AS t ON pl.id_turno = t.id
        WHERE pl.id = ?";

        return $this->query($sql, [$id])->firts();
    }

    public function turno_dia_personal_id($id)
    {

        // Se agregó pl.foto a la consulta
        $sql = "SELECT pl.id, pl.fecha_ingreso, pl.cod_cargo, p.nombre, p.apellido, p.cedula, t.cargo, pl.foto FROM personal AS pl 
        INNER JOIN persona AS p ON p.id = pl.id
        INNER JOIN turno_dia AS td ON pl.id = td.id_personal
        INNER JOIN turnos AS t ON td.id_turno = t.id
        WHERE td.dia = 1 AND pl.id = ?";

        return $this->query($sql, [$id])->firts();
    }

    public function asistencia_personal_fecha()
    {


        $sql = "SELECT pl.id, t.nombre as turno, a.estado_entrada, a.id_persona, a.hora_entrada, p.nombre, p.apellido, p.cedula, t.cargo FROM personal AS pl
        INNER JOIN persona AS p ON p.id = pl.id
        LEFT JOIN turnos AS t ON pl.id_turno = t.id
        INNER JOIN asistencia AS a ON pl.id = a.id_persona
        WHERE pl.id_turno IS NOT NULL AND a.fecha = CURRENT_DATE";

        return $this->query($sql)->get();
    }

    public function asistencia_turnodia_personal_fecha()
    {

        $fecha = date("Y-m-d");
        $hoy = date("N");
        $sql = "SELECT pl.id,  t.nombre as turno, a.estado_entrada,  a.id_persona, a.hora_entrada, p.nombre, p.apellido, p.cedula, t.cargo FROM personal AS pl 
        INNER JOIN persona AS p ON p.id = pl.id
        INNER JOIN turno_dia AS td ON pl.id = td.id_personal
        INNER JOIN turnos AS t ON td.id_turno = t.id
        INNER JOIN asistencia AS a ON pl.id = a.id_persona
        WHERE td.dia = ? AND pl.id_turno IS NULL AND a.fecha = ?";

        return $this->query($sql, [$hoy, $fecha])->get();
    }



    public function all_personal_count()
    {
        $sql = "SELECT COUNT(pl.id) AS trabajadores
            FROM personal AS pl
            INNER JOIN persona AS p ON pl.id = p.id
            LEFT JOIN turnos AS t ON pl.id_turno = t.id
            WHERE pl.activo = 1
            AND COALESCE(t.cargo, t2.cargo) <> 'director'";

        return $this->query($sql)->get();
    }

    public function all_personal()
    {
        $sql = "SELECT
                pl.id,
                p.cedula,
                p.nombre,
                p.apellido,
                COALESCE(t.cargo, t2.cargo) AS cargo,
                CASE
                    WHEN t.nombre IS NOT NULL THEN t.nombre
                    WHEN td.id_turno IS NOT NULL THEN 'mixto'
                    ELSE NULL
                END AS nombre_turno
            FROM personal AS pl
            INNER JOIN persona AS p ON p.id = pl.id
            -- Turno fijo (si tiene)
            LEFT JOIN turnos AS t ON t.id = pl.id_turno
            -- Mixto: su turno vive en turno_dia (tomamos uno de sus turnos asignados)
            LEFT JOIN (
                SELECT id_personal, MIN(id_turno) AS id_turno
                FROM turno_dia
                GROUP BY id_personal
            ) AS td ON td.id_personal = pl.id
            LEFT JOIN turnos AS t2 ON t2.id = td.id_turno
            WHERE pl.activo = 1
            AND COALESCE(t.cargo, t2.cargo) <> 'director'";

        return $this->query($sql)->get();
    }

    public function all_personal_turno($turno, $cargo)
    {
        $sql = "SELECT
                pl.id,
                p.cedula,
                p.nombre,
                p.apellido,
                COALESCE(t.cargo, t2.cargo) AS cargo,
                COALESCE(t.nombre, t2.nombre) AS nombre_turno
            FROM personal AS pl
            INNER JOIN persona AS p ON p.id = pl.id
            -- Turno fijo (si tiene)
            LEFT JOIN turnos AS t ON t.id = pl.id_turno
            -- Mixto: su turno de nómina es la fila con nomina = 1 en turno_dia
            LEFT JOIN turno_dia AS td ON td.id_personal = pl.id AND td.nomina = 1
            LEFT JOIN turnos AS t2 ON t2.id = td.id_turno
            WHERE pl.activo = 1
              AND COALESCE(t.cargo, t2.cargo) <> 'director'";

        $data = [];

        if ($cargo !== 'todos') {
            $sql .= " AND COALESCE(t.cargo, t2.cargo) = ?";
            $data[] = $cargo;
        }
        if ($turno !== 'todos') {
            $sql .= " AND COALESCE(t.nombre, t2.nombre) = ?";
            $data[] = $turno;
        }

        $sql .= " ORDER BY p.apellido, p.nombre";

        return $this->query($sql, $data)->get();
    }

    public function inner_no_director($tabla)
    {

        $sql = "SELECT * FROM {$this->table} 
        INNER JOIN {$tabla} ON {$this->table}.id = {$tabla}.id
        WHERE {$this->table}.activo = 1";

        return $this->query($sql);
    }
}
