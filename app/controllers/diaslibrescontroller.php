<?php

namespace app\controllers;

use app\models\diaslibres;
use libs\CSRF;

class diaslibrescontroller extends controller
{

    public function index()
    {
        $model = new diaslibres;
        $data = $model->all();
        return $this->vista("diaslibres/index", compact('data'));
    }
    public function create()
    {
        return $this->vista("diaslibres/create");
    }

    public function store()
    {
        CSRF::validar();
        $libre = new diaslibres;

        $nombre       = $this->limpiar("nombre", "texto", "Descripcion del evento");
        $fecha_inicio = $this->limpiar("fecha_inicio", "fecha", "Fecha de Inicio");
        $fecha_fin    = $this->limpiar("fecha_fin", "fecha", "Fecha de Fin");
        $check_ano    = $this->limpiar("ano", "letras", "Repeticion Anual");

        if ($check_ano !== "repitesi" && $check_ano !== "repiteno") {
            $this->error("El valor de repetición anual no es válido.");
        }

        $ano_actual  = date("Y");
        $ano = ($check_ano == "repitesi") ? null : $ano_actual;
            
        if (date("Y",strtotime($fecha_inicio)) != date("Y")) {
            $this->error("No puedes registrar dias libres en años diferentes al actual");
        }
        
        if ($fecha_inicio > $fecha_fin) {
            $this->error("Por favor introduzca un rango de fechas válido");
        }

        if ($libre->verify_fecha_rangos($fecha_inicio, $fecha_fin)) {
            $this->error("La fecha seleecionada conincide con una fecha ya registrada");
        }
        
        // Texto ANTES de transformar las fechas (para que el año se vea real)
        $this->registrar_bitacora("Registró el día libre {$nombre} ({$fecha_inicio} a {$fecha_fin})", "config");

        if (!$ano) {

            $ano_restar = date("y");
            $fecha_inicio = date("Y-m-d", strtotime($fecha_inicio . " - {$ano_restar} years"));
            $fecha_fin    = date("Y-m-d", strtotime($fecha_fin . " - {$ano_restar} years"));

        }

        $libre->create(compact("nombre", "fecha_inicio", "fecha_fin", "ano"));
        $this->success("Día libre registrado correctamente");
    }

    public function update($id)
    {

        CSRF::validar();

        $nombre       = $this->limpiar("nombre", "texto");
        $fecha_inicio = $this->limpiar("fecha_inicio", "fecha");
        $fecha_fin    = $this->limpiar("fecha_fin", "fecha");
        $check_ano    = $this->limpiar("ano", "letras");

        $libre = new diaslibres;

        if ($check_ano !== "repitesi" && $check_ano !== "repiteno") {
            $this->error("El valor de repetición anual no es válido.");
        }

        $ano_actual  = date("Y");
        $ano = ($check_ano == "repitesi") ? null : $ano_actual;

        // 2. Validación de coherencia de fechas
        if ($fecha_inicio > $fecha_fin) {
            $this->error("Por favor introduzca un rango de fechas válido");
        }

        if ($verify_fecha = $libre->verify_fecha_rangos($fecha_inicio, $fecha_fin)) {

            if (count($verify_fecha) > 1) {

                $this->error("Las fechas seleccionadas coincide con una fecha registrada");
            }

            foreach ($verify_fecha as $key) {

                if ($key["id"] != $id) {
                    $this->error("Las fechas seleccionadas coincide con una fecha registrada");
                }
            }
        }


        if (!$ano) {

            $ano_restar = date("y");
            $fecha_inicio = date("Y-m-d", strtotime($fecha_inicio . " - {$ano_restar} years"));
            $fecha_fin    = date("Y-m-d", strtotime($fecha_fin . " - {$ano_restar} years"));
        }

        // Creamos el registro pasando todos los datos limpios y seguros
        $libre->update($id, compact("nombre", "fecha_inicio", "fecha_fin", "ano"));

        $this->success("Día libre Actualizado correctamente");
    }
    
    public function edit($id)
    {

        $model = new diaslibres;
        $data = $model->find($id);
        return $this->vista("diaslibres/edit", compact("data"));
    }

    public function destroy($id)
    {

        $model = new diaslibres;

        $registro = $model->find($id);
        $nombre_dl = $registro["nombre"] ?? "ID {$id}";

        $model->delete($id);

        $this->registrar_bitacora("Eliminó el día libre {$nombre_dl}", "config");
        return $this->redirect("/diaslibres");
    }
}
