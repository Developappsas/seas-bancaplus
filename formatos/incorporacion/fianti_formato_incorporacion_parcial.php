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
    $pdf->Image('../plantillas/FIANTI 12-22/FIANTI_page-0006.jpg', 5, 5, 202);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->text(99, 58.4, $int_pagare_deceval);
    //$pdf->text(103, 156, $str_lugar_diligenciamiento." ".$int_dia_diligenciamiento."/".$int_mes_diligenciamiento."/".$int_ano_diligenciamiento);
    
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);
    $pdf->Image('../plantillas/FIANTI 12-22/FIANTI_page-0007.jpg', 5, 5, 202);
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
    $pdf->Image('../plantillas/FIANTI 12-22/FIANTI_page-0008.jpg', 5, 5, 202);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->text(100, 67.5, $int_numero_libranza);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(103, 90.5, "$".$int_total_pagar);
    $pdf->text(103, 102, $str_ciudad_diligenciamiento);
    $pdf->text(103, 111, "CUOTA: $".$int_cuota_mensual." MENSUALES");
    $pdf->text(103, 115, "PLAZO: ".$int_cantidad_cuotas_descuento . " MESES");
    $pdf->text(103, 124, $int_pagaduria);

    $pdf->AddPage();
    $pdf->Image('../plantillas/FIANTI 12-22/FIANTI_page-0009.jpg', 5, 5, 202);
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

    /**************PLANTILLA AUTORIZACION************/

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetAuthor('Sistemas Kredit', true);
    $pdf->SetCreator('Sistemas Kredit', true);
    $pdf->AddPage();
    $pdf->Image('../plantillas/FIANTI 12-22/FIANTI_page-0011.jpg', 5, 5, 202);
    $pdf->text(160, 57, $int_dia_diligenciamiento."/".$int_mes_diligenciamiento."/".$int_ano_diligenciamiento);
    $pdf->text(118.5, 31.3, 'X');

    $pdf->text(16, 91, $str_primer_apellido_solicitante);
    $pdf->text(60, 91, $str_segundo_apellido_solicitante);
    $pdf->text(105, 91, $str_primer_nombre_solicitante);
    $pdf->text(155, 91, $str_segundo_nombre_solicitante);

    switch ($str_tipoidentificacion_solicitante) {
        case 'CC': $pdf->text(16, 102, 'X'); break;
        case 'RC': $pdf->text(45, 102, 'X'); break;
        case 'TI': $pdf->text(74, 102, 'X'); break;
        
    }

    $pdf->text(138, 102, $int_identificacion_solicitante);
    $pdf->text(171, 102, $int_numero_afiliacion);
    $pdf->text(16, 112, $str_direccion_domicilio_solicitante);
    $pdf->text(16, 122, $str_ciudad_domicilio_solicitante);
    $pdf->text(67, 122, $str_departamento_domicilio_solicitante);
    $pdf->text(117, 122, $str_celular_solicitante);
    //$pdf->text(159.5, 122, "03/11/2021");

    //CONDICION BENEFICIARIO DE PENSION

    //switch ($tipo_documento) {
    //  case 'TI': $pdf->text(97, 128.8, 'X'); break;
        //case 'CC': $pdf->text(109, 128.8, 'X'); break;
        //case 'CE': $pdf->text(121, 128.8, 'X'); break;
        //case 'P': $pdf->text(133, 128.8, 'X'); break;
        
    //}
    //$pdf->text(147, 128.8, $numero_documento);

    //FIN CONDICION BENEFICIARIO DE PENSION

    //$pdf->text(17, 150, $str_nombre_replegal);

    //  switch ($str_tipoidentificacion_replegal) {
    //        case 'CC': $pdf->text(98, 149.2, 'X'); break;
    //      case 'CE': $pdf->text(108, 149.2, 'X'); break;
        
        
    //}


    //$pdf->text(125, 149.2, $int_identificacion_replegal);
    //$pdf->text(176, 149.2, $int_telefono_replegal);

    //$pdf->text(17.2, 164, $int_valor_descuento_afiliacion);
    //$pdf->text(60, 164, $str_valor_descuento_afiliacion);

    $pdf->text(17.2, 178.8, $int_total_pagar);
    $pdf->SetFont('Arial', '', 6);
    $pdf->text(60, 178.8, $str_valor_total_prestamo);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(158, 178.8, $int_numero_libranza);

    $pdf->text(17.2, 189, $int_cantidad_cuotas_descuento);
    $pdf->text(33, 189, $int_cuota_mensual);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(75, 189, $str_cuota_descuento_en_letra);

    if($int_formato_digital == 1){
        $pdf->Image('../../images/firma_representante_legal_digital.jpg', 115, 242, 50);

        $pdf->Image('../../images/logo_pagare.png', 15, 210, 16);
        $pdf->SetFont('Arial', '', 7);
        $pdf->text(33, 213, 'Firmado electronicamente por:');
        $pdf->text(33, 216, $str_nombre_tomador);
        $pdf->text(33, 219, 'CC'.$int_identificacion_solicitante);
        $pdf->text(33, 222, 'Fecha: '.$str_fecha_diligenciamiento);
    }else{
        $pdf->Image('../../images/firma_representante_legal_fisico.jpg', 123, 235, 18);
    }

    /**************FIN PLANTILLA AUTORIZACION************/

    ob_end_clean();

    $pdf->Output();
?>