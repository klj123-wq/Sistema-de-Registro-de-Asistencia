<?php

namespace app\controllers;

use app\models\model;


class sistemacontroller extends controller
{


    public function index()
    {

        return $this->vista("sistema/index");
    }

    public function respaldo()
    {

        $model = new model;
        $nombre_archivo = "/respaldo" . date("Y-m-d") . ".sql";
        $directorio = "files/respaldo";
        $ruta = $directorio . $nombre_archivo;


        if (!is_dir($directorio)) {

            mkdir($directorio, 0777, true);
        }
        $output = $model->respaldar_bd($ruta);

        if (file_exists($ruta) && filesize($ruta) > 0) {
            $this->success("Respaldo creado exitosamente");
        }
    }

    public function correo()
    {

        return $this->vista("correos");
    }

    
}
