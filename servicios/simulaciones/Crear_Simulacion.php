<?php
include ('../../functions.php');
include ('../cors.php');

header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input, true);

if (isset($params["operacion"])){
    switch ($params["operacion"]) {
        case 'Crear Simulacion':
            $curl = curl_init();
            $data=array();
            $data["usuario"]=$usuario_seas_modulo_incorporaciones;
            $data["clave"]=$passwd_seas_modulo_incorporaciones;
            
            $curl3 = curl_init();

            curl_setopt_array($curl3, array(
                CURLOPT_URL => $url_layer_security.'Pagadurias/Consultar_Pagadurias_Id',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{"pagaduria_Id":'.$params["informacion_personal_pagaduria"].'}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token_seas_modulo_incorporaciones,
                    'Content-Type: application/json'
                ),
            ));

            $responsePagaduria = curl_exec($curl3);
            $respuestaArrayPagaduria=json_decode($responsePagaduria,true);
            $pagaduria=$respuestaArrayPagaduria["data"]["pagaduria_Nombre"];
            curl_close($curl3);

            $curl4 = curl_init();

            curl_setopt_array($curl4, array(
                CURLOPT_URL => $url_layer_security.'/Medio_Contacto/Consultar_Medio_Contacto_Id',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{"medio_Contacto_Id":'.$params["informacion_personal_medio_contacto"].'}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token_seas_modulo_incorporaciones,
                    'Content-Type: application/json'
                ),
            ));

            $responseMedioContacto = curl_exec($curl4);
            $respuestaArrayMedioContacto=json_decode($responseMedioContacto,true);
            $medio_contacto=$respuestaArrayMedioContacto["data"]["medio_Contacto_Id_SEAS"];

            curl_close($curl4);

            $curl5 = curl_init();

            curl_setopt_array($curl5, array(
                CURLOPT_URL => $url_layer_security.'/Tipo_Contratacion/Consultar_Tipo_Contratacion_Id',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{"tipo_Contratacion_Id":'.$params["informacion_personal_nivel_contratacion"].'}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token_seas_modulo_incorporaciones,
                    'Content-Type: application/json'
                ),
            ));

            $responseNivelContratacion = curl_exec($curl5);
            $respuestaArrayNivelContratacion=json_decode($responseNivelContratacion,true);
            $nivel_contratacion=$respuestaArrayNivelContratacion["data"]["tipo_Contratacion_Id_SEAS"];
            
            curl_close($curl5);


            $curl6 = curl_init();

            curl_setopt_array($curl6, array(
                CURLOPT_URL => $url_layer_security.'/Campanas/Consultar_Campana_Id',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{"campana_Id":'.$params["informacion_financiera_campana"].'}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token_seas_modulo_incorporaciones,
                    'Content-Type: application/json'
                ),
            ));

            $responseCampana = curl_exec($curl6);
            $respuestaArrayCampana=json_decode($responseCampana,true);
            $id_unidad_negocio=$respuestaArrayCampana["data"]["campana_Id_SEAS"];
            curl_close($curl6);


            $curl7 = curl_init();

            curl_setopt_array($curl7, array(
                CURLOPT_URL => $url_layer_security.'/Oficinas/Consultar_Oficina_Por_Id',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{"oficina_Id":'.$params["oficina_Id"].'}',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token_seas_modulo_incorporaciones,
                    'Content-Type: application/json'
                ),
            ));

            $responseOficina = curl_exec($curl7);
            $respuestaArrayOficina=json_decode($responseOficina,true);
            $id_oficina=$respuestaArrayOficina["data"]["oficina_Id_SEAS"];
            curl_close($curl7);

            $parametros = sqlsrv_query($link, "select * from parametros where tipo = 'SIMULADOR' order by id");

            while ($fila1 = sqlsrv_fetch_array($parametros)) {
                $parametro[$j] = $fila1["valor"];
                $j++;
            }

            $descuento1 = $parametro[19];
            $descuento2 = $parametro[20];
            $descuento3 = $parametro[21];
            $descuento4 = $parametro[22];
            $descuento5 = $parametro[23];
            $descuento6 = $parametro[24];
            $servicio_nube = $parametro[40];
            $seguro_parcial = $parametro[42];
            $sin_iva_servicio_nube = $parametro[43];

            $edad_maxima_administrativos_hombres = $parametro[5];
            $edad_maxima_administrativos_mujeres = $parametro[6];
            $edad_maxima_activos = $parametro[7];
            $edad_maxima_pensionados = $parametro[8];
            $descuento_transferencia = $parametro[3];
            $iva = $parametro[16];  
            $params["tasa"]=1;
            $params["plazo"]=1;
            $params["cuota"]=1;
            $params["desembolso"]=1;
            $params["valor_credito"]=1;
            $sin_aportes = "0";
            $nro_libranza = "NULL";
            $fecha_llamada_cliente = "NULL";
            $nro_cuenta = "NULL";
            $tipo_cuenta = "NULL";
            $id_banco = "NULL";
            $id_subestado = "NULL";
            $id_caracteristica = "NULL";
            $calificacion = "NULL";
            $dia_confirmacion = "NULL";
            $dia_vencimiento = "NULL";
            $status = "NULL";
            $empleado_manual = 0;
            $bloqueo_cuota = "0";
            $id_comercial = $params["usuario_seas"];
            $usuario = 'system';
           // $id_oficina = 0;
            $id_origen = 1;

            if($params["informacion_personal_genero"] == 1){
                $params["informacion_personal_genero"] = 'M';
            }
            
            if($params["informacion_personal_genero"] == 2){
                $params["informacion_personal_genero"] = 'F';
            }

            $rs11 = sqlsrv_query($link, "SELECT * FROM usuarios WHERE id_usuario = '".$id_comercial."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if($rs11 && sqlsrv_num_rows($rs11)){
                $resp = sqlsrv_fetch_array($rs11);
                $usuario = $resp['login'];
            }
            

            
            if(isset($params["id_origen"]) && !empty($params["id_origen"])){
                $id_origen = $params["id_origen"];
            }

            //$id_unidad_negocio=1;
            $rs1 = sqlsrv_query($link, "select valor_por_millon_seguro_activos, valor_por_millon_seguro_pensionados, valor_por_millon_seguro_colpensiones, valor_por_millon_seguro_activos_parcial, valor_por_millon_seguro_pensionados_parcial, valor_por_millon_seguro_colpensiones_parcial, gmf from unidades_negocio where id_unidad = '" . $id_unidad_negocio . "'");

            $rs_tasa = sqlsrv_query($link, "select id_tasa from tasas where plazoi <= '" .  $params["plazo"] . "' AND plazof >= '" .  $params["plazo"] . "'");

            if (sqlsrv_num_rows($rs_tasa)) {
                $fila_tasa = sqlsrv_fetch_array($rs_tasa);

                $queryDB = "SELECT  top 1 
                TRIM('0' from cast((t2.tasa_interes+ 0 ) as varchar(50))) as tasa_interes, 
                TRIM('0' from cast((t2.descuento1 +0) as varchar(50)))  as descuento1, 
                TRIM('0' from cast((t2.descuento2 +0) as varchar(50)))  as descuento2, 
                TRIM('0' from cast((t2.descuento3+0) as varchar(50)))  as descuento3
                 from tasas2" . $sufijo_sector . " as t2 INNER JOIN tasas2_unidades as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '" . $fila_tasa["id_tasa"] . "'";

                $queryDB .= " AND t2u.id_unidad_negocio = '" . $id_unidad_negocio . "'";

                $queryDB .= " AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";

                if (strtoupper($nivel_contratacion) == "PENSIONADO"){
                    $queryDB .= " OR t2.solo_pensionados = '1'";
                }else{
                    $queryDB .= " OR t2.solo_activos = '1'";
                }

                $queryDB .= ") order by t2.tasa_interes DESC";

                $rs_tasa2 = sqlsrv_query($link, $queryDB);

                if (sqlsrv_num_rows($rs_tasa2)) {
                    $fila_tasa2 = sqlsrv_fetch_array($rs_tasa2);
                    $tasa_interes = $fila_tasa2["tasa_interes"];
                    $descuento1 = $fila_tasa2["descuento1"];
                    $descuento2 = $fila_tasa2["descuento2"];
                    $descuento3 = $fila_tasa2["descuento3"];
                } else {
                    $tasa_interes = 0;
                    $descuento1 = 0;
                    $descuento2 = 0;
                    $descuento3 = 0;
                }
            } else {
                $tasa_interes = 0;
                $descuento1 = 0;
                $descuento2 = 0;
                $descuento3 = 0;
            }

            $descuento7 = 0;

            $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

            if (!$fila1["gmf"]){
                $descuento4 = 0;
            }

            $fecha_estudio_date = new DateTime("now");
            $fecha_nacimiento_date = new DateTime($params["informacion_personal_fecha_nacimiento"]);
            $diff_fechas = $fecha_nacimiento_date->diff($fecha_estudio_date);

            $valor_por_millon_seguro = 0;
            $valor_por_millon_seguro_parcial = 0;

            if(($fecha_estudio_date) >= date_create("2024-01-01")){

                $rs2 = sqlsrv_query($link, "SELECT * FROM edad_rango_seguro WHERE (".$diff_fechas->y." BETWEEN edad_rango_inicio AND edad_rango_fin) OR (".$diff_fechas->y." BETWEEN edad_rango_inicio AND edad_rango_fin)", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                if($rs2 && sqlsrv_num_rows($rs2) > 0){
                    $fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);

                    $valor_por_millon_seguro_base = $fila2["valor_por_millon"];
                    $valor_por_millon_seguro_parcial = $fila2["valor_por_millon_parcial"];

                    if ($_REQUEST["seguro_parcial"]==1 && $_REQUEST["sin_seguro"]==1){
                        $valor_por_millon_seguro = $fila2["valor_por_millon_parcial"];
                    }else{
                        $valor_por_millon_seguro = $fila2["valor_por_millon"];
                    }
                }
            }

            if(($fecha_estudio_date) < date_create("2024-01-01") || $valor_por_millon_seguro == 0){

                if (strtoupper($nivel_contratacion) == "PENSIONADO"){
                    if (strtoupper($pagaduria) == "COLPENSIONES"){
                        if ($seguro_parcial==1){
                            $valor_por_millon_seguro = $fila1["valor_por_millon_seguro_colpensiones_parcial"];
                            $valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_colpensiones"];
                        }else{
                            $valor_por_millon_seguro = $fila1["valor_por_millon_seguro_colpensiones"];
                            $valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_colpensiones"];
                        }
                        $valor_por_millon_seguro_parcial = $fila1["valor_por_millon_seguro_colpensiones_parcial"];
                    }
                    else{       
                        if ($seguro_parcial==1){
                            $valor_por_millon_seguro = $fila1["valor_por_millon_seguro_pensionados_parcial"];
                            $valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_pensionados"];
                        }else{
                            $valor_por_millon_seguro = $fila1["valor_por_millon_seguro_pensionados"];
                            $valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_pensionados"];
                        }
                        $valor_por_millon_seguro_parcial = $fila1["valor_por_millon_seguro_pensionados_parcial"];
                    }
                }else{
                    if ($seguro_parcial==1){
                        $valor_por_millon_seguro = $fila1["valor_por_millon_seguro_activos_parcial"];
                        $valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_activos"];
                    }else{
                        $valor_por_millon_seguro = $fila1["valor_por_millon_seguro_activos"];
                        $valor_por_millon_seguro_base = $fila1["valor_por_millon_seguro_activos"];
                    }
                    $valor_por_millon_seguro_parcial = $fila1["valor_por_millon_seguro_activos_parcial"];
                }
            }

            $porcentaje_seguro = 0;

            $diff_dias_ultimo_mes = date("j", strtotime($params["informacion_personal_fecha_nacimiento"])) - date("j", strtotime(date("Y-m-d")));

            $queryDB   = "select  (YEAR(DATEADD(YEAR,70,convert(datetime, '1945-03-24T00:00:00', 126))) - YEAR(GETDATE()))* 12 + (MONTH(DATEADD(YEAR,70,convert(datetime,'1945-03-24T00:00:00', 126))) - MONTH(GETDATE())) AS meses_antes_activos,(YEAR(DATEADD(YEAR,75,   convert(datetime, '1945-03-24T00:00:00', 126)))-YEAR(GETDATE())) * 12 + (MONTH(DATEADD(YEAR,75,convert(datetime, '1945-03-24T00:00:00', 126))) - MONTH(GETDATE())) AS meses_antes_pensionados
            from empleados where cedula = '" . trim($params["informacion_personal_numero_identificacion"]) . "' AND pagaduria = '" . $pagaduria . "'";

            $meses_antes_rs = sqlsrv_query($link, $queryDB);

            $fila = sqlsrv_fetch_array($meses_antes_rs);

            $meses_antes = $diff_dias_ultimo_mes >= 0 ? $fila["meses_antes_pensionados"] : ($fila["meses_antes_pensionados"] - 1);

            if ($meses_antes == 1){
                $meses_antes .= " MES";
            }

            if ($meses_antes > 1){
                $meses_antes .= " MESES";
            }

            if ($meses_antes <= 0){
                $meses_antes = "0";
            }
            
 $crearSimulacion = "insert into simulaciones (primer_apellido,
             segundo_apellido,
             primer_nombre,
             segundo_nombre,
             otp_verificado,
             sin_seguro,
            servicio_nube,
             seguro_parcial,
             sin_iva_servicio_nube,
             id_comercial,
             id_oficina,
             telemercadeo,
             fecha_estudio,
             cedula,
             nombre,
             pagaduria,
             pa,
             ciudad,
             institucion,
             nivel_educativo,
             fecha_nacimiento,
             telefono,
             meses_antes_65,
             fecha_inicio_labor,
             medio_contacto,
             salario_basico,
             adicionales,
             bonificacion,
             total_ingresos,
             aportes,
             otros_aportes,
             total_aportes,
             total_egresos,
             salario_minimo,
             ingresos_menos_aportes,
             salario_libre,
             nivel_contratacion,
             embargo_actual,
             historial_embargos,
             embargo_alimentos,
             embargo_centrales,
             descuentos_por_fuera,
             cartera_mora,
             valor_cartera_mora,
             puntaje_datacredito,
             puntaje_cifin,
             valor_descuentos_por_fuera,
             id_unidad_negocio,
             tasa_interes,
             plazo,
             tipo_credito,
             suma_al_presupuesto,
             total_cuota,
             total_valor_pagar,
             retanqueo1_libranza,
             retanqueo1_cuota,
             retanqueo1_valor,
             retanqueo2_libranza,
             retanqueo2_cuota,
             retanqueo2_valor,
             retanqueo3_libranza,
             retanqueo3_cuota,
             retanqueo3_valor,
             retanqueo_total_cuota,
             retanqueo_total,
             opcion_credito,
             opcion_cuota_cli,
             opcion_desembolso_cli,
             opcion_cuota_ccc,
             opcion_desembolso_ccc,
             opcion_cuota_cmp,
             opcion_desembolso_cmp,
             opcion_cuota_cso,
             opcion_desembolso_cso,
             desembolso_cliente,
             decision,
             decision_sistema,
             valor_visado,
             bloqueo_cuota,
             bloqueo_cuota_valor,
             fecha_llamada_cliente,
             nro_cuenta,
             tipo_cuenta,
             id_banco,
             id_subestado,
             id_caracteristica,
             calificacion,
             dia_confirmacion,
             dia_vencimiento,
             status,
             valor_credito,
             resumen_ingreso,
             incor,
             comision,
             utilidad_neta,
             sobre_el_credito,
             estado,
             tipo_producto,
             descuento1,
             descuento2,
             descuento3,
             descuento4,
             descuento5,
             descuento6,
             descuento7,
             descuento_transferencia,
             porcentaje_seguro,
             valor_por_millon_seguro,
             valor_por_millon_seguro_parcial,
             valor_por_millon_seguro_base,
             porcentaje_extraprima,
             sin_aportes,
             empleado_manual,
             iva,
             frente_al_cliente,
             usuario_radicado,
             fecha_radicado,
             usuario_creacion,
             fecha_creacion,
              proposito_credito,
             id_tasa_comision,
             id_tipo_tasa_comision,
             id_origen) values (
             '".trim($params['informacion_personal_primer_apellido'])."',
            '".trim($params['informacion_personal_segundo_apellido'])."',
            '".trim($params['informacion_personal_primer_nombre'])."',
            '".trim($params['informacion_personal_segundo_nombre'])."',
            ".$params["verificado"].",
            1,
            ".$servicio_nube.",
            ".$seguro_parcial.",
            ".$sin_iva_servicio_nube.",
            ".$id_comercial.",
            ".$id_oficina.",
             0,
             GETDATE(),
             '" . trim($params["informacion_personal_numero_identificacion"]) . "',
             '" . strtoupper(trim($params['informacion_personal_primer_apellido'].' '.$params['informacion_personal_segundo_apellido']) . ' ' . trim($params['informacion_personal_primer_nombre'].' '.$params['informacion_personal_segundo_nombre'])) . "',
             '" . $pagaduria . "',
             'ESEFECTIVO',
             '" . strtoupper($params["informacion_personal_ciudad"]) . "',
             '" . strtoupper($params["informacion_personal_institucion"]) . "',
             '',
             '" . $params["informacion_personal_fecha_nacimiento"] . "',
             '" . $params["informacion_personal_numero_celular"] . "',
             '" . $meses_antes . "',
             GETDATE(),
             '".$medio_contacto."',
             ".$params["informacion_financiera_valor_ingresos"].",
             0,
             0,
             ".$params["informacion_financiera_valor_ingresos"].",
             ".$params["informacion_financiera_valor_aportes"].",
             0,
             ".$params["informacion_financiera_valor_aportes"].",
             ".$params["informacion_financiera_valor_egresos"].",
             (select salario_minimo from salario_minimo where ano = YEAR(GETDATE())),
             0,
             0,
             '".$nivel_contratacion."',
             'NO',
             0,
             'NO',
             'NO',
             'NO',
             'NO',
             0,
             ".$params["informacion_financiera_puntaje"].",
             '-1',
             0,
             1,
             " . $params["informacion_financiera_tasa"] . ",
             ".$params["informacion_financiera_plazo"].",
             'CREDITO NORMAL',
             0,
             ".$params["informacion_financiera_suma_saldos_cuotas"].",
             ".$params["informacion_financiera_suma_saldos_carteras"].",
             '',
             0,
             0,
             '',
             0,
             0,
             '',
             0,
             0,
             0,
             0,
             'CSO',
             0,
             '" . (-1.00 * $descuento_transferencia) . "',
             '0',
             '" . (-1.00 * $descuento_transferencia) . "',
             '0',
             '" . (-1.00 * $descuento_transferencia) . "',
             '".($params["informacion_financiera_valor_cuota"])."',
             '" . ($params["informacion_financiera_desembolso"]) . "',
             '" . ($params["informacion_financiera_desembolso"]) . "',
             '" . $label_viable . "',
             '" . $label_negado . "',
             '0',
             '" . $bloqueo_cuota . "',
             '0',
             " . $fecha_llamada_cliente . ",
             " . $nro_cuenta . ",
             " . $tipo_cuenta . ",
             " . $id_banco . ",
             " . $id_subestado . ",
             " . $id_caracteristica . ",
             " . $calificacion . ",
             " . $dia_confirmacion . ",
             " . $dia_vencimiento . ",
             " . $status . ",
             '".$params["informacion_financiera_valor_credito"]."',
             '0',
             '0',
             '0',
             '0',
             '0',
             'PIN',
             '0',
             '" . $descuento1 . "',
             '" . $descuento2 . "',
             '" . $descuento3 . "',
             '" . $descuento4 . "',
             '" . $descuento5 . "',
             '" . $descuento6 . "',
             '" . $descuento7 . "',
             '" . $descuento_transferencia . "',
             '" . $porcentaje_seguro . "',
             " . $valor_por_millon_seguro . ",
             ". $valor_por_millon_seguro_parcial ." ,
             " . $valor_por_millon_seguro_base . ",
             0,
             " . $sin_aportes . ",
             " . $empleado_manual . ",
             " . $iva . ",
             'SI',
             '$usuario',
             GETDATE(),
             '$usuario',
             GETDATE(),
              9,
             0,
             0,
             $id_origen)";

            $queryEmpleados3 = "SELECT nombre FROM empleados WHERE cedula = '".trim($params['informacion_personal_numero_identificacion'])."' AND pagaduria = '".$pagaduria . "'";
            $resultado = sqlsrv_query($link, $queryEmpleados3, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if (sqlsrv_num_rows($resultado) == 0) {
                $queryEmpleados = "INSERT INTO empleados VALUES ("
                    . "'" . trim($params['informacion_personal_numero_identificacion']) . "', "
                    . "'" . strtoupper(trim($params['informacion_personal_primer_apellido'].' '.$params['informacion_personal_segundo_apellido'])) . ' ' . strtoupper(trim($params['informacion_personal_primer_nombre'].' '.$params['informacion_personal_segundo_nombre'])) . "', "
                    . "'" . $pagaduria . "', "
                    . "'', "
                    . "'', "
                    . "'', "
                    . "'0', "
                    . "'0', "
                    . "'0', "
                    . "'0', "
                    . "'', "
                    . "'" . strtoupper($params['informacion_personal_direccion']) . "', "
                    . "'" . $params['informacion_personal_numero_celular'] . "', "
                    . "'" . $params['informacion_personal_correo'] . "', "
                    . "'" . $params['informacion_personal_fecha_nacimiento'] . "', "
                    . "'', "
                    . "'', "
                    . "'" . strtoupper($params['informacion_personal_ciudad']) . "', "
                    . "'" . '1' . "', "
                    . "'', "
                    . "'" . strtoupper($params['informacion_personal_ciudad']) . "', "
                    . "'" . $params['informacion_personal_fecha_vinculacion'] . "', "
                    . "'')";

                sqlsrv_query($link, $queryEmpleados);

                $queryEmpleados2 = "INSERT INTO empleados_creacion ("
                    . "cedula, "
                    . "pagaduria, "
                    . "id_usuario, "
                    . "fecha_creacion) VALUES ("
                    . "'" . trim($params['informacion_personal_numero_identificacion']) . "', "
                    . "'" . $pagaduria . "', "
                    . "'".$id_comercial."', "
                    . "GETDATE())";

                sqlsrv_query($link, $queryEmpleados2);
            }


            if (sqlsrv_query($link,$crearSimulacion)) {
                $id_simul1 = sqlsrv_query($link, "SELECT @@IDENTITY as id");
                $id_simul2= sqlsrv_fetch_array($id_simul1);
                $id_simul = $id_simul2['id'];

                sqlsrv_query($link, "INSERT INTO consultas_externas (Codigo_respuesta,cedula,id_simulacion,usuario_creacion,proveedor,servicio,Salida_respuesta,fecha_creacion,Url_respuesta,puntaje_datacredito) VALUES (0,'" . trim($params["informacion_personal_numero_identificacion"]) . "',$id_simul,'$usuario','EXPERIAN','HDC_ACIERTA','".$params["informacion_financiera_response_experian"]."',CURRENT_TIMESTAMP,'".str_replace('\\',"/",$params["informacion_financiera_url_response_experian"])."','".$params["informacion_financiera_puntaje"]."');");

                $id_registro_datacredito1 = sqlsrv_query($link, "SELECT @@IDENTITY as id");
                $id_registro_datacredito2 = sqlsrv_fetch_array($id_registro_datacredito1);
                $id_registro_datacredito = $id_registro_datacredito2['id'];
                $tipo_adjunto=2;
                $desc_tipo_adjunto="Consulta Datacredito";

                $curl = curl_init();

                $cadena_Credito='{"archivo":"'.str_replace('\\',"/",$params["informacion_financiera_url_response_experian"]).'","id_simulacion":"'.($id_simul).'","tipo_archivo":"'.($tipo_adjunto).'","observacion":"'.$desc_tipo_adjunto.'"}';

                sqlsrv_query($link, "INSERT INTO consultas_externas (Codigo_respuesta,cedula,id_simulacion,usuario_creacion,proveedor,servicio,Salida_respuesta,fecha_creacion,Url_respuesta,puntaje_datacredito) VALUES (0,'" . trim($params["informacion_personal_numero_identificacion"]) . "',$id_simul,'$usuario','EXPERIAN','HDC_ACIERTA','".$params["informacion_financiera_response_experian"]."',CURRENT_TIMESTAMP,'".str_replace('\\',"/",$params["informacion_financiera_url_response_experian"])."','".$params["informacion_financiera_puntaje"]."');");

                $id_registro_datacredito1 = sqlsrv_query($link, "SELECT @@IDENTITY as id");
                $id_registro_datacredito2 = sqlsrv_fetch_array($id_registro_datacredito1);
                $id_registro_datacredito = $id_registro_datacredito2['id'];

                if(isset($params["informacion_observaciones"]) && !empty($params["informacion_observaciones"])){
                    $observacionesSimulaciones="INSERT INTO simulaciones_observaciones (id_simulacion,observacion,usuario_creacion,fecha_creacion) VALUES ('".$id_simul."','".$params["informacion_observaciones"]."','$usuario',CURRENT_TIMESTAMP)";
                    sqlsrv_query($link,$observacionesSimulaciones);
                }

                $cont=1;
                
                foreach($params["informacion_financiera_compras_cartera"] as $carteras) {
                    $crearCompraCarteraSimulacion="INSERT INTO simulaciones_comprascartera (id_simulacion,consecutivo,id_entidad,entidad,cuota,valor_pagar,se_compra,usuario_creacion,fecha_creacion) VALUES ('".$id_simul."','".$cont."','".$carteras["entidad_Id"]."','".$carteras["nombre_entidad"]."','".$carteras["valor_cuota"]."','".$carteras["valor_credito"]."','SI','$usuario',CURRENT_TIMESTAMP)";
                    sqlsrv_query($link,$crearCompraCarteraSimulacion);
                    $cont++;
                } 

                $iva_valor=0;
                $gmf_valor=0;
                $asesoria_financiera_valor=0;
                $interes_anticipado_valor=0;
                $iva_porcentaje=0;
                $gmf_porcentaje=0;
                $asesoria_financiera_porcentaje=0;
                $interes_anticipado_porcentaje=0;

                foreach($params["informacion_financiera_descuentos"] as $descuentos){

                    if ($descuentos["codigo_descuento"]=="iant"){
                        $interes_anticipado_valor=$descuentos["valor"];
                        $interes_anticipado_porcentaje=$descuentos["porcentaje"];
                    }

                    if ($descuentos["codigo_descuento"]=="afin"){
                        $asesoria_financiera_valor=$descuentos["valor"];
                        $asesoria_financiera_porcentaje=$descuentos["porcentaje"];
                    }

                    if ($descuentos["codigo_descuento"]=="gmf"){
                        $gmf_valor=$descuentos["valor"];
                        $gmf_porcentaje=($descuentos["valor"]/$params["informacion_financiera_valor_credito"])*100;
                    }

                    if ($descuentos["codigo_descuento"]=="iva") {
                        $iva_valor=$descuentos["valor"];
                        $iva_porcentaje=($descuentos["valor"]/$params["informacion_financiera_valor_credito"])*100;
                    }
                } 

                $actualizarDescuentos="UPDATE simulaciones SET descuento1='".$interes_anticipado_porcentaje."',descuento2='".$asesoria_financiera_porcentaje."',descuento3='".$iva_porcentaje."',descuento4='".$gmf_porcentaje."' WHERE id_simulacion='".$id_simul."'";
                sqlsrv_query($link,$actualizarDescuentos);

                sqlsrv_query($link, "insert into solicitud (id_simulacion, cedula, apellido1, apellido2, nombre1, nombre2, fecha_nacimiento, tel_residencia, celular, direccion, email, sexo) values ('" . $id_simul . "', '" . trim($params["informacion_personal_numero_identificacion"]) . "', '".trim($params['informacion_personal_primer_apellido'])."','".trim($params['informacion_personal_segundo_apellido'])."','".trim($params['informacion_personal_primer_nombre'])."','".trim($params['informacion_personal_segundo_nombre'])."', '" . $params["informacion_personal_fecha_nacimiento"] . "', '" . trim($params["informacion_personal_numero_celular"]) . "', '" . ($params["informacion_personal_numero_celular"]) . "', '" . (strtoupper($params["informacion_personal_direccion"])) . "', '" . trim($params["informacion_personal_correo"]) . "', '".trim($params["informacion_personal_genero"])."')");


                $crearSubestado="INSERT INTO simulaciones_subestados (id_simulacion,id_subestado,usuario_creacion,fecha_creacion) VALUES ('".
                    $params["id_simulacion"]."',46,'$usuario',CURRENT_TIMESTAMP)";
                sqlsrv_query($link,$crearSubestado);

                sqlsrv_query($link, "INSERT INTO consultas_externas (Codigo_respuesta,cedula,id_simulacion,usuario_creacion,proveedor,servicio,Salida_respuesta,fecha_creacion,Url_respuesta) VALUES (0,'" . trim($params["informacion_personal_numero_identificacion"]) . "',$id_simul,'$usuario','TRANSUNION','INFORMACION_COMERCIAL','".$params["informacion_financiera_response_experian"]."',CURRENT_TIMESTAMP,'".str_replace('\\',"/",$params["informacion_financiera_url_response_cifin"])."');");

                sqlsrv_query($link, "INSERT INTO consultas_externas (Codigo_respuesta,cedula,id_simulacion,usuario_creacion,proveedor,servicio,Salida_respuesta,fecha_creacion,Url_respuesta,puntaje_datacredito) VALUES (0,'" . trim($params["informacion_personal_numero_identificacion"]) . "',$id_simul,'$usuario','EXPERIAN','HDC_ACIERTA','".$params["informacion_financiera_response_experian"]."',CURRENT_TIMESTAMP,'".str_replace('\\',"/",$params["informacion_financiera_url_response_experian"])."','".$params["informacion_financiera_puntaje"]."');");

                $codigo=200;        
                $response = array('operacion' => 'Se ha realizado proceso satisfactoriamente', 'codigo' => $codigo, 'mensaje' => 'Se ha realizado proceso satisfactoriamente','data'=>$id_simul,'cadena'=>json_encode($array));
            }else{
                $codigo=404;        
                $response = array('operacion' => 'Error al actualizar credito', 'codigo' => $codigo,"consulta" =>$crearSimulacion);
            }
        break;

        case 'Guardar Valores Credito':
            $parametros = sqlsrv_query($link, "select * from parametros where tipo = 'SIMULADOR' order by id");

            $j = 0;

            while ($fila1 = sqlsrv_fetch_array($parametros)) {
                $parametro[$j] = $fila1["valor"];
                $j++;
            }

            $salario_minimo = $parametro[32];
            $descuento1 = $parametro[19];
            $descuento2 = $parametro[20];
            $descuento3 = $parametro[21];
            $descuento4 = $parametro[22];
            $descuento5 = $parametro[23];
            $descuento6 = $parametro[24];

            $descuento_transferencia = $parametro[3];
            $otros_descuentos = 0;
            $consultarInformacionCredito="select * from simulaciones where id_simulacion='".$params["informacion_credito_simulacion"]."'";
            $queryInformacionCredito=sqlsrv_query($link,$consultarInformacionCredito, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            
            if (sqlsrv_num_rows($queryInformacionCredito)>0){
                $opcion_cuota_base=0;
                $resInformacionCredito=sqlsrv_fetch_array($queryInformacionCredito, SQLSRV_FETCH_ASSOC);

                if ($params["informacion_financiera_salario_basico"] < $salario_minimo * 2){
                    if (($resInformacionCredito["nivel_contratacion"] == "PENSIONADO") || $sector == "PRIVADO"){
                        $opcion_cuota_base = $params["informacion_financiera_salario_basico"] - round((($params["informacion_financiera_salario_basico"]-$params["informacion_financiera_total_aportes"])/2)) - $params["informacion_financiera_total_egresos"];
                    }
                    else{
                        $opcion_cuota_base = $params["informacion_financiera_salario_basico"]- $salario_minimo - $params["informacion_financiera_total_egresos"];
                    }
                }
                else{
                    $opcion_cuota_base = $params["informacion_financiera_salario_basico"] - round((($params["informacion_financiera_salario_basico"]-$params["informacion_financiera_total_aportes"])/2)) - $params["informacion_financiera_total_egresos"];
                }

                $rs_tasa = sqlsrv_query($link, "select id_tasa from tasas where plazoi <= '".$params["informacion_financiera_plazo"]."' AND plazof >= '".$params["informacion_financiera_plazo"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                if (sqlsrv_num_rows($rs_tasa)){
                    $fila_tasa = sqlsrv_fetch_array($rs_tasa);

                    $queryDB = "SELECT TOP 1 TRIM('0' from cast((t2.tasa_interes+ 0 ) as varchar(50))) as tasa_interes, 
                        TRIM('0' from cast((t2.descuento1 +0) as varchar(50)))  as descuento1, 
                        TRIM('0' from cast((t2.descuento2 +0) as varchar(50)))  as descuento2, 
                        TRIM('0' from cast((t2.descuento3+0) as varchar(50)))  as descuento3
                         from tasas2 as t2 INNER JOIN tasas2_unidades as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa = '".$fila_tasa["id_tasa"]."'";

                    $queryDB .= " AND t2u.id_unidad_negocio = '".$id_unidad_negocio."'";
                    $queryDB .= " AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";

                    if (strtoupper($resInformacionCredito["nivel_contratacion"]) == "PENSIONADO"){
                        $queryDB .= " OR t2.solo_pensionados = '1'";
                    }
                    else{
                        $queryDB .= " OR t2.solo_activos = '1'";
                    }

                    $queryDB .= ") order by t2.tasa_interes DESC LIMIT 1";

                    $rs_tasa2 = sqlsrv_query($link, $queryDB);

                    if (sqlsrv_num_rows($rs_tasa2)){
                        $fila_tasa2 = sqlsrv_fetch_array($rs_tasa2);
                        $tasa_interes = $fila_tasa2["tasa_interes"];
                        $tasa_interes_maxima = $tasa_interes;
                        $descuento_producto0 = $fila_tasa2["descuento1"];
                        $descuento1 = $fila_tasa2["descuento1"];
                        $descuento_producto1 = $fila_tasa2["descuento1_producto"];
                        $descuento2 = $fila_tasa2["descuento2"];
                        $descuento3 = $fila_tasa2["descuento3"];
                    }
                    else{
                        $tasa_interes = 0;
                        $tasa_interes_maxima = 0;
                        $descuento_producto0 = 0;
                        $descuento1 = 0;
                        $descuento_producto1 = 0;
                        $descuento2 = 0;
                        $descuento3 = 0;
                    }
                }
                else{
                    $tasa_interes = 0;
                    $tasa_interes_maxima = 0;
                    $descuento_producto0 = 0;
                    $descuento1 = 0;
                    $descuento_producto1 = 0;
                    $descuento2 = 0;
                    $descuento3 = 0;
                }
                $rs1 = sqlsrv_query($link, "select valor_por_millon_seguro_activos, valor_por_millon_seguro_pensionados, valor_por_millon_seguro_colpensiones, gmf from unidades_negocio where id_unidad = '".$params["informacion_financiera_unidad_negocio"]."'");
                $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

                if (!$fila1["gmf"]){
                    $descuento4 = 0;
                }

                $suma_descuentos = $descuento1 + $descuento2 + $descuento3 + $descuento4;
                $opcion_desembolso_cmp = ($valor_credito_cmp * (100.00 - $suma_descuentos) / 100.00) - $descuento_transferencia - $otros_descuentos;

                $actualizarSimulacion="UPDATE simulaciones SET opcion_desembolso_cso='".$opcion_desembolso_cmp."',opcion_cuota_cso='".$opcion_cuota_base."',opcion_cuota_ccc='".$opcion_cuota_base."',opcion_cuota_cli='".$opcion_cuota_base."',opcion_cuota_cmp='".$opcion_cuota_base."',salario_minimo='".$salario_minimo."',salario_libre='".(($params["informacion_financiera_salario_basico"]-$params["informacion_financiera_total_aportes"])/2)."',ingresos_menos_aportes='".($params["informacion_financiera_salario_basico"]-$params["informacion_financiera_total_aportes"])."',aportes='".$params["informacion_financiera_total_aportes"]."', otros_aportes=0, total_aportes='".$params["informacion_financiera_total_aportes"]."',plazo='".$params["informacion_financiera_plazo"]."',id_unidad_negocio='".$params["informacion_financiera_unidad_negocio"]."',tasa_interes='".$params["informacion_financiera_tasa_interes"]."',salario_basico='".$params["informacion_financiera_salario_basico"]."',total_ingresos='".$params["informacion_financiera_salario_basico"]."',total_egresos='".$params["informacion_financiera_total_egresos"]."' where id_simulacion='".$params["informacion_credito_simulacion"]."'";

                if (sqlsrv_query($link,$actualizarSimulacion)){
                    $codigo=200;        
                    $response = array('operacion' => 'Se ha realizado proceso satisfactoriamente', 'codigo' => $codigo, 'mensaje' => 'Se ha realizado proceso satisfactoriamente');
                }else{
                    $codigo=404;        
                    $response = array('operacion' => 'Error al actualizar credito', 'codigo' => $codigo,"query"=>$actualizarSimulacion);
                }
            }

        break;

        case 'Actualizar Estado Simulacion':

            $crearSimulacion="UPDATE simulaciones SET estado='ING' WHERE id_simulacion='".$params['id_simulacion']."'";

            if (sqlsrv_query($link,$crearSimulacion)){

                $queryACE = sqlsrv_query($link, "SELECT TOP 1 * FROM simulaciones_fdc WHERE id_simulacion = '".$params['id_simulacion']."' AND estado = 1 AND vigente = 's' ORDER BY id DESC", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                if($queryACE){
                    $codigo=200;
                    $response = array('operacion' => 'Actualizar Estado Simulacion', 'codigo' => $codigo, 'mensaje' => 'El Credito ya se encontraba en Fabrica');
                    if(sqlsrv_num_rows($queryACE) == 0){
                        sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$params['id_simulacion']."'");
                        sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) VALUES (".$params['id_simulacion'].",197,197,CURRENT_TIMESTAMP,'s',1,24);");
                        $response = array('operacion' => 'Se ha realizado proceso satisfactoriamente', 'codigo' => $codigo, 'mensaje' => 'Se ha realizado proceso satisfactoriamente');
                    }
                }else{
                    $codigo=500;        
                    $response = array('mensaje' => 'Error al actualizar credito en fabrica', 'codigo' => $codigo,"consulta" =>$crearSimulacion);
                }
            }else{
                $codigo=404;        
                $response = array('mensaje' => 'Error al actualizar credito', 'codigo' => $codigo,"consulta" =>$crearSimulacion);
            }

        break;

        default:
            $codigo=404;        
            $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
        break;   
    }  
}else{
    $codigo=400;        
    $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
}
echo json_encode($response);
http_response_code("200");