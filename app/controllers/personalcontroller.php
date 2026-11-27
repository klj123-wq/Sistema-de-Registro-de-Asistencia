<?php

namespace app\controllers;

use app\models\asistencia;
use app\models\permisos;
use app\models\persona;
use app\models\personal;
use app\models\turnodia;
use app\models\turnos;
use app\models\usuario;
use libs\CSRF;

class personalcontroller extends controller
{

    public function index()
    {

        $personal = new personal;
        $data = $personal->turno_personal();
        $data_dia = $personal->turno_dia_personal();

        return $this->vista('/personal/index', compact('data', "data_dia"));
    }
    public function show($id)
    {
        return "Aquí se mostrará el usuario con id: $id";
    }
    public function create()
    {
        $model = new turnos;
        $data = $model->all();
        return $this->vista("personal/create", compact('data'));
    }

    public function edit($id)
    {
        $model = new personal;
        $data = $model->innerWhere("persona", "id", $id)->firts();


        if ($data["id_turno"] === NULL) {

            $id_personal = $data["id"];
            $model_turno_dia = new turnodia;
            $turnos = $model_turno_dia->innerWhereFull("turnos", "id_turno", "id", "turno_dia", "id_personal", $id_personal)->get();
        } else {
            $id_turno = $data["id_turno"];
            $modelturno = new turnos;
            $turnos = $modelturno->find($id_turno);
        }

        return $this->vista("personal/edit", compact("data", "turnos"));
    }
    public function update($id)
    {
        CSRF::validar();

        $cedula = $this->limpiar("cedula", "cedula", "Cedula", "Algunos campos no cumplen con los criterios establecidos");
        $nombre = ucfirst(strtolower($this->limpiar("nombre", "letras", "Nombre", "Algunos campos no cumplen con los criterios establecidos", 2, 15)));
        $apellido = ucfirst(strtolower($this->limpiar("apellido", "letras", "Apellido", "Algunos campos no cumplen con los criterios establecidos", 2, 15)));
        $cod_cargo = strtoupper($this->limpiar("cod_cargo", "cod_cargo", "Código de cargo", "Algunos campos no cumplen con los criterios establecidos", 6, 6));
        $fecha_ingreso = $this->limpiar("fecha_ingreso", "fecha", "Fecha de ingreso");

        if ($fecha_ingreso > date("Y-m-d")) {
            $this->error("No se puede registrar una fecha de ingreso mayor a la actual");
        }

        $dias = [];
        $nomina = null;

        $persona = new persona;
        $personal = new personal;
        $turnos_model = new turnos;
        $turno_dia = new turnodia;

        $foto = null;

        if (!empty($_FILES["foto"]["name"])) {

            $foto = $this->validar_foto($cedula);
        } elseif (!empty($_POST["foto_base64"])) {

            $foto = $this->guardar_base64("foto_base64", $cedula);
        }

        if (empty($_POST["turno"])) {
            $this->error("error: no se pueden enviar campos vacios");
        }

        $id_turno = ($_POST["turno"] == "mixto") ? NULL : $this->limpiar("turno", "numeros");

        if ($id_turno == NULL) {

            $nomina = $this->limpiar("turno_nomina", "numeros", "Turno de nomina");
            $dias = $this->limpiar_array("dia", "numeros");
        }

        $exist = $persona->where("cedula", $cedula)->firts();

        if ($exist && $exist["id"] != $id) {
            $this->error("La cedula ingresada ya se encuentra asignada a otra persona");
        }

        $result_turno_dia = $turno_dia->where("id_personal", $id)->firts();

        if ($result_turno_dia) {
            $turno_dia->delete_persona_turno($id);
        }

        $persona->update($id, compact("cedula", "nombre", "apellido"));

        if ($foto !== null) {
            $personal->update($id, compact("id_turno", "cod_cargo", "fecha_ingreso", "foto"));
        } else {
            $personal->update($id, compact("id_turno", "cod_cargo", "fecha_ingreso"));
        }

        // ================= RECREAR TURNO MIXTO =================
        $id_personal = $id;
        if ($id_turno == NULL) {
            $turnos_model->create_mixto($dias, $id_personal, $nomina);
        }

        $this->registrar_bitacora("Se editaron los datos de un trabajador", "personal", $id);
        $this->success("El personal ha sido actualizado correctamente");
    }

    public function destroy($id)
    {

        $model = new personal;

        // Datos para la bitácora (antes de eliminar, para tener el nombre)
        $persona = (new persona)->find($id);
        $nombre_afectado = $persona
            ? trim($persona["nombre"] . " " . $persona["apellido"])
            : "ID {$id}";

        $model->delete_logic($id);

        $this->registrar_bitacora("Se eliminó un trabajador", "personal", $id);
        return $this->redirect("/personal");
    }

    public function store()
    {


        CSRF::validar();
        $cedula = $this->limpiar("cedula", "cedula", "Cedula");
        $nombre = ucfirst(strtolower($this->limpiar("nombre", "letras", "Nombre", "Algunos campos no cumplen con los criterios establecidos", 2, 15)));
        $apellido = ucfirst(strtolower($this->limpiar("apellido", "letras", "Apellido", "Algunos campos no cumplen con los criterios establecidos", 2, 15)));
        $cod_cargo = strtoupper($this->limpiar("cod_cargo", "cod_cargo", "Codigo de cargo", "Algunos campos no cumplen con los criterios establecidos", 6, 6));
        $fecha_ingreso = $this->limpiar("fecha_ingreso", "fecha", "Fecha de ingreso");
        $id_turno = ($_POST["turno"] == "mixto") ? NULL : $this->limpiar("turno", "numeros");

        $qr = md5($cedula . time());
        $dias = [];
        $nomina = null;



        $persona = new persona;
        $personal = new personal;
        $turnos_model = new turnos;
        $turno_dia = new turnodia;

        if (!empty($_FILES["foto"]["name"])) {
            $foto = $this->validar_foto($cedula);
        } elseif (!empty($_POST["foto_base64"])) {
            $foto = $this->guardar_base64("foto_base64", $cedula);
        } else {
            $this->error("Es necesario cargar una foto de perfil");
        }


        if ($fecha_ingreso > date("Y-m-d")) {
            $this->error("No se puede registrar una fecha de ingreso mayor a la actual");
        }
        if (empty($_POST["turno"])) {
            $this->error("No se pueden enviar campos vacios");
        }


        if ($id_turno == NULL) {

            $nomina = $this->limpiar("turno_nomina", "numeros", "Turno de nomina");
            $dias = $this->limpiar_array("dia", "numeros");
        }


        $exist = $persona->where("cedula", $cedula)->firts();

        if ($exist) {

            $id = $exist["id"];
            $exist = $personal->find($id);

            if ($exist["activo"] == 1) {
                $this->error("Esta persona ya se encuentra registrada");
            }

            if ($exist["activo"] == 0) {

                $activo = 1;
                $result_turno_dia = $turno_dia->where("id_personal", $id)->firts();

                if ($result_turno_dia) {
                    $turno_dia->delete_persona_turno($result_turno_dia["id_personal"]);
                }

                $persona->update($id, compact("cedula", "nombre", "apellido"));
                $personal->update($id, compact("id_turno", "cod_cargo", "fecha_ingreso", "qr", "foto", "activo"));

                $id_personal = $id;
                if ($id_turno == NULL) {

                    $turnos_model->create_mixto($dias, $id_personal, $nomina);
                }

                $this->registrar_bitacora("Se registró un trabajador", "personal", $id_personal);
                $this->success("El personal ha sido registrado correctamente");
            }
        } else {
            // Si no existe en la tabla base, creamos la persona y capturamos su nuevo ID
            $id = $persona->create(compact("cedula", "nombre", "apellido"));
        }

        // 5. Insertamos en la tabla personal (Quitamos "cargo" de la lista)
        $personal->create(compact("id", "id_turno", "cod_cargo", "fecha_ingreso", "qr", "foto"));
        $id_personal = $id;


        if ($id_turno == NULL) {

            $turnos_model->create_mixto($dias, $id_personal, $nomina);
        }


        $this->registrar_bitacora("Se registró un trabajador", "personal", $id_personal);
        $this->success("El personal ha sido registrado correctamente");
    }

    public function get_turnos()
    {
        // 1. Limpiamos el cargo recibido usando tu método de limpieza con la regla 'texto'
        $cargo = $this->limpiar("cargo", "letras");

        $turnoModel = new turnos;

        $turnos = $turnoModel->where_and("cargo", $cargo, "activo", 1)->get(); // Adapta fetchAll() según los métodos de tu sistema

        // 3. Modificamos las cabeceras para decirle al navegador que la respuesta es un JSON
        header('Content-Type: application/json');

        // 4. Imprimimos el array convertido a JSON y detenemos la ejecución
        echo json_encode($turnos);
        die();
    }

    //nuevo metodo para obtener datos reales en la previsualizacion Dylan 
    public function stats($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        $mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('m');
        $anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');

        $hoy = date('Y-m-d');
        $primerDia = "$anio-$mes-01";
        $ultimoDiaMes = date("Y-m-t", strtotime($primerDia));

        // Solo contar hasta hoy, no hasta fin de mes
        $ultimoDia = ($hoy < $ultimoDiaMes) ? $hoy : $ultimoDiaMes;

        $asistencia = new asistencia;
        $permisos = new permisos;

        $asistencias = $asistencia->encontrar_asistencia($primerDia, $hoy, $id);
        $totalAsistencias = count($asistencias);

        $permisosMes = $permisos->encontrar_permisos($primerDia, $hoy, $id);
        $totalPermisos = 0;
        foreach ($permisosMes as $p) {
            $inicio = max($p['fecha_inicio'], $primerDia);
            $fin = min($p['fecha_fin'], $hoy);
            $totalPermisos += count($permisos->encontrar_permisos($primerDia, $hoy, $id));
        }

        // Contar días hábiles solo hasta hoy
        $diasHabiles = 0;
        $totalDiasMes = date('t', strtotime($primerDia));
        for ($d = 1; $d <= $totalDiasMes; $d++) {
            $fecha = sprintf("%04d-%02d-%02d", $anio, $mes, $d);
            if ($fecha > $hoy) break; // No contar días futuros
            $diaSemana = date('N', strtotime($fecha));
            if ($diaSemana < 6) $diasHabiles++;
        }

        $totalFaltas = $diasHabiles - $totalAsistencias - $totalPermisos;
        if ($totalFaltas < 0) $totalFaltas = 0;

        die(json_encode([
            'success' => true,
            'asistencias' => $totalAsistencias,
            'faltas' => $totalFaltas,
            'permisos' => $totalPermisos,
            'dias_habiles_hasta_hoy' => $diasHabiles
        ], JSON_UNESCAPED_UNICODE));
    }

    public function view_director()
    {

        $personal = new personal;
        $turnos = new turnos;
        $turno = $turnos->where("cargo", "director")->firts();
        $data = [];
        if ($turno) {
            $id_turno = $turno["id"];
            $data = $personal->innerWhereFull("persona", "id", "id", "personal", "id_turno", $id_turno)->firts();
        }



        return $this->vista("personal/director", compact("data"));
    }
    public function update_director()
    {

        CSRF::validar();

        $model = new persona;
        $model_personal = new personal;
        $model_turno = new turnos;

        $nombre = ucfirst(strtolower($this->limpiar("nombre", "letras", "nombre", "Algunos campos no cumplen con los criterios establecidos", 2, 15)));
        $apellido = ucfirst(strtolower($this->limpiar("apellido", "letras", "apellido", "Algunos campos no cumplen con los criterios establecidos", 2, 15)));
        $cedula = $this->limpiar("cedula", "cedula", "cedula", "Algunos campos no cumplen con los criterios establecidos");


        $turno = $model_turno->where("cargo", "director")->firts();
        if (empty($turno)) {

            $cargo = "director";
            $hora_entrada = "00:00:00";
            $hora_salida = "00:00:00";
            $minutos_tolerancia = "0";
            $id_turno = $model_turno->create(compact("cargo", "hora_entrada", "hora_salida", "minutos_tolerancia"));
        } else {
            $id_turno = $turno["id"];
        }
        $result_director = $model_turno->innerWhereFull("personal", "id", "id_turno", "turnos", "cargo", "director");


        if ($result_director) {

            $id_personal = $model_personal->where("id_turno", $id_turno)->firts();
            if ($id_personal) {
                $id_personal = $id_personal["id"];

                if ($model->update($id_personal, compact("nombre", "apellido", "cedula"))) {

                    $this->registrar_bitacora("Se actualizaron los datos del director", "personal", $id_personal);
                    $this->success("Director actualizado correctamente");
                }
            }


            #NULL director 00:00:00 00:00:00 0 1

        }

        $id_persona = $model->where("cedula", $cedula)->firts();

        if (!empty($id_persona)) {
            $id_persona = $id_persona["id"];
        }

        $fecha_ingreso = "0000-00-00";
        $qr = "vacio";
        $foto = "vacio";

        if (empty($id_persona)) {

            $id = $model->create(compact("nombre", "apellido", "cedula"));
            if ($id) {

                if ($model_personal->create(compact("id", "id_turno", "fecha_ingreso", "qr", "foto"))) {

                    $this->success("Director registrado con exito");
                }
            }
        } else {
            $id = $id_persona;
            if ($model_personal->create(compact("id", "id_turno", "fecha_ingreso", "qr", "foto"))) {

                $this->success("Director registrado con exito");
            }
        }
    }
}
