<?php

spl_autoload_register(function($clase){

    $cargar = __DIR__. "/../". str_replace("\\","/", $clase) . ".php";

    if (file_exists($cargar)) {
        require_once "$cargar";
    }
});
