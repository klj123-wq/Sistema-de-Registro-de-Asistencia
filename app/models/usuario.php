<?php

namespace app\models;


class usuario extends model {


    protected $table = "usuario";

    public function usuarios() {

        $sql = "SELECT u.id, u.correo, u.activo, p.cedula, p.nombre, p.apellido  FROM $this->table AS u
        INNER JOIN persona AS p ON u.id = p.id
        INNER JOIN rol AS r ON u.rol = r.id
        WHERE u.activo != 0 AND r.rol != 'superadmin'";
        return $this->query($sql)->get();
    }

    public function increment_counter($id) {

    
        $sql = "UPDATE {$this->table} SET intentos = intentos + 1 WHERE id = ?";
        $this->query($sql, [$id]);

        return $this->find($id);
    }

    /** 
     * @param string $lock 
     * __________________________________
     * Valores de la variable $lock
     * 
     * || 0 = Eliminado  
     * || 1 = Activo  
     * || 2 = bloqueado por equivocarse
     * || 3 = Bloqueado por admin
     * ___________________________________
      
     * */  
    public function lock_user ($id, $lock = 2) {


        $sql = "UPDATE {$this->table} SET activo = ? WHERE id = ?";

        $data = [$lock, $id];
        $this->query($sql, $data);

        return $this->find($id);
    }

    public function reset_counter($id) {

    
        $sql = "UPDATE {$this->table} SET intentos = 0 WHERE id = ?";
        $this->query($sql, [$id]);

        return $this->find($id);
    }

   
    
}

