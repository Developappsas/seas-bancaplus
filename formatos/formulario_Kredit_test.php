<?php
    //error_reporting(E_ALL);
    //ini_set("display_errors", 1); 
 
    require ('../plugins/fpdf183/fpdf.php');
    include ('variables.php');
    $pdf = new FPDF();
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetAuthor('Sistemas Kredit', true);
    $pdf->SetCreator('Sistemas Kredit', true);
  
    $pdf->AddPage();
    $pdf->Image('./plantillas/KREDIT 04-22/KREDIT_page-0007.jpg', 5, 5, 202);
    //$pdf->text(103, 156, $str_lugar_diligenciamiento." ".$int_dia_diligenciamiento."/".$int_mes_diligenciamiento."/".$int_ano_diligenciamiento);

    
    $pdf->AddPage();
    $pdf->Image('./plantillas/KREDIT 04-22/KREDIT_page-0008.jpg', 5, 5, 202);
    $pdf->text(17, 192, $str_nombre_tomador2);
    $pdf->text(36, 204, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(36, 212, $str_lugar_expedicion_solicitante);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(33, 220, $str_direccion_domicilio_solicitante);
    $pdf->text(32, 228, $str_celular_solicitante);
    $pdf->text(29, 236, $str_ciudad_diligenciamiento);
        
    $pdf->Image('../images/logo_pagare.png', 10, 165, 28);
    $pdf->SetFont('Arial', '', 8);
    $pdf->text(40, 166, 'Firmado electronicamente por:');
    $pdf->text(40, 170, $str_nombre_tomador);
    $pdf->text(40, 174, 'CC'.$int_identificacion_solicitante);
    $pdf->text(40, 178, 'Fecha: '.$str_fecha_diligenciamiento);
    
    $pdf->AddPage();
    $pdf->Image('./plantillas/KREDIT 04-22/KREDIT_page-0009.jpg', 5, 5, 202);
    
    $pdf->AddPage();
    $pdf->Image('./plantillas/KREDIT 04-22/KREDIT_page-0010.jpg', 5, 5, 202);
    $pdf->text(138, 94.5, $int_dia_diligenciamiento);
    $pdf->text(166, 94.5, $int_mes_diligenciamiento);
    $pdf->text(22, 97.5, $int_ano_diligenciamiento);
    $pdf->text(60.5, 97.5, $str_ciudad_diligenciamiento);
    $pdf->text(17, 144, $str_nombre_tomador2);
    $pdf->text(36, 156, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(36, 163, $str_lugar_expedicion_solicitante);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(33, 171, $str_direccion_domicilio_solicitante);
    $pdf->text(32, 179, $str_celular_solicitante);
    $pdf->text(29, 187, $str_ciudad_diligenciamiento);

    $pdf->Image('../images/logo_pagare.png', 10, 115, 28);
    $pdf->SetFont('Arial', '', 8);
    $pdf->text(40, 118, 'Firmado electronicamente por:');
    $pdf->text(40, 121, $str_nombre_tomador);
    $pdf->text(40, 124, 'CC'.$int_identificacion_solicitante);
    $pdf->text(40, 127, 'Fecha: '.$str_fecha_diligenciamiento);
  
      $pdf->Output();
    //*/
?>