<?php

namespace libs\authenticator;



use Exception;
use libs\authenticator\base32;

class GoogleAuthenticator {

    /**

     * Genera una clave TOTP

     *
     * @param int $length Longitud de la clave (por defecto 16)

     *
     * @return string La clave TOTP del usuario

     */
    public static function generateSecret(int $length = 16): string

    {
        return base32::base32_encode(random_bytes($length));
    }

    /**

     * Genera un código QR basado en la clave

     *
     * @param string $issuer Nombre de la aplicación, que se muestra como "issuer: label" en Google Authenticator

     * @param string $label Nombre de usuario, que se muestra como "issuer: label" en Google Authenticator

     * @param string $secret Clave TOTP del usuario

     * @param string $path Ruta de almacenamiento del archivo del código QR

     * @param int $width Ancho del código QR

     * @param string $logo Ruta del archivo del logotipo (use una cadena vacía si no se necesita un logotipo)

     * @param int $logo_width Ancho del logotipo (no válido si el logotipo está vacío)

     *
     * @return string|false

     */










    /* public static function getQRCodeFile(string $issuer, string $label, string $secret, string $path, int $width = 300, string $logo = '', int $logo_width = 50): cadena|falso

    {
        $url = 'otpauth://totp/' . urlencode($issuer . ':' . $label) .

            '?secret=' . $secret .

            '&issuer=' . urlencode($issuer);

        // URL para generar el código QR

        $resultado = Builder::create()

            ->writer(new PngWriter())

            ->writerOptions([])

            ->data($url)

            ->encoding(new Encoding('UTF-8'))

            ->errorCorrectionLevel(ErrorCorrectionLevel::High)

            ->size($width)

            ->margin(10)

            ->roundBlockSizeMode(RoundBlockSizeMode::Margin);

        // Si hay un logotipo

        if (!empty($logo)) {

            $result = $result->logoPath($logo)

                ->logoResizeToWidth($logo_width)

                ->logoPunchoutBackground(true);
        }

        $result = $result->validateResult(false)

            ->build();

        $result->saveToFile($path);

        return realpath($path);
    } */






    /**

     * Valida la contraseña de un solo uso (OTP) del usuario

     *
     * @param string $secret Clave TOTP del usuario

     * @param string $code OTP ingresada por el usuario

     *
     * @return bool Estado de validación

     */
    public static function checkCode(string $secret, string $code, int $desface = 0): bool

    { // Establece el intervalo de tiempo (normalmente 30 segundos)
        
        $diferencia = $desface*30;

        $timeWindow = 30;

        // Obtiene la marca de tiempo Unix actual, usando la zona horaria UTC

        $time = floor((time() + $diferencia) / $timeWindow);

        // Genera el HMAC-SHA1 asociado a la marca de tiempo

        $hash = hash_hmac('sha1', pack('N*', 0) . pack('N*', $time), base32::base32_decode($secret), true);

        // Extrae el último byte del HMAC-SHA1

        $offset = ord(substr($hash, -1)) & 0x0F;

        // Extrae el número generado dinámicamente del valor hash

        $codeGenerated = unpack('N', substr($hash, $offset, 4))[1] & 0x7FFFFFFF;

        // Reduce el número generado a 6 dígitos

        $codeGenerated %= 1000000;

        // Compara el código de verificación ingresado por el usuario con el código de verificación generado

        return str_pad($codeGenerated, 6, '0', STR_PAD_LEFT) === $code;
    }
}
