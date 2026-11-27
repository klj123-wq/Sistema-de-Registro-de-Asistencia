<?php

namespace libs;

class mailer
{
    public string $ultimoError = '';

    public function enviar($destinatario, $plantilla, $nombre, $apellido, $asunto = "Notificacion de registro")
    {
        $this->ultimoError = '';

        if ($plantilla != "recuperar") {
            $datos = [
                '{{nombre}}'    => $nombre . " " . $apellido,
                '{{correo}}'    => $destinatario,
                '{{escuela}}'   => Env::get("from_name"),
                '{{direccion}}' => Env::get("direccion"),
                '{{telefono}}'  => Env::get("telefono"),
            ];
        } else {
            $codigo = random_int(100000, 999999);
            $_SESSION["token_recuperar"] = $codigo;
            $_SESSION["token_tiempo"] = time() + 183;

            if (!isset($_SESSION["correo_recuperar"])) {
                $_SESSION["correo_recuperar"] = $destinatario;
                $_SESSION["nombre"] = $nombre;
                $_SESSION["apellido"] = $apellido;
            }

            $datos = [
                '{{nombre}}'    => $nombre . " " . $apellido,
                '{{correo}}'    => $destinatario,
                '{{escuela}}'   => Env::get("from_name"),
                '{{direccion}}' => Env::get("direccion"),
                '{{telefono}}'  => Env::get("telefono"),
                '{{codigo}}'    => $codigo,
            ];
        }

        $html = $this->cargarPlantilla($plantilla . '.html', $datos);
        if ($this->ultimoError !== '') {
            return false;
        }

        $text = $this->cargarPlantilla($plantilla . '.txt', $datos);
        if ($this->ultimoError !== '') {
            return false;
        }

        $fromName  = trim(str_replace('"', '', (string) Env::get("from_name")));
        $fromEmail = trim((string) Env::get('from_email'));

        $payload = [
            'from'    => $fromName . ' <' . $fromEmail . '>',
            'to'      => [trim($destinatario)],
            'subject' => $asunto,
            'html'    => $html,
            'text'    => $text,
        ];

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . trim((string) Env::get('api_key')),
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);

        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        $status    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $status < 200 || $status >= 300) {
            $this->ultimoError = "HTTP $status | cURL: $curlError | Resend: $response";
            error_log("Resend error: " . $this->ultimoError);
            return false;
        }

        return true;
    }

    public function cargarPlantilla(string $archivo, array $datos): string
    {
        $ruta = __DIR__ . '/../public/vistas/emails/' . $archivo;
        if (!file_exists($ruta)) {
            $this->ultimoError = "No existe plantilla en: $ruta";
            return '';
        }
        $contenido = file_get_contents($ruta);
        return str_replace(array_keys($datos), array_values($datos), $contenido);
    }
}