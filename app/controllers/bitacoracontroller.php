<?php

namespace app\controllers;

use app\models\bitacora;

date_default_timezone_set('America/Caracas');
class bitacoracontroller extends controller
{

   public function index()
   {

      $model = new bitacora;
      $data = $model->todas_con_usuario();
      return $this->vista("bitacora/index", compact("data"));
   }
}
