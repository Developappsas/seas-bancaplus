<?php
    include_once ('../../functions.php');
    include ('../cors.php');
    header("Content-Type: application/json; charset=utf-8");
    $link = conectar_utf();

    //ENVIAR CREDITO A MODULO DE VISADOS
	$estadosModuloVisado=array(4); //Mover a functions o parametros de SEAS.
    
	if (in_array($_REQUEST["id_subestado"], $estadosModuloVisado)) {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
		    CURLOPT_URL => 'https://az-ase-use-dev-back-layersecurity-k.azurewebsites.net/api/Login',
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => '',
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 0,
		    CURLOPT_FOLLOWLOCATION => true,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => 'POST',
		    CURLOPT_POSTFIELDS =>'{"usuario":"jzapata1","clave":"sa"}',
		    CURLOPT_HTTPHEADER => array( 'Content-Type: application/json' )
        ));

		$responseLogin = curl_exec($curl);
		curl_close($curl);
		$respuestaArray = json_decode($responseLogin,true);
		if ($respuestaArray["codigo"]=="200") {
			$data = array();
			$consultarInformacionCredito = "SELECT c.identificacion as identificacion_pagaduria, b.nombre1, b.nombre2, b.apellido1, b.apellido2, a.id_simulacion, a.nro_libranza, a.cedula, a.plazo from simulaciones a LEFT JOIN solicitud b ON a.id_simulacion = b.id_simulacion LEFT JOIN pagadurias c ON a.pagaduria = c.nombre where a.id_simulacion = '".$_REQUEST["id_simulacion"]."'";
			
			$queryInformacionCredito = sqlsrv_query($link, $consultarInformacionCredito);
			$resInformacionCredito = sqlsrv_fetch_array($queryInformacionCredito, SQLSRV_FETCH_ASSOC);
			
			$cuota = 0;
			echo "<br><br>"."opcion_credito: ".$_REQUEST["opcion_credito"];
			switch($_REQUEST["opcion_credito"]) {
				case "CLI":	$cuota = $_REQUEST["opcion_cuota_cli"]; break;
				case "CCC":	$cuota = $_REQUEST["opcion_cuota_ccc"]; break;
				case "CMP":	$cuota = $_REQUEST["opcion_cuota_cmp"]; break;
				case "CSO":	$cuota = $_REQUEST["opcion_cuota_cso"]; break;
			}
				

			$consultarComprasCarteraCredito = "SELECT concat(c.nombre,'-',a.entidad) as nombre_entidad,a.*,b.fecha_vencimiento FROM simulaciones_comprascartera a LEFT JOIN agenda b ON a.id_simulacion=b.id_simulacion AND a.consecutivo=b.consecutivo LEFT JOIN entidades_desembolso c ON c.id_entidad=a.id_entidad WHERE a.id_simulacion='".$_REQUEST["id_simulacion"]."' AND a.se_compra='SI' AND a.cuota>0";
			echo "<br><br>";
			$queryComprasCarteraCredito=sqlsrv_query($link, $consultarComprasCarteraCredito, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				
				
			if (sqlsrv_num_rows($queryComprasCarteraCredito)>0) {
				$cadena_compras="";
				while ($resSimulacionesDatos=sqlsrv_fetch_array($queryComprasCarteraCredito)) {					
					if ($resSimulacionesDatos["fecha_vencimiento"]==null || $resSimulacionesDatos["fecha_vencimiento"]=="") {
						$cadena_compras.='{
							"compra_Entidad_Nombre":"'.$resSimulacionesDatos["entidad"].'",
							"compra_Valor_Pagar":'.$resSimulacionesDatos["valor_pagar"].',
							"compra_Valor_Cuota":'.$resSimulacionesDatos["cuota"].'
						},';
					}else{
						$cadena_compras.='{
							"compra_Entidad_Nombre":"'.$resSimulacionesDatos["entidad"].'",
							"compra_Valor_Pagar":'.$resSimulacionesDatos["valor_pagar"].',
							"compra_Valor_Cuota":'.$resSimulacionesDatos["cuota"].',
							"compra_Fecha_Vencimiento":"'.$resSimulacionesDatos["fecha_vencimiento"].'"
						},';
					}					
				}

				$cadena_Credito.='{"credito_Id":'.$_REQUEST["id_simulacion"].',
					"cliente_Nombres":"'.($resInformacionCredito["nombre1"]." ".$resInformacionCredito["nombre2"]).'",
					"cliente_Apellidos":"'.($resInformacionCredito["apellido1"]." ".$resInformacionCredito["apellido2"]).'",
					"credito_Libranza":"'.$resInformacionCredito["nro_libranza"].'",
					"cliente_Identificacion":"'.$resInformacionCredito["cedula"].'",
					"credito_Pagaduria":"'.$resInformacionCredito["identificacion_pagaduria"].'",
					"credito_Valor":'.str_replace(",", "", $_REQUEST["valor_credito"]).',
					"credito_Valor_Cuota":'.str_replace(",", "", $cuota).',
					"credito_Plazo":'.$resInformacionCredito["plazo"].',
					"credito_Valor_Menos_Retanqueo":'.str_replace(",", "", $_REQUEST["sin_retanqueos"]).',
					"credito_Compras_Cartera":['.substr($cadena_compras,0,-1).']}';
				
                $curl = curl_init();

				curl_setopt_array($curl, array(
				    CURLOPT_URL => 'https://az-ase-use-dev-back-inc-k.azurewebsites.net/api/Creditos/Crear_Creditos',
				    CURLOPT_RETURNTRANSFER => true,
				    CURLOPT_ENCODING => '',
				    CURLOPT_MAXREDIRS => 10,
				    CURLOPT_TIMEOUT => 0,
				    CURLOPT_FOLLOWLOCATION => true,
				    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				    CURLOPT_CUSTOMREQUEST => 'POST',
				    CURLOPT_POSTFIELDS =>$cadena_Credito,
				    CURLOPT_HTTPHEADER => array(
				    	'Authorization: Bearer '.$respuestaArray["data"]["usuario_Token"],
				    	'Content-Type: application/json'
				    )
				));

			    $response = curl_exec($curl);

				curl_close($curl);
				echo $response;
			}
		}
	}
?>