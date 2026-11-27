<?php

namespace app\controllers;

use app\libs\resend\Resend;
use app\models\bitacora;
use app\models\persona;
use app\models\personal;
use app\models\roles;
use app\models\usuario;
use libs\CSRF;
use libs\mailer;

class usuariocontroller extends controller
{

    public function index()
    {

        $model = new usuario;
        $data = $model->usuarios();

        return $this->vista("usuarios/index", compact('data'));
    }

    public function create()
    {
        $model = new roles;
        $roles = $model->different("rol", "superadmin");
        return $this->vista("usuarios/create", compact("roles"));
    }

    public function hashear_contrasena()
    {

        $model = new usuario;

        $anterior = $this->limpiar("anterior", "password", "contraseña anterior");
        $pass = $this->limpiar("pass", "password", "nueva contraseña");
        $verify = $this->limpiar("verify", "password", "nueva contraseña");
        $user = $model->find($_SESSION["user_id"] ?? $_SESSION["usuario"][0]["user_id"]);

        if (!password_verify($anterior, $user["password"])) {

            $this->error("La contraseña actual es incorrecta");
        }

        if ($pass != $verify) {

            $this->error("Las contraseñas no coinciden");
        }

        if (password_verify($pass, $user["password"])) {


            $this->error("La nueva contraseña debe ser diferente a la anterior");
        }

        $password = password_hash($pass, PASSWORD_DEFAULT);
        return $password;
    }

    public function match()
    {

        $model = new persona;

        $json = file_get_contents("php://input");
        $datos = json_decode($json, true);
        $cedula = $this->limpiar_variable($datos["cedula"], "cedula");


        $result = $model->where("cedula", $cedula)->firts();

        if ($result) {

            die(json_encode([
                "existe" => true,
                "nombre" => "{$result['nombre']}",
                "apellido" => "{$result['apellido']}"
            ], JSON_UNESCAPED_UNICODE));
        } else {
            die(json_encode([
                "existe" => false
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    public function store()
    {

        CSRF::validar();

        $persona = new persona;
        $user = new usuario;
        $mailer = new mailer;
        $config = require __DIR__ . "/../../Config/correo.php";
        $cedula = $this->limpiar("cedula", "cedula", "Cédula");
        $password = password_hash($this->limpiar("password", "password", "Contraseña", "Algunos campos no cumplen con los criterios establecidos", 16, 20), PASSWORD_DEFAULT);
        $nombre = ucfirst($this->limpiar("nombre", "letras", "Nombre"));
        $rol = $this->limpiar("rol", "numeros");
        $apellido = ucfirst($this->limpiar("apellido", "letras", "Apellido"));
        $correo = strtolower($this->limpiar("correo", "correo", "Correo",));

        $result_persona = $persona->where("cedula", $cedula)->firts();
        $result_usuario_correo = $user->where("correo", $correo)->firts();


        if ($result_usuario_correo) {
            if ($result_usuario_correo["activo"] >= 1) {
                $this->error("Este correo ya se encuentra asignado a otro usuario");
            }

            if ($result_usuario_correo["activo"] == 0) {

                $activo = 1;
                if ($user->update($result_usuario_correo["id"], compact("activo", "correo", "rol", "password"))) {
                    $mailer->enviar($correo, "registro", $nombre, $apellido);
                    $this->registrar_bitacora("Creó el usuario {$correo}", "usuarios");
                    $this->success("Usuario creado exitosamente");
                }
            }
        }



        if ($result_persona) {
            $id = $result_persona["id"];
            $result_usuario = $user->find($id);

            if ($result_usuario) {

                if ($result_usuario["activo"] >= 1) {
                    $this->error("Esta persona ya se encuentra registrada");
                } else {
                    $activo = 1;
                    if ($user->update($id, compact("activo", "correo", "rol", "password"))) {
                        $mailer->enviar($correo, "registro", $nombre, $apellido);
                        $this->registrar_bitacora("Creó el usuario {$correo}", "usuarios");
                        $this->success("Usuario creado exitosamente");
                    }
                }
            } else {
                if ($user->create(compact("id", "correo", "rol", "password"))) {
                    $mailer->enviar($correo, "registro", $nombre, $apellido);
                    $this->registrar_bitacora("Creó el usuario {$correo}", "usuarios");
                    $this->success("Usuario creado exitosamente");
                }
            }
        } else {
            $id = $persona->create(compact("cedula", "nombre", "apellido"));
            if ($id) {
                if ($user->create(compact("id",  "correo", "rol", "password"))) {
                    $mailer->enviar($correo, "registro", $nombre, $apellido);
                    $this->registrar_bitacora("Creó el usuario {$correo}", "usuarios");
                    $this->success("Usuario creado exitosamente");
                }
            }
        }
    }

    public function lock($id)
    {

        $usuario = new usuario;
        $password = $this->limpiar("password", "letras", "Contraseña");
        $user = $usuario->find($_SESSION["user_id"]);
        if ($user) {
            if (!password_verify($password, $user["password"])) {
                $this->error("Contraseña incorrecta");
            }
        }
        if ($usuario->lock_user($id, 3)) {
            $afectado = $usuario->find($id);
            $correo_afectado = $afectado["correo"] ?? "ID {$id}";
            $this->registrar_bitacora("Bloqueó al usuario {$correo_afectado}", "usuarios");
            $this->success("Usuario bloqueado correctamente");
        }
    }
    public function unlock($id)
    {

        $usuario = new usuario;

        if ($usuario->lock_user($id, 1)) {
            $afectado = $usuario->find($id);
            $correo_afectado = $afectado["correo"] ?? "ID {$id}";
            $this->registrar_bitacora("Desbloqueó al usuario {$correo_afectado}", "usuarios");
            $this->success("Usuario desbloqueado correctamente");
        }
    }

    public function destroy($id)
    {

        $usuario = new usuario;
        $password = $this->limpiar("password", "letras", "Contraseña");
        $user = $usuario->find($_SESSION["user_id"]);
        if ($user) {
            if (!password_verify($password, $user["password"])) {
                $this->error("Contraseña incorrecta");
            }
        }
        if ($usuario->delete_logic($id)) {
            $afectado = $usuario->find($id);
            $correo_afectado = $afectado["correo"] ?? "ID {$id}";
            $this->registrar_bitacora("Eliminó al usuario {$correo_afectado}", "usuarios");
            $this->success("Usuario eliminado correctamente");
        }
    }

    public function password()
    {

        return $this->vista("seguridad/contrasena");
    }

    public function change_password()
    {

        $user = new usuario;
        $password = $this->hashear_contrasena();

        if ($user->update($_SESSION["user_id"], compact("password"))) {

            $this->registrar_bitacora("Cambió su contraseña", "auth");
            $this->success("Contraseña cambiada exitosamente");
        }
    }

    public function perfil()
    {
        $model = new usuario;

        $usuario = $model->innerWhere("persona", "id", $_SESSION["user_id"])->firts();
        return $this->vista("usuarios/perfil", compact("usuario"));
    }


    public function roles()
    {

        $permisos_roles = new roles;
        $roles = $permisos_roles->different("rol", "superadmin");
        $modulos = array_keys(array_slice($roles[0], 2));

        return $this->vista("usuarios/permisos_usuario", compact("modulos", "roles"));
    }

    public function modificar_permisos($id)
    {
        $csrf = new CSRF;
        $roles = new roles;
        $csrf->validar();
        $campo  = $_POST['campo'] ?? '';
        $activo = (int)($_POST['activo'] ?? 0);

        // 2. ⛔ LISTA BLANCA — NUNCA uses el valor del POST directo en el SQL.
        //    Solo se permiten estas columnas (ajústalas a tu tabla):
        $columnasPermitidas = ['usuarios', 'director', 'escanear', 'general', 'respaldo'];

        if (!in_array($campo, $columnasPermitidas, true)) {
            echo json_encode(['success' => false, 'message' => 'Permiso no válido']);
            return;
        }

        $valor = $activo === 1 ? 1 : 0;

        $datos[$campo] = $valor;
        $ok = $roles->update($id, $datos); // tu modelo

        if ($ok) {
            $rol_afectado = $roles->find($id);
            $nombre_rol = $rol_afectado["rol"] ?? "ID {$id}";
            $estado_txt = $valor === 1 ? "Activó" : "Desactivó";
            $this->registrar_bitacora("{$estado_txt} el permiso \"{$campo}\" del rol {$nombre_rol}", "usuarios");
            $this->success("Permiso $campo actualizado");
        } else {
            $this->error("No se pudo guardar el cambio");
        }
    }


    public function cambiar_password_perfil()
    {

        $anterior = $this->limpiar("anterior", "password", "Contraseña", "La contraseña no cumple con los parametros", 5, 20);
        $pass = $this->limpiar("pass", "password", "Contraseña", "La contraseña no cumple con los parametros", 16, 20);
        $verify = $this->limpiar("verify", "password", "Contraseña", "La contraseña no cumple con los parametros", 16, 20);
        $model = new usuario;
        $usuario = $model->find($_SESSION["user_id"]);

        if ($pass != $verify) {
            $this->error("La nueva contraseña y la verificacion no coinciden");
        }
        if ($usuario) {
            if (!password_verify($anterior, $usuario["password"])) {
                $this->error("La contraseña Anterior es incorrecta");
            }

            $password = password_hash($pass, PASSWORD_DEFAULT);

            if ($model->update($_SESSION["user_id"], compact("password"))) {
                $this->registrar_bitacora("Cambió su contraseña", "auth");
                $this->success("Contraseña Actualizada");
            } else {
                $this->error("Error al actualizar la contraseña");
            }
        }
    }

    public function perfil_actualizar()
    {
        CSRF::validar();


        // ===== 1. LIMPIAR DATOS PERSONALES =====
        $nombre   = ucfirst(strtolower($this->limpiar("nombre", "letras", "Nombre", "Algunos campos no cumplen con los criterios establecidos", 2, 15)));
        $apellido = ucfirst(strtolower($this->limpiar("apellido", "letras", "Apellido", "Algunos campos no cumplen con los criterios establecidos", 2, 15)));
        $cedula   = $this->limpiar("cedula", "cedula", "Cedula");
        $correo   = strtolower($this->limpiar("correo", "correo", "Correo"));
        $password_nueva = null;

        $password_verificacion = $this->limpiar("password_verificacion", "todo", "Contraseña de verificación", "La contraseña no cumple con los parámetros", 5, 20);
        $this->validar_password_session($password_verificacion);

        $id = $_SESSION["user_id"];

        $model_usuario = new usuario;
        $model_persona = new persona;
        $model_bitacora = new bitacora;

        $existe_correo = $model_usuario->where("correo", $correo)->firts();
        if ($existe_correo && $existe_correo["id"] != $id) {
            $this->error("El correo ingresado ya está registrado por otro usuario");
        }

        $existe_cedula = $model_persona->where("cedula", $cedula)->firts();
        if ($existe_cedula && $existe_cedula["id"] != $id) {
            $this->error("La cédula ingresada ya se encuentra asignada a otra persona");
        }

        $cambiar_password = !empty($_POST["password_nueva"]);

        if ($cambiar_password) {

            // La "actual" del formulario de cambio también debe coincidir
            $password_actual = $this->limpiar("password_actual", "password", "Contraseña actual", "La contraseña no cumple con los parámetros", 16, 20);
            $this->validar_password_session($password_actual);

            $password_nueva   = $this->limpiar("password_nueva", "password", "Nueva contraseña", "La contraseña no cumple con los parámetros", 16, 20);
            $password_confirm = $this->limpiar("password_confirm", "password", "Confirmar contraseña", "La contraseña no cumple con los parámetros", 16, 20);

            if ($password_nueva !== $password_confirm) {
                $this->error("La nueva contraseña y la confirmación no coinciden");
            }

            if ($password_nueva === $password_verificacion) {
                $this->error("La nueva contraseña debe ser diferente a la actual");
            }
        }

        $model_persona->update($id, compact("cedula", "nombre", "apellido"));

        if ($cambiar_password) {
            $password = password_hash($password_nueva, PASSWORD_DEFAULT);
            $model_usuario->update($id, compact("correo", "password"));
        } else {
            $model_usuario->update($id, compact("correo"));
        }

        // ✅ Refrescar nombre/apellido en sesión si tu layout los usa
        if (isset($_SESSION["user_id"])) {
            $_SESSION["nombre"]   = $nombre . " " . $apellido;
        }
        if ($cambiar_password) {

            $model_bitacora->guardar_bitacora("Actualización de perfil y Contraseña");
            session_destroy();
            echo json_encode([
                "success"  => true,
                "icon"     => "success",
                "redirect" => "/login",
                "message"  => "Perfil y contraseña actualizados. Inicie sesión nuevamente con su nueva contraseña."
            ], JSON_UNESCAPED_UNICODE);
            die();
        }
        $model_bitacora->guardar_bitacora("Actualización de perfil");
        $this->success("Su perfil ha sido actualizado correctamente");
    }
}
