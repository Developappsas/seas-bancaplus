<?php 
    include ('../../functions.php');
    include ('../../cors.php');
    header("Content-Type: application/json; charset=utf-8");
    $link = conectar_utf();

    $json_Input = file_get_contents('php://input');
    $params = json_decode($json_Input,true);

    $parametros = sqlsrv_query($link, "SELECT * from parametros where tipo = 'SIMULADOR' order by codigo");
    $j = 0;

    while ($fila1 = sqlsrv_fetch_array($parametros)){
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