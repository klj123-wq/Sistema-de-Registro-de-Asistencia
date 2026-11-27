<?php

namespace app\models;

use libs\Env;
use PDO;
use PDOException;

class model
{

    protected $dsn;
    protected $db_user;
    protected $db_pass;
    protected $connection;
    protected $query;
    protected $table;


    public function __construct()
    {
        $this->connection();
    }

    public function connection()
    {

        try {
            $this->connection = new PDO($this->dsn = Env::get("DSN"), $this->db_user = Env::get("DB_USER"), $this->db_pass = Env::get("DB_PASS"));

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $aver = implode(">", array_slice(explode("\\", $e->getFile()), 4));
            die("<b>Se ha presentado el error: </b>" . $e->getMessage() . "<br><b>Por favor verificar el Archivo: </b>" . $aver . "<b> en la linea: " . $e->getLine() . "</b>");
        }
    }

    public function query($consulta, $data = [])
    {

        if ($data) {

            $i = 0;
            $stmt = $this->connection->prepare($consulta);
            foreach ($data as $key) {
                $i += 1;
                $stmt->bindValue($i, $key);
            }

            $stmt->execute();

            $this->query = $stmt;
        } else {

            $this->query = $this->connection->query($consulta);
        }


        return $this;
    }

    public function firts()
    {
        return $this->query->fetch();
    }

    public function get()
    {
        return $this->query->fetchAll();
    }

    public function all()
    {
        # SELECT * FROM table 

        $sql = "SELECT * FROM {$this->table}";
        return $this->query($sql)->get();
    }

    public function find($id)
    {
        # SELECT * FROM table WHERE id = {id}

        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->query($sql, [$id])->firts();
    }

    public function where($column, $value)
    {

        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";

        return $this->query($sql, [$value]);
    }

    public function create($data = [])
    {
        # INSERT INTO table (columna1, columna2) VALUES ('valor1', 'valor2')


        $columns = implode(", ", array_keys($data));
        $values = array_values($data);
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES (" . str_repeat("?, ", count($data) - 1) . " ?)";

        if ($this->query($sql, $values)) {

            if ($this->connection->lastInsertId()) {
                return $this->connection->lastInsertId();
            } else {
                return true;
            }
        }
    }

    public function update($id, $data)
    {
        $array = [];
        foreach ($data as $key => $value) {
            $array[] = "{$key} = ?";
        }

        $prepare_datos = implode(", ", $array) . "";
        $sql = "UPDATE {$this->table} SET {$prepare_datos} WHERE id = ?";

        $datos = array_values($data);
        $datos[] = $id;

        $this->query($sql, $datos);
        return $this->find($id);
    }

    public function delete($id)
    {
        # DELETE FROM tabla WHERE 'cedula' = 0 
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->query($sql, [$id])->firts();
    }

    public function inner($tabla, $columna = "id")
    {

        if ($columna == "id") {
            $sql = "SELECT * FROM {$this->table} INNER JOIN {$tabla} ON {$this->table}.id = {$tabla}.id";
        } else {
            $sql = "SELECT * FROM {$this->table} INNER JOIN {$tabla} ON {$this->table}.id = {$tabla}.{$columna}";
        }
        

        return $this->query($sql);
    }

    /**
     * @param boolean $selectabla
     * __________________________________
     * Valores de Selectabla
     * 
     * Default true = where en la primera tabla
     * 
     * false = where en la tabla que se escribió
     * 
     * 
     */
    public function innerWhere($tabla, $column, $value, $selectabla = true)
    {

        if ($selectabla) {
            $sql = "SELECT * FROM {$this->table} INNER JOIN {$tabla} ON {$this->table}.id = {$tabla}.id WHERE $tabla.{$column} = ?";
        } else {
            $sql = "SELECT * FROM {$this->table} INNER JOIN {$tabla} ON {$this->table}.id = {$tabla}.id WHERE {$this->table}.{$column} = ?";
        }


        return $this->query($sql, [$value]);
    }

    public function where_and($column1, $value1, $column2, $value2)
    {

        $sql = "SELECT * FROM {$this->table} WHERE {$column1} = ? AND {$column2} = ?";

        return $this->query($sql, compact("value1", "value2"));
    }

    public function delete_logic($id)
    {

        $sql = "UPDATE {$this->table} SET activo = 0 WHERE id = ?";

        $this->query($sql, [$id]);
        return $this->find($id);
    }



    /**
     * @param string $tableWhere
     * __________________________________
     * Escribe el nombr de cual de las 2 tablas vas a usar para el where
     * ej: mitabla (debe ser alguna de las 2)
     * 
     * 
     * @param string $where
     * __________________________________
     * Escribe el nombre de la columna que se va a seleccionar de la tabla
     * 
     * 
     * 
     * @param string $valueWhere
     * __________________________________
     * Escribe el valor que debe tener el Where
     * 
     * 
     */
    public function innerWhereFull($tabla2, $matchTable1, $matchTable2, $tableWhere, $where, $valueWhere)
    {

        $sql = "SELECT * FROM {$this->table} INNER JOIN {$tabla2} ON {$this->table}.{$matchTable1} = {$tabla2}.{$matchTable2} WHERE {$tableWhere}.{$where} = ?";

        return $this->query($sql, [$valueWhere]);
    }

    public function different ($columna, $valor_diferente) {

        $sql = "SELECT * FROM {$this->table} WHERE {$columna} != ?";
        
        return $this->query($sql, [$valor_diferente])->get();

    }

    public function respaldar_bd($nombre_ruta_archivo)
    {

        $command = "mysqldump -u {$this->db_user} pedro_curiel > {$nombre_ruta_archivo}";
        exec($command, $output, $result_code);

        if ($result_code === 0) {
            return true;
            
        } else {
            
            $errorDetalle = implode("\n", $output);
            return "Fallo en el respaldo. Código de error: $result_code. Detalle: $errorDetalle";
        }
    }

    

    
}
