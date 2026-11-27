<?php

namespace app\models;

class persona extends model {

    protected $table = "persona";

    public function found_qr ($id) {


    $sql = "SELECT * FROM $this->table INNER JOIN qr ON $this->table.id = qr.id_personal WHERE $this->table.id = ?";
    return $this->query($sql, [$id])->firts();
    }
}

