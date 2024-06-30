<?php

require '../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

use setasign\Fpdi\PdfParser\StreamReader;


class PDF extends Fpdi
{
    protected $watermark;

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }

    public function setWatermark($imagePath)
    {
        $this->watermark = $imagePath;
    }

    // Método para añadir la marca de agua en cada página
    function Header()
    {
        if ($this->watermark) {
           
            // Calcula las coordenadas para colocar la imagen en la esquina superior derecha
            $pageWidth = $this->GetPageWidth();
            $imageWidth = 50; // Ancho de la imagen
            $x = $pageWidth - $imageWidth - 10; // 10 mm de margen desde la derecha
            $y = 10; // 10 mm de margen desde la parte superior

            $this->Image($this->watermark, $x, $y, $imageWidth);
            
        }
    }
    
}
