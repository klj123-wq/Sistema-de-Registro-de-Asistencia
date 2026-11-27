<?php

namespace app\models;


class diaslibres extends model
{

    protected $table = "diaslibres";


    public function verify($ini, $fin)
    {

        $sql = "SELECT * FROM {$this->table} WHERE (fecha_fin >= ? AND fecha_inicio <= ?) ";
        $data = [$ini, $fin];
        return $this->query($sql, $data);
    }

    public function verify_fecha($fecha)
    {

        $anio = date("y");

        $sql = "SELECT * FROM {$this->table}
         WHERE fecha_inicio <= ? AND fecha_fin >= ?  
         OR DATE_ADD(fecha_inicio, INTERVAL {$anio} YEAR) <= ? AND DATE_ADD(fecha_fin, INTERVAL {$anio} YEAR) <= ?";

        $data = [$fecha, $fecha, $fecha, $fecha];
        return $this->query($sql, $data)->firts();
    }

    public function verify_update($ini, $fin, $id)
    {

        $sql = "SELECT * FROM {$this->table} WHERE (fecha_fin >= ? AND fecha_inicio <= ?) AND id != ?";
        $data = [$ini, $fin, $id];
        return $this->query($sql, $data);
    }

    public function encontrar_fechas($fecha1, $fecha2)
    {

        $sql = "SELECT * FROM {$this->table} 
        WHERE fecha_inicio >= ? AND fecha_inicio < ? 
        OR fecha_fin >= ? AND fecha_fin < ?";
        return $this->query($sql, [$fecha1, $fecha2, $fecha1, $fecha2])->get();
    }

    public function verify_fecha_rangos($fecha_inicio, $fecha_fin)
    {
        $ano = date("y");

        $sql = "SELECT * FROM {$this->table}
                WHERE activo = 1 AND (fecha_inicio <= ? AND fecha_fin >= ?)  
                OR (DATE_ADD(fecha_inicio, INTERVAL {$ano} YEAR) <= ? AND DATE_ADD(fecha_fin, INTERVAL {$ano} YEAR) >= ?)";
         
        $data = [$fecha_fin, $fecha_inicio, $fecha_fin, $fecha_inicio];
          
        return $this->query($sql, $data)->get();
    }

    
}
