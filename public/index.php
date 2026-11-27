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

use libs\Env;

session_set_cookie_params([
    'httponly' => true,   // JS no puede leerla (ya viene por defecto, pero explícito)
    'samesite' => 'Lax',  // protección contra CSRF cross-site
    'secure'   => true,   // solo por HTTPS — en XAMPP (HTTP) déjalo en false o coméntalo
    'path'     => '/'
]);
session_start();
 error_reporting(E_ALL);
ini_set('display_errors', '1'); 

require_once __DIR__ . '/../libs/Env.php';
Env::cargar();

require_once __DIR__ . "/../libs/autoload.php";
require_once __DIR__ . "/../libs/phpqrcode.php";
require_once __DIR__ . "/../libs/CSRF.php";
require_once "web.php";

