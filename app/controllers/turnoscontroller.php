<?php

namespace app\controllers;

use app\models\personal;
use app\models\turnodia;
use app\models\turnos;
use libs\CSRF;

class turnoscontroller extends controller
{

   public function index()
   {
      $model = new turnos;
      $data = $model->where_ndirect("activo", 1)->get();
      return $this->vista("/turnos/index", compact("data"));
   }
   public function create()
   {
      return $this->vista("/turnos/create");
   }

   public function store()
   {
      CSRF::validar();

      $model = new turnos;

      $nombre = strtolower($this->limpiar("nombre", "letras"));
      $cargo = strtolower($this->limpiar("cargo", "letras"));
      $hora_entrada = $this->limpiar("hora_entrada", "hora");
      $hora_salida = $this->limpiar("hora_salida", "hora");
      $minutos_tolerancia = ($_POST["minutos_tolerancia"] == 0) ? 0 : $this->limpiar("minutos_tolerancia", "numeros");

      $validar_horas = (strtotime($hora_salida) - strtotime($hora_entrada)) / 3600;

      if ($validar_horas < 1 || $validar_horas > 13) {  $this->error("Se han detectado inconsistencias en las horas de entrada y salida establecidas");}
      if (!in_array($nombre, $this->turnos)) { $this->error("El turno seleccionado es invalido"); }
      if (!in_array($cargo, $this->cargos)) { $this->error("El cargo seleccionado es invalido"); }
      if ($minutos_tolerancia < 0 || $minutos_tolerancia > 120) { $this->error("El valor seleccionado para los minutos de tolerancia es invalido"); }
      if ($nombre == "mañana" && ($hora_entrada >= "12:00" || $hora_entrada < "04:00")) { $this->error("Hora de entrada invalida para el turno de la mañana"); }
      if ($nombre == "tarde" && ($hora_entrada > "18:00" || $hora_entrada < "11:00")) { $this->error("Hora de entrada invalida para el turno de la tarde"); }

      $result = $this->validar_base("nombre", $nombre, "cargo", $cargo)->firts();

      if ($result) {

         if ($result["activo"] == 1) {
            $this->error("Cargo y turno ya registrado");
         }
         if ($result["activo"] == 0) {
            $id = $result["id"];
            $activo = 1;

            $model->update($id, compact("nombre", "cargo", "hora_entrada", "hora_salida", "minutos_tolerancia", "activo"));

            $this->registrar_bitacora("Registró el turno {$nombre} ({$cargo})", "config");
            $this->success("Registro exitoso");
         }
      }


      $model->create(compact("nombre", "cargo", "hora_entrada", "hora_salida", "minutos_tolerancia"));
      $this->registrar_bitacora("Registró el turno {$nombre} ({$cargo})", "config");
      $this->success("Registro exitoso");
   }

   public function destroy($id)
   {

      $model = new turnos;
      $tabla_personal = $model->innerWhereFull("personal", "id", "id_turno", "personal", "activo", true);
      $tabla_turno_dia = $model->inner("turno_dia", "id_turno");

      if ($tabla_personal || $tabla_turno_dia) {
         $this->error("No se puede eliminar un turno donde hay personal registrado");
      } 
      
      if ($model->delete_logic($id)){

         $turno_afectado = $model->find($id);
         $txt_turno = $turno_afectado
            ? "{$turno_afectado["nombre"]} ({$turno_afectado["cargo"]})"
            : "ID {$id}";
         $this->registrar_bitacora("Eliminó el turno {$txt_turno}", "config");
         $this->success("Turno eliminado correctamente", "/turnos");
      }

      $this->redirect("/turnos");
   }

   public function edit($id)
   {
      $model = new turnos;
      $data = $model->find($id);

      return $this->vista("turnos/edit", compact('data'));
   }

   public function update($id)
   {
      CSRF::validar();
      $model = new turnos;

      $nombre = $this->limpiar("nombre", "letras");
      $cargo = $this->limpiar("cargo", "letras");
      $hora_entrada = $this->limpiar("hora_entrada", "hora");
      $hora_salida = $this->limpiar("hora_salida", "hora");
      $minutos_tolerancia = $this->limpiar("minutos_tolerancia", "numeros");



      $validar_horas = (strtotime($hora_salida) - strtotime($hora_entrada)) / 3600;

      if ($validar_horas < 1 || $validar_horas > 13) { $this->error("Se han detectado inconsistencias en las horas de entrada y salida establecidas"); }
      if (!in_array($nombre, $this->turnos)) { $this->error("El turno seleccionado es invalido"); }
      if (!in_array($cargo, $this->cargos)) {  $this->error("El cargo seleccionado es invalido"); }
      if ($minutos_tolerancia < 0 || $minutos_tolerancia > 120) { $this->error("El valor seleccionado para los minutos de tolerancia es invalido"); }
      if ($nombre == "mañana" && ($hora_entrada >= "12:00" || $hora_entrada < "04:00")) { $this->error("Hora de entrada invalida para el turno de la mañana");}
      if ($nombre == "tarde" && ($hora_entrada > "18:00" || $hora_entrada < "11:00")) { $this->error("Hora de entrada invalida para el turno de la tarde"); }

      if ($result = $this->validar_base("nombre", $nombre, "cargo", $cargo)->firts()) {
         if ($result["id"] != $id) {
            if ($result["activo"] == 1) {
               $this->error("Cargo y turno ya registrado en otro horario activo");
            }
            if ($result["activo"] == 0) {

               $personal = new personal;
               $turnodia = new turnodia;

               // Verificamos si tiene dependencias en el pasado usando tu método first()
               $tiene_personal = $personal->where("id_turno", $result["id"])->firts();
               $tiene_dias     = $turnodia->where("id_turno", $result["id"])->firts();

               if ($tiene_personal || $tiene_dias) {
               } else {
                  // Si de verdad está completamente libre en el sistema, lo borramos para no acumular basura
                  $model->delete($result["id"]);
               }
            }
         }
      }


      $model->update($id, compact("nombre", "cargo", "hora_entrada", "hora_salida", "minutos_tolerancia"));

      $this->registrar_bitacora("Editó el turno {$nombre} ({$cargo})", "config");
      $this->success("Actualización exitosa");
   }
}
