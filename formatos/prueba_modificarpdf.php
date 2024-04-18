<?php
    //error_reporting(E_ALL);
    //ini_set("display_errors", 1); 
    require_once  '../plugins/Zend/Pdf.php';
    //require ('../functions.php');
    

    $pdf = Zend_Pdf::load('1.pdf');
    $page = $pdf->pages[0];
    $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
    $page->setFont($font, 12);
    $page->drawText('Hello world!', 72, 720);
    $pdf->save('zend.pdf');
    //$link = conectar();
    ?>