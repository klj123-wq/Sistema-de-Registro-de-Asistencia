<?php

/**
 * @var array $todos_qr
 * @var array $turnos
 */
while (ob_get_level() > 0) { ob_end_clean(); }
ini_set('display_errors', 0);

require "../libs/fpdf/fpdf.php";

class CarnetMasivo extends FPDF {
    function __construct() {
        parent::__construct('P', 'mm', array(54, 85.6));
        $this->SetAutoPageBreak(false);
    }
    
    function crearCarnet($nombre_apellido, $cedula, $cargo, $ruta_qr, $ruta_fondo = null, $ruta_logo = null) {
        $this->AddPage();
        
        if ($ruta_fondo != null) {
            $this->Image($ruta_fondo, 0, 0, 54, 85.6);
        }
        
        $qr_size = 32;
        $x_qr = ((54 - $qr_size) / 2);
        $y_qr = 20;

        ob_start();
        QRcode::png($ruta_qr, false, QR_ECLEVEL_L, 10, 2); 
        $captura_qr = ob_get_clean();
        $url = "data://image/png;base64," . base64_encode($captura_qr);

        $this->Image($url, $x_qr, $y_qr, $qr_size, $qr_size, "png");
        
        if ($ruta_logo != null) {
            $logo_size = 35;
            $x_logo = ($x_qr + ($qr_size - $logo_size) / 2);
            $y_logo = ($y_qr + ($qr_size - $logo_size) / 2);
            $this->Image($ruta_logo, $x_logo, $y_logo, $logo_size, $logo_size);
        }
        
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY(0, $y_qr + $qr_size + 5);
        $this->Cell(54, 5, mb_convert_encoding($nombre_apellido, "ISO-8859-1", "UTF-8"), 0, 0, 'C');
        
        $this->SetFont('Arial', 'B', 7);
        $this->SetTextColor(50, 50, 50);
        $this->SetXY(0, $y_qr + $qr_size + 11);
        $this->Cell(54, 5, "C.I: V-" . $cedula, 0, 0, 'C');

        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(4, 146, 70);
        $this->SetXY(0, $y_qr + $qr_size + 18);
        $this->Cell(54, 5, $cargo, 0, 0, 'C');
    }
}

$carnet = new CarnetMasivo();

$ruta_fondo_imagen = "assets/carnet.png";
$ruta_logo_imagen  = "assets/bordes.png";



foreach ($todos_qr as $qr) {

    $nombre        = ucfirst($qr["nombre"]) . " " . ucfirst($qr["apellido"]);
    $cargo_trabajo = ucfirst($qr["cargo"] ?? '');
    $cedula        = number_format($qr["cedula"], 0, ",", ".");

    $carnet->crearCarnet(
        $nombre,
        $cedula,
        $cargo_trabajo,
        $qr["qr"],
        $ruta_fondo_imagen,
        $ruta_logo_imagen
    );
}

while (ob_get_level() > 0) { ob_end_clean(); }
$carnet->Output('I', 'carnets_todos.pdf');
exit;