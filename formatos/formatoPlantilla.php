<?php
    //error_reporting(E_ALL);
    //ini_set("display_errors", 1); 
    ob_start();
 
    require ('../plugins/fpdf183/fpdf.php');
    include ('variables.php');
    include('CifrasEnLetras.php');
    $lbr=$_GET["lbr"];
    $cuota=$_GET["cuota"];
    $plazo=$_GET["plazo"];
    $afiliacion=$_GET["afiliacion"];
    $pdf = new FPDF();
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetAuthor('Sistemas Kredit', true);
    $pdf->SetCreator('Sistemas Kredit', true);
  
    $pdf->AddPage();
    $pdf->Image('./plantillas/PAGADURIAS/KREDIT_page-0012.jpg', 5, 5, 202);
    //switch ($int_tipo_novedad_reportar) {
      //  case '1': $pdf->text(85, 31, "X"); break;
     //   case '2': $pdf->text(118, 31, 'X'); break;
       // case '3': $pdf->text(145, 31, 'X'); break;
        
    //}
    $pdf->text(118, 31, 'X');
    $pdf->text(160, 57, $int_dia_diligenciamiento."/".$int_mes_diligenciamiento."/".$int_ano_diligenciamiento);

    $pdf->text(16, 91, $str_primer_apellido_solicitante);
    $pdf->text(60, 91, $str_segundo_apellido_solicitante);
    $pdf->text(105, 91, $str_primer_nombre_solicitante);
    $pdf->text(155, 91, $str_segundo_nombre_solicitante);

    switch ($str_tipoidentificacion_solicitante) {
        case 'CC': $pdf->text(16, 102, 'X'); break;
        case 'RC': $pdf->text(45, 102, 'X'); break;
        case 'TI': $pdf->text(74, 102, 'X'); break;
        
    }


    $cuotaLetras=CifrasEnLetras::convertirNumeroEnLetras(str_replace(".",",",$cuota))." Pesos";
    $montoLetras=CifrasEnLetras::convertirNumeroEnLetras(str_replace(".",",",($int_plazo_meses*$cuota)))." Pesos";

    $pdf->text(138, 102, $int_identificacion_solicitante);
    $pdf->text(171, 102, $afiliacion);
    $pdf->text(16, 112, $str_direccion_domicilio_solicitante);
    $pdf->text(16, 122, $str_ciudad_domicilio_solicitante);
    $pdf->text(67, 122, $str_departamento_domicilio_solicitante);
    $pdf->text(117, 122, $str_celular_solicitante);
    $pdf->text(20, 179, ($int_plazo_meses*$cuota));
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(60, 179, ($montoLetras));
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(148, 178, ($lbr));
    $pdf->text(20, 189, $int_plazo_meses);
    $pdf->text(35, 189, $cuota);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(75, 189, $cuotaLetras);
    $pdf->SetFont('Arial', '', 10);


    
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

    //$pdf->text(17.2, 178.8, $int_valor_total_deuda);
    //$pdf->text(60, 178.8, $str_valor_total_prestamo);
    //$pdf->text(145, 178.8, $int_numero_libranza);


    //$pdf->text(17.2, 189, $int_cantidad_cuotas_descuento);
    //$pdf->text(33, 189, $int_cuota_descuento);
    //$pdf->text(75, 189, $str_cuota_descuento_en_letra);
    $pdf->AddPage();
    //PLANTILLA 2
    $pdf->Image('./plantillas/PAGADURIAS/KREDIT_page-0013.jpg', 5, 5, 202);
    ob_end_clean();

    $pdf->Output();
    //*/
?>