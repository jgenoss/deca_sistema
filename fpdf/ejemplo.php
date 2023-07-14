<?php
require('fpdf.php');

class PDF extends FPDF
{
    // Anchura efectiva de la página
    private $effectivePageWidth;

    function __construct($orientation='P', $unit='mm', $size='A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->effectivePageWidth = $this->GetPageWidth() - $this->lMargin - $this->rMargin;
    }

    // Función para imprimir un texto con saltos de línea automáticos
    function AutoMultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
    {
        // Dividir el texto en líneas individuales
        $lines = explode("\n", $txt);

        foreach ($lines as $line) {
            // Obtener el ancho del texto
            $lineWidth = $this->GetStringWidth($line);

            // Si el ancho del texto es menor que la anchura efectiva de la página,
            // simplemente imprimir el texto
            if ($lineWidth < $this->effectivePageWidth) {
                $this->MultiCell($w, $h, $line, $border, $align, $fill);
            } else {
                // Si el ancho del texto es mayor que la anchura efectiva de la página,
                // dividir la línea en múltiples líneas
                $words = explode(' ', $line);
                $currentWidth = 0;
                $line = '';
                foreach ($words as $word) {
                    $wordWidth = $this->GetStringWidth($word);
                    if ($currentWidth + $wordWidth < $this->effectivePageWidth) {
                        $line .= $word . ' ';
                        $currentWidth += $wordWidth + $this->GetStringWidth(' ');
                    } else {
                        $this->MultiCell($w, $h, $line, $border, $align, $fill);
                        $line = $word . ' ';
                        $currentWidth = $wordWidth + $this->GetStringWidth(' ');
                    }
                }
                $this->MultiCell($w, $h, $line, $border, $align, $fill);
            }
        }
    }
}

$pdf = new PDF();
$pdf->AddPage();

// Ejemplo de uso
$texto = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel augue eget diam aliquet malesuada a sit amet ex. In sed pulvinar odio. Vestibulum a ultricies quam. Vestibulum maximus enim eu est commodo, eget sodales nulla dignissim. Sed eu velit ex. Nulla bibendum nisl ac nibh iaculis, eu blandit elit facilisis. Phasellus mattis blandit commodo. Aliquam ultricies sapien vitae erat fringilla, eget lacinia elit egestas. Donec vel lacus vel nisl finibus commodo. Sed commodo bibendum mauris, sit amet efficitur ipsum eleifend vel.";

$pdf->SetFont('Arial','',12);
$pdf->AutoMultiCell(0, 6, $texto);

$pdf->Output();
?>
