<?php

namespace libs;

class route
{
    public static $routes = [];

    public static $rutas_publicas = [
        '',
        'login',
        'verify',
        'new',
        'authenticator',
        'recuperar_password',
        'recuperar_password/verificar',
        'consulta',
        'asistencia/procesar/qr',
        'consulta/buscar',
        'consulta/pdf'
    ];

    public static $rutas_recuperar = [
        'recuperar_password/cambiar',
        'recuperar_password/verificar_correo',
        'recuperar_password/verificar_codigo',
        'reenviar_correo'
    ];

    public static $users = [
        "configuraciones",
        "usuario/registrar"
    ];

    public static $director = [
        "personal/director"
    ];

    public static $escanear = [
        "escaner"
    ];

    public static $general = [
        "home",
        "personal",
        "personal/registrar",
        "reportes/todos/qr",
        "turnos",
        "turnos/registrar",
        "diaslibres",
        "bitacora",
        "reportes",
        "diaslibres/#/edit",
        "turnos/#/edit",
        "personal/#/edit",
    ];

    public static function get($uri, $callback)
    {
        $uri = trim($uri, "/");
        self::$routes['GET'][$uri] = $callback;
    }

    public static function post($uri, $callback)
    {
        $uri = trim($uri, "/");
        self::$routes['POST'][$uri] = $callback;
    }

    public static function dispatch()
    {
        // Quita el query string (?i=1, etc.) antes de normalizar la ruta
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) ?? "";
        $uri = trim($uri, "/");

        // Si el path empieza con "public", lo quita para que coincida con tus rutas
        if ($uri === "public") {
            $uri = "";
        } elseif (strpos($uri, "public/") === 0) {
            $uri = substr($uri, strlen("public/"));
            $uri = trim($uri, "/");
        }

        $metodo = $_SERVER["REQUEST_METHOD"];
        $logueado = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
        $logueo_recuperar = isset($_SESSION['correo_recuperar']) && !empty($_SESSION['correo_recuperar']);

        if (!isset(self::$routes[$metodo])) {
            self::enviarError404();
            return;
        }

        foreach (self::$routes[$metodo] as $ruta => $callback) {
            if (strpos($ruta, ":") !== false) {
                $ruta = preg_replace("#:[a-zA-Z]+#", "([a-zA-Z0-9]+)", $ruta);
            }

            if (preg_match("#^$ruta$#", $uri, $matches)) {
                $params = array_slice($matches, 1);

                if (!empty($ruta) && !$logueado && !in_array($uri, self::$rutas_publicas)) {
                    if (!in_array($uri, self::$rutas_recuperar)) {
                        self::enviarError401();
                        return;
                    }
                }
                if (!empty($ruta) && !$logueo_recuperar && in_array($uri, self::$rutas_recuperar)) {
                    self::enviarError401();
                    return;
                }

                if (in_array($uri, self::$users)) {
                    if (!empty($_SESSION["permisos"])) {
                        if ($_SESSION["permisos"]["usuarios"] != 1) {
                            self::enviarError403();
                            return;
                        }
                    } else {
                        self::enviarError403();
                        return;
                    }
                }
                if (in_array($uri, self::$director)) {
                    if (!empty($_SESSION["permisos"])) {
                        if ($_SESSION["permisos"]["director"] != 1) {
                            self::enviarError403();
                            return;
                        }
                    } else {
                        self::enviarError403();
                        return;
                    }
                }
                if (in_array($uri, self::$escanear)) {
                    if (!empty($_SESSION["permisos"])) {
                        if ($_SESSION["permisos"]["escanear"] != 1) {
                            self::enviarError403();
                            return;
                        }
                    } else {
                        self::enviarError403();
                        return;
                    }
                }
                if (in_array($uri, self::$general)) {
                    if (!empty($_SESSION["permisos"])) {
                        if ($_SESSION["permisos"]["general"] != 1) {
                            self::enviarError403();
                            return;
                        }
                    } else {
                        self::enviarError403();
                        return;
                    }
                }

                if (is_array($callback)) {
                    $object = new $callback[0];
                    $response = $object->{$callback[1]}(...$params);
                }

                if (is_callable($callback)) {
                    $response = $callback(...$params);
                }

                if (is_array($response) || is_object($response)) {
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    return;
                } elseif (!empty($response)) {
                    echo $response;
                    return;
                }

                return;
            }
        }

        self::enviarError404();
    }

    private static function resolverVistaError(string $archivo): string
    {
        // Si este archivo está en /htdocs/libs/route.php, sube a /htdocs/public/vistas/error/...
        $desdePublic = dirname(__DIR__) . '/public/vistas/error/' . $archivo;
        if (file_exists($desdePublic)) {
            return $desdePublic;
        }

        // Respaldo por si las vistas están en /htdocs/vistas/error/...
        return dirname(__DIR__) . '/vistas/error/' . $archivo;
    }

    private static function enviarError404()
    {
        http_response_code(404);
        $ruta_vista = self::resolverVistaError('404.php');

        if (file_exists($ruta_vista)) {
            require_once $ruta_vista;
        } else {
            echo "<h1 style='text-align:center; margin-top:50px; font-family:sans-serif;'>404 - Página no encontrada</h1>";
            echo "<p style='text-align:center; color: red;'>PHP no encuentra el archivo en: <br><b>" . $ruta_vista . "</b></p>";
        }
    }

    private static function enviarError401()
    {
        http_response_code(401);
        $ruta_vista = self::resolverVistaError('401.php');

        if (file_exists($ruta_vista)) {
            require_once $ruta_vista;
        } else {
            echo "<h1 style='text-align:center; margin-top:50px; font-family:sans-serif;'>401 - Acceso Denegado</h1>";
            echo "<p style='text-align:center; color: red;'>No tienes permiso para acceder a este apartado</p>";
        }
    }

    private static function enviarError403()
    {
        http_response_code(403);
        $ruta_vista = self::resolverVistaError('403.php');

        if (file_exists($ruta_vista)) {
            require_once $ruta_vista;
        } else {
            echo "<h1 style='text-align:center; margin-top:50px; font-family:sans-serif;'>403 - Prohibido</h1>";
            echo "<p style='text-align:center; color: red;'>No tienes permiso para acceder a este apartado</p>";
        }
    }
}