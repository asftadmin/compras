<?php
require_once('../../docs/fpdf.php');
require_once("../../config/conexion.php");
require_once("../../models/Evaluacion.php");

class PDF extends FPDF {
    // Cabecera de página
    function Header() {
        $this->SetY(15);
        $this->Image('../../public/img/logo.png', 10, 8, 35);
        $this->SetX(200);
        $this->SetFont('Arial', '', 8);

        $this->SetX(140);
        $this->Cell(30, 5, 'Version:', 0, 0, 'R');
        $this->Cell(30, 5, '', 0, 1, 'L');

        $this->SetX(140);
        $this->Cell(30, 5, 'Implementacion:', 0, 0, 'R');
        $this->Cell(30, 5, 'Enero 6 del 2025', 0, 1, 'L');

        $this->SetX(140);
        $this->Cell(30, 5, 'Codigo:', 0, 0, 'R');
        $this->Cell(30, 5, '', 0, 1, 'L');

        $this->SetX(140);
        $this->Cell(30, 5, utf8_decode('Página: '), 0, 0, 'R');
        $this->Cell(30, 5, $this->PageNo() . ' de {nb}', 0, 1, 'L');

        $this->SetFont('Arial', 'B', 13);
        $this->SetY(15);
        $this->SetX(70);
        $this->Cell(55, 15, utf8_decode('EVALUACIÓN PROVEEDORES'), 0, 0, 'C');
        $this->Ln(25);
    }

    // -------- Función para contar líneas en MultiCell --------
    function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    // -------- Fila agrupada con celdas combinadas --------
    function filaAgrupada($titulo, $items, $promedios) {
        $x_inicial = $this->GetX();
        $y_inicial = $this->GetY();

        $ancho_izq = 40;
        $ancho_central = 110;
        $ancho_prom = 40;

        $this->SetFont('Arial', '', 9);
        $this->SetFillColor(179, 224, 255);

        // Calcular altura total del bloque
        $alturas = [];
        foreach ($items as $texto) {
            $nbLineas = $this->NbLines($ancho_central, utf8_decode($texto));
            $alturas[] = $nbLineas * 5;
        }
        $altura_total = array_sum($alturas);

        // Celda azul combinada
        $this->SetXY($x_inicial, $y_inicial);
        $this->MultiCell($ancho_izq, $altura_total, utf8_decode($titulo), 1, 'C', true);

        // Volver a posición inicial
        $this->SetXY($x_inicial + $ancho_izq, $y_inicial);

        // Filas internas
        foreach ($items as $i => $texto) {
            $y_inicio_item = $this->GetY();

            // Texto central
            $this->MultiCell($ancho_central, 5, utf8_decode($texto), 1, 'L');

            // Calcular altura de esta fila
            $y_fin_item = $this->GetY();
            $altura_fila = $y_fin_item - $y_inicio_item;

            // Promedio a la derecha
            $this->SetXY($x_inicial + $ancho_izq + $ancho_central, $y_inicio_item);
            $this->Cell($ancho_prom, $altura_fila, isset($promedios[$i]) ? $promedios[$i] : '100', 1, 1, 'C');

            // Volver al siguiente inicio
            $this->SetXY($x_inicial + $ancho_izq, $y_fin_item);
        }

        // Ajustar posición final
        $this->SetY($y_inicial + $altura_total);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'El espiritu de las Grandes Obras ', 'T', 0, 'C');
    }
}

// Obtener el ID desde la URL
// Obtener los IDs desde la URL
if (isset($_GET['id1']) && isset($_GET['id2'])) {
    $codigo_eval = $_GET['id1'];
    $nit_proveedor = $_GET['id2'];
} else {
    die("Error: Parámetros incompletos. Se requieren id1 y id2.");
}

$evaluacionClass = new Evaluacion();
$evaluacion = $evaluacionClass->get_evaluacion_id($codigo_eval, $nit_proveedor);


$razon_social = isset($evaluacion[0]['razon_social']) ? $evaluacion[0]['razon_social'] : 'N/A';
$documentos_sst = isset($evaluacion[0]['prom_p1']) ? $evaluacion[0]['prom_p1'] : 0;
$documentos_amb = isset($evaluacion[0]['prom_p2']) ? $evaluacion[0]['prom_p2'] : 0;
$embalaje = isset($evaluacion[0]['prom_p3']) ? $evaluacion[0]['prom_p3'] : 0;
$atencion = isset($evaluacion[0]['prom_p4']) ? $evaluacion[0]['prom_p4'] : 0;
$matriz = isset($evaluacion[0]['prom_p5']) ? $evaluacion[0]['prom_p5'] : 0;
$especificaciones = isset($evaluacion[0]['prom_p6']) ? $evaluacion[0]['prom_p6'] : 0;
$tiempo = isset($evaluacion[0]['prom_p7']) ? $evaluacion[0]['prom_p7'] : 0;
$precios = isset($evaluacion[0]['prom_p8']) ? $evaluacion[0]['prom_p8'] : 0;
$cantidades = isset($evaluacion[0]['prom_p9']) ? $evaluacion[0]['prom_p9'] : 0;
$total = isset($evaluacion[0]['porcentaje_promedio']) ? $evaluacion[0]['porcentaje_promedio'] : 0;

$total = floatval($total); // Aseguramos que sea número

if ($total >= 71) {
    $clasificacion = 'MUY CONFIABLE';
} elseif ($total >= 50 && $total <= 70) {
    $clasificacion = 'CONDICIONAL';
} else {
    $clasificacion = 'NO CONFIABLE';
}

$pdf = new PDF();
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain', 'es_ES');
$meses = [
    1 => 'enero',
    2 => 'febrero',
    3 => 'marzo',
    4 => 'abril',
    5 => 'mayo',
    6 => 'junio',
    7 => 'julio',
    8 => 'agosto',
    9 => 'septiembre',
    10 => 'octubre',
    11 => 'noviembre',
    12 => 'diciembre'
];

$dia = date('j');
$mes = $meses[intval(date('n'))];
$anio = date('Y');
$fecha_actual = "Bucaramanga, {$dia} de {$mes} del {$anio}";

// Encabezado y cuerpo introductorio
$pdf->Cell(0, 8, utf8_decode($fecha_actual), 0, 1);
$pdf->Ln(2);
$pdf->Cell(0, 8, utf8_decode('Señores:'), 0, 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 8, utf8_decode($razon_social), 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 8, utf8_decode('Ciudad'), 0, 1);
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 8, utf8_decode('REF: REPORTE DESEMPEÑO PROVEEDORES AÑO 2025'), 0, 1);
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 9);
$texto = utf8_decode(
    "Reciba un cordial saludo,\n\n" .
        "En búsqueda del mejoramiento continuo de nuestros procesos, y partiendo de la gran importancia que representa para nuestro sistema de gestión integrado la oportuna respuesta y permanente acompañamiento de nuestros proveedores, atentamente nos permitimos reportar la calificación obtenida en la evaluación de desempeño para el periodo mencionado en referencia con relación a los criterios considerados por ASFALTART:"
);
$pdf->MultiCell(0, 6, $texto);
$pdf->Ln(4);

// ======================= TABLA PRINCIPAL =======================
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(255, 255, 153);
$pdf->Cell(150, 8, utf8_decode('PRODUCTOS'), 1, 0, 'C', true);
$pdf->Cell(40, 8, utf8_decode('PROM'), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
// SST
$pdf->filaAgrupada('SST', [
    'Entrega de documentos SST establecidos en la matriz de requisitos y controles a proveedores y contratistas',
    'Entrega de documentos ambientales establecidos en la matriz de requisitos y controles a proveedores y contratistas'
], [$documentos_sst, $documentos_amb]);

// Ambiental
$pdf->filaAgrupada('Ambiental', [
    'Embalaje y Transporte',
    'Atención'
], [$embalaje, $atencion]);

// Condiciones comerciales
$pdf->filaAgrupada('Condiciones comerciales', [
    'Entrega de documentos de calidad matriz de requisitos y controles a proveedores y contratistas'
], [$matriz]);

// Cumplimiento de especificaciones
$pdf->filaAgrupada('Cumplimiento Especif.', [
    'Especificaciones Técnicas',
    'Tiempo de Entrega'
], [$especificaciones, $tiempo]);

// Condiciones administrativas
$pdf->filaAgrupada('Condiciones Admin.', [
    'Mantuvo Precios',
    'Cumplimiento de Cantidades Solicitadas'
], [$precios, $cantidades]);

$pdf->Ln(3);

// ======================= PARÁMETROS DE MEDICIÓN =======================
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(204, 255, 255);
$pdf->Cell(100, 8, utf8_decode('PARÁMETROS DE MEDICIÓN'), 1, 0, 'C', true);
$pdf->Cell(60, 8, utf8_decode('Promedio General de Evaluación'), 1, 0, 'C', true);
$pdf->Cell(30, 8, utf8_decode($total), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(204, 255, 255);
$pdf->Cell(35, 8, utf8_decode('Tipo Proveedor'), 1, 0, 'C', true);
$pdf->Cell(30, 8, utf8_decode('Muy Confiable'), 1, 0, 'C');
$pdf->Cell(35, 8, utf8_decode('Condicional'), 1, 0, 'C');
$pdf->Cell(30, 8, utf8_decode('No Confiable'), 1, 0, 'C');
$pdf->SetFillColor(255, 255, 153);
$pdf->Cell(60, 8, utf8_decode('Clasificación del Proveedor'), 1, 1, 'C', true);

$pdf->SetFillColor(204, 255, 255);
$pdf->Cell(35, 8, utf8_decode('Calificación'), 1, 0, 'C', true);
$pdf->Cell(30, 8, utf8_decode('100 a 71'), 1, 0, 'C');
$pdf->Cell(35, 8, utf8_decode('50 a 70'), 1, 0, 'C');
$pdf->Cell(30, 8, utf8_decode('<50'), 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60, 8, utf8_decode($clasificacion), 1, 1, 'C');

// ======================= FIRMAS =======================
$pdf->Ln(3);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 6, utf8_decode('Esperamos que esta retroalimentación del servicio nos permita mejorar los procesos, ASFALTART S.A.S agradece su gestión.'));
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(90, 8, utf8_decode('__________________________________'), 0, 0, 'C');
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(90, 8, utf8_decode('__________________________________'), 0, 1, 'C');

$firmaAdr = '../../public/img/firma_adriana_fina.png';
if (file_exists($firmaAdr)) {
    // X = 35 mm, Y =  posición actual -17, ancho = 25 mm
    $pdf->Image($firmaAdr, 35, $pdf->GetY() - 17, 45);
}

// Agregar imagen de firma de Damaris
$firmaPathDam = '../../public/img/firma_damaris.png';
if (file_exists($firmaPathDam)) {
    // Coordenadas: X = 115, Y = posición actual -5 px, ancho 40 mm
    $pdf->Image($firmaPathDam, 135, $pdf->GetY() - 17, 25);
}

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(90, 8, utf8_decode('ADRIANA MARIA PINTO RIBERO'), 0, 0, 'C');
$pdf->Cell(5, 8, '', 0, 0);
$pdf->Cell(90, 8, utf8_decode('DAMARIS ANDREA ALFONSO SÁNCHEZ'), 0, 1, 'C');

$pdf->SetFont('Arial', 'I', 9);
$pdf->Cell(90, 5, utf8_decode('Coordinadora de Compras'), 0, 0, 'C');
$pdf->Cell(5, 5, '', 0, 0);
$pdf->Cell(90, 5, utf8_decode('Directora SIG'), 0, 1, 'C');

// Verificar si se ha pasado un parámetro 'savepath'
if (isset($_GET['savepath']) && !empty($_GET['savepath'])) {
    // Guardar el PDF en la ruta indicada
    $pdf->Output('F', $_GET['savepath']);
} else {
    // Mostrar en pantalla como siempre
    $pdf->Output();
}