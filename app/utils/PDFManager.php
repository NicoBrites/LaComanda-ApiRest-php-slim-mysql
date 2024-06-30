<?php

require '../vendor/autoload.php';
require_once 'PDF.php';

class PDFManager{

    public static function CrearPdf($titulo, $cuerpo){


        $pdf = new PDF();
        $pdf->setWatermark('./img/loguitoo.png'); // Establece la marca de agua
        $pdf->AddPage(); 

        // Suponiendo que $pdf es tu instancia de FPDF

        // Configuración de la posición y el tamaño del texto
        $pdf->SetY(10); // Posición vertical (10 mm desde el borde superior)
        $pdf->SetX(10); // Posición horizontal (10 mm desde el borde izquierdo)
        $pdf->SetFont('Arial', '', 9 ); // Fuente Arial, tamaño 10

        // Obtener la fecha de hoy
        $hoy = date('d/m/Y');

        // Texto a mostrar (fecha y lugar)
        $texto = "Fecha: $hoy | Lugar: Lo mas recondito de PHP Docs";

        // Dibujar el texto
        $pdf->Cell(0, 10, $texto, 0, 2, 'L'); // 0 indica ancho automático, 1 indica salto de línea después, 'L' para alineación a la izquierda

        $pdf->SetFont('Arial', 'BU', 16); // B para bold (negrita), U para underline (subrayado)
        $pdf->Cell(0, 10, $titulo, 0, 1, 'C'); // 0 indica ancho automático, 1 indica salto de línea después
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, $cuerpo, 0, 'C'); // 0 indica ancho automático, 'C' para centrar la alineación

        //$pdf->Output('documento.pdf', 'D'); 

        return $pdf;
    }

}