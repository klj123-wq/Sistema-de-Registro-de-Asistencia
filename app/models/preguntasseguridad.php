<?php

namespace app\models;

class preguntasseguridad extends model
{

    protected $table = "pregunta_seguridad";

    public function guardar_preguntas($id_usuario, $p1, $p2, $p3, $r1, $r2, $r3)
    {
        $values = [$p1, $r1, $id_usuario, $p2, $r2, $id_usuario, $p3, $r3, $id_usuario];
        $sql = "INSERT INTO {$this->table} (pregunta, respuesta, id_usuario) VALUES (?, ?, ?), (?, ?, ?), (?, ?, ?)";
        
        $this->query($sql, $values);

        return $this->connection->lastInsertId();
    }
    public function eliminar_preguntas($id_usuario)
    {
        $sql = "DELETE FROM {$this->table} WHERE id_usuario = ?";
        
        $this->query($sql, [$id_usuario]);
        
        return $this->query($sql, [$id_usuario]);
    }
}
