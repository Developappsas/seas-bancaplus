<?php
    //error_reporting(E_ALL);
    //ini_set("display_errors", 1); 
    ob_start();
 
    require_once ('../../plugins/fpdf183/fpdf.php');
    require_once ('./variables.php');

    $pdf = new FPDF();

    /**************PAGARE************/

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetAuthor('Sistemas Kredit', true);
    $pdf->SetCreator('Sistemas Kredit', true);

    $pdf->AddPage();
    $pdf->Image('../plantillas/KREDIT 04-22/KREDIT_page-0007.jpg', 5, 5, 202);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->text(99, 58.4, $int_pagare_deceval);
    //$pdf->text(103, 156, $str_lugar_diligenciamiento." ".$int_dia_diligenciamiento."/".$int_mes_diligenciamiento."/".$int_ano_diligenciamiento);
    
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);
    $pdf->Image('../plantillas/KREDIT 04-22/KREDIT_page-0008.jpg', 5, 5, 202);
    $pdf->text(17, 192, $str_nombre_tomador2);
    $pdf->text(36, 204, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(36, 212, $str_lugar_expedicion_solicitante);
    $pdf->text(33, 220, $str_direccion_domicilio_solicitante);
    $pdf->text(32, 228, $str_celular_solicitante);
    $pdf->text(29, 236, $str_ciudad_diligenciamiento);

    if($int_formato_digital == 1){

        $pdf->Image('../../images/logo_pagare.png', 15, 165, 28);
        $pdf->SetFont('Arial', '', 8);
        $pdf->text(50, 166, 'Firmado electronicamente por:');
        $pdf->text(50, 169, $str_nombre_tomador);
        $pdf->text(50, 172, 'CC'.$int_identificacion_solicitante);
        $pdf->text(50, 175, 'Fecha: '.$str_fecha_diligenciamiento);

        $pdf->SetFont('Arial', '', 10);
        $pdf->text(80, 290, 'MD5: '.$md5_firma_expirian);
    }

    /**********FIN PAGARE**********/


    /**************LIBRANZA************/

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetAuthor('Sistemas Kredit', true);
    $pdf->SetCreator('Sistemas Kredit', true);
    $pdf->AddPage();
    $pdf->Image('../plantillas/KREDIT 04-22/KREDIT_page-0009.jpg', 5, 5, 202);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->text(100, 67.5, $int_numero_libranza);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(103, 90.5, "$".$int_total_pagar);
    $pdf->text(103, 102, $str_ciudad_diligenciamiento);
    $pdf->text(103, 111, "CUOTA: $".$int_cuota_mensual." MENSUALES");
    $pdf->text(103, 115, "PLAZO: ".$int_cantidad_cuotas_descuento . " MESES");
    $pdf->text(103, 124, $int_pagaduria);

    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);
    $pdf->Image('../plantillas/KREDIT 04-22/KREDIT_page-0010.jpg', 5, 5, 202);
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

    if($int_formato_digital == 1){
        $pdf->Image('../../images/logo_pagare.png', 15, 115, 28);
        $pdf->SetFont('Arial', '', 8);
        $pdf->text(50, 116, 'Firmado electronicamente por:');
        $pdf->text(50, 119, $str_nombre_tomador);
        $pdf->text(50, 122, 'CC'.$int_identificacion_solicitante);
        $pdf->text(50, 125, 'Fecha: '.$str_fecha_diligenciamiento);

        $pdf->SetFont('Arial', '', 10);
        $pdf->text(80, 290, 'MD5: '.$md5_firma_expirian);
    }

    /**************FIN LIBRANZA************/

    ob_end_clean();

    $pdf->Output();


?>