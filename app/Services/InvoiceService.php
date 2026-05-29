<?php
namespace App\Services;
class InvoiceService
{
    public function generate($html, $filename)
    {
        $dompdfPath = __DIR__ . '/../../vendor/dompdf/dompdf/src';
        if (!class_exists('\Dompdf\Dompdf')) {
            header('Content-Type: text/html; charset=utf-8');
            echo $html;
            exit;
        }
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}
