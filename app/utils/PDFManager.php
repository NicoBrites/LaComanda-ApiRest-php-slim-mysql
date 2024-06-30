<?php

require '../vendor/autoload.php';

class PDFManager{

    public static function CrearPdf(){

        // Crear una instancia de FPDF
        $pdf = new FPDF();
        $pdf->AddPage(); // Agregar una página

        // Establecer fuente
        $pdf->SetFont('Arial', 'B', 16);

        // Agregar un título
        $pdf->Cell(40, 10, '¡Hola, Mundo!');

        // Agregar más contenido
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10); // Salto de línea
        $pdf->Cell(40, 10, 'Este es un ejemplo de PDF generado con FPDF en PHP.');

        // Salida del PDF al navegador
        $pdf->Output('D', 'documento.pdf'); // El segundo parámetro 'I' significa "Inline", para mostrar en el navegador

        return true;
    }

}