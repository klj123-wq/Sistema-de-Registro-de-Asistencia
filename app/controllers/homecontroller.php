<?php

namespace app\controllers;

use app\models\asistencia;
use app\models\bitacora;
use app\models\diaslibres;
use app\models\permisos;
use app\models\persona;
use app\models\personal;
use app\models\qr;
use app\models\roles;
use app\models\turnodia;
use app\models\turnos;
use app\models\usuario;
use libs\authenticator\GoogleAuthenticator;
use libs\CSRF;
use libs\mailer;

date_default_timezone_set('America/Caracas');

class homecontroller extends controller
{
    public function index()
    {
        $asistencias = new asistencia;
        $recientes = $asistencias->asistencias_hoy();

        return $this->vista('index', compact("recientes"));
    }

    public function home()
    {
        $personal = new personal;
        $model_turnos = new turnos;
        $turno_dia = new turnodia;
        $permisos = new permisos;
        $fecha = date("Y-m-d");
        $dia_semana = (int)date('N');

        $hora = date("A", strtotime("now + 30 minutes"));
        $turno_actual = ($hora == "AM") ? 'mañana' : 'tarde';

        // Mapa completo de turnos (todos los ids, incluidos los por-cargo)
        $mapa_turno_ids = [];
        $turnos_unicos = [];
        foreach ($model_turnos->all() as $t) {
            if (!empty($t['nombre'])) {
                $mapa_turno_ids[(int)$t['id']] = $t['nombre'];
                if (!isset($turnos_unicos[$t['nombre']])) {
                    $turnos_unicos[$t['nombre']] = $t;
                }
            }
        }
        $turnos = array_values($turnos_unicos);

        // ✅ Selección del turno a mostrar:
        // - 1 solo turno  → se carga ese, aunque no coincida con la hora
        // - Varios        → el de la hora; si no existe, el primero disponible
        // - Ninguno       → null (la vista muestra "SIN TURNOS" y tarjetas en blanco)
        if (count($turnos) === 1) {
            $turno_actual = $turnos[0]['nombre'];
        } elseif (!empty($turnos) && !in_array($turno_actual, array_column($turnos, 'nombre'))) {
            $turno_actual = $turnos[0]['nombre'];
        } elseif (empty($turnos)) {
            $turno_actual = null;
        }

        $asistencias = $personal->asistencia_personal_fecha();
        $asistencias_dias = $personal->asistencia_turnodia_personal_fecha();
        $total_personas = $personal->inner_no_director("persona")->get();

        $permisos_hoy = $permisos->permisos_de_hoy();
        $fecha_hoy = $this->semana[$dia_semana] . ", " . date("j") . " de " . lcfirst($this->meses[date("n")]);

        if ($permisos_hoy) {
            $id_persona_permiso = array_column($permisos_hoy, "id_persona");
        }

        if ($asistencias) {
            $id_personas_asistentes = array_column($asistencias, "id_persona");
        }
        if ($asistencias_dias) {
            $id_personas_asistentes_dias = array_column($asistencias_dias, "id_persona");
        }

        $personal_por_turno = [];
        $detalle = [];
        $metricas = [];
        foreach ($turnos as $t) {
            $nombre = $t['nombre'];
            $personal_por_turno[$nombre] = [];
            $metricas[$nombre] = ['total' => 0, 'asistieron' => 0, 'retardos' => 0, 'permisos' => 0, 'ausentes' => 0];
            $detalle[$nombre] = ['total' => [], 'asistieron' => [], 'retardos' => [], 'permisos' => [], 'ausentes' => []];
        }

        foreach ($total_personas as $persona) {

            if ($persona['id_turno'] !== null) {

                $nombreTurno = $mapa_turno_ids[(int)$persona['id_turno']] ?? null;

                if ($nombreTurno) {

                    $metricas[$nombreTurno]["total"] += 1;
                    $detalle[$nombreTurno]["total"][] = [
                        "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                        "extra" => $persona["cedula"]
                    ];

                    if (isset($id_personas_asistentes)) {
                        if (in_array($persona["id"], $id_personas_asistentes)) {

                            $metricas[$nombreTurno]["asistieron"] += 1;
                            foreach ($asistencias as $asistencia) {
                                if ($persona["id"] == $asistencia["id_persona"]) {
                                    $detalle[$nombreTurno]["asistieron"][] = [
                                        "nombre" => $asistencia["nombre"] . " " . $asistencia["apellido"],
                                        "extra" => $asistencia["cedula"]
                                    ];
                                }
                            }
                        }
                        foreach ($asistencias as $asistencia) {
                            if ($asistencia["id_persona"] == $persona["id"] && in_array($asistencia["estado_entrada"], ["Con retardo", "Tarde"])) {
                                $metricas[$nombreTurno]["retardos"] += 1;
                                $detalle[$nombreTurno]["retardos"][] = [
                                    "nombre" => $asistencia["nombre"] . " " . $asistencia["apellido"],
                                    "extra" => $asistencia["hora_entrada"]
                                ];
                            }
                        }
                    }

                    if (isset($id_personas_asistentes) && isset($id_persona_permiso)) {
                        if (!in_array($persona["id"], $id_personas_asistentes) && !in_array($persona["id"], $id_persona_permiso)) {
                            $metricas[$nombreTurno]["ausentes"] += 1;
                            $detalle[$nombreTurno]["ausentes"][] = [
                                "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                                "extra" => $persona["cedula"]
                            ];
                        }
                    } elseif (isset($id_personas_asistentes)) {
                        if (!in_array($persona["id"], $id_personas_asistentes)) {
                            $metricas[$nombreTurno]["ausentes"] += 1;
                            $detalle[$nombreTurno]["ausentes"][] = [
                                "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                                "extra" => $persona["cedula"]
                            ];
                        }
                    } elseif (isset($id_persona_permiso)) {
                        if (!in_array($persona["id"], $id_persona_permiso)) {
                            $metricas[$nombreTurno]["ausentes"] += 1;
                            $detalle[$nombreTurno]["ausentes"][] = [
                                "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                                "extra" => $persona["cedula"]
                            ];
                        }
                    } else {
                        // Aún no hay registros hoy: todo el personal esperado es ausente
                        $metricas[$nombreTurno]["ausentes"] += 1;
                        $detalle[$nombreTurno]["ausentes"][] = [
                            "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                            "extra" => $persona["cedula"]
                        ];
                    }

                    if (isset($id_persona_permiso)) {
                        if (in_array($persona["id"], $id_persona_permiso)) {
                            $metricas[$nombreTurno]["permisos"] += 1;
                            foreach ($permisos_hoy as $permiso) {
                                if ($permiso["id_persona"] == $persona["id"]) {
                                    $detalle[$nombreTurno]["permisos"][] = [
                                        "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                                        "extra" => $permiso["descripcion"]
                                    ];
                                }
                            }
                        }
                    }
                }
            } else {

                $asignacion = $turno_dia->where_and("id_personal", $persona['id'], "dia", $dia_semana)->firts();

                if ($asignacion) {
                    $nombreTurno = $mapa_turno_ids[(int)$asignacion['id_turno']] ?? null;
                    if ($nombreTurno) {

                        $metricas[$nombreTurno]["total"] += 1;
                        $detalle[$nombreTurno]["total"][] = [
                            "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                            "extra" => $persona["cedula"]
                        ];

                        if (isset($id_personas_asistentes_dias)) {
                            if (in_array($persona["id"], $id_personas_asistentes_dias)) {
                                $metricas[$nombreTurno]["asistieron"] += 1;
                                foreach ($asistencias_dias as $asistencia) {
                                    if ($persona["id"] == $asistencia["id_persona"]) {
                                        $detalle[$nombreTurno]["asistieron"][] = [
                                            "nombre" => $asistencia["nombre"] . " " . $asistencia["apellido"],
                                            "extra" => $asistencia["cedula"]
                                        ];
                                    }
                                }
                            }
                            foreach ($asistencias_dias as $asistencia) {
                                if ($asistencia["id_persona"] == $persona["id"] && in_array($asistencia["estado_entrada"], ["Con retardo", "Tarde"])) {
                                    $metricas[$nombreTurno]["retardos"] += 1;
                                    $detalle[$nombreTurno]["retardos"][] = [
                                        "nombre" => $asistencia["nombre"] . " " . $asistencia["apellido"],
                                        "extra" => $asistencia["hora_entrada"]
                                    ];
                                }
                            }
                        }

                        if (isset($id_personas_asistentes_dias) && isset($id_persona_permiso)) {
                            if (!in_array($persona["id"], $id_personas_asistentes_dias) && !in_array($persona["id"], $id_persona_permiso)) {
                                $metricas[$nombreTurno]["ausentes"] += 1;
                                $detalle[$nombreTurno]["ausentes"][] = [
                                    "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                                    "extra" => $persona["cedula"]
                                ];
                            }
                        } elseif (isset($id_personas_asistentes_dias)) {
                            if (!in_array($persona["id"], $id_personas_asistentes_dias)) {
                                $metricas[$nombreTurno]["ausentes"] += 1;
                                $detalle[$nombreTurno]["ausentes"][] = [
                                    "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                                    "extra" => $persona["cedula"]
                                ];
                            }
                        } elseif (isset($id_persona_permiso)) {
                            if (!in_array($persona["id"], $id_persona_permiso)) {
                                $metricas[$nombreTurno]["ausentes"] += 1;
                                $detalle[$nombreTurno]["ausentes"][] = [
                                    "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                                    "extra" => $persona["cedula"]
                                ];
                            }
                        } else {
                            // Aún no hay registros hoy: todo el personal esperado es ausente
                            $metricas[$nombreTurno]["ausentes"] += 1;
                            $detalle[$nombreTurno]["ausentes"][] = [
                                "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                                "extra" => $persona["cedula"]
                            ];
                        }

                        if (isset($id_persona_permiso)) {
                            if (in_array($persona["id"], $id_persona_permiso)) {
                                $metricas[$nombreTurno]["permisos"] += 1;
                                foreach ($permisos_hoy as $permiso) {
                                    if ($permiso["id_persona"] == $persona["id"]) {
                                        $detalle[$nombreTurno]["permisos"][] = [
                                            "nombre" => $persona["nombre"] . " " . $persona["apellido"],
                                            "extra" => $permiso["descripcion"]
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->vista('inicio', compact("turnos", "turno_actual", "metricas", "detalle", "asistencias", "fecha_hoy"));
    }

    public function login()
    {
        $no_autorizado = true;
        return $this->vista('login', compact('no_autorizado'));
    }

    public function iniciar()
    {
        $model = new usuario;
        $permisos = new roles;
        $persona = new persona;
        $model_bitacora = new bitacora;



        $user = $this->sanitizar("usuario", "user");
        $pass = $this->sanitizar("password", "user");

        $result = $model->where("correo", $user)->firts();

        if (!empty($result)) {
            if ($result["activo"] == 1) {
                if (password_verify($pass, $result["password"])) {

                    $nombre = $persona->find($result["id"]);
                    $permisos_rol = $permisos->find($result["rol"]);

                    $_SESSION = array();

                    $_SESSION["usuario"] = [
                        [
                            "user_id" => $result["id"],
                            "user_name" => $result["correo"],
                            "permisos" => $permisos_rol,
                            "nombre" => ucfirst($nombre["nombre"]) . " " . ucfirst($nombre["apellido"])

                        ]
                    ];


                    $model->reset_counter($result["id"]);
                    $this->registrar_bitacora("Inicio de sesión", "auth");
                    if ($result["a2fa"] == 1) $this->success("", "/verify");
                    if ($result["a2fa"] == 0) $this->success("", "/authenticator");
                } else {
                    $model->increment_counter($result["id"]);
                    if (($result["intentos"] + 1) > 2) {
                        $model->lock_user($result["id"], 2);
                        $this->registrar_bitacora("Bloqueó al usuario {$result["correo"]} por intentos fallidos", "auth");
                        $this->error("Usuario bloqueado, por favor comuniquese un un usuario superadministrador para el desbloquo de su cuenta");
                    }
                }
            }
            if ($result["activo"] == 0 || $result["activo"] == 2) {
                $this->error("Usuario bloqueado");
            }
            $this->registrar_bitacora("Intento fallido de inicio de sesión ({$user})", "auth");
            $this->error("Usuario o contraseña incorrectas Te quedan " . 2 - $result['intentos'] . " intento(s)");
        }
        $this->registrar_bitacora("Intento fallido de inicio de sesión ({$user})", "auth");
        $this->error("Usuario o contraseña incorrectas");
    }

    public function close()
    {
        $this->registrar_bitacora("Cierre de sesión", "auth"); // antes de destruir la sesión
        session_regenerate_id(true);
        $_SESSION = array();
        session_destroy();
        $this->redirect("/");
        die;
    }

    public function authenticator()
    {
        $no_autorizado = true;
        return $this->vista("seguridad/authenticator", compact("no_autorizado"));
    }

    public function verify()
    {
        $no_autorizado = true;
        return $this->vista("/seguridad/verify", compact('no_autorizado'));
    }

    public function verificando()
    {
        $usuario = new usuario;
        $ga = new GoogleAuthenticator;
        $codigo_usuario = $this->limpiar("codigo_2fa", "numeros", 6);
        $secret = $usuario->find($_SESSION["usuario"][0]["user_id"])["codigo_2fa"];
        $result = $ga->checkCode($secret, $codigo_usuario);
        if ($result) {
            $_SESSION["user_id"] = $_SESSION["usuario"][0]["user_id"];
            $_SESSION["user_name"] = $_SESSION["usuario"][0]["user_name"];
            $_SESSION["permisos"] = $_SESSION["usuario"][0]["permisos"];
            $_SESSION["nombre"] = $_SESSION["usuario"][0]["nombre"];
            unset($_SESSION["usuario"]);
            if ($_SESSION["permisos"]["general"]) {
                $this->success("codigo correcto");
            } else {
                $this->success("codigo correcto", "/escaner");
            }
        } else {
            $this->error("Codigo Incorrecto");
        }
    }

    public function new()
    {
        $no_autorizado = true;
        return $this->vista("/seguridad/nuevousuario", compact("no_autorizado"));
    }

    public function create2fa()
    {

        $ga = new GoogleAuthenticator;
        $usuario = new usuario;
        $codigo_usuario = $this->limpiar("codigo_2fa", "numeros", "Codigo 2FA", "Algunos campos no cumplen con los criterios establecidos", 6, 6);
        $codigo_2fa = $this->limpiar("secret", "alfanumerico", "Codigo secreto", "Algunos campos no cumplen con los criterios establecidos", 26, 26);
        $es_valido = $ga->checkCode($codigo_2fa, $codigo_usuario);
        if ($es_valido) {
            $a2fa = 1;
            if ($usuario->update($_SESSION["usuario"][0]["user_id"], compact("codigo_2fa", "a2fa"))) {
                $_SESSION = array();
                session_destroy();
                $this->success("Registro de Autenticación en 2 pasos exitosa", "/");
            }
        } else {
            $this->error("error");
        }
    }

    // ==========================================
    // MÓDULO DE RECUPERACIÓN (NUEVO)
    // ==========================================
    public function recuperar_password()
    {
        $no_autorizado = true;
        if (isset($_SESSION["correo_recuperar"]) && !empty($_SESSION["correo_recuperar"]) && isset($_SESSION["token_tiempo"]) && $_SESSION["token_tiempo"] > time() + 15) {
            return $this->vista("seguridad/verificar_correo", compact("no_autorizado"));
        } else {
            return $this->vista('seguridad/recuperar_password', compact('no_autorizado'));
        }
    }


    public function verificar_correo()
    {

        $no_autorizado = true;
        return $this->vista("seguridad/verificar_correo", compact("no_autorizado"));
    }

    public function verificar_token()
    {
        ob_clean();
        header('Content-Type: application/json');

        $model = new usuario;

        $token = $this->limpiar("codigo", "alfanumerico", "Codigo de recuperacion", "Algunos campos no cumplen con los criterios establecidos", 6, 6);

        if (time() > $_SESSION["token_tiempo"]) {
            $this->error("Codigo expirado por favor intente de nuevo");
        }
        if ($_SESSION["token_recuperar"] != $token) {
            $this->error("Codigo Incorrecto por favor intente nuevamente");
        }

        $this->success("Verificación exitosa", "/");
    }

    public function cambiar_password_recuperacion()
    {

        $no_autorizado = true;
        return $this->vista("seguridad/cambiar_password", compact("no_autorizado"));
    }

    public function password_update()
    {

        $model = new usuario;

        $pass = $this->limpiar("pass", "password");
        $verify = $this->limpiar("verify", "password");
        $password = password_hash($pass, PASSWORD_DEFAULT);
        $user_id = $model->where("correo", $_SESSION["correo_recuperar"])->firts();

        if ($pass != $verify) {
            $this->error("Las contraseñas no coinciden");
        }

        if ($model->update($user_id["id"], compact("password"))) {
            $_SESSION = array();
            session_destroy();
            $this->success("Contraseña actualizada correctamente", "/", false);
        }
    }

    public function verificar_recuperacion()
    {
        $usuario = new usuario;
        $correo = $this->limpiar("correo", "correo", "correo");

        $result = $usuario->where("correo", $correo)->firts();

        if (!$result || $result["activo"] == 0) {
            $this->error("Este correo no se encuentra asignado a ningún usuario, Por favor verifique en intente nuevamente");
        } elseif ($result["activo"] == 3) {
            $this->error("Usuario bloqueado por administrador, por favor comuniquese con un administrador para desbloquear");
        } else {
            $mail = new mailer;
            $persona = new persona;
            $nombres = $persona->find($result["id"]);

            if ($mail->enviar($correo, "recuperar", $nombres["nombre"], $nombres["apellido"], "Recuperación de contraseña")) {
                $this->success("correo enviado satisfactoriamente");
            } else {
                $this->error("Error al enviar el correo: " . $mail->ultimoError);
            }
        }
    }

    public function reenviar_correo()
    {

        $reenviar = $_SESSION["correo_recuperar"];
        $nombre = $_SESSION["nombre"];
        $apellido = $_SESSION["apellido"];

        if (isset($_SESSION["token_tiempo"]) && $_SESSION["token_tiempo"] > time()) {
            $this->error("Para enviar un nuevo codigo el contador debe llegar primero a cero");
        }

        $mail = new mailer;
        if ($mail->enviar($reenviar, "recuperar", $nombre, $apellido, "Recuperación de contraseña")) {

            $this->success("correo enviado satisfactoriamente");
        } else {
            $this->error("Error al enviar el correo");
        }
    }

    public function consulta()
    {
        $no_autorizado = true;
        return $this->vista("consulta", compact("no_autorizado"));
    }
    public function consultar_asistencia()
    {

        header('Content-Type: application/json; charset=utf-8');

        $json   = file_get_contents("php://input");
        $datos  = json_decode($json, true);
        $cedula = $this->limpiar_variable($datos["cedula"] ?? '', "cedula");


        $model_persona = new persona;
        $model_personal = new personal;
        $model_asistencia = new asistencia;

        $persona = $model_persona->where("cedula", $cedula)->firts();

        if (!$persona) {
            $this->error("Persona no registrada");
        }

        $personal = $model_personal->where_and("id", $persona["id"], "activo", true)->firts();

        if (!$personal) {
            $this->error("Persona no registrada");
        }

        $asistencias = $model_asistencia->periodos_con_asistencia($personal["id"]);

        if (!$asistencias) {
            $this->error("Esta persona no tiene asistencias registradas");
        }


        $periodosAgrupados = [];
        foreach ($asistencias as $fila) {
            $anio = (int)$fila["anio"];
            if (!isset($periodosAgrupados[$anio])) {
                $periodosAgrupados[$anio] = ["anio" => $anio, "meses" => []];
            }
            $periodosAgrupados[$anio]["meses"][] = (int)$fila["mes"];
        }
        $periodos = array_values($periodosAgrupados);


        die(json_encode([
            "success"  => true,
            "nombre"   => trim($persona["nombre"] . " " . $persona["apellido"]),
            "cedula"   => $persona["cedula"],
            "periodos" => $periodos
        ], JSON_UNESCAPED_UNICODE));
    }

    // ===== Reporte imprimible del periodo (vista reutilizada de reportes/especifico) =====
    public function reporte()
    {
        $personal     = new personal;
        $asistencia   = new asistencia;
        $dias_libre   = new diaslibres;
        $permiso      = new permisos;
        $turnos       = new turnos;
        $model_persona  = new persona;
        $model_personal = new personal;

        // ===== Entrada por POST (formulario dinámico de la vista pública) =====
        $cedula = $this->limpiar("cedula", "cedula", "Cédula");
        $ano    = (int)$this->limpiar("anio", "numeros", "Año");
        $mes    = (int)$this->limpiar("mes", "numeros", "Mes");

        if ($mes < 1 || $mes > 12 || $ano < 2020 || $ano > (int)date('Y')) {
            die("Periodo solicitado inválido.");
        }

        // ===== Localizar trabajador y verificar que esté activo =====
        $persona = $model_persona->where("cedula", $cedula)->firts();
        if (!$persona) {
            die("Trabajador no encontrado.");
        }
        $registro = $model_personal->find($persona["id"]);
        if (!$registro || (int)$registro["activo"] !== 1) {
            die("Este trabajador no se encuentra activo.");
        }
        $id = $persona["id"];

        // ===== Cálculo de fechas (idéntico a reporte_especifico) =====
        $fecha  = date("Y-m-d", strtotime($ano . "-" . $mes));
        $fecha2 = date("Y-m-d", strtotime($ano . "-" . $mes . " + 1 month"));

        if (date("m-d", strtotime($fecha)) < "08-15") {
            $ano_escolar = date("Y", strtotime($fecha . " - 1 years")) . "-" . $ano;
        } else {
            $ano_escolar = $ano . "-" . date("Y", strtotime($fecha . " + 1 years"));
        }

        // ===== Datos del trabajador y su turno =====
        $result = $personal->find($id);
        if ($result) {
            if ($result["id_turno"] == NULL) {
                $data = $personal->turno_dia_personal_id($id);
            } else {
                $data = $personal->turno_personal_id($id);
            }
        } else {
            die("No se pudo conseguir al trabajador");
        }

        // ===== Datos del periodo =====
        $asistencias  = $asistencia->encontrar_asistencia($fecha, $fecha2, $id);
        $dias_libres  = $dias_libre->encontrar_fechas($fecha, $fecha2);
        $permisos     = $permiso->encontrar_permisos($fecha, $fecha2, $id);

        // ===== Acumulado del año escolar =====
        $inicio_ano_escolar = explode("-", $ano_escolar)[0] . "-09-01";
        $hoy = date("Y-m-d");
        if ($hoy > $fecha2) {
            $hoy = $fecha2;
        }

        $ano_escolar_asistencia = $asistencia->encontrar_asistencia($inicio_ano_escolar, $hoy, $id);
        $ano_escolar_dias_libre = $dias_libre->encontrar_fechas($inicio_ano_escolar, $hoy);
        $ano_escolar_permiso    = $permiso->encontrar_permisos($inicio_ano_escolar, $hoy, $id);

        // ===== Director para la firma =====
        $turno_director = $turnos->where("cargo", "director")->firts();
        if ($turno_director) {
            $id_turno_director = $turno_director["id"];
            $director = $personal->innerWhereFull("persona", "id", "id", "personal", "id_turno", $id_turno_director)->firts();
        } else {
            $director = ["cedula" => 12345678];
        }

        // ✅ no_autorizado = true → tu vista() incluye el reporte SIN layout
        $no_autorizado = true;

        return $this->vista('/reportes/especifico', compact(
            "director",
            "ano_escolar",
            "ano_escolar_asistencia",
            "ano_escolar_dias_libre",
            "ano_escolar_permiso",
            'fecha',
            'data',
            "asistencias",
            "dias_libres",
            "permisos",
            'no_autorizado'
        ));
    }
}
