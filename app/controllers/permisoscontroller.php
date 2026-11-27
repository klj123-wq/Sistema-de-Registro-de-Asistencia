<?php

namespace app\controllers;

use app\models\permisos;
use app\models\persona;
use libs\CSRF;

class permisoscontroller extends controller
{

    public function index()
    {

        $model = new permisos;
        $data = $model->all();
        return $this->vista("/permisos/index");
    }

    public function create($id)
    {

        $model = new persona;
        $data = $model->find($id);
        return $this->vista("/permisos/create", compact('data'));
    }

    public function store($id)
    {
        CSRF::validar();

        $id_persona = $id;
        $descripcion = $this->limpiar("descripcion", "texto");
        $fecha_inicio = $this->limpiar("fecha_inicio", "fecha");
        $fecha_fin = $this->limpiar("fecha_fin", "fecha");

        $model = new permisos;
        if ($fecha_inicio > $fecha_fin) {
            $this->error("Por favor introduzca un rango de fechas válido");
        }

        if ($model->verify($fecha_inicio, $fecha_fin, $id)->firts()) {
            $this->error("Error, este trabajador ya tiene permiso en las fechas seleccionadas");
        }

        if ($model->create(compact("descripcion", "fecha_inicio", "fecha_fin", "id_persona"))) {

            $this->success("Permiso creado exitosamente");
        }
    }
    public function show($id)
    {

        $model = new permisos;
        $data = $model->where("id_persona", $id)->get();
        return $this->vista("/permisos/show", compact('data', 'id'));
    }
    public function edit($id)
    {

        $model = new permisos;
        $data = $model->find($id);
        return $this->vista("/permisos/edit", compact('data'));
    }
    public function update($id)
    {

        CSRF::validar();

        $id_persona = $this->limpiar("id_persona", "numeros");
        $descripcion = $this->limpiar("descripcion", "texto");
        $fecha_inicio = $this->limpiar("fecha_inicio", "fecha");
        $fecha_fin = $this->limpiar("fecha_fin", "fecha");

        $model = new permisos;

        $result = $model->verify($fecha_inicio, $fecha_fin, $id_persona)->get();
        

        if ($fecha_inicio > $fecha_fin) {
            $this->error("Por favor introduzca un rango de fechas válido");
        }
        
        if (count($result) > 1) {
            
            $this->error("Error, este trabajador ya tiene permiso en las fechas seleccionadas");
        }

        foreach ($result as $key) {

            if ($key["id"] != $id) {
                $this->error("Error, este trabajador ya tiene permiso en las fechas seleccionadas");
            }
        }

        if ($model->update($id, compact("descripcion", "fecha_inicio", "fecha_fin"))) {

            $this->success("Permiso actualizado exitosamente");
        }
    }
    public function destroy($id)
    {

        $model = new permisos;
        $data = $model->find($id);
        $model->delete($id);
        $this->redirect("/permisos/{$data['id_persona']}");
    }
}
