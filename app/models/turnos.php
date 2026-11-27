<?php

namespace app\models;

use Override;

class turnos extends model
{

    protected $table = "turnos";


    public function create_mixto($dias, $id_personal, $nomina)
    {

        # INSERT INTO turno_dia ("","") VALUES ("",""), ("","")

        $columns = "(dia, id_turno, id_personal, nomina)";
        $sql = "INSERT INTO turno_dia {$columns} VALUES ";
        $data = [];
        foreach ($dias as $dia => $turno) {

            $sql .= "(?, ?, ?, ?), ";

            if ($turno == $nomina) {
                array_push($data, $dia, $turno, $id_personal, $nomina);
                $nomina = null;
            }
            else {
              
                array_push($data, $dia, $turno, $id_personal, null);
              
            }
        }

        $sql = substr($sql, 0, -2);

        $this->query($sql, $data);

        return $this->connection->lastInsertId();
    }

    public function where_ndirect($column, $value)
    {

        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ? AND cargo != 'director'";

        return $this->query($sql, [$value]);
    }

    public function all()
    {
        # SELECT * FROM table 

        $sql = "SELECT * FROM {$this->table} WHERE nombre IS NOT NULL AND activo = 1";
        return $this->query($sql)->get();
    }

    public function combos_operativos()
    {
        $sql = "SELECT DISTINCT cargo, nombre AS turno
            FROM turnos
            WHERE cargo <> 'director' AND activo = 1
            ORDER BY cargo, turno";
        return $this->query($sql)->get();
    }
}
