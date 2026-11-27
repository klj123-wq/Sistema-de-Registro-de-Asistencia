<?php

namespace app\models;



class turnodia extends model{

    protected $table = "turno_dia";

    /**
     * Esta funcion elimina el horario personalizado de
     * de la persona
     */
    public function delete_persona_turno ($id_personal)
    {
        # DELETE FROM tabla WHERE 'cedula' = 0 

        $sql = "DELETE FROM {$this->table} WHERE id_personal = ?";
        return $this->query($sql, [$id_personal])->firts();

    }
}