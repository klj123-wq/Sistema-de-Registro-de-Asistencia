<?php
    
    /*
Sistema de Registro de Asistencia - SRA
Copyright (C) 2026 Hidalgo, Medina, Nelo, Montilva

Este programa es software libre: usted puede redistribuirlo y/o modificarlo
bajo los términos de la Licencia Pública General Affero de GNU tal como se
publica por la Free Software Foundation.

Este programa se distribuye con la esperanza de que sea útil, pero SIN
NINGUNA GARANTÍA; sin siquiera la garantía implícita de COMERCIABILIDAD
o IDONEIDAD PARA UN PROPÓSITO PARTICULAR. Consulte la Licencia Pública
General Affero de GNU para obtener más detalles.

Debería haber recibido una copia de la Licencia Pública General Affero de GNU
junto con este programa. Si no, consulte <https://www.gnu.org/licenses/>.
*/

use libs\route; 
use app\controllers\homecontroller;
use app\controllers\personalcontroller;
use app\controllers\asistenciacontroller;
use app\controllers\bitacoracontroller;
use app\controllers\diaslibrescontroller;
use app\controllers\permisoscontroller;
use app\controllers\reportescontroller;
use app\controllers\turnoscontroller;
use app\controllers\usuariocontroller;
use app\controllers\sistemacontroller;

route::get("/escaner", [homecontroller::class, 'index']);
route::get("/", [homecontroller::class, 'login']);
route::post("/login", [homecontroller::class, 'iniciar']);
route::get("/home", [homecontroller::class, 'home']);
route::get("/close", [homecontroller::class, 'close']);

route::get("/recuperar_password", [homecontroller::class, 'recuperar_password']);
route::post("/recuperar_password/verificar", [homecontroller::class, 'verificar_recuperacion']);
route::get("/recuperar_password/verificar_correo", [homecontroller::class, 'verificar_correo']);
route::post("/reenviar_correo", [homecontroller::class, 'reenviar_correo']);
route::post("/recuperar_password/verificar_codigo", [homecontroller::class, 'verificar_token']);
route::get("/recuperar_password/cambiar", [homecontroller::class, 'cambiar_password_recuperacion']);
route::post("/recuperar_password/cambiar", [homecontroller::class, 'password_update']);

route::get("/authenticator", [homecontroller::class, 'authenticator']);
route::get("/verify", [homecontroller::class, 'verify']);
route::get("/new", [homecontroller::class, 'new']);
route::post("/verify", [homecontroller::class, 'verificando']);
route::post("/authenticator", [homecontroller::class, 'create2fa']);

route::get ("/personal", [personalcontroller::class, 'index']);
route::get ("/personal/registrar", [personalcontroller::class, 'create']);
route::post("/personal", [personalcontroller::class, 'store']);
route::get("/personal/director", [personalcontroller::class, 'view_director']);
route::post("/personal/director", [personalcontroller::class, 'update_director']);
route::get ("/personal/:id", [personalcontroller::class, 'show']);
route::get("/personal/:id/stats", [personalcontroller::class, 'stats']);
route::get ("/personal/:id/edit", [personalcontroller::class, 'edit']);
route::post("/personal/:id", [personalcontroller::class, 'update']);
route::post("/personal/:id/delete", [personalcontroller::class, 'destroy']);
route::post("/personal/get_turnos", [personalcontroller::class, 'get_turnos']);

route::get("/reportes", [reportescontroller::class, 'index']);
route::post("/reportes/general", [reportescontroller::class, 'general']);
route::post("/reportes/general-datos", [reportescontroller::class, 'datos_general_json']);
route::get("/reportes/todos/qr", [reportescontroller::class, 'todosqr']);
route::get("/reportes/:id/qr", [reportescontroller::class, 'qr']);
route::post("/reportes/:id/especifico", [reportescontroller::class, 'reporte_especifico']);

route::get("/asistencia", [asistenciacontroller::class, 'index']);
route::get("/asistencia/:id/registrar", [asistenciacontroller::class, 'create']);
route::get("/asistencia/prueba", [asistenciacontroller::class, 'prueba']);
route::get("/asistencia/:id/edit", [asistenciacontroller::class, 'edit']);
route::post("/asistencia/:id/update", [asistenciacontroller::class, 'update']);
route::post("/asistencia/:id/delete", [asistenciacontroller::class, 'destroy']);
route::post("/asistencia/procesar/qr", [asistenciacontroller::class, 'procesar']);
route::get("/asistencia/recientes-hoy", [asistenciacontroller::class, 'asistencias_recientes']);
route::post("/asistencia/:id", [asistenciacontroller::class, 'store']);
route::get("/asistencia/:id", [asistenciacontroller::class, 'show']);

route::get("/diaslibres", [diaslibrescontroller::class, 'index']);
route::get("/diaslibres/registrar", [diaslibrescontroller::class, 'create']);
route::post("/diaslibres", [diaslibrescontroller::class, 'store']);
route::get("/diaslibres/:id/edit", [diaslibrescontroller::class, 'edit']);
route::post("/diaslibres/:id", [diaslibrescontroller::class, 'update']);
route::post("/diaslibres/:id/delete", [diaslibrescontroller::class, 'destroy']);

route::get("/bitacora", [bitacoracontroller::class, 'index']);

route::get("/turnos", [turnoscontroller::class, 'index']);
route::get("/turnos/registrar", [turnoscontroller::class, 'create']);
route::post("/turnos", [turnoscontroller::class, 'store']);
route::get("/turnos/:id/edit", [turnoscontroller::class, 'edit']);
route::post("/turnos/:id", [turnoscontroller::class, 'update']);
route::post("/turnos/:id/delete", [turnoscontroller::class, 'destroy']);

route::get("/configuraciones", [usuariocontroller::class, 'index']);
route::get("/usuario/registrar", [usuariocontroller::class, 'create']);
route::get("/usuarios/password", [usuariocontroller::class, 'password']);
route::get("/usuario/roles", [usuariocontroller::class, 'roles']);
route::get("/usuarios/perfil", [usuariocontroller::class, 'perfil']);
route::post("/usuarios/perfil_actualizar", [usuariocontroller::class, 'perfil_actualizar']);
route::post("/usuarios/change_password", [usuariocontroller::class, 'change_password']);
route::post("/usuario", [usuariocontroller::class, 'store']);
route::post("/usuario/buscar-trabajador", [usuariocontroller::class, 'match']);
route::post("/new", [usuariocontroller::class, 'nuevo_usuario']);
route::post("/roles/:id/permisos/modificar", [usuariocontroller::class, 'modificar_permisos']);
route::post("/usuario/:id/delete", [usuariocontroller::class, 'destroy']);
route::post("/usuario/:id/lock", [usuariocontroller::class, 'lock']);
route::post("/usuario/:id/unlock", [usuariocontroller::class, 'unlock']);
route::post("/usuario/cambiar_password_perfil", [usuariocontroller::class, 'cambiar_password_perfil']);

route::get("/permisos", [permisoscontroller::class, 'index']);
route::get("/permisos/:id", [permisoscontroller::class, 'show']);
route::post("/permisos/:id/create", [permisoscontroller::class, 'store']);
route::get("/permisos/:id/edit", [permisoscontroller::class, 'edit']);
route::post("/permisos/:id", [permisoscontroller::class, 'update']);
route::get("/permisos/:id/registrar",[permisoscontroller::class, 'create']);
route::post("/permisos/:id/delete", [permisoscontroller::class, 'destroy']);

route::get("sistema/respaldo", [sistemacontroller::class, "index"]);
route::get("sistema/crear_respaldo", [sistemacontroller::class, "respaldo"]);

route::get("/consulta", [homecontroller::class, "consulta"]);
route::post("/consulta/buscar", [homecontroller::class, "consultar_asistencia"]);
route::post("/consulta/pdf", [homecontroller::class, "reporte"]);

route::dispatch();
