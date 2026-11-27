<?php

namespace app\controllers;

use app\models\asistencia;
use app\models\diaslibres;
use app\models\permisos;
use app\models\persona;
use app\models\personal;
use app\models\turnodia;
use app\models\turnos;
use libs\CSRF;

date_default_timezone_set('America/Caracas');

class asistenciacontroller extends controller
{

    public function index()
    {
        return $this->vista("/asistencia/index");
    }

    public function create($id)
    {
        $model = new persona;
        $data = $model->find($id);
        return $this->vista("/asistencia/create", compact('data'));
    }

    public function show($id)
    {
        $model = new asistencia;
        $data = $model->where("id_persona", $id)->get();
        return $this->vista("/asistencia/show", compact('data', 'id'));
    }

    public function store($id)
    {
        CSRF::validar();

        $bitacora_controller = new bitacoracontroller;
        $asistencia = new asistencia;
        $dia_libre = new diaslibres;
        $turno_dia = new turnodia;
        $personal = new personal;
        $permiso = new permisos;
        $persona = new persona;
        $turnos = new turnos;

        $hoy = date("Y-m-d");
        $id_persona = $id;
        $fecha = $this->limpiar("fecha", "fecha");
        $hora_entrada = $this->limpiar("hora_entrada", "hora");
        $hora_salida = (!empty($_POST["hora_salida"])) ? $this->limpiar("hora_salida", "hora") : NULL;
        $datos_persona = $persona->find($id);
        $estado_salida = NULL;

        $result = $personal->where_and("id", $id, "activo", 1)->firts();
        if (!$result) {
            $this->error("Trabajador no encontrado");
        }

        if ($fecha > $hoy) {
            $this->error("No se pueden registrar asistencias en días futuro");
        }

        if ($asistencia->where_and("fecha", $fecha, "id_persona", $id)->firts()) {
            $this->error("Este trabajador ya tiene una asistencia registrada este día");
        }
        if ($result = $dia_libre->verify_fecha($fecha)) {
            $this->error("El día seleccionado está registrado como libre o feriado, no se pueden registrar asistencias");
        }
        if ($permiso->verify($fecha, $fecha, $id)->firts()) {
            $this->error("Este trabajador tiene un permiso registrado para este fecha");
        }


        $id_turno = $personal->find($id)["id_turno"];

        if ($id_turno == NULL) {

            $timestamp = strtotime($fecha);
            $dia_semana = date("w", $timestamp);
            $id_turno = $turno_dia->where_and("id_personal", $id, "dia", $dia_semana)->firts()["id_turno"];
        }

        $turno_select = $turnos->find($id_turno);
        $estado_entrada = $this->validar_entrada($hora_entrada, $turno_select["hora_entrada"], $turno_select["hora_salida"], $turno_select["minutos_tolerancia"]);

        if ($hora_salida != NULL) {
            $estado_salida = $this->validar_salida($hora_salida, $turno_select["hora_entrada"], $turno_select["hora_salida"], $turno_select["minutos_tolerancia"]);
        }

        if ($asistencia->create(compact("fecha", "hora_entrada", "hora_salida", "estado_entrada", "estado_salida", "id_persona"))) {

            $hora_entrada_formateada = date("h:i a", strtotime($hora_entrada));
            $cedula = "V-" . number_format($datos_persona["cedula"], 0, ",", ".");

            if (!empty($hora_salida)) {

                $hora_salida_formateada = date("h:i a", strtotime($hora_salida));


                $accion = "Se registró una asistencia para {$datos_persona['nombre']} {$datos_persona['apellido']} C.I: {$cedula} para el día {$fecha} con Hora de entrada: {$hora_entrada_formateada} y Hora de salida: {$hora_salida_formateada}";
            } else {
                $accion = "Se registró una asistencia para {$datos_persona['nombre']} {$datos_persona['apellido']} C.I: {$cedula} para el día {$fecha} con Hora de entrada: {$hora_entrada_formateada}";
            }

            $bitacora_controller->guardar_bitacora($accion, $id_persona);
        }

        $this->success("Asistencia generada exitosamente", "/asistencia/{$datos_persona['id']}");
    }

    public function update($id)
    {
        CSRF::validar();

        $bitacora_controller = new bitacoracontroller;
        $asistencia = new asistencia;
        $dia_libre = new diaslibres;
        $turno_dia = new turnodia;
        $personal = new personal;
        $permiso = new permisos;
        $persona = new persona;
        $turnos = new turnos;

        $hoy = date("Y-m-d");
        $fecha = $this->limpiar("fecha", "fecha", "fecha");

        // CORRECCIÓN: "H:i:s" convierte el HH:MM del formulario al formato exacto de la BD
        $hora_entrada_raw = $this->limpiar("hora_entrada", "hora", "Hora de entrada");
        $hora_entrada = date("H:i:s", strtotime($hora_entrada_raw));

        // CORRECCIÓN: Validamos si viene vacía y también la convertimos a H:i:s
        $hora_salida_raw = !empty($_POST["hora_salida"]) ? $this->limpiar("hora_salida", "hora", "Hora de salida") : NULL;
        $hora_salida = $hora_salida_raw ? date("H:i:s", strtotime($hora_salida_raw)) : NULL;

        $datos_asistencia = $asistencia->find($id);
        $datos_persona = $persona->find($datos_asistencia["id_persona"]);
        $estado_salida = NULL;

        if ($fecha > $hoy) {
            $this->error("No puedes registrar una asistencia en días futuros");
        }

        $verify_asistencia = $asistencia->where_and("fecha", $fecha, "id_persona", $id)->get();
        foreach ($verify_asistencia as $asis) {
            if ($asis["id"] != $id) {
                $this->error("Este trabajador ya tiene una asistencia registrada este día");
            }
        }

        if ($dia_libre->verify_fecha($fecha)) {
            $this->error("Este día está registrado como libre o feriado, no se pueden registrar asistencias");
        }
        if ($permiso->verify($fecha, $fecha, $id)->firts()) {
            $this->error("Este trabajador tiene un permiso registrado para esta fecha");
        }

        $id_turno = $personal->find($datos_persona["id"])["id_turno"];

        if ($id_turno == NULL) {
            $timestamp = strtotime($fecha);
            $dia_semana = date("w", $timestamp);
            $id_turno = $turno_dia->where_and("id_personal", $datos_persona["id"], "dia", $dia_semana)->firts()["id_turno"];
        }

        $turno_select = $turnos->find($id_turno);
        $estado_entrada = $this->validar_entrada($hora_entrada, $turno_select["hora_entrada"], $turno_select["hora_salida"], $turno_select["minutos_tolerancia"]);

        if ($hora_salida != NULL) {
            $estado_salida = $this->validar_salida($hora_salida, $turno_select["hora_entrada"], $turno_select["hora_salida"], $turno_select["minutos_tolerancia"]);
        }

        $result_asistencia = $asistencia->update($id, compact("fecha", "hora_entrada", "hora_salida", "estado_entrada", "estado_salida"));

        // LÓGICA DE BITÁCORA MEJORADA
        if ($result_asistencia) {
            $cambios = [];

            // 1. Detectar exactamente qué cambió comparando BD vs Variables Nuevas
            if ($datos_asistencia['fecha'] != $fecha) {
                $cambios['fecha'] = [
                    'viejo' => $datos_asistencia['fecha'],
                    'nuevo' => $fecha
                ];
            }
            if ($datos_asistencia['hora_entrada'] != $hora_entrada) {
                $cambios['hora_entrada'] = [
                    'viejo' => date("h:i A", strtotime($datos_asistencia['hora_entrada'])),
                    'nuevo' => date("h:i A", strtotime($hora_entrada))
                ];
            }
            if ($datos_asistencia['hora_salida'] != $hora_salida) {
                $vieja_salida = !empty($datos_asistencia['hora_salida']) ? date("h:i A", strtotime($datos_asistencia['hora_salida'])) : "--:--";
                $nueva_salida = !empty($hora_salida) ? date("h:i A", strtotime($hora_salida)) : "--:--";

                $cambios['hora_salida'] = [
                    'viejo' => $vieja_salida,
                    'nuevo' => $nueva_salida
                ];
            }

            // 2. Si hay cambios, construimos el mensaje de forma inteligente
            if (!empty($cambios)) {
                $cedula = "V-" . number_format($datos_persona["cedula"], 0, ",", ".");
                $count = count($cambios);

                $accion = "Se realizaron modificaciones en la asistencia de {$datos_persona['nombre']} {$datos_persona['apellido']} C.I: {$cedula}. ";

                $str_viejos = [];
                $str_nuevos = [];

                // Llenamos arreglos solo con los textos que cambiaron
                if (isset($cambios['fecha'])) {
                    $str_viejos[] = "Fecha: " . $cambios['fecha']['viejo'];
                    $str_nuevos[] = "Fecha: " . $cambios['fecha']['nuevo'];
                }
                if (isset($cambios['hora_entrada'])) {
                    $str_viejos[] = "Entrada: " . $cambios['hora_entrada']['viejo'];
                    $str_nuevos[] = "Entrada: " . $cambios['hora_entrada']['nuevo'];
                }
                if (isset($cambios['hora_salida'])) {
                    $str_viejos[] = "Salida: " . $cambios['hora_salida']['viejo'];
                    $str_nuevos[] = "Salida: " . $cambios['hora_salida']['nuevo'];
                }

                // Unimos los textos con comas de forma automática
                $accion .= ($count > 1) ? "Valores anteriores: " : "Valor anterior: ";
                $accion .= implode(", ", $str_viejos) . ". ";

                $accion .= ($count > 1) ? "Valores actualizados: " : "Valor actualizado: ";
                $accion .= implode(", ", $str_nuevos) . ".";

                // Guardar
                $bitacora_controller->guardar_bitacora($accion, $datos_persona["id"]);
            }
        }

        $this->success("Asistencia actualizada exitosamente", "/asistencia/{$datos_persona['id']}");
    }

    public function edit($id)
    {

        $model = new asistencia;
        $data = $model->find($id);
        return $this->vista("/asistencia/edit", compact('data'));
    }

    public function destroy($id)
    {

        $bitacora_controller = new bitacoracontroller;
        $asistencia = new asistencia;
        $persona = new persona;
        $datos_asistencia = $asistencia->find($id);
        $datos_persona = $persona->find($datos_asistencia["id_persona"]);
        $asistencia->delete($id);
        $cedula = number_format($datos_persona['cedula'], 0, ",", ".");

        if ($datos_persona) {

            $accion = "Se eliminó la asistencia del dia {$datos_asistencia['fecha']} de {$datos_persona['nombre']} {$datos_persona['apellido']} con la Cedula V-{$datos_persona['cedula']}";

            $bitacora_controller->guardar_bitacora($accion, $datos_persona["id"]);
        }

        $this->success("Asistencia eliminada exitosamente", "/asistencia/{$datos_persona['id']}", false);
    }

    public function procesar()
    {


        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty(trim($input['id']))) {
            $this->error('ERROR');
        }




        $asistencia = new asistencia;
        $turnos = new turnos;
        $turno_dia = new turnodia;
        $personal = new personal;
        $persona = new persona;
        $dia_libre = new diaslibres;
        $permiso = new permisos;


        $fecha = date("Y-m-d");
        $hora = date("H:i");
        $hora_salida = NULL;
        $estado_salida = NULL;



        $qr = trim($input['id']);
        $result_personal = $personal->where_and("qr", $qr, "activo", 1)->firts();


        if (!$result_personal) {
            $this->error('ERROR: Qr no Valido');
        }

        if (date("N") > 5) {
            $this->error("No se pueden tomar asistencias sabados ni domingos");
        }


        $id_persona = $result_personal["id"];
        $result_persona = $persona->find($id_persona);


        if ($result = $dia_libre->verify_fecha($fecha)) {

            $this->error("Este día está registrado como libre o feriado, no se pueden registrar asistencias");
        }
        if ($permiso->verify($fecha, $fecha, $id_persona)->firts()) {

            $this->error("ERROR: Hay un permiso registrado para esta fecha");
        }


        $result_asistencia = $asistencia->where_and("fecha", $fecha, "id_persona", $id_persona)->firts();

        if (!empty($result_asistencia["hora_entrada"]) && !empty($result_asistencia["hora_salida"])) {

            $this->error("ERROR: Ya se han registrado las horas de entrada y salida");
        }

        $id_turno = $result_personal["id_turno"];

        if ($id_turno == NULL) {

            $timestamp = strtotime($fecha);
            $dia_semana = date("w", $timestamp);
            $id_turno = $turno_dia->where_and("id_personal", $id_persona, "dia", $dia_semana)->firts()["id_turno"];
        }

        $turno_select = $turnos->find($id_turno);



        if (empty($result_asistencia["hora_entrada"])) {

            $hora_entrada = $hora;

            $estado_entrada = $this->validar_entrada($hora_entrada, $turno_select["hora_entrada"], $turno_select["hora_salida"], $turno_select["minutos_tolerancia"]);

            if ($asistencia->create(compact("fecha", "hora_entrada", "hora_salida", "estado_entrada", "estado_salida", "id_persona"))) {

                die(json_encode([
                    'success'  => true,
                    'message'  => 'Registro exitoso',
                    'empleado' => "{$result_persona['nombre']} {$result_persona['apellido']} {$result_persona['cedula']} registro exitoso"
                ], JSON_UNESCAPED_UNICODE));
            }
        } else {

            $espera_escaneo = date("H:i", strtotime($result_asistencia["hora_entrada"] . "+ 10 minutes"));

            if ($espera_escaneo < $hora) {

                $id = $result_asistencia["id"];
                $hora_salida = $hora;

                $estado_salida = $this->validar_salida($hora_salida, $turno_select["hora_entrada"], $turno_select["hora_salida"], $turno_select["minutos_tolerancia"]);

                if ($asistencia->update($id, compact("hora_salida", "estado_salida"))) {

                    die(json_encode([
                        'success'  => true,
                        'message'  => 'Actualización exitosa',
                        'empleado' => "{$result_persona['nombre']} {$result_persona['apellido']} {$result_persona['cedula']} registro exitoso"
                    ], JSON_UNESCAPED_UNICODE));
                }
            } else {

                $this->error('Debes esperar 10 minutos desde el registro de tu entrada');
            }
        }
    }




    public function asistencias_recientes()
    {


        $asistencias = new asistencia;
        $recientes = $asistencias->asistencias_hoy();

        die(json_encode($recientes));
    }








    public function validar_entrada($hora, $entrada_asignada, $salida_asignada, $tolerancia)
    {

        $entrada_antes = date("H:i", strtotime($entrada_asignada . "- 1 hour"));

        if ($hora <= $salida_asignada && $hora >= $entrada_antes) {
            $entrada_tolerancia = date("H:i", strtotime($entrada_asignada . " + " .  $tolerancia . " minutes"));

            if ($hora <= $entrada_tolerancia) {

                $mensaje = "A tiempo";
            } else {
                $mensaje = "Con retardo";
            }

            return $mensaje;
        } else {

            $this->error("No se puede registrar una entrada fuera del turno correspondiente");
        }
    }

    public function validar_salida($hora, $entrada_asignada, $salida_asignada, $tolerancia)
    {

        $salida_despues = date("H:i", strtotime($salida_asignada . "+ 2 hour"));

        if ($hora <= $salida_despues && $hora >= $entrada_asignada) {
            $salida_tolerancia = date("H:i", strtotime($salida_asignada . " - 10 minutes"));

            if ($hora >= $salida_tolerancia) {

                $mensaje = "A tiempo";
            } else {
                $mensaje = "Anticipada";
            }

            return $mensaje;
        } else {

            $this->error("La hora para el registro de salida se encuentra fuera del turno establecido");
        }
    }

    public function prueba()
    {

        return $this->vista("asistencia/prueba");
    }
}
