<?php
use mikehaertl\wkhtmlto\Pdf;
class PDFConverter {


    public function __construct() {

    }

    public function createPDF($html,$filename) {
       $pdf = new Pdf(array(
            'orientation' => 'landscape',
            'user-style-sheet' => base_path(). '/public/css/download.css'
           ));
        $content ='<html>
                <head>
                <title>Diggit Project Report</title>
                <meta charset="UTF-8" />
                </head>
                <body>
                <div class="container">
                <div class="row">'.$html.'</div></div></body></html>';
                
        $pdf->addPage($content);
        
        

        $pdf->binary = base_path().'/app/wkhtmltopdf';
        $pdf->saveAs(base_path()."/public/documents/".$filename);
        //$pdf->send();
        //$pdf->send('sample2.pdf');
        //phpinfo();

    }


}