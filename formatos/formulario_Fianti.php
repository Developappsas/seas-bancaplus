<?php
    //error_reporting(E_ALL);
    //ini_set("display_errors", 1);
    ob_start();
    //JAIRO
    require ('../plugins/fpdf183/fpdf.php');
    include ('variables.php');    
    
    $cedula = $_GET["cedula"];
    
    //VARIBALES PENDIENTES

    $pdf = new FPDF();
    $pdf->SetFont('Arial', '', 10);
    $pdf->AddPage();
    $pdf->SetAuthor('Sistemas Kredit', true);
    $pdf->SetCreator('Sistemas Kredit', true);
    
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0002.jpg', 5, 5, 202);

    $pdf->text(160, 40, $int_numerosolicitud);
    //Dia
    $pdf->text(25,62.7, substr($int_dia_diligenciamiento, 0, 1));
    $pdf->text(30,62.7, substr($int_dia_diligenciamiento, 1));
    //mes
    $pdf->text(35,62.7, substr($int_mes_diligenciamiento, 0, 1));
    $pdf->text(39,62.7, substr($int_mes_diligenciamiento, 1));
    //año
    $pdf->text(44,62.7, substr($int_ano_diligenciamiento, 0, 1));
    $pdf->text(48.5,62.7, substr($int_ano_diligenciamiento, 1, 1));
    $pdf->text(53,62.7, substr($int_ano_diligenciamiento, 2, 1));
    $pdf->text(57,62.7, substr($int_ano_diligenciamiento, 3, 1));

    $pdf->SetFont('Arial', '', 7);
    $pdf->text(24,66, $str_nombre_comercial);
    
    $pdf->text(24,69, $str_apellido_comercial);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(73,69, $str_nombre_oficina);
    
    $pdf->text(20, 84, $str_primer_nombre_solicitante);
    $pdf->text(68, 84, $str_segundo_nombre_solicitante);
    $pdf->text(115, 84, $str_primer_apellido_solicitante);
    $pdf->text(160, 84, $str_segundo_apellido_solicitante);

    switch ($str_tipoidentificacion_solicitante) {
        case 'CC': $pdf->text(20.7, 92.7, 'X'); break;
        case 'RC': $pdf->text(31.5, 92.7, 'X'); break;
        case 'TI': $pdf->text(42.3, 92.7, 'X'); break;
        case 'CE': $pdf->text(53.2, 92.7, 'X'); break;
    }

    $pdf->text(70, 93, $int_identificacion_solicitante);
    
    /*Fecha Expedicion*/
    //Dia
    $pdf->text(115,92.7, $str_dia_expedicion_solicitante);
    

    // //mes
    $pdf->text(129,92.7, $str_mes_expedicion_solicitante);

    // //año
    $pdf->text(142,92.7, $str_ano_expedicion_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(156,92.7, $str_lugar_expedicion_solicitante);
    $pdf->SetFont('Arial', '', 10);
    /*Fecha Nacimiento*/
    //Dia
    $pdf->text(20,101,$str_dia_fecha_nacimiento_solicitante );
    

    // //mes
    $pdf->text(34,101, $str_mes_fecha_nacimiento_solicitante);
    

    // //año
    $pdf->text(48,101, $str_ano_fecha_nacimiento_solicitante);
    
    switch ($str_genero_solicitante) {
        case 'M': $pdf->text(67, 101, 'X'); break;
        case 'F': $pdf->text(78, 101, 'X'); break;
        default: /* code...*/ break;
    }
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(85, 101, $str_lugar_nacimiento);
    $pdf->SetFont('Arial', '', 10);
    //estado civil
    switch ($str_estado_civil) {
        case 'SOLTERO': $pdf->text(161.7, 97.5, 'X'); break;
        case 'SOLTERA': $pdf->text(161.7, 97.5, 'X'); break;
        case 'UNION LIBRE': $pdf->text(177.7, 97.5, 'X'); break;
        case 'CASADO': $pdf->text(193.7, 97.5, 'X'); break;
        case 'CASADA': $pdf->text(193.7, 97.5, 'X'); break;
        case 'DIVORCIADO': $pdf->text(159, 101.5, 'X'); break;
        case 'DIVORCIADA': $pdf->text(159, 101.5, 'X'); break;
        case 'SEPARADO': $pdf->text(177.7, 101.5, 'X'); break;
        case 'SEPARADA': $pdf->text(177.7, 101.5, 'X'); break;
        case 'VIUDO': $pdf->text(193.7, 101.5, 'X'); break;
        case 'VIUDA': $pdf->text(193.7, 101.5, 'X'); break;
    }

    $pdf->text(20, 110, $str_pais_residencia);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(65, 110, $str_ciudad_departamento);
    $pdf->SetFont('Arial', '', 10);
    switch ($str_tipo_vivienda) {
        case 'FAMILIAR': $pdf->text(135, 109.5, 'X'); break;
        case 'ARRENDADA': $pdf->text(153, 109.5, 'X'); break;
        case 'PROPIA': $pdf->text(167.7, 109.5, 'X'); break;                
    }

    $pdf->text(175, 109.5, $str_estrato);

    if ($str_tipo_vivienda == 'ARRENDADA') {
        $pdf->text(20,118, $str_nombre_arrendatario);
        $pdf->text(108, 118, $str_telefono_arrendatario);
    }

    $pdf->text(150, 118, $str_barrio);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(17, 126.5, $str_direccion_residencia);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(108, 126.5, $str_telefono_residencial);
    $pdf->text(150, 126.5, $str_telefono_celular);

    switch ($str_lugar_envio_correspondencia) {
        case 'CASA': $pdf->text(22.7, 134.3, 'X'); break;
        case 'OFICINA': $pdf->text(39, 134.3, 'X'); break;
        case 'EMAIL': $pdf->text(65, 134.3, 'X'); break;
    }
    $pdf->SetFont('Arial', '', 8);
    $pdf->text(72, 134.3, $str_correo);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(160, 134.5, $str_tiempo_residencia_anios);
    $pdf->text(185, 134.5, $str_tiempo_residencia_meses);

    $pdf->text(20, 143.2, $str_eps);
    $pdf->text(102, 143.2, $str_personas_acargo_adultos);
    $pdf->text(136.5, 143.2, $str_personas_acargo_menores);
    $pdf->text(152, 143.2, $str_profesion);

    switch ($str_nivel_estudios) {
        case 'Primaria': $pdf->text(28.5, 151, 'X'); break;
        case 'Bachiller': $pdf->text(48.7, 151, 'X'); break;
        case 'Tecnico': $pdf->text(65, 151, 'X'); break;
        case 'Tenologo': $pdf->text(84.2, 151, 'X'); break;
        case 'Universitario': $pdf->text(106.5, 151, 'X'); break;
        case 'Especializacion': $pdf->text(137.5, 151, 'X'); break;
        case 'Maestria': $pdf->text(160.3, 151, 'X'); break;
        case 'Doctorado': $pdf->text(186.5, 151, 'X'); break;
    }

    $pdf->text(20, 165, $str_primer_nombre_conyuge);
    $pdf->text(68, 165, $str_segundo_nombre_conyuge);
    $pdf->text(115, 165, $str_primer_apellido_conyuge);
    $pdf->text(157, 165, $str_segundo_apellido_conyuge);

    switch ($str_tipo_documento_conyuge) {
        case 'CC': $pdf->text(20.7, 174, 'X'); break;
        case 'RC': $pdf->text(31.5, 174, 'X'); break;
        case 'TI': $pdf->text(42.3, 174, 'X'); break;
        case 'CE': $pdf->text(53.2, 174, 'X'); break;
    }

    $pdf->text(67, 174, $str_numero_documento_conyuge);

    /*Fecha Expedicion*/
    //Dia
    $pdf->text(115, 174, $str_dia_fecha_expedicion_documento_conyuge);
    

    // //mes
    $pdf->text(129, 174,$str_mes_fecha_expedicion_documento_conyuge );

    // //año
    $pdf->text(142, 174, $str_ano_fecha_expedicion_documento_conyuge);
    

    $pdf->text(156, 174, $str_lugar_expedicion_documento_conyuge);

    /*Fecha Nacimiento*/
    //Dia
    $pdf->text(20,182, $str_dia_fecha_nacimiento_conyuge);

    // //mes
    $pdf->text(34,182, $str_mes_fecha_nacimiento_conyuge);
    

    // //año
    $pdf->text(48,182, $str_ano_fecha_nacimiento_conyuge);
    
    switch ($str_genero_conyuge) {
        case 'M': $pdf->text(67, 182, 'X'); break;
        case 'F': $pdf->text(78, 182, 'X'); break;
        default: /* code...*/ break;
    }

    $pdf->text(85, 182, $str_lugar_nacimiento_conyuge);

    $pdf->text(150, 182, $str_trabajo_conyuge);
    
    switch ($str_ocupacion_conyuge) {
        case 'EMPLEADO': $pdf->text(27.1, 190.2,'X'); break;
        case 'INDEPENDIENTE': $pdf->text(46.8, 190.2,'X'); break;
        case 'PENSIONADO': $pdf->text(65.2, 190.2,'X'); break;
        case 'AMA DE CASA': $pdf->text(85, 190.2,'X'); break;
        case 'ESTUDIANTE': $pdf->text(101.7, 190.2,'X'); break;
        case 'RENTISTA CAPITAL': $pdf->text(124.3, 190.2,'X'); break;
    }
    
    if ($str_dependencia_economica_conyuge=="s"){
      $pdf->text(136.8, 190.2,'X');
    }
    else if ($str_dependencia_economica_conyuge=="n")
    {
      $pdf->text(154, 190.2,'X');
    }
    else
    {

    }

    $pdf->text(162, 190, $str_telefono_celular_conyuge);

    /*Actividad Laboral*/
    switch ($str_ocupacion) {
        case '1': $pdf->text(30.5, 203.8,'X'); break;
        case '3': $pdf->text(53, 203.8,'X'); break;
        case '5': $pdf->text(74, 203.8,'X'); break;
        case '4': $pdf->text(96.5, 203.8,'X'); break;
        case '6': $pdf->text(116.5, 203.8,'X'); break;
        case '7': $pdf->text(141.7, 203.8,'X'); break;
    }
    

    if ($str_declara_renta=="s"){
      $pdf->text(174, 202.5, 'X');
    }
    else if ($str_declara_renta=="n")
    {
      $pdf->text(191.5, 202.5, 'X');
    }
    else
    {

    }
    
    if ($str_impacto_social_politica=="s"){
      $pdf->text(62, 210.7, 'X');
    }
    else if ($str_impacto_social_politica=="n")
    {
      $pdf->text(74, 210.7, 'X');
    }
    else
    {

    }

    if ($str_maneja_recursos_publicos=="s"){
      $pdf->text(94, 212.3, 'X');
    }
    else if ($str_maneja_recursos_publicos=="n")
    {
      $pdf->text(106, 212.3, 'X');
    }
    else
    {

    }


    if ($str_personaje_publico=="s"){
      $pdf->text(164, 211, 'X');
    }
    else if ($str_personaje_publico=="n")
    {
      $pdf->text(176, 211, 'X');
    }
    else
    {

    }
    
    $pdf->text(48, 219, $str_actividad_economica);
    $pdf->text(20, 229, $str_nombre_empresa_actual);
    $pdf->text(122, 229, $str_cargo);

     /*Fecha Vinculacion*/
    //Dia
    $pdf->text(161,229, $str_dia_fecha_vinculacion);
    
    // //mes
    $pdf->text(173,229, $str_mes_fecha_vinculacion);
    
    // //año
    $pdf->text(187,229, $str_ano_fecha_vinculacion);
    
    $pdf->text(20, 238, $str_direccion_lugar_trabajo);
    $pdf->text(100, 238, $str_ciudad_trabajo);
    $pdf->text(145, 238, $str_nit_trabajo);
    $pdf->text(20, 246, $str_telefono_trabajo);
    $pdf->text(100, 246, $str_extension_trabajo);
        
    switch ($str_tipo_empresa) {
        case 'PUBLICA': $pdf->text(153, 246.5, 'X'); break;
        case 'PRIVADA': $pdf->text(168.5, 246.5, 'X'); break;
        case 'MIXTA': $pdf->text(184, 246.5, 'X'); break;
    }

    switch ($str_actividad_economica_empresa) {
        case 'SERVICIOS': $pdf->text(28.5, 254.7, 'X'); break;
        case 'COMERCIAL': $pdf->text(49.5, 254.7, 'X'); break;
        case 'CONSTRUCCION': $pdf->text(70.3, 254.7, 'X'); break;
        case 'INDUSTRIAL': $pdf->text(91.2, 254.7, 'X'); break;
        case 'AGROPECUARIA': $pdf->text(115, 254.7, 'X'); break;
        case 'OTRA': $pdf->text(133, 254.7, 'X'); break;
    }

    switch ($str_tipo_contrato) {
        case 'Indefinido': $pdf->text(158.7, 254.7, 'X'); break;
        case 'Contratista': $pdf->text(178.5, 254.7, 'X'); break;
        case 'Fijo': $pdf->text(190, 254.7, 'X'); break;
    }

    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0003.jpg', 5, 5, 202);
    if ($cargo_publico=="SI"){
      $pdf->text(51, 24, 'X');
    }
    else if ($cargo_publico=="NO")
    {
      $pdf->text(63.5, 24, 'X');
    }
    else
    {

    }
    $pdf->text(38, 125, $str_ingresos_laborales);
    $pdf->text(130, 125, $str_gastos_familiares);
    $pdf->text(43, 133, $str_honorario_comisiones);
    $pdf->text(143, 133, $str_arrendamiento_cuota_vivienda);
    $pdf->text(43, 141, $str_otros_ingresos);
    $pdf->text(132, 141, $str_pasivos);
    $pdf->text(33, 149, $str_total_ingresos);
    $pdf->text(133, 149, $str_pasivos_corrientes);
    $pdf->text(62, 159, $str_activos);
    $pdf->text(134, 159, $str_otros_pasivos);
    $pdf->text(32, 167, $str_total_activos);
    $pdf->text(124, 167, $str_total_pasivos);
    
    $pdf->text(20, 182, $str_referencia_familiar_nombre);
    $pdf->text(109, 182, $str_referencia_familiar_parentezco);
    $pdf->text(158, 182, $str_referencia_familiar_telefono);
    $pdf->text(20, 190, $str_referencia_familiar_direccion);
    $pdf->text(109, 190, $str_referencia_familiar_ciudad);
    $pdf->text(158, 190, $str_referencia_familiar_celular);

    $pdf->text(20, 204, $str_referencia_personal_nombre);
    $pdf->text(109, 204, $str_referencia_personal_parentezco);
    $pdf->text(158, 204, $str_referencia_personal_telefono);
    $pdf->text(20, 212, $str_referencia_personal_direccion);
    $pdf->text(109, 212, $str_referencia_personal_ciudad);
    $pdf->text(158, 212, $str_referencia_personal_celular);

    //Datos Operaciones Internacionales

    
    
    if($str_operaciones_moneda_extranjera==true){$pdf->text(26, 232.7, 'X');}else{$pdf->text(38, 232.7, 'X');};
    if($str_cuenta_en_el_exterior==true){$pdf->text(26, 252.7, 'X');}else{$pdf->text(38.5, 252.7, 'X');};
    
    if($str_operacion_exterior_exportacion==true){$pdf->text(68, 229.5, 'X');}
    if($str_operacion_exterior_importacion==true){$pdf->text(88, 229.5, 'X');}
    if($str_operacion_exterior_inversiones==true){$pdf->text(108, 229.5, 'X');}
    if($str_operacion_exterior_prestamo==true){$pdf->text(151, 229.5, 'X');}
    if($str_operacion_exterior_otra==true){$pdf->text(68, 234.5, 'X');}
    $pdf->text(78, 234.5, $str_operacion_exterior_otra_descripcion);
    
    if ($criptomoneda=="SI"){
      $pdf->text(105, 244, 'X');
    }
    else if ($criptomoneda=="NO") {
      $pdf->text(117, 244, 'X');
    }
    
    if ($actividades_apnfd=="SI"){
      $pdf->text(174.5, 244, 'X');
    }
    else if ($actividades_apnfd=="NO"){
      $pdf->text(186, 244, 'X');
    }

    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0004.jpg', 5, 5, 202);
 
    //Productos en moneda extranjera
    $pdf->text(15, 30, $str_producto_1_moneda_extranjera_nombre);
    $pdf->text(42, 30, $str_producto_1_moneda_extranjera_cuenta);
    $pdf->text(71, 30, $str_producto_1_moneda_extranjera_tipo); 
    $pdf->text(106, 30, $str_producto_1_moneda_extranjera_monto);
    $pdf->text(140, 30, $str_producto_1_moneda_extranjera_moneda);
    $pdf->text(160, 30, $str_producto_1_moneda_extranjera_ciudad);
    $pdf->text(180, 30, $str_producto_1_moneda_extranjera_pais); 

    $pdf->text( 15, 49, $str_producto_2_moneda_extranjera_nombre);
    $pdf->text( 42, 49, $str_producto_2_moneda_extranjera_cuenta);
    $pdf->text( 71, 49, $str_producto_2_moneda_extranjera_tipo);
    $pdf->text(106, 49, $str_producto_2_moneda_extranjera_monto);
    $pdf->text(140, 49, $str_producto_2_moneda_extranjera_moneda);
    $pdf->text(160, 49, $str_producto_2_moneda_extranjera_ciudad);
    $pdf->text(180, 49, $str_producto_2_moneda_extranjera_pais);
    $pdf->text( 15, 58, $str_producto_3_moneda_extranjera_nombre);
    $pdf->text( 42, 58, $str_producto_3_moneda_extranjera_cuenta);
    $pdf->text( 71, 58, $str_producto_3_moneda_extranjera_tipo);
    $pdf->text(106, 58, $str_producto_3_moneda_extranjera_monto);
    $pdf->text(140, 58, $str_producto_3_moneda_extranjera_moneda);
    $pdf->text(160, 58, $str_producto_3_moneda_extranjera_ciudad);
    $pdf->text(180, 58, $str_producto_3_moneda_extranjera_pais);
    

    //Declaración FACTCA - CRS
    
    
    
    
    if ($str_ciudadania_extranjera? $pdf->text(172.5, 60, 'X') : $pdf->text(185, 60, 'X'));
    if ($str_residente_usa_green_card? $pdf->text(172.5, 69, 'X') :$pdf->text(185, 69, 'X'));
    if ($str_obligado_tributario_exterior? $pdf->text(172.5, 76.5, 'X') : $pdf->text(185, 76.5, 'X'));
    if ($str_otorgo_poder_persona_viva_exterior? $pdf->text(172.5, 85, 'X') : $pdf->text(185.2, 85, 'X'));

    $pdf->text(21, 190, $str_nombre_tomador);
    $pdf->text(136, 190, $int_identificacion_solicitante);
     
    $pdf->text(17, 109, $str_apoderado_1_exterior_pais);
    $pdf->text(65, 109, $str_apoderado_1_exterior_identificacion);
    $pdf->text(137, 109, $str_apoderado_1_exterior_objeto);

    $pdf->text(17, 118, $str_apoderado_2_exterior_pais);
    $pdf->text(65, 118, $str_apoderado_2_exterior_identificacion);
    $pdf->text(137, 118, $str_apoderado_2_exterior_objeto);
    
    //Informacion Apoderado o Representante
    $pdf->text(15, 27, $str_apoderado_primer_nombre);
    $pdf->text(62, 27, $str_apoderado_segundo_nombre);
    $pdf->text(107, 27, $str_apoderado_primer_apellido);
    $pdf->text(150, 27, $str_apoderado_segundo_apellido);
    
    switch ($str_apoderado_tipo_identificacion) {
       case 'C.C.': $pdf->text(27.5, 36, 'X'); break;
       case 'T.I.': $pdf->text(42.3, 36, 'X'); break;
       case 'R.C.': $pdf->text(58.5, 36, 'X'); break;
       case 'C.E.': $pdf->text(77, 36, 'X'); break;
    } 
   
    $pdf->text(90, 36, $str_apoderado_numero_documento);
    $pdf->text(135, 36, $str_apoderado_celular);

    $pdf->text(15, 45, $str_apoderado_telefono);
    $pdf->text(58, 45, $str_apoderado_email);
    $pdf->text(135, 45, $str_apoderado_direccion);
    
    //if ($str_apoderado_administra_recursos_publicos ? $pdf->text(35, 65, 'X') : $pdf->text(53, 65, 'X'));    
    //if ($str_apoderado_ejerce_poder_publico ? $pdf->text(126.5, 65, 'X') : $pdf->text(145, 65, 'X'));     
    //if ($str_apoderado_reconocimiento_publico ? $pdf ->text(38, 74.3, 'X') : $pdf ->text(55.5, 74.3, 'X'));
    
    //Dia
    $pdf->text(115, 74.3, $str_dia_apoderado_fecha_inicio);
    

    // //mes
    $pdf->text(129, 74.3, $str_mes_apoderado_fecha_inicio);
    

    // //año
    $pdf->text(142, 74.3, $str_ano_apoderado_fecha_inicio);
    
    //Dia
    $pdf->text(158, 74.3, $str_dia_apoderado_fecha_final);
    

    // //mes
    $pdf->text(172, 74.3, $str_mes_apoderado_fecha_final);
    

    // //año
    $pdf->text(184, 74.3, $str_ano_apoderado_fecha_final);
    
    
 
    //Compras Cartera
    //$pdf->text(15, 93, $str_compras_cartera_1_entidad);
    //$pdf->text(63, 93, $str_compras_cartera_1_numero_obligacion);
    //$pdf->text(130, 93, $str_compras_cartera_1_valor_estimado);
    //$pdf->text(170, 93, $str_compras_cartera_1_valor_cancelado);

    //$pdf->text(15, 99, $str_compras_cartera_2_entidad);
    //$pdf->text(63, 99, $str_compras_cartera_2_numero_obligacion);
    //$pdf->text(130, 99, $str_compras_cartera_2_valor_estimado);
    //$pdf->text(170, 99, $str_compras_cartera_2_valor_cancelado);

    //$pdf->text(15, 106, $str_compras_cartera_3_entidad);
    //$pdf->text(63, 106, $str_compras_cartera_3_numero_obligacion);
    //$pdf->text(130, 106, $str_compras_cartera_3_valor_estimado);
    //$pdf->text(170, 106, $str_compras_cartera_3_valor_cancelado);

    //$pdf->text(15, 114, $str_compras_cartera_4_entidad);
    //$pdf->text(63, 114, $str_compras_cartera_4_numero_obligacion);
    //$pdf->text(130, 114, $str_compras_cartera_4_valor_estimado);
    //$pdf->text(170, 114, $str_compras_cartera_4_valor_cancelado);

    //$pdf->text(15, 121, $str_compras_cartera_5_entidad);
    //$pdf->text(63, 121, $str_compras_cartera_5_numero_obligacion);
    //$pdf->text(130, 121, $str_compras_cartera_5_valor_estimado);
    //$pdf->text(170, 121, $str_compras_cartera_5_valor_cancelado);

    //$pdf->text(15, 128, $str_compras_cartera_6_entidad);
    //$pdf->text(63, 128, $str_compras_cartera_6_numero_obligacion);
    //$pdf->text(130, 128, $str_compras_cartera_6_valor_estimado);
    //$pdf->text(170, 128, $str_compras_cartera_6_valor_cancelado);

    //Instrucciones_desmbolso
    //$pdf->text(15, 150, $str_instrucciones_desmbolso_1_banco);
    //$pdf->text(63, 150, $str_instrucciones_desmbolso_1_numero_cuenta);
    //$pdf->text(130, 150, $str_instrucciones_desmbolso_1_tipo);
    //$pdf->text(170, 150, $str_instrucciones_desmbolso_1_giro_pin);

    //$pdf->text(15, 157, $str_instrucciones_desmbolso_2_banco);
    //$pdf->text(63, 157, $str_instrucciones_desmbolso_2_numero_cuenta);
    //$pdf->text(130, 157, $str_instrucciones_desmbolso_2_tipo);
    //$pdf->text(170, 157, $str_instrucciones_desmbolso_2_giro_pin);

    
    $pdf->SetFont('Arial', '', 10);
    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0005.jpg', 5, 5, 202);
    $pdf->SetFont('Arial', '', 8);
    $pdf->text(27, 29, $str_actividades);
    $pdf->text(41, 43, $str_nombre_tomador2);
    
    $pdf->text(40, 45.5, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(97, 45.5, $str_lugar_expedicion_solicitante);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(80, 143.5, $int_clave_consulta_desprendible);
    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0006.jpg', 5, 5, 202);
    $pdf->text(160, 40, $int_numero_libranza);

    //$pdf->text(53, 27, $str_banco_1);
    //$pdf->text(93, 27, $str_tipo_cuenta_1);
    //$pdf->text(130, 27, $str_numero_cuenta_1);

    //$pdf->text(33, 35, $str_banco_2);
    //$pdf->text(66, 35, $str_tipo_cuenta_2);
    //$pdf->text(109, 35, $str_numero_cuenta_2);

    //$pdf->text(88, 100, $str_vigencia_credito_1);
    //$pdf->text(133, 100, $str_vigencia_credito_2);

    //$pdf->text(172, 122, $str_tasa_efectiva_anual);

    //$pdf->text(22, 130, $int_cuota_1);
    //$pdf->text(68, 130, $int_plazo_1);
    //$pdf->text(117, 130, $int_valor_credito_1);
    //$pdf->text(165, 130, $int_tasa_solicitada_1);

    //$pdf->text(22, 135, $int_cuota_2);
    //$pdf->text(68, 135, $int_plazo_2);
    //$pdf->text(117, 135, $int_valor_credito_2);
    //$pdf->text(165, 135, $int_tasa_solicitada_2);

    if ($bool_conoce_caracteristicas_condiciones_coberturas==true)
    {$pdf->text(150, 160, "X");}
    else
    {$pdf->text(160, 160, "X");}
    $pdf->text(165, 159, $str_iniciales_nombre);


    if ($bool_conoce_forma_pago==true)
    {$pdf->text(150, 164, "X");}
    else
    {$pdf->text(160, 164, "X");}
    $pdf->text(165, 164, $str_iniciales_nombre);

    if ($bool_conoce_adquirir_productos_cancela_libranza==true)
    {$pdf->text(150, 169, "X");}
    else
    {$pdf->text(160, 169, "X");}
    $pdf->text(165, 169, $str_iniciales_nombre);


    if ($bool_conoce_ampliar_plazo_en_caso_mora==true)
    {$pdf->text(150, 174, "X");}
    else
    {$pdf->text(160, 174, "X");}
    $pdf->text(165, 174, $str_iniciales_nombre);

    if ($bool_conoce_valores_descontados==true)
    {$pdf->text(150, 179, "X");}
    else
    {$pdf->text(160, 179, "X");}
    $pdf->text(165, 179, $str_iniciales_nombre);

    $pdf->text(162, 235, $str_dia_fecha_entrevista);
    $pdf->text(175, 235, $str_mes_fecha_entrevista);
    $pdf->text(188, 235, $str_ano_fecha_entrevista);

    $pdf->SetFont('Arial', '', 8);
    //$pdf->text(18, 193, $str_nombre_tomador);
    $pdf->text(18, 208, $int_identificacion_solicitante);

    
    
    if ($str_resultado_entrevista="ACEPTADO")
    {$pdf->text(35, 230, "X");}
    else 
    {$pdf->text(35, 237, "X");}
    
    $pdf->text(17, 245, $str_observaciones_entrevista);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(134, 244, "ASESOR COMERCIAL");
    $pdf->text(134, 250, $str_cedula_comercial);

    //$pdf->text(18, 199, $str_nomnre_tomador2);
    //$pdf->text(18, 208, $int_identificacion_solicitante);

    $pdf->text(153, 205, $int_numerosolicitud);
    
    
    //$pdf->text(158, 236, $dia_entrevista);
    //$pdf->text(171, 236, $mes_entrevista);
    //$pdf->text(185, 236, $ano_entrevista);


    //$pdf->text(143, 241, $ano_entrevista);
    //$pdf->text(145, 248, $ano_entrevista);
    //$pdf->text(142, 255, $ano_entrevista);

    $pdf->SetFont('Arial', '', 10);
    $pdf->AddPage();
    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0006.jpg', 5, 5, 202);

    //$pdf->text(53, 27, $str_banco_1);
    //$pdf->text(93, 27, $str_tipo_cuenta_1);
    //$pdf->text(130, 27, $str_numero_cuenta_1);

    //$pdf->text(33, 35, $str_banco_2);
    //$pdf->text(66, 35, $str_tipo_cuenta_2);
    //$pdf->text(109, 35, $str_numero_cuenta_2);

    //$pdf->text(88, 100, $str_vigencia_credito_1);
    //$pdf->text(133, 100, $str_vigencia_credito_2);

    //$pdf->text(172, 122, $str_tasa_efectiva_anual);

    //$pdf->text(22, 130, $int_cuota_1);
    //$pdf->text(68, 130, $int_plazo_1);
    //$pdf->text(117, 130, $int_valor_credito_1);
    //$pdf->text(165, 130, $int_tasa_solicitada_1);

    //$pdf->text(22, 135, $int_cuota_2);
    //$pdf->text(68, 135, $int_plazo_2);
    //$pdf->text(117, 135, $int_valor_credito_2);
    //$pdf->text(165, 135, $int_tasa_solicitada_2);

    if ($bool_conoce_caracteristicas_condiciones_coberturas==true)
    {$pdf->text(150, 160, "X");}
    else
    {$pdf->text(160, 160, "X");}
    $pdf->text(165, 159, $str_iniciales_nombre);


    if ($bool_conoce_forma_pago==true)
    {$pdf->text(150, 164, "X");}
    else
    {$pdf->text(160, 164, "X");}
    $pdf->text(165, 164, $str_iniciales_nombre);

    if ($bool_conoce_adquirir_productos_cancela_libranza==true)
    {$pdf->text(150, 169, "X");}
    else
    {$pdf->text(160, 169, "X");}
    $pdf->text(165, 169, $str_iniciales_nombre);

    if ($bool_conoce_ampliar_plazo_en_caso_mora==true)
    {$pdf->text(150, 174, "X");}
    else
    {$pdf->text(160, 174, "X");}
    $pdf->text(165, 174, $str_iniciales_nombre);

    if ($bool_conoce_valores_descontados==true)
    {$pdf->text(150, 179, "X");}
    else
    {$pdf->text(160, 179, "X");}
    $pdf->text(165, 179, $str_iniciales_nombre);


    $pdf->text(162, 235, $str_dia_fecha_entrevista);
    $pdf->text(175, 235, $str_mes_fecha_entrevista);
    $pdf->text(188, 235, $str_ano_fecha_entrevista);

    
    
    if ($str_resultado_entrevista="ACEPTADO")
    {$pdf->text(35, 230, "X");}
    else 
    {$pdf->text(35, 237, "X");}
    
    $pdf->text(17, 245, $str_observaciones_entrevista);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(134, 244, "ASESOR COMERCIAL");
    $pdf->text(134, 250, $str_cedula_comercial);

    //$pdf->text(18, 199, $str_nomnre_tomador2);
    $pdf->text(18, 208, $int_identificacion_solicitante);

    $pdf->text(153, 205, $int_numerosolicitud);

    
    
    
    //$pdf->text(158, 236, $dia_entrevista);
    //$pdf->text(171, 236, $mes_entrevista);
    //$pdf->text(185, 236, $ano_entrevista);


    //$pdf->text(143, 241, $ano_entrevista);
    //$pdf->text(145, 248, $ano_entrevista);
    //$pdf->text(142, 255, $ano_entrevista);

    $pdf->SetFont('Arial', '', 10);
    $pdf->AddPage();
    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0007.jpg', 5, 5, 202);

    //$pdf->text(100, 58, $int_numero_pagare);
    //$pdf->text(103, 78, $int_numero_pagare);
    //$pdf->text(103, 90, $str_nomnre_tomador2);
    //$pdf->text(103, 101, $str_ciudad_diligenciamiento);
    //$pdf->text(103, 112, $int_valor_capital);
    //$pdf->text(103, 123, $int_valor_interes_remunerado);
    //$pdf->text(103, 134, $int_valor_interes_mora);
    //$pdf->text(103, 145, $str_fecha_vencimiento);
    //$pdf->text(103, 156, $str_lugar_diligenciamiento." ".$int_dia_diligenciamiento."/".$int_mes_diligenciamiento."/".$int_ano_diligenciamiento);

    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0008.jpg', 5, 5, 202);
    
    $pdf->text(17, 192, $str_nombre_tomador2);
    $pdf->text(36, 204, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(36, 212, $str_lugar_expedicion_solicitante);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(33, 220, $str_direccion_domicilio_solicitante);
    $pdf->text(32, 228, $str_celular_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(29, 236, $str_ciudad_diligenciamiento);
    $pdf->SetFont('Arial', '', 10);
    //$pdf->AddPage();
    //$pdf->Image('./plantillas/Fianti_Agosto_2021/Fianti_Agosto_2021-07.png', 5, 5, 202);
    //$pdf->text(102, 66, $int_numerosolicitud);
    //$pdf->text(103, 88, $int_valor_total_deuda);
    //$pdf->text(103, 99, $str_ciudad_diligenciamiento);
    //$pdf->text(103, 111 , $int_cuota_mensual." - ".$int_cantidad_cuotas_descuento." CUOTAS");
    //$pdf->text(103, 122 , $autorizacion_libranza);


    //$pdf->AddPage();
    //$pdf->Image('./plantillas/Fianti_Agosto_2021/Fianti_Agosto_2021-08.png', 5, 5, 202);
    //$pdf->text(138, 94.5, $int_dia_diligenciamiento);
    //$pdf->text(166, 94.5, $str_mes_letras_diligencamiento);
    //$pdf->text(22, 97.5, $int_ano_diligenciamiento);
    //$pdf->text(60.5, 97.5, $str_ciudad_diligenciamiento);
   // $pdf->text(17, 144, $str_nomnre_tomador2);
   // $pdf->text(36, 156, $int_identificacion_solicitante);
   // $pdf->text(36, 163, $str_lugar_expedicion_solicitante);
   // $pdf->text(33, 171, $str_direccion_domicilio_solicitante);
   // $pdf->text(32, 179, $str_celular_solicitante);
   // $pdf->text(29, 187, $str_ciudad_diligenciamiento);





    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0009.jpg', 5, 5, 202);
    //$pdf->text(102, 67.5, $int_numerosolicitud);
    //$pdf->text(103, 89, $int_valor_total_deuda);
    //$pdf->text(103, 100, $str_ciudad_diligenciamiento);
    //$pdf->text(103, 111 , $int_cuota_mensual." - ".$int_cantidad_cuotas_descuento." CUOTAS");
    //$pdf->text(103, 122 , $autorizacion_libranza);

    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0010.jpg', 5, 5, 202);
    $pdf->SetFont('Arial', '', 8);
    $pdf->text(138, 94.5, $int_dia_diligenciamiento);
    $pdf->text(166, 94.5, $str_mes_letras_diligencamiento);
    $pdf->text(22, 97.5, $int_ano_diligenciamiento);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(60.5, 97.5, $str_ciudad_diligenciamiento);
    $pdf->SetFont('Arial', '', 10);





    $pdf->text(17, 143.5, $str_primer_nombre_solicitante." ".$str_segundo_nombre_solicitante." ".$str_primer_apellido_solicitante." ".$str_segundo_apellido_solicitante);
    $pdf->text(36, 155.5, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(36, 163, $str_lugar_expedicion_solicitante);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(33, 170, $str_direccion_domicilio_solicitante);
    $pdf->text(32, 179, $str_celular_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(29, 187, $str_ciudad_domicilio_solicitante."/".$str_departamento_domicilio_solicitante);
    $pdf->SetFont('Arial', '', 10);


    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0009.jpg', 5, 5, 202);
    //$pdf->text(102, 67.5, $int_numerosolicitud);
    //$pdf->text(103, 89, $int_valor_total_deuda);
    //$pdf->text(103, 100, $str_ciudad_diligenciamiento);
    //$pdf->text(103, 111 , $int_cuota_mensual." - ".$int_cantidad_cuotas_descuento." CUOTAS");
    //$pdf->text(103, 122 , $autorizacion_libranza);
    
    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0010.jpg', 5, 5, 202);
    $pdf->text(138, 94.5, $int_dia_diligenciamiento);
    $pdf->text(166, 94.5, $str_mes_letras_diligencamiento);
    $pdf->text(22, 97.5, $int_ano_diligenciamiento);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(60.5, 97.5, $str_ciudad_diligenciamiento);
    $pdf->SetFont('Arial', '', 10);





    $pdf->text(17, 143.5, $str_primer_nombre_solicitante." ".$str_segundo_nombre_solicitante." ".$str_primer_apellido_solicitante." ".$str_segundo_apellido_solicitante);
    $pdf->text(36, 155.5, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(36, 163, $str_lugar_expedicion_solicitante);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(33, 170, $str_direccion_domicilio_solicitante);
    $pdf->text(32, 179, $str_celular_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(29, 187, $str_ciudad_domicilio_solicitante."/".$str_departamento_domicilio_solicitante);
    $pdf->SetFont('Arial', '', 10);


    

    //$pdf->AddPage();
    //$pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0011.jpg', 5, 5, 202);
    //$pdf->SetFont('Arial', '', 7);
//    $pdf->text(30, 44.5, $str_ciudad_diligenciamiento);
    //$pdf->SetFont('Arial', '', 10);
    //$pdf->text(95, 44.5, $int_dia_diligenciamiento);
    //$pdf->text(130, 44.5, $int_mes_diligenciamiento);
    //$pdf->text(170, 44.5, $int_ano_diligenciamiento);


    //$pdf->text(22, 123, $str_nombre_tomador2);
    //$pdf->text(100, 127, $int_identificacion_solicitante);
    //$pdf->SetFont('Arial', '', 7);
    //$pdf->text(17, 130, $str_lugar_expedicion_solicitante);
    //$pdf->SetFont('Arial', '', 7);
    //$pdf->text(124, 130, $str_ciudad_domicilio_solicitante);
    //$pdf->text(34, 132.5, $int_numero_cuenta_giro);
    //$pdf->text(31, 136, $str_entidad_cuenta_giro);
    //$pdf->text(88, 136, $str_tipo_cuenta_giro);
    //$pdf->SetFont('Arial', '', 7);
    //$pdf->text(125, 178.5, $str_ciudad_diligenciamiento);
    //$pdf->SetFont('Arial', '', 10);
    //$pdf->text(22, 182, $int_dia_diligenciamiento);
    //$pdf->text(120, 182, $str_mes_letras_diligencamiento);
    //$pdf->text(16, 185, $int_ano_diligenciamiento);



    //$pdf->text(17, 212, $str_primer_nombre_solicitante." ".$str_segundo_nombre_solicitante." ".$str_primer_apellido_solicitante." ".$str_segundo_apellido_solicitante);
    ///$pdf->text(36, 219.5, $int_identificacion_solicitante);
    //$pdf->SetFont('Arial', '', 7);
    //$pdf->text(36, 227, $str_lugar_expedicion_solicitante);
    //$pdf->SetFont('Arial', '', 10);
    //$pdf->text(33, 235, $str_direccion_domicilio_solicitante);
    //$pdf->text(32, 247, $str_celular_solicitante);
    //$pdf->SetFont('Arial', '', 7);
    //$pdf->text(29, 255, $str_ciudad_domicilio_solicitante."/".$str_departamento_domicilio_solicitante);
    //$pdf->SetFont('Arial', '', 10);

    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 12-22/COSTOS_page-0001.jpg', 5, 5, 202);
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetFont('Arial', '', 9);
    $pdf->text(32, 46, $str_ciudad_diligenciamiento);
    $pdf->text(100, 46, $int_dia_diligenciamiento);
    $pdf->text(135, 46, $int_mes_diligenciamiento);
    $pdf->text(174, 46, $int_ano_diligenciamiento); 
    $pdf->text(25, 129, $str_nombre_tomador2);
    $pdf->text(85, 134, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(147, 134, $str_lugar_expedicion_solicitante);
    $pdf->text(42, 137.5, $str_ciudad_domicilio_solicitante);
    //$pdf->text(34, 132.5, $int_numero_cuenta_giro);
    //$pdf->text(31, 136, $str_entidad_cuenta_giro);
    //$pdf->text(88, 136, $str_tipo_cuenta_giro);
    $pdf->text(105, 177.5, $str_ciudad_diligenciamiento);
    $pdf->SetFont('Arial', '', 9);
    $pdf->text(30, 182, $int_dia_diligenciamiento);
    $pdf->text(75, 182, $str_mes_letras_diligencamiento);
    $pdf->text(130, 182, $int_ano_diligenciamiento);
    $pdf->SetFont('Arial', '', 8);
    $pdf->text(21, 212.3, $str_primer_nombre_solicitante." ".$str_segundo_nombre_solicitante." ".$str_primer_apellido_solicitante." ".$str_segundo_apellido_solicitante);
    $pdf->text(38, 225, $int_identificacion_solicitante);
    $pdf->text(38, 232.2, $str_lugar_expedicion_solicitante);
    $pdf->text(36, 240.5, $str_direccion_domicilio_solicitante);
    $pdf->text(34, 248.5, $str_celular_solicitante);
    $pdf->text(31.5, 256, $str_ciudad_domicilio_solicitante."/".$str_departamento_domicilio_solicitante);
    $pdf->SetFont('Arial', '', 10);
    $pdf->AddPage();
    $pdf->AddPage();
    //PLANTILLA 1




    //$pdf->AddPage();
    //$pdf->Image('./plantillas//Fianti_Agosto_2021/Fianti_Agosto_2021-14.png', 5, 5, 202);
    //$pdf->text(24, 75.9, $str_nomnre_tomador2);
    //$pdf->text(60, 84, $int_identificacion_solicitante);
    //$pdf->text(148, 84, $str_lugar_expedicion_solicitante);

    //$pdf->text(108, 91.4, $int_cantidad_cuotas_descuento);
    //$pdf->text(33, 98.4, $int_cuota_descuento);
    //$pdf->text(17, 105.4, $int_valor_total_deuda);
    //$pdf->text(75, 113.4, $int_numero_libranza);
    //$pdf->text(25, 120.6, $int_dia_compromiso_adquirido);
    //$pdf->text(75, 120.6, $str_mes_letras_compromiso_adquirido);
    //$pdf->text(140, 120.6, $int_ano_compromiso_adquirido);

    //$pdf->text(17, 150.6, $int_dia_diligenciamiento);
    //$pdf->text(83, 150.6, $str_mes_letras_diligencamiento);
    //$pdf->text(143, 150.6, $int_ano_diligenciamiento);



    //$pdf->text(17, 190.5, $str_primer_nombre_solicitante." ".$str_segundo_nombre_solicitante." ".$str_primer_apellido_solicitante." ".$str_segundo_apellido_solicitante);
    //$pdf->text(36, 202.5,  $int_identificacion_solicitante);
    //$pdf->text(36, 209.5, $str_lugar_expedicion_solicitante);
    //$pdf->text(33, 217.5, $str_direccion_domicilio_solicitante);
    //$pdf->text(32, 225.5, $str_celular_solicitante);
    //$pdf->text(29, 233.5, $str_ciudad_domicilio_solicitante."/".$str_departamento_domicilio_solicitante);

    $pdf->AddPage();

    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0017.jpg', 5, 5, 202);
    $pdf->text(35, 63, $str_fecha_diligenciamiento);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(115, 63, $str_ciudad_diligenciamiento);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetFont('Arial', '', 8);
    $pdf->text(70, 84, $str_primer_nombre_solicitante." ".$str_segundo_nombre_solicitante." ".$str_primer_apellido_solicitante." ".$str_segundo_apellido_solicitante);
    $pdf->SetFont('Arial', '', 10);
    //$pdf->text(150, 79, $int_numero_poliza);
    $pdf->text(150, 84, $int_identificacion_solicitante);


    //$pdf->text(30, 119, $str_nombreben1);
    //$pdf->text(82, 119, $str_tipoidenben1);
    //$pdf->text(103, 119, $int_numidenben1);
    //$pdf->text(124, 119, $str_parenben1);
    //$pdf->text(157, 119, $str_porcben1);

    //$pdf->text(30, 124, $str_nombreben2);
    //$pdf->text(82, 124, $str_tipoidenben2);
    //$pdf->text(103, 124, $int_numidenben2);
    //$pdf->text(124, 124, $str_parenben2);
    //$pdf->text(157, 124, $str_porcben2);

    //$pdf->text(30, 129, $str_nombreben3);
    //$pdf->text(82, 129, $str_tipoidenben3);
    //$pdf->text(103, 129, $int_numidenben3);
    //$pdf->text(124, 129, $str_parenben3);
    //$pdf->text(157, 129, $str_porcben3);

    //$pdf->text(30, 134, $str_nombreben4);
    //$pdf->text(82, 134, $str_tipoidenben4);
    //$pdf->text(103, 134, $int_numidenben4);
    //$pdf->text(124, 134, $str_parenben4);
    //$pdf->text(157, 134, $str_porcben4);

    //$pdf->text(30, 139, $str_nombreben5);
    //$pdf->text(82, 139, $str_tipoidenben5);
    //$pdf->text(103, 139, $int_numidenben5);
    //$pdf->text(124, 139, $str_parenben5);
    //$pdf->text(157, 139, $str_porcben5);

    //$pdf->text(30, 144, $str_nombreben6);
    //$pdf->text(82, 144, $str_tipoidenben6);
    //$pdf->text(103, 144, $int_numidenben6);
    //$pdf->text(124, 144, $str_parenben6);
    //$pdf->text(157, 144, $str_porcben6);

    //$pdf->text(30, 149, $str_nombreben7);
    //$pdf->text(82, 149, $str_tipoidenben7);
    //$pdf->text(103, 149, $int_numidenben7);
    //$pdf->text(124, 149, $str_parenben7);
    //$pdf->text(157, 149, $str_porcben7);

    //$pdf->text(30, 154, $str_nombreben8);
    //$pdf->text(82, 154, $str_tipoidenben8);
    //$pdf->text(103, 154, $int_numidenben8);
    //$pdf->text(124, 154, $str_parenben8);
    //$pdf->text(157, 154, $str_porcben8);

    //$pdf->text(30, 159.5, $str_nombreben9);
    //$pdf->text(82, 159.5, $str_tipoidenben9);
    //$pdf->text(103, 159.5, $int_numidenben9);
    //$pdf->text(124, 159.5, $str_parenben9);
    //$pdf->text(157, 159.5, $str_porcben9);

    //$pdf->text(30, 165, $str_nombreben10);
    //$pdf->text(82, 165, $str_tipoidenben10);
    //$pdf->text(103, 165, $int_numidenben10);
    //$pdf->text(124, 165, $str_parenben10);
    //$pdf->text(157, 165, $str_porcben10);





    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0018.jpg', 5, 5, 202);
    //if ($bool_estudiosgustos2==true)
    //{
      //  $pdf->text(91, 126.5, "X");
    //}else{
      //  $pdf->text(110, 126.5, "X");    
    //}
    
    
    //if ($bool_ofrecimientobienesaseguradora2==true)
    //{
      //  $pdf->text(156, 131, "X");
    //}else{
      //  $pdf->text(170, 131, "X");  
    //}

    //if ($bool_ofrecimientobienesmarcacompartida2==true)
    //{
      //  $pdf->text(156, 131, "X");
    //}else{
      //  $pdf->text(170, 131, "X");  
    //}

    

    $pdf->text(17, 243, $str_nombre_tomador2);
    $pdf->text(25, 253, $int_identificacion_solicitante);

    

    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0015.jpg', 5, 5, 202);

    //CAMBIAR TAMAÑO FUENTE
    $pdf->SetFont('Arial', '', 5);
    $pdf->text(141, 35, $str_ciudad_diligenciamiento);
    $pdf->SetFont('Arial', '', 8);
    $pdf->text(172, 35, $int_dia_diligenciamiento);
    $pdf->text(177, 35, $int_mes_diligenciamiento);
    $pdf->text(183, 35, $int_ano_diligenciamiento);

    //$pdf->text(16, 63, $str_nomnre_tomador2);
    //$pdf->text(147, 63, $int_nit);
    $pdf->text(175, 63, $int_poliza_matriz);
    $pdf->text(16, 63, "Kredit Plus S.A.S.");
    $pdf->text(147, 63, "900.387.878-5");    
    $pdf->text(16, 75, $str_primer_nombre_solicitante);
    $pdf->text(56, 75, $str_segundo_nombre_solicitante);
    $pdf->text(100, 75, $str_primer_apellido_solicitante);
    $pdf->text(145, 75, $str_segundo_apellido_solicitante);

    $pdf->SetFont('Arial', '', 5);
	$pdf->text(178, 75, $str_ano_fecha_nacimiento_solicitante);
    $pdf->SetFont('Arial', '', 10);
	$pdf->text(183, 75, $str_mes_fecha_nacimiento_solicitante);
	$pdf->text(189, 75, $str_dia_fecha_nacimiento_solicitante);

	//agregar condicional tipo de identificacion
	if ($str_tipoidentificacion_solicitante=="CC")
	{
		$pdf->text(18, 83, "X");
	}else if ($str_tipoidentificacion_solicitante=="CE")
	{
		$pdf->text(23, 83, "X");	
	}
	
	
	$pdf->text(30, 83, $int_identificacion_solicitante);
	$pdf->text(87, 83, $str_dia_expedicion_solicitante);
	$pdf->text(93, 83, $str_mes_expedicion_solicitante);
	$pdf->text(99, 83, $str_ano_expedicion_solicitante);

	if ($str_genero_solicitante=="F")
	{
		$pdf->text(108.7, 83, "x");	
	}
	else
	{
		$pdf->text(113, 83, "x");
	}
	
  $pdf->SetFont('Arial', '', 7);
	$pdf->text(117, 83, $str_ciudad_nacimiento_solicitante);
  $pdf->SetFont('Arial', '', 10);
	$pdf->text(155, 83, $str_departmento_nacimiento_solicitante);
  $pdf->SetFont('Arial', '', 6);
	$pdf->text(15, 90, $str_direccion_domicilio_solicitante);
  $pdf->SetFont('Arial', '', 6);
	$pdf->text(75, 90, $str_ciudad_domicilio_solicitante);
  $pdf->SetFont('Arial', '', 10);
	$pdf->text(107, 90, $str_departamento_domicilio_solicitante);
  $pdf->SetFont('Arial', '', 8);
	$pdf->text(145, 90, $str_email_solicitante);
  $pdf->SetFont('Arial', '', 10);
	$pdf->text(15, 97, $str_ocupacion_solicitante);
	$pdf->text(88, 97, $str_celular_solicitante);
	//$pdf->text(150, 97, $str_estatura_solicitante);
	//$pdf->text(179, 97, $str_peso_solicitante);


	//$pdf->text(174, 108.8, $int_auxilio_gastos_exequiales);
	//$pdf->text(174, 113, $int_incapacidad_total);
	//$pdf->text(174, 117.2, $int_auxilio_gastos_exequiales);

    //if ($int_periocidad_pago_prima==1)
    //{
      //  $pdf->text(71, 120.2, "X");
    //}else if ($int_periocidad_pago_prima==2)
    //{
      //  $pdf->text(97, 120.2, "X");
    //}else if ($int_periocidad_pago_prima==3)
    //{
      //  $pdf->text(123, 120.2, "X");
    //}else if ($int_periocidad_pago_prima==4)
    //{
      //  $pdf->text(149.2, 120.2, "X");
    //}else if ($int_periocidad_pago_prima==5)
    //{
      //  $pdf->text(169, 120.2, "X");
    //}else if ($int_periocidad_pago_prima==6)
    //{
      //  $pdf->text(188, 120.2, "X");
    //}
	
	
	
	
	
	

    //if ($int_valor_asegurado==1)
    //{
      //  $pdf->text(72, 123.2, "X");
    //}else{
      //  $pdf->text(117, 123.2, "X");    
    //}

	
	

	//$pdf->text(99, 127.2, $int_dia_inicio_vigencia);
	//$pdf->text(105, 127.2, $int_mes_inicio_vigencia);
	//$pdf->text(110, 127.2, $int_ano_inicio_vigencia);
	//$pdf->text(173, 127.2, $int_valor_total_prima);

    //if ($bool_continuidad_cobertura==true)
    //{
        //$pdf->text(106, 131.2, "X");
    //}else{
      //  $pdf->text(114, 131.2, "X");    
    //}
	
	
	//$pdf->text(142, 131.2, $str_compania_continuidad_cobertura);


	//$pdf->text(17, 138.5, $int_cuota_mensual);
	//$pdf->text(40, 138.5, $int_plazo_meses);
	//$pdf->text(68, 138.5, $int_valor_inicial);
	//$pdf->text(95, 138.5, $int_prima_mensual);
	//$pdf->text(118, 138.5, $int_dia_inicio_vigencia);
	//$pdf->text(126, 138.5, $int_mes_inicio_vigencia);
	//$pdf->text(135, 138.5, $int_ano_inicio_vigencia);
	//CONDICION TIENE CREDITO

    //if ($bool_tiene_otro_credito==true)
    //{
      //  $pdf->text(153, 138.5, "X");
    //}else{
      //  $pdf->text(163, 138.5, "X");
    //}
	
	


	//$pdf->text(169, 138.5, $int_monto_credito_anterior);



	//$pdf->text(18, 162, $str_nombreben1_titulo_gratuito);
	//$pdf->text(121, 162, $str_parenben1_titulo_gratuito);
	//$pdf->text(173, 162, $str_porcben1_titulo_gratuito);


	//$pdf->text(18, 166.2, $str_nombreben2_titulo_gratuito);
	//$pdf->text(121, 166.2, $str_parenben2_titulo_gratuito);
	//$pdf->text(173, 166.2, $str_porcben2_titulo_gratuito);


	//$pdf->text(18, 170.2, $str_nombreben3_titulo_gratuito);
	//$pdf->text(121, 170.2, $str_parenben3_titulo_gratuito);
	//$pdf->text(173, 170.2, $str_porcben3_titulo_gratuito);

	//$pdf->text(18, 174.2, $str_nombreben4_titulo_gratuito);
	//$pdf->text(121, 174.2, $str_parenben4_titulo_gratuito);
	//$pdf->text(173, 174.2, $str_porcben4_titulo_gratuito);

    //if ($bool_condicion1_declaracion_asegurabilidad==true)
    //{
      //  $pdf->text(182, 189, "X");
    //}else{
      //  $pdf->text(191, 189, "X");    
    //}
	
	
    //if ($bool_condicion2_declaracion_asegurabilidad==true)
    //{
	  // $pdf->text(182, 192, "X");
    //}else{
      //  $pdf->text(191, 192, "X");    
    //}
	
    //if ($bool_condicion3_declaracion_asegurabilidad==true)
    //{
	  // $pdf->text(182, 195, "X");
    //}else{
      //  $pdf->text(191, 195, "X");    
    //}
	

    //if ($bool_condicion4_declaracion_asegurabilidad==true)
    //{
	   //$pdf->text(182, 198.8, "X");
    //}else{
        //$pdf->text(191, 198.8, "X");    
    //}
	
    //if ($bool_condicion5_declaracion_asegurabilidad==true)
    //{
      //  $pdf->text(182, 202, "X");
    //}else{
      //  $pdf->text(191, 202, "X");    
    //}
	
	
	
    //if ($bool_condicion6_declaracion_asegurabilidad==true)
    //{
      //  $pdf->text(182, 207, "X");
    //}else{
      //  $pdf->text(191, 207, "X");     
    //} 
	
	

    //if ($bool_condicion7_declaracion_asegurabilidad==true)
    //{
	  // $pdf->text(182, 215, "X");
    //}else{
      // $pdf->text(191, 215, "X");    
    //}
	

    //if ($bool_condicion8_declaracion_asegurabilidad==true)
    //{
      //  $pdf->text(182, 219, "X");    
    //}else{
      //  $pdf->text(191, 219, "X");    
    //}
	
	


	//$pdf->text(51, 225.5, $str_nombre_enfermedad_declaracion_asegurabilidad);
	
	//$pdf->text(90, 228.5, $str_tratamientos_medicos_declaracion_asegurabilidad);
	//$pdf->text(150, 228.5, $str_fecha_diagnostico_declaracion_asegurabilidad);


	//$pdf->text(37, 232, $str_secuelas_declaracion_asegurabilidad);
	//$pdf->text(157, 232, $str_tratamiento_actual_declaracion_asegurabilidad);


	//$pdf->text(50, 236, $str_observaciones_declaracion_asegurabilidad);
	

    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0016.jpg', 5, 5, 202);
    //if ($bool_estudiosgustos1==true)
    //{
      //  $pdf->text(62, 181.8, "X");
    //}
    //else
    //{
      //  $pdf->text(72, 181.8, "X");
    //}
    
	
    //if ($bool_ofrecimientobienesaseguradora1==true)
    //{
      //  $pdf->text(106, 184, "X");
    
    //}
    //else
    //{
      //  $pdf->text(117, 184, "X");
    //}


    //if ($bool_ofrecimientobienesmarcacompartida1==true)
    //{
      //  $pdf->text(152, 187, "X");
    
    //}
    //else
    //{
      //  $pdf->text(163, 187, "X");
    //}
    $pdf->SetFont('Arial', '', 6);
    $pdf->text(143, 247.5, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 10);

    $pdf->AddPage();
    //PLANTILLA 2
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0014.jpg', 5, 5, 202);
    $pdf->text(24, 76, $str_primer_nombre_solicitante." ".$str_segundo_nombre_solicitante." ".$str_primer_apellido_solicitante." ".$str_segundo_apellido_solicitante);
    $pdf->text(60, 84, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(150, 84, $str_lugar_expedicion_solicitante);
    $pdf->SetFont('Arial', '', 10);
    //$pdf->text(110, 91, $str_lugar_expedicion_solicitante);
    $pdf->text(17, 150, $int_dia_diligenciamiento);
    $pdf->text(85, 150, $str_mes_letras_diligencamiento);
    $pdf->text(145, 150, $int_ano_diligenciamiento);

    $pdf->text(17, 191, $str_primer_nombre_solicitante." ".$str_segundo_nombre_solicitante." ".$str_primer_apellido_solicitante." ".$str_segundo_apellido_solicitante);
    $pdf->text(38, 203, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(38, 210, $str_lugar_expedicion_solicitante);
    $pdf->SetFont('Arial', '', 10);
    $pdf->text(35, 218, $str_direccion_domicilio_solicitante);
    $pdf->text(32, 226.5, $str_celular_solicitante);
    $pdf->text(31, 234.5, $str_ciudad_domicilio_solicitante."/".$str_departamento_domicilio_solicitante);


    

    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0015.jpg', 5, 5, 202);

    //CAMBIAR TAMAÑO FUENTE
    $pdf->SetFont('Arial', '', 5);
    $pdf->text(141, 35, $str_ciudad_diligenciamiento);
    $pdf->SetFont('Arial', '', 8);
    $pdf->text(172, 35, $int_dia_diligenciamiento);
    $pdf->text(177, 35, $int_mes_diligenciamiento);
    $pdf->text(183, 35, $int_ano_diligenciamiento);

    //$pdf->text(16, 63, $str_nomnre_tomador2);
    //$pdf->text(147, 63, $int_nit);
    //$pdf->text(175, 63, $int_poliza_matriz);
    $pdf->text(16, 63, "Kredit Plus S.A.S.");
    $pdf->text(147, 63, "900.387.878-5");    
    $pdf->text(16, 75, $str_primer_nombre_solicitante);
    $pdf->text(56, 75, $str_segundo_nombre_solicitante);
    $pdf->text(100, 75, $str_primer_apellido_solicitante);
    $pdf->text(145, 75, $str_segundo_apellido_solicitante);

    $pdf->SetFont('Arial', '', 5);
	$pdf->text(178, 75, $str_ano_fecha_nacimiento_solicitante);
  $pdf->SetFont('Arial', '', 10);
	$pdf->text(183, 75, $str_mes_fecha_nacimiento_solicitante);
	$pdf->text(189, 75, $str_dia_fecha_nacimiento_solicitante);

	//agregar condicional tipo de identificacion
	if ($str_tipoidentificacion_solicitante=="CC")
	{
		$pdf->text(18, 83, "X");
	}else if ($str_tipoidentificacion_solicitante=="CE")
	{
		$pdf->text(23, 83, "X");	
	}
	
	
	$pdf->text(30, 83, $int_identificacion_solicitante);
	$pdf->text(87, 83, $str_dia_expedicion_solicitante);
	$pdf->text(93, 83, $str_mes_expedicion_solicitante);
	$pdf->text(99, 83, $str_ano_expedicion_solicitante);

	if ($str_genero_solicitante=="F")
	{
		$pdf->text(108.7, 83, "x");	
	}
	else
	{
		$pdf->text(113, 83, "x");
	}
	
  $pdf->SetFont('Arial', '', 7);
	$pdf->text(117, 83, $str_ciudad_nacimiento_solicitante);
  $pdf->SetFont('Arial', '', 10);
	$pdf->text(155, 83, $str_departmento_nacimiento_solicitante);
  $pdf->SetFont('Arial', '', 6);
	$pdf->text(15, 90, $str_direccion_domicilio_solicitante);
  $pdf->SetFont('Arial', '', 6);
	$pdf->text(75, 90, $str_ciudad_domicilio_solicitante);
  $pdf->SetFont('Arial', '', 10);
	$pdf->text(107, 90, $str_departamento_domicilio_solicitante);
  $pdf->SetFont('Arial', '', 8);
	$pdf->text(145, 90, $str_email_solicitante);
  $pdf->SetFont('Arial', '', 10);
	$pdf->text(15, 97, $str_ocupacion_solicitante);
	$pdf->text(88, 97, $str_celular_solicitante);
	//$pdf->text(150, 97, $str_estatura_solicitante);
	//$pdf->text(179, 97, $str_peso_solicitante);


	//$pdf->text(174, 108.8, $int_auxilio_gastos_exequiales);
	//$pdf->text(174, 113, $int_incapacidad_total);
	//$pdf->text(174, 117.2, $int_auxilio_gastos_exequiales);

    //if ($int_periocidad_pago_prima==1)
    //{
      //  $pdf->text(71, 120.2, "X");
    //}else if ($int_periocidad_pago_prima==2)
    //{
      //  $pdf->text(97, 120.2, "X");
    //}else if ($int_periocidad_pago_prima==3)
    //{
      //  $pdf->text(123, 120.2, "X");
    //}else if ($int_periocidad_pago_prima==4)
    //{
      //  $pdf->text(149.2, 120.2, "X");
    //}else if ($int_periocidad_pago_prima==5)
    //{
      //  $pdf->text(169, 120.2, "X");
    //}else if ($int_periocidad_pago_prima==6)
    //{
      //  $pdf->text(188, 120.2, "X");
    //}
	
	
	
	
	
	

    //if ($int_valor_asegurado==1)
    //{
      //  $pdf->text(72, 123.2, "X");
    //}else{
      //  $pdf->text(117, 123.2, "X");    
    //}

	
	

	//$pdf->text(99, 127.2, $int_dia_inicio_vigencia);
	//$pdf->text(105, 127.2, $int_mes_inicio_vigencia);
	//$pdf->text(110, 127.2, $int_ano_inicio_vigencia);
	//$pdf->text(173, 127.2, $int_valor_total_prima);

    //if ($bool_continuidad_cobertura==true)
    //{
        //$pdf->text(106, 131.2, "X");
    //}else{
      //  $pdf->text(114, 131.2, "X");    
    //}
	
	
	//$pdf->text(142, 131.2, $str_compania_continuidad_cobertura);


	//$pdf->text(17, 138.5, $int_cuota_mensual);
	//$pdf->text(40, 138.5, $int_plazo_meses);
	//$pdf->text(68, 138.5, $int_valor_inicial);
	//$pdf->text(95, 138.5, $int_prima_mensual);
	//$pdf->text(118, 138.5, $int_dia_inicio_vigencia);
	//$pdf->text(126, 138.5, $int_mes_inicio_vigencia);
	//$pdf->text(135, 138.5, $int_ano_inicio_vigencia);
	//CONDICION TIENE CREDITO

    //if ($bool_tiene_otro_credito==true)
    //{
      //  $pdf->text(153, 138.5, "X");
    //}else{
      //  $pdf->text(163, 138.5, "X");
    //}
	
	


	//$pdf->text(169, 138.5, $int_monto_credito_anterior);



	//$pdf->text(18, 162, $str_nombreben1_titulo_gratuito);
	//$pdf->text(121, 162, $str_parenben1_titulo_gratuito);
	//$pdf->text(173, 162, $str_porcben1_titulo_gratuito);


	//$pdf->text(18, 166.2, $str_nombreben2_titulo_gratuito);
	//$pdf->text(121, 166.2, $str_parenben2_titulo_gratuito);
	//$pdf->text(173, 166.2, $str_porcben2_titulo_gratuito);


	//$pdf->text(18, 170.2, $str_nombreben3_titulo_gratuito);
	//$pdf->text(121, 170.2, $str_parenben3_titulo_gratuito);
	//$pdf->text(173, 170.2, $str_porcben3_titulo_gratuito);

	//$pdf->text(18, 174.2, $str_nombreben4_titulo_gratuito);
	//$pdf->text(121, 174.2, $str_parenben4_titulo_gratuito);
	//$pdf->text(173, 174.2, $str_porcben4_titulo_gratuito);

    //if ($bool_condicion1_declaracion_asegurabilidad==true)
    //{
      //  $pdf->text(182, 189, "X");
    //}else{
      //  $pdf->text(191, 189, "X");    
    //}
	
	
    //if ($bool_condicion2_declaracion_asegurabilidad==true)
    //{
	  // $pdf->text(182, 192, "X");
    //}else{
      //  $pdf->text(191, 192, "X");    
    //}
	
    //if ($bool_condicion3_declaracion_asegurabilidad==true)
    //{
	  // $pdf->text(182, 195, "X");
    //}else{
      //  $pdf->text(191, 195, "X");    
    //}
	

    //if ($bool_condicion4_declaracion_asegurabilidad==true)
    //{
	   //$pdf->text(182, 198.8, "X");
    //}else{
        //$pdf->text(191, 198.8, "X");    
    //}
	
    //if ($bool_condicion5_declaracion_asegurabilidad==true)
    //{
      //  $pdf->text(182, 202, "X");
    //}else{
      //  $pdf->text(191, 202, "X");    
    //}
	
	
	
    //if ($bool_condicion6_declaracion_asegurabilidad==true)
    //{
      //  $pdf->text(182, 207, "X");
    //}else{
      //  $pdf->text(191, 207, "X");     
    //} 
	
	

    //if ($bool_condicion7_declaracion_asegurabilidad==true)
    //{
	  // $pdf->text(182, 215, "X");
    //}else{
      // $pdf->text(191, 215, "X");    
    //}
	

    //if ($bool_condicion8_declaracion_asegurabilidad==true)
    //{
      //  $pdf->text(182, 219, "X");    
    //}else{
      //  $pdf->text(191, 219, "X");    
    //}
	
	


	//$pdf->text(51, 225.5, $str_nombre_enfermedad_declaracion_asegurabilidad);
	
	//$pdf->text(90, 228.5, $str_tratamientos_medicos_declaracion_asegurabilidad);
	//$pdf->text(150, 228.5, $str_fecha_diagnostico_declaracion_asegurabilidad);


	//$pdf->text(37, 232, $str_secuelas_declaracion_asegurabilidad);
	//$pdf->text(157, 232, $str_tratamiento_actual_declaracion_asegurabilidad);


	//$pdf->text(50, 236, $str_observaciones_declaracion_asegurabilidad);
	

    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0016.jpg', 5, 5, 202);
    //if ($bool_estudiosgustos1==true)
    //{
      //  $pdf->text(62, 181.8, "X");
    //}
    //else
    //{
      //  $pdf->text(72, 181.8, "X");
    //}
    
	
    //if ($bool_ofrecimientobienesaseguradora1==true)
    //{
      //  $pdf->text(106, 184, "X");
    
    //}
    //else
    //{
      //  $pdf->text(117, 184, "X");
    //}


    //if ($bool_ofrecimientobienesmarcacompartida1==true)
    //{
      //  $pdf->text(152, 187, "X");
    
    //}
    //else
    //{
      //  $pdf->text(163, 187, "X");
    //}
    $pdf->SetFont('Arial', '', 6);
    $pdf->text(143, 247.5, $int_identificacion_solicitante);
    $pdf->SetFont('Arial', '', 10);




    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0012.jpg', 5, 5, 202);
    //switch ($int_tipo_novedad_reportar) {
      //  case '1': $pdf->text(85, 31, "X"); break;
     //   case '2': $pdf->text(118, 31, 'X'); break;
       // case '3': $pdf->text(145, 31, 'X'); break;
        
    //}
    $pdf->text(160, 57, $int_dia_diligenciamiento."/".$int_mes_diligenciamiento."/".$int_mes_diligenciamiento);

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
    $pdf->SetFont('Arial', '', 7);
    $pdf->text(16, 122, $str_ciudad_domicilio_solicitante);
    $pdf->SetFont('Arial', '', 10);
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

    //$pdf->text(17.2, 178.8, $int_valor_total_deuda);
    //$pdf->text(60, 178.8, $str_valor_total_prestamo);
    $pdf->text(145, 178.8, $int_numero_libranza);


    $pdf->text(17.2, 189, $int_cantidad_cuotas_descuento);
    $pdf->text(33, 189, $int_cuota_descuento);
    //$pdf->text(75, 189, $str_cuota_descuento_en_letra);


    $pdf->AddPage();
    $pdf->Image('./plantillas/FIANTI 04-22/FIANTI_page-0013.jpg', 5, 5, 202);

    ob_end_clean();
    $pdf->Output();
    //*/
?>