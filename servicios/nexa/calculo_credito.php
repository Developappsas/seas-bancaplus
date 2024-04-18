<?php 
    include ('../../functions.php');
    include ('../../cors.php');
    header("Content-Type: application/json; charset=utf-8");
    $link = conectar_utf();

    $json_Input = file_get_contents('php://input');
    $params = json_decode($json_Input,true);

    $parametros = sqlsrv_query($link, "select * from parametros where tipo = 'SIMULADOR' order by codigo");

    $j = 0;

    while ($fila1 = sqlsrv_fetch_array($parametros)) {
        $parametro[$j] = $fila1["valor"];
        $j++;
    }
    
    $edad_maxima_activos = $parametro[7];
    $edad_maxima_pensionados = $parametro[8];
    $plazo_maximo = $parametro[11];
    $porcentaje_aportes_pensionados = $parametro[25];
    $porcentaje_aportes_activos = $parametro[17];
    //calcularPlazoMaximo("1993-12-24",$edad_maxima_activos,$edad_maxima_pensionados,$plazo_maximo,"PENSIONADO");
    //calcularTasas(168,1,"PENSIONADO");
    if ($params["Action"]=="CALCULAR_CREDITO") {
        $val1=0;$val2=0;$val3=0;$mensaje="";
        if (!isset($params["fecha_nacimiento"])) {
            $val1=1;
            $mensaje.="Debe ingresar fecha de nacimiento. ";
        }else{
            $params["fecha_nacimiento"] = trim($params["fecha_nacimiento"]);
            $params["fecha_nacimiento"] = str_replace('/', '-', $params["fecha_nacimiento"]);
            $fechaNacimiento = date("Y-m-d", strtotime($params["fecha_nacimiento"]));
        }
        
        if (!isset($params["unidad_negocio"])){            
            $val2=1;
            $mensaje.="Debe ingresar unidad de negocio. ";
        }else{
            if ($params["unidad_negocio"]=="KREDIT") {
                $unidad_negocio=1;
            }else{
                $unidad_negocio=2;
            }            
        }
        
        if (!isset($params["salario"])) {            
            $mensaje.="Debe ingresar salario. ";
            $val3=1;
        }else{
            $salario=str_replace(".","",$params["salario"]);
        }

        $total_secompra = intval($params["total_secompra"]);
        $otros_descuentos = intval($params["otros_descuentos"]);
        
        if ($val1==1 || $val2==1 || $val3==1) {
            header("HTTP/2.0 200 OK");
            $data = array("code"=>"403","message"=>$mensaje,"data" =>null);
            echo json_encode($data);      
        } else {
            calcularValorCredito($fechaNacimiento,$edad_maxima_activos,$edad_maxima_pensionados,$plazo_maximo, $porcentaje_aportes_pensionados,$porcentaje_aportes_activos,"PROPIEDAD",$unidad_negocio,0,$salario,0,$total_secompra,$otros_descuentos);
        }

        //calcularValorCredito("1993-12-24",$edad_maxima_activos,$edad_maxima_pensionados,$plazo_maximo, $porcentaje_aportes_pensionados,$porcentaje_aportes_activos,"PENSIONADO",1,0,4850000,0);        
    }else{
        header("HTTP/2.0 200 OK");
        $data = array("code"=>"404","message"=>"Servicio consultado no existe","data" =>null);
        echo json_encode($data);
    }
    
    
    function calcularValorCredito($fechaNacimiento,$edad_maxima_activos,$edad_maxima_pensionados,$plazo_maximo,$porcentaje_aportes_pensionados,$porcentaje_aportes_activos,$nivel_contratacion,$id_unidad_negocio,$porcentaje_extraprima,$total_ingresos,$total_egresos,$total_secompra,$otros_descuentos) {
        global $link;
        $salario_minimo=1000000;
        //CALCULO DE PLAZO
        $plazo=$plazo_maximo;
        $plazo_maximo_segun_edad = $plazo_maximo;
        $consultaPlazos="select ((DATEPART(YEAR , Format('".$fechaNacimiento."', 'Y-m-d')) +".$edad_maxima_activos.") - DATEPART(YEAR , GETDATE())) * 12 + (DATEPART(MONTH , DATEADD(YEAR,".$edad_maxima_activos.",Format('".$fechaNacimiento."', 'Y-m-d'))) - DATEPART(MONTH , GETDATE()))as meses_antes_activos, 
        ((DATEPART(YEAR , Format('".$fechaNacimiento."', 'Y-m-d')) +".$edad_maxima_pnsionados.") - DATEPART(YEAR , GETDATE())) * 12 + (DATEPART(MONTH , DATEADD(YEAR,".$edad_maxima_pensinado.",Format('".$fechaNacimiento."', 'Y-m-d'))) - DATEPART(MONTH , GETDATE())) as meses_antes_pensionados";
        $queryPlazos=sqlsrv_query($link, $consultaPlazos);
        $resPlazos=sqlsrv_fetch_array($queryPlazos);

        $diff_dias_ultimo_mes = date("j", strtotime($fechaNacimiento)) - date("j", strtotime(date("Y-m-d")));

        if ($nivel_contratacion == "PENSIONADO") {
            $meses_antes = $diff_dias_ultimo_mes >= 0 ? $resPlazos["meses_antes_pensionados"] : ($resPlazos["meses_antes_pensionados"] - 1);
        }else{
            $meses_antes = $diff_dias_ultimo_mes >= 0 ? $resPlazos["meses_antes_activos"] : ($resPlazos["meses_antes_activos"] - 1);
        }

        if (strtoupper($nivel_contratacion) != "PENSIONADO") {
            if ($meses_antes < $plazo_maximo) {
                $plazo = $meses_antes;
            }
        }

        if ($meses_antes < 0) {
            $plazo = 0;
        }

        //CALCULO MARGEN DE SEGURIDAD
        $cantSalariosMinimos = $total_ingresos/$salario_minimo;
        $margen_seguridad = 0;
        if($cantSalariosMinimos >= 1 && $cantSalariosMinimos <= 1.5){
            $margen_seguridad = 4500;
        }else if($cantSalariosMinimos >= 1.6 && $cantSalariosMinimos <= 3){
            $margen_seguridad = 6500;
        }else if($cantSalariosMinimos > 3){
            $margen_seguridad = 8500;
        }
        
        //CALCULO DE TASA
     
        $queryDB = "select t2.id_tasa2,t2.id_tasa,TRIM(t2.tasa_interes) + 0 as tasa_interes from tasas2 as t2 INNER JOIN tasas2_unidades as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa IN (select id_tasa from tasas where '".$plazo."' >= plazoi AND '".$plazo."' <= plazof)";             
        $queryDB .= " AND t2u.id_unidad_negocio = '".$id_unidad_negocio."'";
        $queryDB .= " AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";          
        if (strtoupper($nivel_contratacion) == "PENSIONADO")
            $queryDB .= " OR t2.solo_pensionados = '1'";
        else
            $queryDB .= " OR t2.solo_activos = '1'";
        
        $queryDB .= ") order by t2.tasa_interes DESC";

        $queryExec=sqlsrv_query($link, $queryDB);
        while ($resTasa=sqlsrv_fetch_array($queryExec)){
            //CALCULO DE CREDITO
            $tasa_interes=$resTasa["tasa_interes"];
            $rs1 = sqlsrv_query($link, "select valor_por_millon_seguro_activos, valor_por_millon_seguro_pensionados, valor_por_millon_seguro_colpensiones, gmf from unidades_negocio where id_unidad = '".$id_unidad_negocio."'");
                    
            $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
                    
            if (strtoupper($nivel_contratacion) == "PENSIONADO"){
                $porcentaje_aportes = $porcentaje_aportes_pensionados;
                $valor_por_millon_seguro = $fila1["valor_por_millon_seguro_pensionados"];
            }
            else{
                $porcentaje_aportes = $porcentaje_aportes_activos;
                $valor_por_millon_seguro = $fila1["valor_por_millon_seguro_activos"];
            }

            $valor_credito_calculo=0;
            $seguro_vida_calculo=0;
            if ($plazo){
                $opcion_cuota = 100000;
                $seguro_vida = 0;
                $cuota_corriente = $opcion_cuota - $seguro_vida;
                        
                $valor_credito = $cuota_corriente * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));
                            
                $seguro_vida = $valor_credito / 1000000.00 * $valor_por_millon_seguro * (1 + ($porcentaje_extraprima / 100));
                            
                $cuota_corriente = $opcion_cuota - $seguro_vida;
                            
                $porcentaje_seguro_biz = $seguro_vida / $opcion_cuota * 100;
                            
                $diferencia = $porcentaje_seguro_biz - $porcentaje_seguro;
                            
                $porcentaje_seguro = $porcentaje_seguro_biz;


                $valor_credito_calculo=$valor_credito;
                $seguro_vida_calculo=$seguro_vida;
            }
            else{
                $porcentaje_seguro = 0;
            }

            $valorPorcSeguro=round($porcentaje_seguro, 15);    
            $aportes = ($total_ingresos) * $porcentaje_aportes / 100.00;
            $total_aportes = round($aportes);

            $ingresos_menos_aportes = $total_ingresos - $total_aportes;


            if ($total_ingresos < $salario_minimo * 2){
                if ((strtoupper($nivel_contratacion) == "PENSIONADO")){
                    $salario_libre = $ingresos_menos_aportes / 2;
                }else{
                    $salario_libre = $salario_minimo;
                }       
            }
            else{
                $salario_libre = $ingresos_menos_aportes / 2;
                if (!((strtoupper($nivel_contratacion) == "PENSIONADO"))){
                    if ($salario_libre < $salario_minimo){
                        $salario_libre = $salario_minimo;
                    }                                
                }
            }

            $total_egresos=$total_aportes;

            if ($total_ingresos < $salario_minimo * 2){
                if (strtoupper($nivel_contratacion) == "PENSIONADO"){
                    $opcion_cuota_base = $total_ingresos - round($salario_libre) - $total_egresos;
                }
                else{
                    $opcion_cuota_base = $total_ingresos - $salario_minimo - $total_egresos;
                }
            }
            else{
                $opcion_cuota_base = $total_ingresos - round($salario_libre) - $total_egresos;
            }

            $opcion_cuota_cli = (($opcion_cuota_base)-$otros_descuentos)-$margen_seguridad;
            $opcion_cuota_cli_menos_seguro = round($opcion_cuota_cli) * (100.00 - $valorPorcSeguro) / 100.00;

            if ($tasa_interes){
                $valor_credito_cli = $opcion_cuota_cli_menos_seguro * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));
            }else{
                $valor_credito_cli = 0;
            }

            $result[]=array("id_tasa"=>$resTasa["id_tasa"],
                "id_tasa2"=>$resTasa["id_tasa2"],
                "porc_tasa"=>$resTasa["tasa_interes"],
                "valor_credito_cli"=>$valor_credito_cli,
                "opcion_cuota_cli"=>$opcion_cuota_cli,
                "plazo"=>$plazo,
                "opcion_cuota_cli_menos_seguro"=>$opcion_cuota_cli_menos_seguro,
                "total_aportes"=>$total_aportes,
                "otros_descuentos"=>$otros_descuentos,
                "margen_seguridad"=>$margen_seguridad,
                "cantSalariosMinimos"=>$cantSalariosMinimos
            ); 
        }

   



                

        $result2[]=array("valor_credito_cli"=>$valor_credito_cli,
            "opcion_cuota_cli_menos_seguro"=>$opcion_cuota_cli_menos_seguro,
            "opcion_cuota_base"=>$opcion_cuota_base,
            "valor_por_millon_seguro"=>$valor_por_millon_seguro,
            "valorPorcSeguro"=>$valorPorcSeguro,
            "seguro_vida_calculo"=>$seguro_vida_calculo,
            "valor_credito_calculo"=>$valor_credito_calculo,
            "porcentaje_aportes"=>$porcentaje_aportes,
            "salario_libre"=>$salario_libre,
            "total_aportes"=>$total_aportes,
            "opcion_cuota_cli"=>$opcion_cuota_cli     
        );

        header("HTTP/2.0 200 OK");
        $data = array("code"=>"200","message"=>"Consultado Satisfactoriamente","data" =>$result);
        echo json_encode($data);
    }


    function calcularTasas($plazo,$id_unidad_negocio,$nivel_contratacion)
    {
        global $link;
        $queryDB = "select t2.id_tasa2,t2.id_tasa,TRIM(t2.tasa_interes) + 0 as tasa_interes from tasas2 as t2 INNER JOIN tasas2_unidades as t2u ON t2.id_tasa2 = t2u.id_tasa2 where t2.id_tasa IN (select id_tasa from tasas where '".$plazo."' >= plazoi AND '".$plazo."' <= plazof)"; 
            
        $queryDB .= " AND t2u.id_unidad_negocio = '".$id_unidad_negocio."'";
            
            $queryDB .= " AND ((t2.solo_activos = '0' AND t2.solo_pensionados = '0')";
            
            if (strtoupper($nivel_contratacion) == "PENSIONADO")
                $queryDB .= " OR t2.solo_pensionados = '1'";
            else
                $queryDB .= " OR t2.solo_activos = '1'";
            
            $queryDB .= ") order by t2.tasa_interes DESC";


        $queryExec=sqlsrv_query($link, $queryDB);
        while ($resTasa=sqlsrv_fetch_array($queryExec))
        {
            $result[]=array("id_tasa"=>$resTasa["id_tasa"],
            "id_tasa2"=>$resTasa["id_tasa2"],
            "porc_tasa"=>$resTasa["tasa_interes"]
            ); 
        }

        header("HTTP/2.0 200 OK");
        $data = array("code"=>"200","message"=>"Consultado Satisfactoriamente","data" =>$result);
        echo json_encode($data);
    }


    function calcularPlazoMaximo($fechaNacimiento,$edad_maxima_activos,$edad_maxima_pensionados,$plazo_maximo,$nivel_contratacion)
    {
        global $link;
        $plazo=$plazo_maximo;
        $plazo_maximo_segun_edad = $plazo_maximo;
        $consultaPlazos="select ((DATEPART(YEAR , '".$fechaNacimiento."') +".$edad_maxima_activos.") - DATEPART(YEAR , GETDATE())) * 12 + (DATEPART(MONTH , DATEADD(YEAR,".$edad_maxima_activos.",'".$fechaNacimiento."')) - DATEPART(MONTH , GETDATE()))as meses_antes_activos, 
        ((DATEPART(YEAR , '".$fechaNacimiento."') +".$edad_maxima_pnsionados.") - DATEPART(YEAR , GETDATE())) * 12 + (DATEPART(MONTH , DATEADD(YEAR,".$edad_maxima_pensinado.",'".$fechaNacimiento."')) - DATEPART(MONTH , GETDATE())) as meses_antes_pensionados";

        $queryPlazos=sqlsrv_query($link, $consultaPlazos);
        $resPlazos=sqlsrv_fetch_array($queryPlazos);

        $diff_dias_ultimo_mes = date("j", strtotime($fechaNacimiento)) - date("j", strtotime(date("Y-m-d")));

        if ($nivel_contratacion == "PENSIONADO")
        {
            $meses_antes = $diff_dias_ultimo_mes >= 0 ? $resPlazos["meses_antes_pensionados"] : ($resPlazos["meses_antes_pensionados"] - 1);
        }else{
            $meses_antes = $diff_dias_ultimo_mes >= 0 ? $resPlazos["meses_antes_activos"] : ($resPlazos["meses_antes_activos"] - 1);
        }

        if (strtoupper($nivel_contratacion) != "PENSIONADO")
        {
            if ($meses_antes < $plazo_maximo)
            {
                $plazo = $meses_antes;
            }
        }

        if ($meses_antes < 0)
        {
            $plazo = 0;
        }
        
        
        $data[]=array("edad_maxima_activos"=>$edad_maxima_activos,
            "edad_maxima_pensionados"=>$edad_maxima_pensionados,
            "meses_antes_pensionados"=>$resPlazos["meses_antes_pensionados"],
            "meses_antes_activos"=>$resPlazos["meses_antes_activos"],
            "plazo"=>$plazo

        
            ); 

        $result = array('estado' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $data);
        return ($result);
    }
    
    
    
    
?>