<?php
	
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	include_once('../functions.php');

	$link = conectar_utf();

	//$rutaAdo = 'https://adocolombia-QA.ado-tech.com/KreditQA/api/KreditQA/';
	$rutaAdo = 'https://adocolumbia.ado-tech.com/Kredit/api/Kredit/'; //produccion

	if (isset($_POST['id_simulacion'])) {
		$key = 'db92efc69991';
		$id_simulacion = $_POST['id_simulacion'];

		//Consultamos si ya esta verificado
		$query = "SELECT TOP 1 estado_respuesta, registro_nuevo, id_transaccion FROM historial_tokens_verificacion_id WHERE id_simulacion = " . $id_simulacion . " AND fecha_visto IS NOT NULL AND (id_transaccion IS NOT NULL OR (estado_respuesta = 14 AND id < 41286)) ORDER BY id DESC";
		$consultar = sqlsrv_query($link, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if (sqlsrv_num_rows($consultar) > 0) {
			
			$verificacionDatos = sqlsrv_fetch_array($consultar, SQLSRV_FETCH_ASSOC);
			//2-Verificado o 14-verificado Anteriormente
			//Consultamos si ya esta verificado
			if ($verificacionDatos["registro_nuevo"] == 0) {
				if ($verificacionDatos["estado_respuesta"] == 2 || $verificacionDatos["estado_respuesta"] == 14) {
					
					$queryAdjunto = sqlsrv_query($link, "SELECT id_simulacion from adjuntos WHERE id_tipo = 32 AND id_simulacion = " . $id_simulacion, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					if (sqlsrv_num_rows($queryAdjunto) > 0) {
						$generar_doc = 0;
					} else {
						$generar_doc = 1;
					}
					header("HTTP/2.0 200 OK");
					$response = array("code" => "201", "mensaje" => "Cliente Verificado", "respuesta" => $verificacionDatos["estado_respuesta"], "generar_doc" => $generar_doc);
				} else {
					$opciones = array('http'=>array(
							'method' => 'GET',
							'header'=> "apiKey: ".$key."\r\n"
						)
					);

					$contexto = stream_context_create($opciones);
					//$json_Input = file_get_contents($rutaAdo . 'FindByNumberId?identification=' . $cedula . '&docType=1&returnImages=false', false, $contexto);
					$json_Input = file_get_contents($rutaAdo.'ValidationFS/'.$verificacionDatos["id_transaccion"].'?returnImages=false', false, $contexto);

					if($json_Input){
						$json_Input = preg_replace("[\n|\r|\n\r]", "", $json_Input);
						$datos = json_decode($json_Input);
						if (is_array($datos->Extras)) {
							$IdState = $datos->Extras[0]->IdState;
							$StateName = $datos->Extras[0]->StateName;
						} else {
							$IdState = $datos->Extras->IdState;
							$StateName = $datos->Extras->StateName;
						}

						sqlsrv_query($link, "UPDATE historial_tokens_verificacion_id SET estado_respuesta = " . $IdState . ", fecha_respuesta = CURRENT_TIMESTAMP, respuesta = '" . $json_Input . "' WHERE id_simulacion = " . $id_simulacion . "  AND fecha_visto IS NOT NULL");

						if ($IdState == 2 || $IdState == 14) {
							$queryAdjunto = sqlsrv_query($link, "SELECT id_simulacion from adjuntos WHERE id_tipo = 32 AND id_simulacion = " . $id_simulacion, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
							if (sqlsrv_num_rows($queryAdjunto) > 0) {
								$generar_doc = 0;
							} else {
								$generar_doc = 1;
							}

							header("HTTP/2.0 200 OK");
							if ($IdState == 2) {
								$response = array("code" => "200", "mensaje" => "Cliente Verificado", "respuesta" => $IdState, "generar_doc" => $generar_doc, "query"=> $query);
							} else {
								$response = array("code" => "200", "mensaje" => "Cliente Verificado (AT)", "respuesta" => $IdState, "generar_doc" => $generar_doc);
							}
						} else {
							$queryDesc = sqlsrv_query($link, "SELECT descripcion FROM ado_descripciones WHERE id = " . $IdState);
							$descAdo = sqlsrv_fetch_array($queryDesc, SQLSRV_FETCH_ASSOC);
							$descripcion = $descAdo["descripcion"];

							header("HTTP/2.0 200 OK");
							$response = array("code" => "400", "mensaje" => $StateName, "respuesta" => $IdState, "descripcion" => $descripcion);
						}
					} else {
						header("HTTP/2.0 200 OK");
						$response = array("code" => "404", "mensaje" => "No Se Obtuvo Respuesta del servidor de ADO", "respuesta" => 0);
					}
				}
			} else {
				header("HTTP/2.0 200 OK");
				$response = array("code" => "404", "mensaje" => "El cliente debe realizar el registro", "respuesta" => 0);
			}
		} else {
			header("HTTP/2.0 200 OK");
			$response = array("code" => "404", "mensaje" => "El cliente Aun no se ha verificado", "respuesta" => 0);
		}
	} else {
		header("HTTP/2.0 200 OK");
		$response = array("code" => "404", "mensaje" => "Datos No encontrados", "respuesta" => 0);
	}
	echo json_encode($response);
?>