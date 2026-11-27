<?php

namespace app\controllers;

use app\models\asistencia;
use app\models\diaslibres;
use app\models\permisos;
use app\models\personal;
use app\models\turnodia;
use app\models\turnos;

class reportescontroller extends controller
{

   public function index()
   {
      $mes  = date('n');    // mes actual por defecto al entrar
      $anio = date('Y');

      $datos = $this->calcularEstadisticas($mes, $anio);

      $mes_actual       = $mes;
      $anio_actual      = $anio;
      $totales          = $datos['totales'];
      $reporte_personal = $datos['reporte_personal'];
      $meses = $this->meses;


      // Pasa a la vista las variables del contrato (ajusta a tu método de render):
      return $this->vista('reportes/index', compact("mes_actual", "anio_actual", "totales", "reporte_personal", "meses"));
   }

   public function qr($id)
   {
      $model = new personal;
      $turnos = new turnos;
      $turno_dia = new turnodia;
      $qr = $model->innerWhere("persona", "id", $id)->firts();
      $id_turno = $qr["id_turno"];

      if ($id_turno == NULL) {
         $id_turno = $turno_dia->where("id_personal", $id)->firts()["id_turno"];
      }

      $turno = $turnos->find($id_turno);

      if (!$qr || empty($qr["qr"])) {

         $this->error("Este usuario no posee qr");
      }


      $no_autorizado = true;
      return $this->vista('/reportes/qr', compact("qr", "no_autorizado", "turno"));
   }

   public function reporte_especifico($id)
   {

      $personal = new personal;
      $asistencia = new asistencia;
      $dias_libre = new diaslibres;
      $permiso = new permisos;
      $turnos = new turnos;


      $ano = $this->limpiar("ano", "numeros");
      $mes = $this->limpiar("mes", "numeros");

      $fecha = date("Y-m-d", strtotime($ano . "-" . $mes));
      $fecha2 = date("Y-m-d", strtotime($ano . "-" . $mes . " + 1 month"));

      if (date("m-d", strtotime($fecha)) < "08-15") {
         $ano_escolar =  date("Y", strtotime($fecha . " - 1 years")) . "-" . $ano;
      } else {
         $ano_escolar =  $ano . "-" . date("Y", strtotime($fecha . " + 1 years"));
      }

      $result = $personal->find($id);

      if ($result) {

         if ($result["id_turno"] == NULL) {
            $data = $personal->turno_dia_personal_id($id);
         } else {
            $data = $personal->turno_personal_id($id);
         }
      } else {
         $this->error("No se pudo conseguir al trabajador");
      }



      $asistencias = $asistencia->encontrar_asistencia($fecha, $fecha2, $id);
      $dias_libres = $dias_libre->encontrar_fechas($fecha, $fecha2);
      $permisos = $permiso->encontrar_permisos($fecha, $fecha2, $id);

      $inicio_ano_escolar = explode("-", $ano_escolar)[0];
      $inicio_ano_escolar = $inicio_ano_escolar . "-09-01";
      $hoy = date("Y-m-d");

      if ($hoy > $fecha2) {
         $hoy = $fecha2;
      }

      $ano_escolar_asistencia = $asistencia->encontrar_asistencia($inicio_ano_escolar, $hoy, $id);
      $ano_escolar_dias_libre = $dias_libre->encontrar_fechas($inicio_ano_escolar, $hoy);
      $ano_escolar_permiso = $permiso->encontrar_permisos($inicio_ano_escolar, $hoy, $id);
      $no_autorizado = true;

      $turno_director = $turnos->where("cargo", "director")->firts();

      if ($turno_director) {

         $id_turno_director = $turno_director["id"];
         $director = $personal->innerWhereFull("persona", "id", "id", "personal", "id_turno", $id_turno_director)->firts();
      } else {
         $director = [
            "cedula" => 12345678
         ];
      }


      return $this->vista('/reportes/especifico', compact("director", "ano_escolar", "ano_escolar_asistencia", "ano_escolar_dias_libre", "ano_escolar_permiso", 'fecha', 'data', "asistencias", "dias_libres", "permisos", 'no_autorizado'));
   }

   public function general()
   {
      $personal    = new personal;
      $asistencia  = new asistencia;
      $dias_libres = new diaslibres;
      $permiso     = new permisos;
      $turnos      = new turnos;

      $turno = strtolower($this->limpiar("turno", "letras"));
      $cargo = strtolower($this->limpiar("cargo", "letras"));
      $ano   = $this->limpiar("ano", "numeros");
      $mes   = $this->limpiar("mes", "mes", 0, "El mes ingresado es inválido");

      if ($cargo !== 'todos' && !in_array($cargo, $this->cargos)) {
         $this->error("El cargo seleccionado es inválido");
      }
      if ($turno !== 'todos' && !in_array($turno, $this->turnos)) {
         $this->error("El turno seleccionado es inválido");
      }

      $total_dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
      $mesTexto = $this->meses[$mes];
      $hoy = date("Y-m-d");

      if (date("m-d", strtotime("$ano-$mes-01")) < "08-15") {
         $ano_escolar = ((int)$ano - 1) . "-" . $ano;
      } else {
         $ano_escolar = $ano . "-" . ((int)$ano + 1);
      }

      // Días laborales del mes (una sola vez, sirve para todos los grupos)
      $dias_laborales = [];
      for ($dia = 1; $dia <= $total_dias_mes; $dia++) {
         $fecha_bucle = date("Y-m-d", strtotime("$ano-$mes-$dia"));
         $dia_semana = date("N", strtotime($fecha_bucle));
         if ($dia_semana == 6 || $dia_semana == 7 || $dias_libres->verify_fecha($fecha_bucle)) continue;
         $dias_laborales[] = $dia;
      }

      // ===== ¿Qué grupos (cargo-turno) se imprimen? =====
      if ($cargo !== 'todos' && $turno !== 'todos') {
         // Filtro específico: un solo grupo
         $grupos = [['cargo' => $cargo, 'turno' => $turno]];
      } else {
         // Todos: cada combinación existente (respetando el filtro parcial si hay)
         $grupos = [];
         foreach ($turnos->combos_operativos() as $combo) {
            if ($cargo !== 'todos' && $combo['cargo'] !== $cargo) continue;
            if ($turno !== 'todos' && $combo['turno'] !== $turno) continue;
            $grupos[] = $combo;
         }
      }

      // ===== Armar cada grupo =====
      $reportes = [];
      foreach ($grupos as $g) {
         $trabajadores = $personal->all_personal_turno($g['turno'], $g['cargo']);
         $trabajador_asistencia = [];

         foreach ($trabajadores as $trabajador) {
            $dias = []; // reiniciar por trabajador

            foreach ($dias_laborales as $dia) {
               $fecha_bucle = date("Y-m-d", strtotime("$ano-$mes-$dia"));
               if ($fecha_bucle > $hoy) continue;

               $a = $asistencia->asistencia_general($fecha_bucle, $trabajador["id"]);
               $p = $permiso->permiso_general($fecha_bucle, $trabajador["id"]);

               if ($a)      $dias[$dia] = "A";
               elseif ($p)  $dias[$dia] = "P";
               else         $dias[$dia] = "I";
            }

            $trabajador_asistencia[] = [
               'cedula'          => $trabajador["cedula"],
               'nombre_completo' => trim(($trabajador["nombre"] ?? '') . ' ' . ($trabajador["apellido"] ?? '')),
               'dias'            => $dias
            ];
         }

         // Solo grupos con personal (evita hojas vacías)
         if (!empty($trabajador_asistencia)) {
            $reportes[] = [
               'cargo'                 => $g['cargo'],
               'turno'                 => $g['turno'],
               'trabajador_asistencia' => $trabajador_asistencia
            ];
         }
      }

      if (empty($reportes)) {
         $this->error("No se encontró personal que cumpla con los parámetros seleccionados");
      }

      $no_autorizado = true;
      $id_turno_director = $turnos->where("cargo", "director")->firts()["id"];
      $director = $personal->innerWhereFull("persona", "id", "id", "personal", "id_turno", $id_turno_director)->firts();

      // Ojo: ahora se pasa $reportes (grupos), no $trabajador_asistencia plano
      return $this->vista("reportes/general", compact("director", "no_autorizado", "ano", "mesTexto", "dias_laborales", "reportes", "ano_escolar"));
   }

   public function todosqr()
   {
      $personal = new personal;
      

      $trabajadores = $personal->turno_personal();
      $trabajadores_mixto = $personal->turno_dia_personal();

      $todos_qr = array_merge($trabajadores, $trabajadores_mixto);

      
      $no_autorizado = true;

      $this->vista("reportes/allqr", compact("no_autorizado", "todos_qr"));
   }



   public function datos_general_json()
   {
      header('Content-Type: application/json');

      $mes  = (int)$this->limpiar("mes",  "mes",     "mes");
      $anio = (int)$this->limpiar("anio", "numeros", "año", "El campo año no cumple con los criterios", 4, 4);

      echo json_encode($this->calcularEstadisticas($mes, $anio));
      exit;
   }


   private function calcularEstadisticas(int $mes, int $anio): array
   {
      $personal    = new personal;
      $asistencia  = new asistencia;
      $dias_libres = new diaslibres;
      $permiso     = new permisos;

      $total_dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
      $hoy = date("Y-m-d");

      // Días laborables transcurridos del mes
      $dias_a_evaluar = [];
      for ($dia = 1; $dia <= $total_dias_mes; $dia++) {
         $fecha_bucle = date("Y-m-d", strtotime("$anio-$mes-$dia"));
         $dia_semana  = date("N", strtotime($fecha_bucle));

         if ($dia_semana == 6 || $dia_semana == 7 || $dias_libres->verify_fecha($fecha_bucle)) continue;
         if ($fecha_bucle > $hoy) continue;
         $dias_a_evaluar[] = $fecha_bucle;
      }

      $reporte_personal = [];
      $totales = ['trabajadores' => 0, 'asistencias' => 0, 'faltas' => 0, 'permisos' => 0];

      foreach ($personal->all_personal() as $trabajador) {
         $asist = 0;
         $retard = 0;
         $perm = 0;

         foreach ($dias_a_evaluar as $fecha_bucle) {
            $registro = $asistencia->asistencia_general($fecha_bucle, $trabajador["id"]);

            if ($registro) {
               $asist++;
               $estado = is_array($registro) ? ($registro['estado_entrada'] ?? '') : '';
               if ($estado === 'Con retardo' || $estado === 'Tarde') {
                  $retard++;
               }
            } elseif ($permiso->permiso_general($fecha_bucle, $trabajador["id"])) {
               $perm++;
            }
         }

         // Regla de negocio: los permisos cuentan como inasistencia
         $faltas = max(0, count($dias_a_evaluar) - $asist);

         $reporte_personal[] = [
            'nombre'            => $trabajador['nombre'] ?? '',
            'apellido'          => $trabajador['apellido'] ?? '',
            'cargo'             => $trabajador['cargo'] ?? '',
            'turno'             => !empty($trabajador['nombre_turno']) ? ucfirst($trabajador['nombre_turno']) : 'Mixto',
            'total_asistencias' => $asist,
            'total_retardos'    => $retard,
            'total_faltas'      => $faltas,
            'total_permisos'    => $perm,
         ];

         $totales['trabajadores']++;
         $totales['asistencias'] += $asist;
         $totales['faltas']      += $faltas;
         $totales['permisos']    += $perm;
      }

      return [
         'totales'          => $totales,
         'reporte_personal' => $reporte_personal,
      ];
   }
}
