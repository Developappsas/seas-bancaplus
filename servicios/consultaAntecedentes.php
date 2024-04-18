<?php
include_once ('../functions.php');
$link = conectar_utf();

$ruta = "https://cloud.uipath.com/";

if(isset($_POST['id_simulacion'])){
	$id_simulacion = $_POST['id_simulacion'];
	$consulta = sqlsrv_query($link,"SELECT id, iif(respuesta IS NULL, 0, respuesta) AS respuesta  FROM historial_consultas_judiciales WHERE id_simulacion = $id_simulacion AND id = (SELECT MAX(id) FROM historial_consultas_judiciales WHERE id_simulacion = $id_simulacion)", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

	$consultado = 0; $respuesta = 0;
	if(sqlsrv_num_rows($consulta) > 0){
		$registro = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);

		if($registro["respuesta"] == 2){
			$consultado = 2;
		}else{
			$consultado = 1;
		}
	}

	if($_POST['peticion'] == 'consultarPeticion'){
		if($consultado == 0){
			header("HTTP/2.0 200 OK");
			$response = array( "code"=>"400","mensaje"=>"No existe Petición A Gattaca");
		}elseif($consultado == 1){
			header("HTTP/2.0 200 OK");
			$response = array( "code"=>"201","mensaje"=>"Existe Petición Pendiente", "dato"  => $consultado);
		}elseif($consultado == 2){
			header("HTTP/2.0 200 OK");
			$response = array( "code"=>"200","mensaje"=>"Consultado Exitosamente", "dato"  => $consultado);
		}
	} else if($_POST['peticion'] == 'enviarPeticion'){

		if($consultado == 0){

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $urlPrincipal.'/servicios/obtenerToken.php',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
			));

			$json_Input = curl_exec($curl);

			curl_close($curl);

			if(!empty($json_Input)){
				$data = json_decode($json_Input);
				
				if($data->code == "200"){
					$token = $data->token;

					$curl = curl_init();

					$consultar = sqlsrv_query($link,"SELECT cedula, nombre FROM simulaciones WHERE id_simulacion = ".$id_simulacion);
					$datosCedula = sqlsrv_fetch_array($consultar, SQLSRV_FETCH_ASSOC);
					$cedula = $datosCedula[0];
					$nombres = $datosCedula[1];
					$apellidos = $datosCedula[1];

					curl_setopt_array($curl, array(
						CURLOPT_URL => $ruta.'gattaovpukru/DefaultTenant/odata/Queues/UiPathODataSvc.AddQueueItem',
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'POST',
						CURLOPT_POSTFIELDS =>'{
						    "itemData": {
						        "Name": "KreditLegalQueue",
						        "Priority": "Normal",
						        "SpecificContent": {
						            "id_simulacion": "'.$id_simulacion.'",
						            "cedula": "'.$cedula.'",
						            "nombres": "'.$nombres.'",
						            "apellidos": "'.$apellidos.'"
						        },
						        "Reference": "'.$id_simulacion.'"
						    }
						}',
						CURLOPT_HTTPHEADER => array(
							'X-UIPATH-TenantName: DefaultTenant',
							'X-UIPATH-OrganizationUnitId: 3404068',
							'Authorization: Bearer '.$token,
							'Content-Type: application/json'
						),
					));

					$response = curl_exec($curl);

					curl_close($curl);

					if(!empty($response)){
						$data = json_decode($response);
						
						if(isset($data->Id) && !empty($data->Id)){
							if(sqlsrv_query($link,"INSERT INTO historial_consultas_judiciales (`id_simulacion`, `id_gattaca`) VALUES ($id_simulacion, $data->Id)")){
								header("HTTP/2.0 200 OK");
								$response = array( "code"=>"200","mensaje"=>"Respuesta Satisfactoria.", "dato" => $data->Id);
							}else{
								header("HTTP/2.0 200 OK");
								$response = array( "code"=>"500","mensaje"=>"Erro al Guardar Consulta Juridica Gattaca.");
							}
						}else{
							header("HTTP/2.0 200 OK");
							$response = array( "code"=>"500","mensaje"=>"No se ha encontrado respuesta.");
						}
					}else{
						header("HTTP/2.0 200 OK");
						$response = array( "code"=>"406","mensaje"=>"No se encuentra contenido del servidor de Gattaca");
					}
				}else if(isset($data->code)){
					header("HTTP/2.0 200 OK");
					$response = array("code"=>$data->code,"mensaje"=>$data->mensaje);
				}else{
					header("HTTP/2.0 200 OK");
					$response = array("code"=>'404',"mensaje"=>$json_Input);
				}
			}else{
				header("HTTP/2.0 200 OK");
				$response = array( "code"=>"500","mensaje"=>"No se pudo obtener token de Gattaca (1).");
			}
		}else{
			header("HTTP/2.0 200 OK");
			$response = array( "code"=>"201", "mensaje"=>"Consultado y Generado Anteriormente", "dato" => $consultado);
		}
	}else{
		header("HTTP/2.0 200 OK");
		$response = array( "code"=>"404","mensaje"=>"No existen datos de entrada 2".$_POST['peticion']);
	}
}else{
	header("HTTP/2.0 200 OK");
	$response = array( "code"=>"404","mensaje"=>"No existen datos de entrada 1");
}

echo json_encode($response);
?>
