<?php

/**
 * @var array $dias_laborales        
 * @var array $trabajador_asistencia 
 * @var array $cargoTexto;
 * @var array $turnoTexto;
 * @var array $reportes; 
 * @var string $ano_escolar          
 * @var string $ano        
 * @var string $cargo        
 * @var string $turno     
 
 */

require "../libs/fpdf/fpdf.php";

class PDF_RegistroMes extends FPDF
{
    public $mesTexto;
    public $ano;
    public $ano_escolar;
    public $diasHabiles;
    public $arregloDias;
    public $margen_tabla_x;
    public $cargoTexto;
    public $turnoTexto;
    public $reportes;

    // Anchos de las columnas (idénticos a tu formato original)
    public $w_n = 7;
    public $w_ced = 20;
    public $w_nom = 40;
    public $w_dias_total = 120;
    public $w_resumen = 6;
    public $w_firma = 20;

    function Header()
    {
        // --- CÁLCULO PREVIO PARA CENTRAR LA TABLA ---
        $ancho_total_tabla = $this->w_n + $this->w_ced + $this->w_nom + $this->w_dias_total + ($this->w_resumen * 6) + $this->w_firma;
        $this->margen_tabla_x = ($this->w - $ancho_total_tabla) / 2;

        // El texto informativo comenzará justo donde empieza la columna Cédula (Margen + Ancho N°)
        $x_inicio_texto = $this->margen_tabla_x + $this->w_n;

        // --- 0. LOGO DE LA INSTITUCIÓN ---
        $ruta_logo = 'assets/ministerio.png';
        if (file_exists($ruta_logo)) {
            $this->Image($ruta_logo, $x_inicio_texto, $this->GetY(), 60, 15);
            $this->Ln(18); // Espacio de separación para que el texto salga abajo del logo
        }

        // --- 1. DATOS DE LA INSTITUCIÓN ---
        $this->SetFont('Arial', '', 8);

        // Línea 1: Institución
        $this->SetX($x_inicio_texto);
        $this->Cell(0, 4.4, utf8_decode('Institución Educativa: E.T.C.N. PEDRO CURIEL RAMIREZ'), 0, 1, 'L');

        // Línea 2: Nivel de Educación + Año Escolar 
        $y_linea2 = $this->GetY();
        $this->SetX($x_inicio_texto);
        $this->Cell(0, 4.3, utf8_decode('Nivel de Educación: MEDIA TÉCNICA GENERAL'), 0, 0, 'L');

        // Posicionamos Año Escolar centrado en el documento manteniendo la misma altura Y
        $this->SetXY(10, $y_linea2);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 4.3, utf8_decode('Año Escolar ' . $this->ano_escolar), 0, 1, 'C');

        // Línea 3: Registro y control
        $this->SetFont('Arial', '', 8);
        $this->SetX($x_inicio_texto);
        $this->Cell(0, 4.3, utf8_decode('Registro y control de asistencia'), 0, 1, 'L');

        // Línea 4: Título principal del reporte
        $this->SetFont('Arial', 'B', 8);
        $this->SetX($x_inicio_texto);
        $this->Cell(0, 4.3, utf8_decode('REGISTRO DE ASISTENCIA: PERSONAL ' . mb_strtoupper($this->cargoTexto) . '        TURNO ' . mb_strtoupper($this->turnoTexto)), 0, 1, 'L');        $this->Ln(1);

        // --- 2. ENCABEZADO DE LA TABLA ---
        $this->SetX($this->margen_tabla_x);
        $y_inicial = $this->GetY();

        $this->SetFont('Arial', 'B', 7);

        $this->Cell($this->w_n, 10, utf8_decode('N°'), 1, 0, 'C');
        $this->Cell($this->w_ced, 10, utf8_decode('Cédula'), 1, 0, 'C');
        $this->Cell($this->w_nom, 10, utf8_decode('Nombres y apellidos'), 1, 0, 'C');

        $x_dias = $this->GetX();
        $textoMes = utf8_decode('Mes: ' . $this->mesTexto . '  año: ' . $this->ano . '                      Días hábiles: ' . $this->diasHabiles);
        $this->Cell($this->w_dias_total, 5, $textoMes, 1, 0, 'L');

        $this->Cell($this->w_resumen * 3, 5, 'ASISTENC.', 1, 0, 'C');
        $this->Cell($this->w_resumen * 3, 5, 'INAS.', 1, 0, 'C');
        $this->Cell($this->w_firma, 10, 'FIRMA', 1, 0, 'C');

        // --- 3. SUB-ENCABEZADOS (Días y Letras) ---
        $this->SetXY($x_dias, $y_inicial + 5);

        $num_dias = count($this->arregloDias);
        $w_dia = $num_dias > 0 ? ($this->w_dias_total / $num_dias) : 0;

        foreach ($this->arregloDias as $dia) {
            $this->Cell($w_dia, 5, $dia, 1, 0, 'C');
        }

        $this->Cell($this->w_resumen, 5, 'A', 1, 0, 'C');
        $this->Cell($this->w_resumen, 5, 'R', 1, 0, 'C');
        $this->Cell($this->w_resumen, 5, 'P', 1, 0, 'C');

        $this->Cell($this->w_resumen, 5, 'I', 1, 0, 'C');
        $this->Cell($this->w_resumen, 5, 'J', 1, 0, 'C');
        $this->Cell($this->w_resumen, 5, 'S', 1, 0, 'C');

        $this->Ln();
    }

    function Footer()
    {
        $this->SetY(-29);
        $this->SetFont('Arial', '', 9);

        // Margen dinámico aplicado al Footer
        $this->SetX($this->margen_tabla_x);
        $this->Cell(0, 9, 'Director: ______________________________         C.I _____________________         V- Firma: ____________________', 0, 1, 'L');

        $this->SetX($this->margen_tabla_x);
        $this->Cell(0, 6, utf8_decode('Institución Educativa: ______________________________________________________________'), 0, 1, 'L');
    }
}

// =====================================================================
// CONFIGURACIÓN E INICIALIZACIÓN
// =====================================================================

$pdf = new PDF_RegistroMes('L', 'mm', 'Letter');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 30);

// Asignación de variables dinámicas iniciales
$pdf->ano_escolar = $ano_escolar ?? '20XX-20XX';
$pdf->mesTexto    = $mesTexto ?? 'Febrero';
$pdf->ano        = $ano ?? '2025';
$pdf->arregloDias = $dias_laborales;
$pdf->diasHabiles = count($dias_laborales);

$pdf->SetFont('Arial', '', 7);
$h_fila = 5;

// ===== UNA PÁGINA POR GRUPO (cargo-turno) =====
foreach ($reportes as $grupo) {

    // Configurar el encabezado ANTES de AddPage (Header lo lee al crearse)
    $pdf->cargoTexto  = $grupo['cargo'];
    $pdf->turnoTexto  = $grupo['turno'];
    $pdf->arregloDias = $dias_laborales;
    $pdf->diasHabiles = count($dias_laborales);

    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 7);

    $w_dia = $pdf->diasHabiles > 0 ? ($pdf->w_dias_total / $pdf->diasHabiles) : 0;
    $contador = 1;

    foreach ($grupo['trabajador_asistencia'] as $emp) {
        $pdf->SetX($pdf->margen_tabla_x);

        $pdf->Cell($pdf->w_n, $h_fila, $contador, 1, 0, 'C');
        $pdf->Cell($pdf->w_ced, $h_fila, "V-" . number_format($emp['cedula'], 0, ",", "."), 1, 0, 'C');

        $nombre_mayuscula = utf8_decode(mb_strtoupper($emp['nombre_completo'], 'UTF-8'));
        $pdf->Cell($pdf->w_nom, $h_fila, $nombre_mayuscula, 1, 0, 'L');

        $conteo_letras = ['A' => 0, 'R' => 0, 'P' => 0, 'I' => 0, 'J' => 0, 'S' => 0];

        foreach ($dias_laborales as $dia) {
            $letra_dia = '';
            if (isset($emp['dias'][$dia])) {
                $letra_dia = strtoupper(trim($emp['dias'][$dia]));
                if (array_key_exists($letra_dia, $conteo_letras)) {
                    $conteo_letras[$letra_dia]++;
                }
            }
            $pdf->Cell($w_dia, $h_fila, $letra_dia, 1, 0, 'C');
        }

        if ($conteo_letras['I'] > 0) $conteo_letras['S'] = $conteo_letras['I'];
        if ($conteo_letras['R'] > 0) $conteo_letras['J'] = $conteo_letras['R'];
        if ($conteo_letras['P'] > 0) $conteo_letras['J'] += $conteo_letras['P'];
        if ($conteo_letras['J'] > 0) $conteo_letras['I'] += $conteo_letras['J'];

        $pdf->Cell($pdf->w_resumen, $h_fila, $conteo_letras['A'] > 0 ? $conteo_letras['A'] : '', 1, 0, 'C');
        $pdf->Cell($pdf->w_resumen, $h_fila, $conteo_letras['R'] > 0 ? $conteo_letras['R'] : '', 1, 0, 'C');
        $pdf->Cell($pdf->w_resumen, $h_fila, $conteo_letras['P'] > 0 ? $conteo_letras['P'] : '', 1, 0, 'C');
        $pdf->Cell($pdf->w_resumen, $h_fila, $conteo_letras['I'] > 0 ? $conteo_letras['I'] : '', 1, 0, 'C');
        $pdf->Cell($pdf->w_resumen, $h_fila, $conteo_letras['J'] > 0 ? $conteo_letras['J'] : '', 1, 0, 'C');
        $pdf->Cell($pdf->w_resumen, $h_fila, $conteo_letras['S'] > 0 ? $conteo_letras['S'] : '', 1, 0, 'C');
        $pdf->Cell($pdf->w_firma, $h_fila, '', 1, 0, 'C');

        $pdf->Ln();
        $contador++;
    }
}
$pdf->Output('I', 'Reporte_Asistencia_General.pdf');
