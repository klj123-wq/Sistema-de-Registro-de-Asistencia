<?php

/**
 * @var array $data
 * @var array $permisos
 * @var array $dias_libres 
 * @var array $asistencias
 * @var string $fecha
 * @var string $ano_escolar
 * @var array $ano_escolar_asistencia
 * @var array $ano_escolar_dias_libre
 * @var array $ano_escolar_permiso
 */

require "libs/fpdf/fpdf.php";

class PDF extends FPDF
{
    function Header() {}
    function Footer() {}
}

// Es vital habilitar la zona horaria para la evaluación de inasistencias y días futuros
date_default_timezone_set('America/Caracas');

// Crear instancia
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 10);

// ============================================
// MARGEN COMÚN PARA TODO EL CONTENIDO
// ============================================
$ancho_tabla = 178;
$leftMargin = (215.9 - $ancho_tabla) / 2;

// ============================================
// LOGO (esquina superior izquierda)
// ============================================
$logoFile = 'assets/ministerio.png';
if (file_exists($logoFile)) {
    $pdf->Image($logoFile, 13, 10, 45);
}

// ============================================
// CONFIGURACIÓN DINÁMICA DE FECHAS (MES ACTUAL)
// ============================================
$mes = date("m", strtotime($fecha));
$anio = date("Y", strtotime($fecha));
$hoy = date("Y-m-d");
$total_dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);

// Diccionario de meses en español para los títulos dinámicos
$meses_es = [
    '01' => 'ENERO',
    '02' => 'FEBRERO',
    '03' => 'MARZO',
    '04' => 'ABRIL',
    '05' => 'MAYO',
    '06' => 'JUNIO',
    '07' => 'JULIO',
    '08' => 'AGOSTO',
    '09' => 'SEPTIEMBRE',
    '10' => 'OCTUBRE',
    '11' => 'NOVIEMBRE',
    '12' => 'DICIEMBRE'
];
$nombre_mes_actual = $meses_es[$mes] ?? 'MAYO';

// ============================================
// LÓGICA DE PROCESAMIENTO ACUMULADO DEL AÑO ESCOLAR
// ============================================

// Inicialización de contadores ANUALES
$anual_habiles = 0;
$anual_laborados = 0;
$anual_justificados = 0;
$anual_sin_justificar = 0;
$anual_retardos = 0;
$anual_permisos = 0;

// Extraemos los años a partir del string del año escolar (Ej: "2025-2026")
$partes_ano_escolar = explode("-", $ano_escolar);
$ano_inicio_escolar = !empty($partes_ano_escolar[0]) ? trim($partes_ano_escolar[0]) : ($anio - 1);

// Definimos el rango cronológico del cálculo anual
$fecha_inicio_ciclo = $ano_inicio_escolar . "-09-01"; // El año escolar inicia el 1 de Septiembre

// Calculamos el último día del mes que se está reportando (Ej: 2025-09-30)
$ultimo_dia_mes = date("Y-m-t", strtotime($fecha));

// Si el último día del mes del reporte es mayor a hoy, frenamos en "hoy" para no contar el futuro.
// De lo contrario, calculamos hasta el último día del mes solicitado.
$fecha_fin_ciclo = (strtotime($ultimo_dia_mes) > strtotime($hoy)) ? $hoy : $ultimo_dia_mes;

// Arrays de indexación para el año escolar
$anual_fechas_asistencias = array_column($ano_escolar_asistencia, 'fecha');

// Función auxiliar para verificar rangos (reutilizable)
$verificarRango = function ($fecha_evaluar, $arreglo_datos) {
    foreach ($arreglo_datos as $fila) {
        if ($fecha_evaluar >= $fila['fecha_inicio'] && $fecha_evaluar <= $fila['fecha_fin']) {
            return $fila;
        }
    }
    return false;
};

// Bucle cronológico del Año Escolar completo
$fecha_actual_proceso = $fecha_inicio_ciclo;
while (strtotime($fecha_actual_proceso) <= strtotime($fecha_fin_ciclo)) {
    $dia_semana_anual = date('N', strtotime($fecha_actual_proceso));

    // Ignorar sábados y domingos del conteo institucional
    if ($dia_semana_anual != 6 && $dia_semana_anual != 7) {
        $anual_habiles++; // Suma base como día hábil predeterminado

        $idx_asistencia_anual = array_search($fecha_actual_proceso, $anual_fechas_asistencias);
        $rango_libre_anual = $verificarRango($fecha_actual_proceso, $ano_escolar_dias_libre);
        $rango_permiso_anual = $verificarRango($fecha_actual_proceso, $ano_escolar_permiso);

        if ($idx_asistencia_anual !== false) {
            $fila_anual = $ano_escolar_asistencia[$idx_asistencia_anual];
            $anual_laborados++;

            if (strtoupper(trim($fila_anual["estado_entrada"])) === 'CON RETARDO' || strtoupper(trim($fila_anual["estado_entrada"])) === 'RETARDO') {
                $anual_retardos++;
            }
        } elseif ($rango_libre_anual !== false) {
            $anual_habiles--; // Se resta de los exigidos obligatorios
        } elseif ($rango_permiso_anual !== false) {
            $anual_permisos++;
            $anual_justificados++;
        } else {
            // Evaluamos si el día histórico procesado está en el pasado real
            if ($fecha_actual_proceso <= $hoy) {
                $anual_sin_justificar++;
            }
        }
    }
    // Avanzar exactamente 1 día en el calendario
    $fecha_actual_proceso = date("Y-m-d", strtotime($fecha_actual_proceso . " +1 day"));
}

$anual_no_laborados = $anual_justificados + $anual_sin_justificar;

// ============================================
// TÍTULO PRINCIPAL DINÁMICO
// ============================================
$pdf->Ln(15);
$pdf->SetX($leftMargin);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, utf8_decode("REGISTRO DE ASISTENCIA MES: $nombre_mes_actual $anio"), 0, 1, 'C');
$pdf->Ln(8);

// ============================================
// DATOS DEL FUNCIONARIO
// ============================================
$ingreso = new DateTime($data["fecha_ingreso"]);

// --- CONDICIÓN MEJORADA: EVALUACIÓN DE FECHA ACTUAL VS MES REPORTADO ---
$mes_actual_real = date("m");
$anio_actual_real = date("Y");

if ($mes == $mes_actual_real && $anio == $anio_actual_real) {
    $actual = new DateTime(); // Si es el mes en curso, evalúa contra la fecha y hora de hoy de forma exacta
} else {
    $actual = new DateTime(date("Y-m-t", strtotime($fecha))); // Si es un mes pasado, evalúa contra el cierre de ese mes
}

$pdf->SetX($leftMargin);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 6, utf8_decode('FUNCIONARIO:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(50, 6, utf8_decode(mb_strtoupper($data["nombre"], 'UTF-8') . " " . mb_strtoupper($data["apellido"], 'UTF-8')), 0, 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 6, 'C.I:', 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(50, 6, utf8_decode("V-" . number_format($data["cedula"], 0, ",", ".")), 0, 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(15, 6, utf8_decode('CARGO:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(43, 6, utf8_decode(strtoupper($data["cargo"])), 0, 1);

$pdf->SetX($leftMargin);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(28, 6, utf8_decode('CÓDIGO CARGO:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(47, 6, utf8_decode(strtoupper($data["cod_cargo"])), 0, 0);

// --- CÁLCULO DEL TIEMPO DE SERVICIO (T.S) ---
if ($ingreso > $actual) {
    $texto_ts = "0 DIAS";
} else {
    $ts = $ingreso->diff($actual);
    if ($ts->y == 0 && $ts->m == 0) {
        $texto_ts = $ts->d . " DIAS";
    } else {
        $texto_ts = $ts->y . " AÑOS";
        if ($ts->m > 0) {
            $texto_ts .= " " . $ts->m . " MESES";
        }
    }
}

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 6, utf8_decode('T.S:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(50, 6, utf8_decode($texto_ts), 0, 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(16, 6, utf8_decode('INGRESO:'), 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(43, 6, utf8_decode(date("Y/m/d", strtotime($data["fecha_ingreso"]))), 0, 1);

$pdf->Ln(2);

// ============================================
// TABLA DE ASISTENCIA (Cabecera)
// ============================================
$w_fecha = 22;
$w_entrada = 27;
$w_salida = 27;
$w_obs = 75;
$w_estatus = 27;
$alto_fila = 5.8;

$pdf->SetX($leftMargin);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell($w_fecha, $alto_fila, 'Fecha', 1, 0, 'C', true);
$pdf->Cell($w_entrada, $alto_fila, 'Hora Entrada', 1, 0, 'C', true);
$pdf->Cell($w_salida, $alto_fila, 'Hora Salida', 1, 0, 'C', true);
$pdf->Cell($w_obs, $alto_fila, 'Observaciones', 1, 0, 'C', true);
$pdf->Cell($w_estatus, $alto_fila, 'Firma', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 7.5);

// ============================================
// LÓGICA DE ASISTENCIA CONDICIONAL DEL MES ACTUAL
// ============================================
$fechas_asistencias = array_column($asistencias, 'fecha');
$dias_esp = [1 => 'LU', 2 => 'MA', 3 => 'MI', 4 => 'JU', 5 => 'VI'];

$dias_habiles_mes = 0;
$dias_laborados_mes = 0;
$justificados_mes = 0;
$sin_justificar_mes = 0;
$retardos_mes = 0;
$permisos_mes = 0;

for ($dia = 1; $dia <= $total_dias_mes; $dia++) {

    $fecha_bucle = $anio . '-' . $mes . '-' . str_pad($dia, 2, '0', STR_PAD_LEFT);
    $dia_semana = date('N', strtotime($fecha_bucle));

    if ($dia_semana == 6 || $dia_semana == 7) {
        continue;
    }

    $v_entrada = '------';
    $v_salida = '------';
    $v_obs = '';
    $v_estatus = '';

    $dias_habiles_mes++;

    $index_asistencia = array_search($fecha_bucle, $fechas_asistencias);
    $rango_libre = $verificarRango($fecha_bucle, $dias_libres);
    $rango_permiso = $verificarRango($fecha_bucle, $permisos);

    if ($index_asistencia !== false) {
        $fila = $asistencias[$index_asistencia];
        $v_entrada = date("h:i A", strtotime($fila["hora_entrada"]));
        $v_salida  = date("h:i A", strtotime($fila["hora_salida"]));
        $v_obs     = "";
        $v_estatus = "";

        $dias_laborados_mes++;

        if (strtoupper(trim($fila["estado_entrada"])) === 'CON RETARDO' || strtoupper(trim($fila["estado_entrada"])) === 'RETARDO') {
            $retardos_mes++;
        }
    } elseif ($rango_libre !== false) {
        foreach ($dias_libres as $valores) {
            if ($fecha_bucle >= $valores['fecha_inicio'] && $fecha_bucle <= $valores['fecha_fin']) {
                $v_obs = strtoupper($valores["nombre"]) ?? "DIA LIBRE";
                $dias_habiles_mes--;
            }
        }
    } elseif ($rango_permiso !== false) {
        $v_obs = 'PERMISO';
        $permisos_mes++;
        $justificados_mes++;
    } else {
        if ($fecha_bucle <= $hoy) {
            $v_obs = 'INASISTENCIA';
            $sin_justificar_mes++;
        } else {
            $v_entrada = '';
            $v_salida = '';
            $v_obs = '';
        }
    }

    $etiqueta_fecha = $dias_esp[$dia_semana] . '-' . str_pad($dia, 2, '0', STR_PAD_LEFT);

    $pdf->SetX($leftMargin);
    $pdf->Cell($w_fecha, $alto_fila, utf8_decode($etiqueta_fecha), 1, 0, 'C');
    $pdf->Cell($w_entrada, $alto_fila, utf8_decode($v_entrada), 1, 0, 'C');
    $pdf->Cell($w_salida, $alto_fila, utf8_decode($v_salida), 1, 0, 'C');
    $pdf->Cell($w_obs, $alto_fila, utf8_decode($v_obs), 1, 0, 'C');
    $pdf->Cell($w_estatus, $alto_fila, utf8_decode($v_estatus), 1, 1, 'C');
}

$pdf->Ln(3);

// ============================================
// TABLA RESUMEN MENSUAL
// ============================================
$pdf->SetX($leftMargin);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($ancho_tabla, 6, utf8_decode("CONTROL DE ASISTENCIA MES $nombre_mes_actual"), 0, 1, 'L');
$pdf->Ln(0.3);

$w1 = 50;
$w2 = 26;
$w3 = 31;
$w4 = 26;
$w5 = 45;
$alto_fila = 6;

$no_laborados_mes = $justificados_mes + $sin_justificar_mes;

$y0 = $pdf->GetY();
$pdf->SetXY($leftMargin, $y0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($w1, $alto_fila, utf8_decode("Días Hábiles: $dias_habiles_mes días"), 1, 0, 'L', true);
$pdf->Cell($w2 + $w3 + $w4 + $w5, $alto_fila, utf8_decode("Días no laborados: $no_laborados_mes"), 1, 0, 'L', true);
$pdf->Ln();

$pdf->SetX($leftMargin);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell($w1, $alto_fila, utf8_decode("Días Laborados: $dias_laborados_mes"), 1, 0, 'L');
$pdf->Cell($w2, $alto_fila, utf8_decode("Justificados: $justificados_mes"), 1, 0, 'L');
$pdf->Cell($w3, $alto_fila, utf8_decode("Sin justificar: $sin_justificar_mes"), 1, 0, 'L');
$pdf->Cell($w4, $alto_fila, utf8_decode("Retardos: $retardos_mes"), 1, 0, 'L');
$pdf->Cell($w5, $alto_fila, utf8_decode("Permisos concedidos: $permisos_mes"), 1, 1, 'L');

$pdf->Ln(3);

// ============================================
// TABLA RESUMEN ANUAL ESCOLAR (YALENADA DINÁMICAMENTE)
// ============================================
$pdf->SetX($leftMargin);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($ancho_tabla, 6, utf8_decode("CONTROL DE ASISTENCIA AÑO ESCOLAR {$ano_escolar}"), 0, 1, 'L');
$pdf->Ln(0.3);

$y0 = $pdf->GetY();
$pdf->SetXY($leftMargin, $y0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($w1, $alto_fila, utf8_decode("Días Hábiles transcurridos: $anual_habiles"), 1, 0, 'L', true);
$pdf->Cell($w2 + $w3 + $w4 + $w5, $alto_fila, utf8_decode("Días no laborados durante el año escolar: $anual_no_laborados"), 1, 0, 'L', true);
$pdf->Ln();

$pdf->SetX($leftMargin);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell($w1, $alto_fila, utf8_decode("Días Laborados: $anual_laborados"), 1, 0, 'L');
$pdf->Cell($w2, $alto_fila, utf8_decode("Justificados: $anual_justificados"), 1, 0, 'L');
$pdf->Cell($w3, $alto_fila, utf8_decode("Sin justificar: $anual_sin_justificar"), 1, 0, 'L');
$pdf->Cell($w4, $alto_fila, utf8_decode("Retardos: $anual_retardos"), 1, 0, 'L');
$pdf->Cell($w5, $alto_fila, utf8_decode("Permisos concedidos: $anual_permisos"), 1, 1, 'L');

$pdf->Ln(15);

$ancho_firma = 60;
$x_firma = (215.9 - $ancho_firma) / 2;
$y_firma = $pdf->GetY();
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(0.3);
$pdf->Line($x_firma, $y_firma + 5, $x_firma + $ancho_firma, $y_firma + 5);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY($x_firma, $y_firma + 7);
$pdf->Cell($ancho_firma, 4, 'C.I: 9.506.398', 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0);
$pdf->SetX($x_firma);
$pdf->Cell($ancho_firma, 5, 'Director (E)', 0, 1, 'C');

$pdf->Output('I', 'registro_asistencia.pdf');