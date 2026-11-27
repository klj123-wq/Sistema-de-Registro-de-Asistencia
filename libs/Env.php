<?php

namespace libs;

class Env
{
    /**
     * Carga las variables del archivo .env a $_ENV y getenv().
     * Llamar UNA vez al arrancar la app (public/index.php).
     */
    public static function cargar($ruta = null)
    {
        $ruta = $ruta ?? dirname(__DIR__) . '/.env';

        if (!file_exists($ruta)) {
            die("Error: no se encontró el archivo .env");
        }

        $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lineas as $linea) {
            $linea = trim($linea);

            // Ignorar comentarios
            if (strpos($linea, '#') === 0) continue;

            // Separar CLAVE=VALOR
            $partes = explode('=', $linea, 2);
            if (count($partes) !== 2) continue;

            $clave = trim($partes[0]);
            $valor = trim($partes[1]);

            // Limpiar comillas si las tiene
            $valor = trim($valor, "\"'");

            // Disponible en $_ENV, getenv() y $_SERVER
            $_ENV[$clave]    = $valor;
            $_SERVER[$clave] = $valor;
            putenv("{$clave}={$valor}");
        }
    }

    /**
     * Obtener una variable con valor por defecto opcional.
     * Uso: Env::get('DB_NAME')
     */
    public static function get($clave, $default = null)
    {
        return $_ENV[$clave] ?? getenv($clave) ?: $default;
    }
}