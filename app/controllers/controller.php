<?php

namespace app\controllers;

use app\models\bitacora;
use app\models\turnos;
use app\models\usuario;

use libs\CSRF;



date_default_timezone_set('America/Caracas');

class controller
{


    private $tipo = [
        'letras'        =>  '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/',
        'texto'         =>  '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
        'cedula'        =>  '/^(V-|E-)?[0-9]{7,8}$/i',
        'alfanumerico'  =>  '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ]+$/',
        'fecha'         =>  '/^\d{4}-\d{2}-\d{2}$/',
        'numeros'       =>  '/^[0-9]+$/',
        'mes'           =>  '/^(0?[1-9]|1[0-2])$/',
        'cod_cargo'     =>  '/^[0-9]{4}[a-zA-Z]{2}$/',
        'hora'          =>  '/^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
        'password'      =>  '/^(?=.*[A-ZÁÉÍÓÚÑ])(?=.*[a-záéíóúñ])(?=.*[0-9])(?=.*[#$\-_*])[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ#$\-_*]+$/',
        'correo'        =>  '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        'preguntas'     =>  '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\?¿]+$/',
        'base64'        =>  '/^(data:[a-zA-Z0-9\/.+-]+;base64,)?[a-zA-Z0-9+\/]+={0,2}$/',
        'todo'          =>  '/^[a-zA-ZñÑ0-9!@#$*_\-]+$/'
    ];

    private $sanitizar = [
        'user' => '/[^\p{L}\p{N}#$\-_*@\.]/u'
    ];

    public  $turnos = [
        "mañana",
        "tarde",
        "todos"
    ];
    public  $cargos = [
        "docente",
        "obrero",
        "administrativo",
        "cocinero",
        "todos"
    ];

    public $meses = [
        1   =>  "Enero",
        2   =>  "Febrero",
        3   =>  "Marzo",
        4   =>  "Abril",
        5   =>  "Mayo",
        6   =>  "Junio",
        7   =>  "Julio",
        8   =>  "Agosto",
        9   =>  "Septiembre",
        10  =>  "Octubre",
        11  =>  "Noviembre",
        12  =>  "Diciembre"
    ];

    public $semana = [
        1   =>  "Lunes",
        2   =>  "Martes",
        3   =>  "Miércoles",
        4   =>  "Jueves",
        5   =>  "Viernes",
        6   =>  "Sábado",
        7   =>  "Domingo"
    ];

    public function vista($ruta, $datos = [])
    {
        // 1. Extrae las variables de la base de datos para la vista
        if (!empty($datos)) {
            extract($datos);
        }

        // 2. Guardamos la subvista (ej: 'inicio' o 'personal') en una variable
        $vista_interna = $ruta;

        // 3. Buscamos el layout usando la ruta absoluta del servidor
        $basePath = 'vistas/';

        ob_start();
        if (!isset($no_autorizado)) {


            if (file_exists($basePath . 'inc/layout.php')) {

                include $basePath . 'inc/layout.php';
            }
        } else {
            // Si no encuentra el layout, por seguridad carga la vista directa
            if (file_exists($basePath . $ruta . '.php')) {

                include $basePath . $ruta . '.php';
            }
        }

        $value = ob_get_clean();
        return $value;
    }

    public function redirect($location)
    {

        header("Location: {$location}");
        exit;
    }

    public function limpiar_array($clave, $caracteres, $mensaje_error = "Error")
    {

        if (empty($_POST[$clave])) {
            $this->error("Error: no se pueden enviar campos vacios");
        }

        $data = [];

        foreach ($_POST[$clave] as $dia => $turno) {

            if (empty($turno)) {
                $this->error("Error: no se pueden enviar campos vacios");
            }


            if (!preg_match($this->tipo[$caracteres], $turno)) {
                $this->error($mensaje_error);
            }


            $data[$dia] = $turno;
        }


        return $data;
    }

    /**  @param string $caracteres
     *
     * 
     * Valores de la variable Caracteres
     * _______________________________________
     * "letras"    =>      A-Z sin espacios            
     * "texto"     =>      A-Z con espacios            
     * "cedula"    =>      Numeros 7-8 dígitos         
     * "alfanumerico" =>   A-Z 0-9 sin espacios        
     * "fecha"     =>      formato YYYY-MM-DD          
     * "numeros"   =>      numero del 0 al-9                        
     * "mes"       =>      numero de mes del 1 al 12                         
     * "cod_cargo" =>      0-4  4 digitos y 2 letras   
     * "hora"      =>      Formato HH:MM               
     * "password"  =>      A-Z 0-9 #$-_                
     * "correo"    =>      Formato de correo
     * "preguntas" =>      A-Z con espacios y ¿?  
     * "base64"    =>      Permite todos lo de base64    
     * _________________________________________
     */
    public function limpiar($clave, $caracteres, $nombre_campo = "", $mensaje_error = "Algunos campos no cumplen con los criterios establecidos",  $minimo_caracteres = 0, $maximo_caracteres = 0)
    {

        if (empty($_POST[$clave])) {
            (empty($nombre_campo)) ? $this->error("Se han detectado campos vacios") : $this->error("El campo {$nombre_campo} no puede estar vacio");
        }

        $valor = trim($_POST[$clave]);

        if (empty($valor)) {
            (empty($nombre_campo)) ? $this->error("Se han detectado campos vacios") : $this->error("El campo {$nombre_campo} no puede estar vacio");
        }

        if (!preg_match($this->tipo[$caracteres], $valor)) {
            (empty($nombre_campo)) ? $this->error($mensaje_error) : $this->error("El campo {$nombre_campo} no cumple con los criterios establecidos");
        }

        if (!empty($minimo_caracteres)) {
            if (strlen($valor) < $minimo_caracteres) {
                (empty($nombre_campo)) ? $this->error("Algunos campos no cumplen el mínimo de caracteres") : $this->error("El campo {$nombre_campo} no cumple con el mínimo de {$minimo_caracteres} caracteres");
            }
        }
        if (!empty($maximo_caracteres)) {
            if (strlen($valor) > $maximo_caracteres) {
                (empty($nombre_campo)) ? $this->error("Algunos campos exceden el máximo de caracteres permitidos") : $this->error("El campo {$nombre_campo} excede el máximo de {$minimo_caracteres} caracteres");
            }
        }

        return $valor;
    }
    /**
     * 
     * @param string $cedula 
     * || Nombre del archivo
     */
    public function validar_foto($cedula)
    {

        if (empty($_FILES['foto']['name']) || $_FILES['foto']['error'] !== 0) {
            $this->error("Error: La foto del personal es obligatoria o está corrupta.");
        }

        $tamano_maximo = 5 * 1024 * 1024; // 5 MB en bytes
        if ($_FILES['foto']['size'] > $tamano_maximo) {
            $mb_actual = round($_FILES['foto']['size'] / 1048576, 1);
            $this->error("Error: La foto pesa {$mb_actual} MB y supera el máximo permitido de 5 MB.");
        }

        $tipo_archivo = $_FILES['foto']['type'];
        if (!in_array($tipo_archivo, ['image/jpeg', 'image/png', 'image/jpg'])) {
            $this->error("error: Tipo de archivo no permitido. Suba una foto JPG o PNG.");
        }

        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $carpeta_destino = "../files/perfil/";

        if (!is_dir($carpeta_destino)) {

            mkdir($carpeta_destino, 0777, true);
        }

        $nombre_foto_final = $carpeta_destino . $cedula . "." . $extension;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $nombre_foto_final)) {
            $this->error("Error: No se pudo guardar la foto en el servidor.");
        }
        return $cedula . "." . $extension;
    }

    public function validar_base($campo1, $valor1, $campo2, $valor2)
    {

        $model = new turnos;
        return $model->where_and($campo1, $valor1, $campo2, $valor2);
    }

    public function limpiar_variable($valor, $caracteres, $mensaje_error = "Error")
    {

        if (empty($valor)) {
            $this->error("Error: no se pueden enviar campos vacios");
        }

        $valor = trim($valor);

        if (empty($valor)) {
            $this->error("Error: no se pueden enviar campos vacios");
        }


        if (!preg_match($this->tipo[$caracteres], $valor)) {
            $this->error($mensaje_error);
        }

        return $valor;
    }

    public function sanitizar($clave, $caracteres,)
    {

        $valor = trim($_POST[$clave]);
        $resultado = preg_replace($this->sanitizar[$caracteres], '', $valor);

        return $resultado;
    }

    public function success($mensaje = "exito", $redirect = "")
    {

        die(json_encode([
            "success" => true,
            "icon" => "success",
            "redirect" => "{$redirect}",
            "message" => "{$mensaje}"
        ], JSON_UNESCAPED_UNICODE));
    }

    public function error($mensaje = "error")
    {

        die(json_encode([
            "success" => false,
            "icon" => "error",
            "message" => "{$mensaje}"
        ], JSON_UNESCAPED_UNICODE));
    }

    public function validar_password_session($password)
    {

        $model = new usuario;
        $password_hash = $model->find($_SESSION["user_id"])["password"];

        if (password_verify($password, $password_hash)) {

            return true;
        } else {
            $this->error("Contraseña Incorrecta");
        }
    }

    public function guardar_base64($clave, $nombre)
    {
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $_POST[$clave]);

        $datosBinarios = base64_decode($base64, true);
        if ($datosBinarios === false || @getimagesizefromstring($datosBinarios) === false) {
            $this->error("Error: El contenido enviado no es una imagen válida.");
        }

        // 3. ✅ Límite de tamaño (5 MB) — base64 infla ~33%, peso real ≈ 75% del string
        $tamano_maximo = 5 * 1024 * 1024;
        if (strlen($datosBinarios) > $tamano_maximo) {
            $mb_actual = round(strlen($datosBinarios) / 1048576, 1);
            $this->error("Error: La foto pesa {$mb_actual} MB y supera el máximo permitido de 5 MB.");
        }

        $nombreArchivo = $nombre . '.jpg';
        $rutaGuardado = '../files/perfil/';

        if (!is_dir($rutaGuardado)) {
            mkdir($rutaGuardado, 0777, true);
        }

        $rutaCompleta = $rutaGuardado . $nombreArchivo;

        if (file_put_contents($rutaCompleta, $datosBinarios) === false) {
            $this->error("Error: No se pudo guardar la foto en el servidor.");
        }

        return $nombreArchivo;
    }

    /**
     * Registra un evento en la bitácora del sistema.
     *
     * @param string $accion     Texto descriptivo del evento (ver inventario aprobado)
     * @param string $categoria  auth | personal | config | usuarios
     * @param int|null $id_persona  Trabajador afectado (NULL si no aplica)
     */
    protected function registrar_bitacora($accion, $categoria, $id_persona = null)
    {
        $model = new bitacora;

        $fecha     = date("Y-m-d");
        $hora      = date("H:i:s");
        $id_user   = $_SESSION["user_id"] ?? $_SESSION["usuario"][0]["user_id"] ?? null; // NULL en fallos de login

        $model->create(compact("fecha", "hora", "accion", "categoria", "id_user", "id_persona"));
    }
}
