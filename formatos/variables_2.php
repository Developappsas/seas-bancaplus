<?php
  //VARIBALES PENDIENTES
  require ('../functions.php');
  $link = conectar();
  $query = "SELECT c.municipio as municipio_expedicion,c.departamento as departamento_expedicion,
  d.municipio as municipio_nacimiento,d.departamento as departamento_nacimiento,
  e.municipio as municipio_residencia,e.departamento as departamento_residencia,
  f.municipio as municipio_conyuge,e.departamento as departamento_conyuge,
  g.municipio as municipio_conyuge_nacimiento,g.departamento as departamento_conyuge_nacimiento,
  h.municipio as municipio_familiar,h.departamento as departamento_familiar,
  i.municipio as municipio_personal,i.departamento as departamento_personal,
  a.*,b.opcion_credito,b.opcion_cuota_cli,b.opcion_cuota_ccc,b.opcion_cuota_cmp,b.opcion_cuota_cso,
  b.nro_libranza,b.plazo,b.valor_credito,
  k.nombre as oficina,j.nombre as nombre_comercial,j.apellido as apellido_comercial
  FROM solicitud a 
  LEFT JOIN simulaciones b ON a.id_simulacion=b.id_simulacion 
  LEFT JOIN ciudades c ON c.cod_municipio=a.lugar_expedicion
  LEFT JOIN ciudades d ON d.cod_municipio=a.lugar_nacimiento
  LEFT JOIN ciudades e ON e.cod_municipio=a.ciudad
  LEFT JOIN ciudades f ON f.cod_municipio=a.conyugue_lugar_expedicion
  LEFT JOIN ciudades g ON g.cod_municipio=a.conyugue_lugar_nacimiento
  LEFT JOIN ciudades h ON h.cod_municipio=a.ciudad_familiar
  LEFT JOIN ciudades i ON i.cod_municipio=a.ciudad_personal 
  LEFT JOIN usuarios j ON j.id_usuario=b.id_comercial
  LEFT JOIN oficinas k ON k.id_oficina=b.id_oficina
  WHERE a.id_simulacion = '".$_GET["id_simulacion"]."'";    
  //echo $query;
  $respuesta = sqlsrv_query($link,$query);
  $fila = sqlsrv_fetch_array($respuesta);
  switch($fila["opcion_credito"])
  {
    case "CLI":	$int_cuota_mensual = $fila["opcion_cuota_cli"];
                $int_cuota_descuento=$fila["opcion_cuota_cli"];
          break;
    case "CCC":	$int_cuota_mensual = $fila["opcion_cuota_ccc"];
    $int_cuota_descuento=$fila["opcion_cuota_ccc"];
          
          break;
    case "CMP":	$int_cuota_mensual = $fila["opcion_cuota_cmp"];
    $int_cuota_descuento=$fila["opcion_cuota_cmp"];
          
          break;
    case "CSO":	$int_cuota_mensual = $fila["opcion_cuota_cso"];
    $int_cuota_descuento=$fila["opcion_cuota_cso"];
          
          break;
  }

  $int_cantidad_cuotas_descuento=$fila["plazo"];
  
    //$nombre = explode(" ", $fila['nombre']);

    //$str_primer_apellido_solicitante = $nombre[0];
    
    //$str_segundo_apellido_solicitante = $nombre[1];
    //if (count($nombre) >= 3) { $str_segundo_nombre_solicitante = $nombre[3]; }
    //if (count($nombre) == 3) {  $str_primer_nombre_solicitante = $nombre[2]; }

        //$numero_documento = $fila["cedula"];
        $str_nombre_oficina=$fila["oficina"];
        $str_nombre_comercial=$fila["nombre_comercial"];
        $str_apellido_comercial=$fila["apellido_comercial"];
        $str_primer_apellido_solicitante=$fila["apellido1"];
        $str_segundo_apellido_solicitante=$fila["apellido2"];
        $str_segundo_nombre_solicitante=$fila["nombre2"];
        $str_primer_nombre_solicitante=$fila["nombre1"];

        $str_inicial_primer_nombre=substr($str_primer_nombre_solicitante, 0, 1);
        $str_inicial_segundo_nombre=substr($str_segundo_nombre_solicitante, 0, 1);
        $str_inicial_primer_apellido=substr($str_primer_apellido_solicitante, 0, 1);
        $str_inicial_segundo_apellido=substr($str_segundo_apellido_solicitante, 0, 1);

        $str_iniciales_nombre=$str_inicial_primer_nombre.$str_inicial_segundo_nombre.$str_inicial_primer_apellido.$str_inicial_segundo_apellido;

        $str_fecha_nacimiento_solicitante=$fila["fecha_nacimiento"];
        $str_ano_fecha_nacimiento_solicitante=substr($str_fecha_nacimiento_solicitante, 0, 1).substr($str_fecha_nacimiento_solicitante, 1, 1).substr($str_fecha_nacimiento_solicitante, 2, 1).substr($str_fecha_nacimiento_solicitante, 3, 1);
        $str_mes_fecha_nacimiento_solicitante=substr($str_fecha_nacimiento_solicitante, 5, 1).substr($str_fecha_nacimiento_solicitante, 6, 1);
        $str_dia_fecha_nacimiento_solicitante=substr($str_fecha_nacimiento_solicitante, 8, 1).substr($str_fecha_nacimiento_solicitante, 9, 1);

        
  

    
    $int_numero_poliza='';    
    $int_numerosolicitud = '';

    $int_identificacion_solicitante=$fila["cedula"];
    if ($fila["tipo_documento"]=="CEDULA")
    {$str_tipoidentificacion_solicitante = 'CC';}
    else if ($fila["tipo_documento"]=="REGISTRO CIVIL")
    {$str_tipoidentificacion_solicitante = 'RC';}
    else if ($fila["tipo_documento"]=="TARJETA DE IDENTIDAD")
    {$str_tipoidentificacion_solicitante = 'TI';}
    else if ($fila["tipo_documento"]=="CEDULA DE EXTRANJERIA")
    {$str_tipoidentificacion_solicitante = 'CE';}
    
    

    $str_nombre_tomador=$str_primer_apellido_solicitante." ".$str_segundo_apellido_solicitante." ".$str_primer_nombre_solicitante." ".$str_segundo_nombre_solicitante;
    $str_nombre_tomador2=$str_primer_nombre_solicitante." ".$str_segundo_nombre_solicitante." ".$str_primer_apellido_solicitante." ".$str_segundo_apellido_solicitante;
    $str_fecha_expedicion_solicitante=$fila["fecha_expedicion"];
    $str_ano_expedicion_solicitante=substr($str_fecha_expedicion_solicitante, 0, 1).substr($str_fecha_expedicion_solicitante, 1, 1).substr($str_fecha_expedicion_solicitante, 2, 1).substr($str_fecha_expedicion_solicitante, 3, 1);
    $str_mes_expedicion_solicitante=substr($str_fecha_expedicion_solicitante, 5, 1).substr($str_fecha_expedicion_solicitante, 6, 1);
    $str_dia_expedicion_solicitante=substr($str_fecha_expedicion_solicitante, 8, 1).substr($str_fecha_expedicion_solicitante, 9, 1);


  


    $str_genero_solicitante=$fila["sexo"];
    $str_ciudad_nacimiento_solicitante=$fila["municipio_nacimiento"];
    $str_departmento_nacimiento_solicitante=$fila["departamento_nacimiento"];

    $str_direccion_domicilio_solicitante=$fila["direccion"];
    $str_ciudad_domicilio_solicitante=$fila["municipio_residencia"];

    $str_departamento_domicilio_solicitante=$fila["departamento_residencia"];

    $str_email_solicitante=$fila["email"];

    if ($fila["ocupacion"]==1)
    {$str_ocupacion_solicitante="EMPLEADO";}
    
    else if ($fila["ocupacion"]==3)
    {$str_ocupacion_solicitante="INDEPENDIENTE";}
    else if ($fila["ocupacion"]==4)
    {$str_ocupacion_solicitante="AMA DE CASA";}
    else if ($fila["ocupacion"]==5)
    {$str_ocupacion_solicitante="PENSIONADO";}
    else if ($fila["ocupacion"]==6)
    {$str_ocupacion_solicitante="ESTUDIANTE";}
    else if ($fila["ocupacion"]==7)
    {$str_ocupacion_solicitante="RENTISTA CAPITAL";}
    

    $str_celular_solicitante=$fila["celular"];

    $str_estatura_solicitante="";

    $str_peso_solicitante="";

    


    
    $str_lugar_expedicion_solicitante = $fila["municipio_expedicion"];
    
    
    
    $str_lugar_nacimiento = $fila["municipio_nacimiento"];
    $str_estado_civil = $fila["estado_civil"];
    $str_pais_residencia = $fila["residencia_pais"];
    $str_ciudad_departamento = $fila["municipio_residencia"]."/".$fila["departamento_residencia"];
    $str_ciudad = $fila["municipio_residencia"];
    $str_tipo_vivienda = $fila["tipo_vivienda"];
    $str_estrato = $fila["residencia_estrato"];
    $str_nombre_arrendatario = $fila["arrendador_nombre"];
    $str_telefono_arrendatario = $fila["arrendador_nombre"];
    $str_barrio = $fila["residencia_barrio"];
    $str_direccion_residencia = $fila["direccion"];
    $str_telefono_residencial = $fila["tel_residencia"];
    $str_telefono_celular = $fila["celular"];
    $str_lugar_envio_correspondencia = $fila["lugar_correspondencia"];
    $str_correo = $fila["email"];
    $str_tiempo_residencia_anios = $fila["anios"];
    $str_tiempo_residencia_meses = $fila["meses"];
    $str_eps = $fila["eps"];
    $str_personas_acargo_adultos = $fila["personas_acargo_adultos"];
    $str_personas_acargo_menores = $fila["personas_acargo_menores"];
    $str_profesion = $fila["profesion"];
    
    if ($fila["nivel_estudios"]=="PRIMARIA")
    {$str_nivel_estudios = "Primaria";}
    else if ($fila["nivel_estudios"]=="BACHILLER")
    {$str_nivel_estudios = "Bachiller";}
    else if ($fila["nivel_estudios"]=="TECNICO")
    {$str_nivel_estudios = "Tecnico";}
    else if ($fila["nivel_estudios"]=="TECNOLOGO")
    {$str_nivel_estudios = "Tecnologo";}
    else if ($fila["nivel_estudios"]=="UNIVERSITARIO")
    {$str_nivel_estudios = "Universitario";}
    else if ($fila["nivel_estudios"]=="ESPECIALIZACION")
    {$str_nivel_estudios = "Especializacion";}
    else if ($fila["nivel_estudios"]=="MAESTRIA")
    {$str_nivel_estudios = "Maestria";}
    else if ($fila["nivel_estudios"]=="DOCTORADO")
    {$str_nivel_estudios = "Doctorado";}
    
    


    $bool_estudiosgustos1="true";
    $bool_ofrecimientobienesaseguradora1="true";
    $bool_ofrecimientobienesmarcacompartida1="true";


    $bool_estudiosgustos2="true";
    $bool_ofrecimientobienesaseguradora2="true";
    $bool_ofrecimientobienesmarcacompartida2="true";

    $str_nombreben1_titulo_gratuito="";
    $str_parenben1_titulo_gratuito="";
    $str_porcben1_titulo_gratuito="";

    $str_nombreben2_titulo_gratuito="";
    $str_parenben2_titulo_gratuito="";
    $str_porcben2_titulo_gratuito="";


    $str_nombreben3_titulo_gratuito="";
    $str_parenben3_titulo_gratuito="";
    $str_porcben3_titulo_gratuito="";


    $int_valor_total_prima="1312";
    $str_nombreben4_titulo_gratuito="";
    $str_parenben4_titulo_gratuito="";
    $str_porcben4_titulo_gratuito="";


    
    $int_plazo_meses=$fila["plazo"];
    $int_valor_inicial=$fila["valor_credito"];
    $int_prima_mensual="12321";

    $int_dia_inicio_vigencia="12321";
    $int_mes_inicio_vigencia="12321";
    $int_ano_inicio_vigencia="12321";

    $bool_tiene_otro_credito=false;
    $int_monto_credito_anterior="1221";

    
    $bool_continuidad_cobertura=true;
    $str_compania_continuidad_cobertura="SEGUROS DEL ESTADO";

    $int_periocidad_pago_prima=1;

    $int_auxilio_gastos_exequiales=1235;
    $int_incapacidad_total=1234;
    $int_muerte_cualquier_cosa=123;

    $int_valor_asegurado=1;
    $int_valor_total_prima=1123;



    $str_nombreben1="nombre ben1";
    $str_tipoidenben1="CC1";
    $int_numidenben1="num ben1";
    $str_parenben1="num ben1";
    $str_porcben1="porc ben1";

    $str_nombreben2="nombre ben1";
    $str_tipoidenben2="CC1";
    $int_numidenben2="num ben1";
    $str_parenben2="num ben1";
    $str_porcben2="porc ben1";

    $str_nombreben3="nombre ben1";
    $str_tipoidenben3="CC1";
    $int_numidenben3="num ben1";
    $str_parenben3="num ben1";
    $str_porcben3="porc ben1";

    $str_nombreben4="nombre ben1";
    $str_tipoidenben4="CC1";
    $int_numidenben4="num ben1";
    $str_parenben4="num ben1";
    $str_porcben4="porc ben1";

    $str_nombreben5="nombre ben1";
    $str_tipoidenben5="CC1";
    $int_numidenben5="num ben1";
    $str_parenben5="num ben1";
    $str_porcben5="porc ben1";

    $str_nombreben6="nombre ben1";
    $str_tipoidenben6="CC1";
    $int_numidenben6="num ben1";
    $str_parenben6="num ben1";
    $str_porcben6="porc ben1";

    $str_nombreben7="nombre ben1";
    $str_tipoidenben7="CC1";
    $int_numidenben7="num ben1";
    $str_parenben7="num ben1";
    $str_porcben7="porc ben1";

    $str_nombreben8="nombre ben1";
    $str_tipoidenben8="CC1";
    $int_numidenben8="num ben1";
    $str_parenben8="num ben1";
    $str_porcben8="porc ben1";

    $str_nombreben9="nombre ben1";
    $str_tipoidenben9="CC1";
    $int_numidenben9="num ben1";
    $str_parenben9="num ben1";
    $str_porcben9="porc ben1";

    $str_nombreben10="nombre ben1";
    $str_tipoidenben10="CC1";
    $int_numidenben10="num ben1";
    $str_parenben10="num ben1";
    $str_porcben10="porc ben1";

    $int_poliza_matriz="01888";
    $int_nit="809131233";
    

    $int_ano_diligenciamiento=date("Y");
    $int_mes_diligenciamiento=date("m");
    $int_dia_diligenciamiento=date("d");

    if ($int_mes_diligenciamiento=="01")
    {
    	$str_mes_letras_diligencamiento="Enero";
    }else if ($int_mes_diligenciamiento=="02")
    {
    	$str_mes_letras_diligencamiento="Febrero";
    }else if ($int_mes_diligenciamiento=="03")
    {
    	$str_mes_letras_diligencamiento="Marzo";
    }else if ($int_mes_diligenciamiento=="04")
    {
    	$str_mes_letras_diligencamiento="Abril";
    }else if ($int_mes_diligenciamiento=="05")
    {
    	$str_mes_letras_diligencamiento="Mayo";
    }else if ($int_mes_diligenciamiento=="06")
    {
    	$str_mes_letras_diligencamiento="Junio";
    }else if ($int_mes_diligenciamiento=="07")
    {
    	$str_mes_letras_diligencamiento="Julio";
    }else if ($int_mes_diligenciamiento=="08")
    {
    	$str_mes_letras_diligencamiento="Agosto";
    }else if ($int_mes_diligenciamiento=="09")
    {
    	$str_mes_letras_diligencamiento="Septiembre";
    }else if ($int_mes_diligenciamiento=="10")
    {
    	$str_mes_letras_diligencamiento="Octubre";
    }else if ($int_mes_diligenciamiento=="11")
    {
    	$str_mes_letras_diligencamiento="Noviembre";
    }else if ($int_mes_diligenciamiento=="12")
    {
    	$str_mes_letras_diligencamiento="Diciembre";
    }
    	

    $str_ciudad_diligenciamiento=$str_ciudad_departamento;

    $int_numero_cuenta_giro="11223344";
    $str_entidad_cuenta_giro="Entidad";
    $str_tipo_cuenta_giro="Tipo";
    $str_fecha_diligenciamiento=date("Y-m-d");
    
    $str_lugar_diligenciamiento="";

    $str_nombre_enfermedad_declaracion_asegurabilidad="observacion";
    $str_tratamientos_medicos_declaracion_asegurabilidad="observaciones";
    $str_fecha_diagnostico_declaracion_asegurabilidad="observaciones";
    $str_secuelas_declaracion_asegurabilidad="observaciones";
    $str_tratamiento_actual_declaracion_asegurabilidad="observaciones";
    $str_observaciones_declaracion_asegurabilidad="observaciones";
    $int_tipo_novedad_reportar=1;
    $bool_condicion1_declaracion_asegurabilidad=true;
    $bool_condicion2_declaracion_asegurabilidad=true;
    $bool_condicion3_declaracion_asegurabilidad=true;
    $bool_condicion4_declaracion_asegurabilidad=true;
    $bool_condicion5_declaracion_asegurabilidad=true;
    $bool_condicion6_declaracion_asegurabilidad=true;
    $bool_condicion7_declaracion_asegurabilidad=true;
    $bool_condicion8_declaracion_asegurabilidad=true;
    
    $str_cuota_descuento_en_letra="CUARENTA Y CINCO MIL PESOS";
    
    $int_valor_total_deuda=500000;
    $str_valor_total_prestamo="QUINIENTOS MIL PESOS";
    $int_numero_libranza=$fila["nro_libranza"];
    $int_valor_descuento_afiliacion="12000";
    $str_valor_descuento_afiliacion="DOCE MIL PESOS";
    $int_dia_compromiso_adquirido="05";
    $int_mes_compromiso_adquirido="11";
    $int_ano_compromiso_adquirido="2021";
    $int_telefono_replegal="12312321";
    $int_identificacion_replegal="123112321";
    $str_tipoidentificacion_replegal="CC";
    $str_nombre_replegal="PRUEBA REP LEGAL";
    $int_numero_afiliacion="";

       if ($int_mes_compromiso_adquirido=="01")
    {
    	$str_mes_letras_compromiso_adquirido="Enero";
    }else if ($int_mes_compromiso_adquirido=="02")
    {
    	$str_mes_letras_compromiso_adquirido="Febrero";
    }else if ($int_mes_compromiso_adquirido=="03")
    {
    	$str_mes_letras_compromiso_adquirido="Marzo";
    }else if ($int_mes_compromiso_adquirido=="04")
    {
    	$str_mes_letras_compromiso_adquirido="Abril";
    }else if ($int_mes_compromiso_adquirido=="05")
    {
    	$str_mes_letras_compromiso_adquirido="Mayo";
    }else if ($int_mes_compromiso_adquirido=="06")
    {
    	$str_mes_letras_compromiso_adquirido="Junio";
    }else if ($int_mes_compromiso_adquirido=="07")
    {
    	$str_mes_letras_compromiso_adquirido="Julio";
    }else if ($int_mes_compromiso_adquirido=="08")
    {
    	$str_mes_letras_compromiso_adquirido="Agosto";
    }else if ($int_mes_compromiso_adquirido=="09")
    {
    	$str_mes_letras_compromiso_adquirido="Septiembre";
    }else if ($int_mes_compromiso_adquirido=="10")
    {
    	$str_mes_letras_compromiso_adquirido="Octubre";
    }else if ($int_mes_compromiso_adquirido=="11")
    {
    	$str_mes_letras_compromiso_adquirido="Noviembre";
    }else if ($int_mes_compromiso_adquirido=="12")
    {
    	$str_mes_letras_compromiso_adquirido="Diciembre";
    }
    	


    

    /*Datos Conyuge*/
    $str_primer_nombre_conyuge = $fila["nombre_conyugue"];
    $str_segundo_nombre_conyuge = $fila["conyugue_nombre_2"];
    $str_primer_apellido_conyuge = $fila["conyugue_apellido_1"];
    $str_segundo_apellido_conyuge = $fila["conyugue_apellido_2"];
    $str_genero_conyuge=$fila["conyugue_sexo"];
    if ($fila["conyugue_tipo_documento"]=="CEDULA")
    {$str_tipo_documento_conyuge = 'CC';}
    else if ($fila["conyugue_tipo_documento"]=="REGISTRO CIVIL")
    {$str_tipo_documento_conyuge = 'RC';}
    else if ($fila["conyugue_tipo_documento"]=="TARJETA DE IDENTIDAD")
    {$str_tipo_documento_conyuge = 'TI';}
    else if ($fila["conyugue_tipo_documento"]=="CEDULA DE EXTRANJERIA")
    {$str_tipo_documento_conyuge = 'CE';}
    
    $str_numero_documento_conyuge = $fila["cedula_conyugue"];
    $str_fecha_expedicion_documento_conyuge = $fila["conyugue_fecha_expedicion"];

    $str_dia_fecha_expedicion_documento_conyuge=substr($str_fecha_expedicion_documento_conyuge, 8, 1).substr($str_fecha_expedicion_documento_conyuge, 9, 1);
    $str_mes_fecha_expedicion_documento_conyuge=substr($str_fecha_expedicion_documento_conyuge, 5, 1).substr($str_fecha_expedicion_documento_conyuge, 6, 1);
    $str_ano_fecha_expedicion_documento_conyuge=substr($str_fecha_expedicion_documento_conyuge, 0, 1).substr($str_fecha_expedicion_documento_conyuge, 1, 1).substr($str_fecha_expedicion_documento_conyuge, 2, 1).substr($str_fecha_expedicion_documento_conyuge, 3, 1);


    $str_lugar_expedicion_documento_conyuge = $fila["municipio_conyuge"];
    $str_fecha_nacimiento_conyuge = $fila["conyugue_fecha_nacimiento"];
    $str_dia_fecha_nacimiento_conyuge=substr($str_fecha_nacimiento_conyuge, 0, 1).substr($str_fecha_nacimiento_conyuge, 1, 1);
    $str_mes_fecha_nacimiento_conyuge=substr($str_fecha_nacimiento_conyuge, 3, 1).substr($str_fecha_nacimiento_conyuge, 4, 1);
    $str_ano_fecha_nacimiento_conyuge=substr($str_fecha_nacimiento_conyuge, 6, 1).substr($str_fecha_nacimiento_conyuge, 7, 1).substr($str_fecha_nacimiento_conyuge, 8, 1).substr($str_fecha_nacimiento_conyuge, 9, 1);

    $str_lugar_nacimiento_conyuge = $fila["municipio_conyuge_nacimiento"];
    $str_trabajo_conyuge = $fila["conyugue_nombre_empresa"];
    $str_ocupacion_conyuge = $fila["conyugue_ocupacion"];
    if ($fila["conyugue_dependencia"]=="SI")
    {
      $str_dependencia_economica_conyuge = true;
    }
    else if ($fila["conyugue_dependencia"]=="NO")
    {
      $str_dependencia_economica_conyuge = false;
    }
    
    $str_telefono_celular_conyuge = $fila["conyugue_celular"];

    /*Actividad Laboral*/
    $str_ocupacion = $fila["ocupacion"];
    $str_declara_renta = $fila["declara_renta"];
    $str_impacto_social_politica = $fila["funcionario_publico"];
    $str_maneja_recursos_publicos = $fila["recursos_publicos"];
    $str_personaje_publico = $fila["personaje_publico"];
    $str_actividad_economica = $fila["actividad_economica_principal"];
    $str_nombre_empresa_actual = $fila["nombre_empresa"];
    $str_cargo = $fila["cargo"];
    $str_fecha_vinculacion = $fila["fecha_vinculacion"];
    $str_dia_fecha_vinculacion =substr($str_fecha_vinculacion, 8, 1).substr($str_fecha_vinculacion, 9, 1);
    $str_mes_fecha_vinculacion =substr($str_fecha_vinculacion, 5, 1).substr($str_fecha_vinculacion, 6, 1);
    $str_ano_fecha_vinculacion =substr($str_fecha_vinculacion, 0, 1).substr($str_fecha_vinculacion, 1, 1).substr($str_fecha_vinculacion, 2, 1).substr($str_fecha_vinculacion, 3, 1);

    $str_direccion_lugar_trabajo = $fila["direccion_trabajo"];
    $str_ciudad_trabajo = $fila["ciudad_trabajo"];
    $str_nit_trabajo = $fila["nit_empresa"];
    $str_telefono_trabajo = $fila["telefono_trabajo"];
    $str_extension_trabajo = $fila["extension"];
    $str_tipo_empresa = $fila["tipo_empresa"];
    $str_actividad_economica_empresa = $fila["actividad_economica_empresa"];
    if ($fila["tipo_contrato"]==1){
      $str_tipo_contrato = 'Indefinido';
    }else if ($fila["tipo_contrato"]==4){
      $str_tipo_contrato = 'Contratista';
    }else if ($fila["tipo_contrato"]==5){
      $str_tipo_contrato = 'Fijo';
    }
    

    /*Informacion Financiera*/
    $str_ingresos_laborales = $fila["ingresos_laborales"];
    $str_gastos_familiares = $fila["gastos_familiares"];
    $str_honorario_comisiones = $fila["honorarios_comisiones"];
    $str_arrendamiento_cuota_vivienda = $fila["valor_arrendo"];
    $str_otros_ingresos = $fila["otros_ingresos"];
    $str_pasivos = $fila["pasivos_financieros"];
    $str_total_ingresos = $fila["total_ingresos"];
    $str_pasivos_corrientes = $fila["pasivos_corrientes"];
    $str_activos = $fila["activos_fijos"];
    $str_otros_pasivos = $fila["otros_pasivos"];
    $str_total_activos = $fila["total_activos"];
    $str_total_pasivos = $fila["total_pasivos"];

    //Referencia Familiar
    $str_referencia_familiar_nombre = $fila["nombre_familiar"];
    $str_referencia_familiar_parentezco = $fila["parentesco_familiar"];
    $str_referencia_familiar_telefono = $fila["telefono_familiar"];
    $str_referencia_familiar_direccion = $fila["direccion_familiar"];
    $str_referencia_familiar_ciudad = $fila["municipio_familiar"];
    $str_referencia_familiar_celular = $fila["celular_familiar"];
    
    //Referencia Personal
    $str_referencia_personal_nombre = $fila["nombre_personal"];
    $str_referencia_personal_parentezco = $fila["parentesco_personal"];
    $str_referencia_personal_telefono = $fila["telefono_personal"];
    $str_referencia_personal_direccion = $fila["direccion_personal"];
    $str_referencia_personal_ciudad = $fila["municipio_personal"];
    $str_referencia_personal_celular = $fila["celular_personal"];

    //Datos de operaciones internacionales
    $str_actividades_criptomoneda=false;
    $str_actividades_apnfd=true;
    if ($fila["moneda_extranjera"]=="SI")
    {
      $str_operaciones_moneda_extranjera=false;
    }
    else if ($fila["moneda_extranjera"]=="NO")
    {
      $str_operaciones_moneda_extranjera=false;
    }

    if ($fila["cuentas_exterior"]=="SI")
    {
      $str_cuenta_en_el_exterior=false;
    }
    else if ($fila["cuentas_exterior"]=="NO")
    {
      $str_cuenta_en_el_exterior=false;
    }
    $tipo_transaccion=explode("|",$fila["tipo_transaccion"]);
    $tipo_transaccion2=explode(",",$tipo_transaccion[0]);
    
    if (in_array("EXPORTACION", $tipo_transaccion2))
    {$str_operacion_exterior_exportacion = true;}else{$str_operacion_exterior_exportacion = false;}

    if (in_array("IMPORTACION", $tipo_transaccion2))
    {$str_operacion_exterior_importacion = true;}else{$str_operacion_exterior_importacion = false;}
    
    if (in_array("INVERSION", $tipo_transaccion2))
    {$str_operacion_exterior_inversiones = true;}else{$str_operacion_exterior_inversiones = false;}

    
    if (in_array("PRESTAMO EN MONEDA EXTRANJERA", $tipo_transaccion2))
    {$str_operacion_exterior_prestamo = true;}else{$str_operacion_exterior_prestamo = false;}

    if (in_array("OTRA", $tipo_transaccion2))
    {$str_operacion_exterior_otra = true;}else{$str_operacion_exterior_otra = false;}

    
    $str_operacion_exterior_otra_descripcion = $tipo_transaccion[1];

    //Datos de productos en moneda extranjera
    $str_producto_1_moneda_extranjera_nombre = $fila["banco1"];
    $str_producto_1_moneda_extranjera_cuenta = $fila["num_cuenta1"];
    $str_producto_1_moneda_extranjera_tipo = $fila["tipo_producto_operaciones1"];
    $str_producto_1_moneda_extranjera_monto = $fila["monto_operaciones1"];
    $str_producto_1_moneda_extranjera_moneda = $fila["moneda_operaciones1"];
    $str_producto_1_moneda_extranjera_ciudad = $fila["ciudad_operaciones1"];
    $str_producto_1_moneda_extranjera_pais = $fila["pais_operaciones1"];

    $str_producto_2_moneda_extranjera_nombre = $fila["banco2"];
    $str_producto_2_moneda_extranjera_cuenta = $fila["num_cuenta2"];
    $str_producto_2_moneda_extranjera_tipo = $fila["tipo_producto_operaciones2"];
    $str_producto_2_moneda_extranjera_monto = $fila["monto_operaciones2"];
    $str_producto_2_moneda_extranjera_moneda = $fila["moneda_operaciones2"];
    $str_producto_2_moneda_extranjera_ciudad = $fila["ciudad_operaciones2"];
    $str_producto_2_moneda_extranjera_pais = $fila["pais_operaciones2"];

    $str_producto_3_moneda_extranjera_nombre = $fila["banco3"];
    $str_producto_3_moneda_extranjera_cuenta = $fila["num_cuenta3"];
    $str_producto_3_moneda_extranjera_tipo = $fila["tipo_producto_operaciones3"];
    $str_producto_3_moneda_extranjera_monto = $fila["monto_operaciones3"];
    $str_producto_3_moneda_extranjera_moneda = $fila["moneda_operaciones3"];
    $str_producto_3_moneda_extranjera_ciudad = $fila["ciudad_operaciones3"];
    $str_producto_3_moneda_extranjera_pais = $fila["pais_operaciones3"];

    //Declaración FACTCA - CRS
    if ($fila["ciudadania_extranjera"]=="SI")
    { 
      $ciudadania_extranjera = true;
    }else{
      $ciudadania_extranjera = false;
    }
    
    if ($fila["residencia_extranjera"]=="SI")
    {
      $str_residente_usa_green_card = true;
    } 
    else if ($fila["residencia_extranjera"]=="NO")
    {
      $str_residente_usa_green_card = false;
    } 
    
    
    if ($fila["impuestos_extranjera"]=="SI")
    {
      $str_obligado_tributario_exterior = true;
    } 
    else if ($fila["impuestos_extranjera"]=="NO")
    {
      $str_obligado_tributario_exterior = false;
    } 

    if ($fila["representacion_extranjera"]=="SI")
    {
      $str_otorgo_poder_persona_viva_exterior = true;
    } 
    else if ($fila["representacion_extranjera"]=="NO")
    {
      $str_otorgo_poder_persona_viva_exterior = false;
    } 
    
    
    $str_apoderado_1_exterior_pais = $fila["poder_pais1"];
    $str_apoderado_1_exterior_identificacion = $fila["poder_pais1"];
    $str_apoderado_1_exterior_objeto = $fila["poder_pais1"];
    $str_apoderado_2_exterior_pais = $fila["poder_pais2"];
    $str_apoderado_2_exterior_identificacion = $fila["poder_pais2"];
    $str_apoderado_2_exterior_objeto = $fila["poder_pais2"];

    //Informacion Apoderado o Representante
    $str_apoderado_primer_nombre = $fila["apoderado_nombre1"];
    $str_apoderado_segundo_nombre = $fila["apoderado_nombre2"];
    $str_apoderado_primer_apellido = $fila["apoderado_apellido1"];
    $str_apoderado_segundo_apellido = $fila["apoderado_apellido2"];

    if ($fila["apoderado_tipo_documento"]=="CEDULA")
    {$str_apoderado_tipo_identificacion = 'CC';}
    else if ($fila["apoderado_tipo_documento"]=="REGISTRO CIVIL")
    {$str_apoderado_tipo_identificacion = 'RC';}
    else if ($fila["apoderado_tipo_documento"]=="TARJETA DE IDENTIDAD")
    {$str_apoderado_tipo_identificacion = 'TI';}
    else if ($fila["apoderado_tipo_documento"]=="CEDULA DE EXTRANJERIA")
    {$str_apoderado_tipo_identificacion = 'CE';}


    $str_apoderado_numero_documento = $fila["apoderado_nro_documento"];
    $str_apoderado_celular = $fila["apoderado_celular"];
    $str_apoderado_telefono = $fila["apoderado_telefono"];
    $str_apoderado_email = $fila["apoderado_email"];
    $str_apoderado_direccion = $fila["apoderado_direccion"];
    if ($fila["apoderado_recursos_publicos"]=="SI")
    {
      $str_apoderado_administra_recursos_publicos = true;
    } 
    else if ($fila["apoderado_recursos_publicos"]=="NO")
    {
      $str_apoderado_administra_recursos_publicos = false;
    } 


    if ($fila["apoderado_funcionario_publico"]=="SI")
    {
      $str_apoderado_ejerce_poder_publico = true;
    } 
    else if ($fila["apoderado_funcionario_publico"]=="NO")
    {
      $str_apoderado_ejerce_poder_publico = false;
    } 
    

    if ($fila["apoderado_personaje_publico"]=="SI")
    {
      $str_apoderado_reconocimiento_publico = true;
    } 
    else if ($fila["apoderado_personaje_publico"]=="NO")
    {
      $str_apoderado_reconocimiento_publico = false;
    } 
    
    
    
    $str_apoderado_fecha_inicio = $fila["apoderado_fecha_inicio"];
    $str_dia_apoderado_fecha_inicio=substr($str_apoderado_fecha_inicio, 8, 1).substr($str_apoderado_fecha_inicio, 9, 1);
    $str_mes_apoderado_fecha_inicio=substr($str_apoderado_fecha_inicio, 5, 1).substr($str_apoderado_fecha_inicio, 6, 1);
    $str_ano_apoderado_fecha_inicio=substr($str_apoderado_fecha_inicio, 0, 1).substr($str_apoderado_fecha_inicio, 1, 1).substr($str_apoderado_fecha_inicio, 2, 1).substr($str_apoderado_fecha_inicio, 3, 1);
    $str_apoderado_fecha_fila = $fila["apoderado_fecha_fila"];
    $str_dia_apoderado_fecha_fila=substr($str_apoderado_fecha_fila, 8, 1).substr($str_apoderado_fecha_fila, 9, 1);
    $str_mes_apoderado_fecha_fila=substr($str_apoderado_fecha_fila, 5, 1).substr($str_apoderado_fecha_fila, 6, 1);
    $str_ano_apoderado_fecha_fila=substr($str_apoderado_fecha_fila, 0, 1).substr($str_apoderado_fecha_fila, 1, 1).substr($str_apoderado_fecha_fila, 2, 1).substr($str_apoderado_fecha_fila, 3, 1);

    //Compras Cartera
    $str_compras_cartera_1_entidad = 'compras_cartera_1_entidad';
    $str_compras_cartera_1_numero_obligacion = 'compras_cartera_1_numero_obligacion';
    $str_compras_cartera_1_valor_estimado = '123456789';
    $str_compras_cartera_1_valor_cancelado = '123456789';

    $str_compras_cartera_2_entidad = 'compras_cartera_2_entidad';
    $str_compras_cartera_2_numero_obligacion = 'compras_cartera_2_numero_obligacion';
    $str_compras_cartera_2_valor_estimado = '123456789';
    $str_compras_cartera_2_valor_cancelado = '123456789';

    $str_compras_cartera_3_entidad = 'compras_cartera_3_entidad';
    $str_compras_cartera_3_numero_obligacion = 'compras_cartera_3_numero_obligacion';
    $str_compras_cartera_3_valor_estimado = '123456789';
    $str_compras_cartera_3_valor_cancelado = '123456789';

    $str_compras_cartera_4_entidad = 'compras_cartera_4_entidad';
    $str_compras_cartera_4_numero_obligacion = 'compras_cartera_4_numero_obligacion';
    $str_compras_cartera_4_valor_estimado = '123456789';
    $str_compras_cartera_4_valor_cancelado = '123456789';

    $str_compras_cartera_5_entidad = 'compras_cartera_5_entidad';
    $str_compras_cartera_5_numero_obligacion = 'compras_cartera_5_numero_obligacion';
    $str_compras_cartera_5_valor_estimado = '123456789';
    $str_compras_cartera_5_valor_cancelado = '123456789';

    $str_compras_cartera_6_entidad = 'compras_cartera_6_entidad';
    $str_compras_cartera_6_numero_obligacion = 'compras_cartera_6_numero_obligacion';
    $str_compras_cartera_6_valor_estimado = '123456789';
    $str_compras_cartera_6_valor_cancelado = '123456789';

    //Instrucciones_desmbolso
    $str_instrucciones_desmbolso_1_banco = 'banco';
    $str_instrucciones_desmbolso_1_numero_cuenta = '123456789';
    $str_instrucciones_desmbolso_1_tipo = 'tipo';
    $str_instrucciones_desmbolso_1_giro_pin = '159753';

    $str_instrucciones_desmbolso_2_banco = 'banco';
    $str_instrucciones_desmbolso_2_numero_cuenta = '123456789';
    $str_instrucciones_desmbolso_2_tipo = 'tipo';
    $str_instrucciones_desmbolso_2_giro_pin = '159753';

    $str_actividades = $fila["fuentes_actividades_licitas"];
	
    $int_clave_consulta_desprendible = $fila["clave"];

    $str_banco_1 = 'Banco 1';
    $str_tipo_cuenta_1 = 'Ahorro 1';
    $str_numero_cuenta_1 = '123456789 1';
    $str_banco_2 = 'Banco 2';
    $str_tipo_cuenta_2 = 'Ahorro 2';
    $str_numero_cuenta_2 = '123456789 2';

    $str_vigencia_credito_1 = '12';
    $str_vigencia_credito_2 = '42';

    $str_tasa_efectiva_anual = '18.7';
    $int_cuota_1 = '100.000';
    $int_plazo_1 = '12';
    $int_valor_credito_1 = '1.000.000';
    $int_tasa_solicitada_1 = '10';

    $int_cuota_2 = '100.000';
    $int_plazo_2 = '12';
    $int_valor_credito_2 = '1.000.000';
    $int_tasa_solicitada_2 = '10';

    $bool_conoce_caracteristicas_condiciones_coberturas = true;
    $bool_conoce_forma_pago = true;
    $bool_conoce_adquirir_productos_cancela_libranza = true;
    $bool_conoce_ampliar_plazo_en_caso_mora = true;
    $bool_conoce_valores_descontados = true;

    
    $str_fecha_entrevista=$fila["fecha_entrevista"];
    
    $str_dia_fecha_entrevista=substr($str_fecha_entrevista, 8, 1).substr($str_fecha_entrevista, 9, 1);
    $str_mes_fecha_entrevista=substr($str_fecha_entrevista, 5, 1).substr($str_fecha_entrevista, 6, 1);
    $str_ano_fecha_entrevista=substr($str_fecha_entrevista, 0, 1).substr($str_fecha_entrevista, 1, 1).substr($str_fecha_entrevista, 2, 1).substr($str_fecha_entrevista, 3, 1);
    $str_resultado_entrevista=$fila["resultado_entrevista"];
    $str_observaciones_entrevista=$fila["observaciones"];
    
    $int_numero_pagare= '123456789';
    $int_valor_capital="2000000";
    $int_valor_interes_remunerado="2323";  
    $int_valor_interes_mora="23232";  
    $str_fecha_vencimiento="03/11/2021";
    $ciudadfecha_otorgamiento="03/11/2021";
    $cantidad_forma_pago=$valor_credito_1." CUOTAS";
    $autorizacion_libranza="AUTORIZADO";


    $autorozacion_descuento_fiducoomeva=3;

?>