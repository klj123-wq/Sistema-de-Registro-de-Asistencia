<?php

namespace libs;

use app\controllers\controller;

class CSRF
{
    public static function generar()
    {

        if (!empty($_SESSION["csrf_token"])) {
            unset($_SESSION['csrf_token']);
        }
        if (!empty($_SESSION["csrf_token_time"])) {
            if (time() - $_SESSION['csrf_token_time'] > 1800) {

                unset($_SESSION['csrf_token_time']);
                $_SESSION['csrf_token_time'] = time();
            }
        } else {
            $_SESSION['csrf_token_time'] = time();
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        

        return $_SESSION['csrf_token'];
    }

    public static function validar()
    {
        $controller = new controller;

        if (!isset($_POST["CSRF"])) {
            $controller->error("Token CSRF inválido");
        }

        $token = $_POST["CSRF"];

        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            $controller->error("Token CSRF inválido o expirado, Por favor recargue el formulario e intente de nuevo");
        }

        $tiempo_expiracion = 1800;

        
        if (time() - $_SESSION['csrf_token_time'] > $tiempo_expiracion) {
            
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);

            $controller->error("El token CSRF ha expirado, Por favor recargue el formulario e intente de nuevo");
        }

        // Validamos el token de forma segura
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            $controller->error("Token CSRF inválido, Por favor recargue el formulario e intente de nuevo");
        }
    }
}
