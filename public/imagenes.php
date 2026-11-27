<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Acceso Denegado');
}

// 3. Validar y limpiar el nombre del archivo recibido por la URL
if (!isset($_GET['imagen']) || empty($_GET['imagen'])) {
    http_response_code(404);
    
    exit('Archivo no encontrado');
}

$archivo_solicitado = basename($_GET['imagen']); 
$ruta_completa = __DIR__ . '/../files/perfil/' . $archivo_solicitado;

// 4. Verificar si el archivo realmente existe en el disco
if (!file_exists($ruta_completa)) {
    var_dump($ruta_completa);
    http_response_code(404);
    exit('Archivo no encontrado');
}

// 5. Obtener el tipo de contenido (MIME type) para que el navegador sepa si es JPG, PNG, PDF, etc.
$tipo_mimetype = mime_content_type($ruta_completa);

// 6. Configurar las cabeceras HTTP y enviar el archivo al navegador
header("Content-Type: " . $tipo_mimetype);
header("Content-Length: " . filesize($ruta_completa));

// Lee el archivo del disco bloqueado y lo transmite directamente al usuario legítimo
readfile($ruta_completa);
exit;