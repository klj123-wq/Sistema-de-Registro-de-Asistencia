<?php
require "../libs/fpdf/fpdf.php";

/** @var array $qr 
 *  @var array $turno
 * */;

class Carnet extends FPDF {
    function __construct() {
        parent::__construct('P', 'mm', array(54, 85.6)); // Vertical: ancho=54mm, alto=85.6mm
        $this->SetAutoPageBreak(false);
    }
    
    function crearCarnet($nombre_apellido, $cedula, $cargo, $ruta_qr, $ruta_fondo = null, $ruta_logo = null, $cuadro = null) {
        $this->AddPage();
        
        // Cargar imagen de fondo si se proporciona
        if ($ruta_fondo != null) {
            $this->Image($ruta_fondo, 0, 0, 54, 85.6);
        }
        
        // Cargar QR en el centro (posición central)
        $qr_size = 32; // Tamaño del QR en mm
        $x_qr = ((54 - $qr_size) / 2); // Centrar horizontalmente
        $y_qr = 20; // Posición vertical del QR

        ob_start();
        QRcode::png($ruta_qr, false, QR_ECLEVEL_L, 10, 2); 
        
        $captura_qr = ob_get_clean();
        $url = "data://image/png;base64,". base64_encode($captura_qr);

        $this->Image($url, $x_qr, $y_qr, $qr_size, $qr_size, "png");
        
        // Cargar imagen adicional encima del QR (logo sin fondo)
        if ($ruta_logo != null) {
            $logo_size = 35; // Tamaño del logo en mm (ajústalo según necesites)
            $x_logo = ($x_qr + ($qr_size - $logo_size) / 2); // Centrar en el QR
            $y_logo = ($y_qr + ($qr_size - $logo_size) / 2); // Centrar en el QR
            
            $this->Image($ruta_logo, $x_logo, $y_logo, $logo_size, $logo_size);
        }
        /* if ($cuadro != null) {
            $logo_size = 4.3; // Tamaño del logo en mm (ajústalo según necesites)
            $x_logo = ($x_qr + ($qr_size - $logo_size) / 2) -9; // Centrar en el QR
            $y_logo = ($y_qr + ($qr_size - $logo_size) / 2) -9; // Centrar en el QR
            $this->Image($cuadro, $x_logo, $y_logo, $logo_size, $logo_size);

            $logo_size = 4.3; // Tamaño del logo en mm (ajústalo según necesites)
            $x_logo = ($x_qr + ($qr_size - $logo_size) / 2) +9; // Centrar en el QR
            $y_logo = ($y_qr + ($qr_size - $logo_size) / 2) -9; // Centrar en el QR
            $this->Image($cuadro, $x_logo, $y_logo, $logo_size, $logo_size);

            $logo_size = 4.3; // Tamaño del logo en mm (ajústalo según necesites)
            $x_logo = ($x_qr + ($qr_size - $logo_size) / 2) -9; // Centrar en el QR
            $y_logo = ($y_qr + ($qr_size - $logo_size) / 2) +9; // Centrar en el QR
            $this->Image($cuadro, $x_logo, $y_logo, $logo_size, $logo_size);
        } */
        
        
        // Agregar nombre y apellido debajo del QR
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(4, 146, 70);
        $this->SetTextColor(0, 0, 0); // Color negro
        
        // Centrar texto de nombre
        $this->SetXY(0, $y_qr + $qr_size + 5);
        $this->Cell(54, 5, mb_convert_encoding($nombre_apellido, "ISO-8859-1", "UTF-8"), 0, 0, 'C');
        
        // Agregar cargo debajo del nombre
        $this->SetFont('Arial', 'B', 7);
        $this->SetTextColor(50, 50, 50);
        $this->SetXY(0, $y_qr + $qr_size + 11);
        $this->Cell(54, 5, "C.I: V-". $cedula, 0, 0, 'C');


        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(4, 146, 70);
        $this->SetXY(0, $y_qr + $qr_size + 18);
        $this->Cell(54, 5, $cargo, 0, 0, 'C');
    }
}



// Ejemplo de uso
$carnet = new Carnet();

// Datos del carnet
$nombre = ucfirst($qr["nombre"]). " ". ucfirst($qr["apellido"]);
$cargo_trabajo = ucfirst($turno["cargo"]);
$cedula = number_format($qr["cedula"], 0, ",", ".");

// Rutas de las imágenes
$ruta_qr_imagen = $qr["qr"];
$ruta_fondo_imagen = "assets/carnet.png";
$ruta_logo_imagen = "assets/bordes.png"; // Ruta de tu imagen PNG sin fondo
$box = "assets/cuadro.png";


// Crear el carnet con la imagen adicional
$carnet->crearCarnet($nombre, $cedula, $cargo_trabajo, $ruta_qr_imagen, $ruta_fondo_imagen, $ruta_logo_imagen, $box);

// Guardar el PDF
$carnet->Output('I', 'carnet.pdf');

echo "✅ Carnet creado exitosamente: carnet.pdf";
?>