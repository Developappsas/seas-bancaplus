<?php 
	include ('../functions.php');
	$link = conectar();

	$response = array();

	if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || $_SESSION["S_SUBTIPO"] == "ANALISTA_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA") {
		echo "Debe iniciar sesion con un perfil que tenga este modulo habilitado.";
		exit;
	}else{	
		if ($_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR") {
			if ($_GET["nro_libranza"]) {			
				$query_select_simulaciones = "SELECT nro_libranza, id_simulacion, sum(retanqueo1_valor + retanqueo2_valor + retanqueo3_valor) AS retanqueo1_valor FROM simulaciones WHERE nro_libranza = '".$_GET["nro_libranza"]."'";	
				$ejecutar = sqlsrv_query($link,$query_select_simulaciones);
				if ($ejecutar) {			
					$response = sqlsrv_fetch_array($ejecutar);
					$nro_libranza = $response["nro_libranza"];
					$id_simulacion = $response["id_simulacion"];
					$retanqueo1_valor = $response["retanqueo1_valor"];
					$query_update_giros = "INSERT INTO giros (id_simulacion, id_beneficiario, beneficiario, identificacion, valor_girar, nro_cheque, clasificacion, id_cuentabancaria, fecha_giro, forma_pago, usuario_creacion, fecha_creacion) VALUES (".$id_simulacion.", '104', 'P.A. ESEFECTIVO', '900840591-1', ".$retanqueo1_valor.", 'SINTRANSFER', 'RET', 2, GETDATE(), 'EFE', 'ajimenez', current_timestamp)"; 
					$ejecutar_giro = sqlsrv_query( $link,$query_update_giros);
					if ($ejecutar_giro) {
						$response[] = array("1"=>"Giro agregado");

						$query_tesoreria_cc = "UPDATE tesoreria_cc SET fecha_giro = GETDATE(), pagada = 1 WHERE id_simulacion = '".$id_simulacion."'";
						$ejecutar_tesoreria_cc = sqlsrv_query($link,$query_tesoreria_cc);

						if ($ejecutar_tesoreria_cc) {
							$response[] = array("2"=>"Fecha y pago de giro OK");
						}

						$query_fecha_vencimiento = "UPDATE agenda SET fecha_vencimiento = GETDATE() WHERE id_simulacion = '".$id_simulacion."' AND entidad like 'P.A. ESE%'";
						$ejecutar_fecha_vencimiento = sqlsrv_query($link,$query_fecha_vencimiento);
						if ($ejecutar_fecha_vencimiento) {
							$response[] = array("3"=>"Fecha de vencimiento OK");
						}

						
						$fecha_cartera = ("SELECT FORMAT(si.fecha_cartera, 'Y-m') AS mes_cartera FROM simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion LEFT JOIN bancos ba ON si.id_banco = ba.id_banco WHERE si.id_simulacion = '".$id_simulacion."'");
						$query_mes_cartera = ("UPDATE simulaciones SET fecha_cartera = ".$fecha_cartera." where id_simulacion = '".$id_simulacion."'");

						$ejecutar_mes_cartera = sqlsrv_query($link,$query_mes_cartera);
						if ($ejecutar_mes_cartera) {
							$response[] = array("4"=>"Fecha cartera OK");
						}
						
						$simulaciones_estado_tesoreria = ("UPDATE simulaciones SET estado_tesoreria = 'PAR', fecha_desembolso = (SELECT MIN(fecha_giro) FROM giros WHERE id_simulacion = '".$id_simulacion."') where id_simulacion = '".$id_simulacion."'");
						$ejecutar_simulaciones_estado_tesoreria = sqlsrv_query( $link,$simulaciones_estado_tesoreria);
						if ($ejecutar_simulaciones_estado_tesoreria) {
							$response[] = array("5"=>"Simulaciones Estado Tesoreria OK");
						}

						$query_cierre_lib_simulaciones = ("UPDATE simulaciones SET estado_tesoreria = 'CER', estado = 'DES', fecha_desembolso = (SELECT MIN(fecha_giro) FROM giros WHERE id_simulacion = '".$id_simulacion."'), id_subestado = NULL WHERE id_simulacion = '".$id_simulacion."'");
						$ejecutar_cierre_lib_simulaciones = sqlsrv_query($link,$query_cierre_lib_simulaciones);
						if ($ejecutar_cierre_lib_simulaciones) {
							$response[] = array("5"=>"Cierre Libranza Simulaciones OK");
						}

						$response[] = array("Resultado"=>"Giro ingresado correctamente");
					
					}

				}else{
					$response = "No se pudo consultar la libranza =>". sqlsrv_error($link);
				}
				
			}else{
				echo "Debe indicar un numero de libranza."."<br>";
				echo "Por ejemplo:"."<br>";
				echo "https://seas.kredit.com.co/home/tesoreria_cierres.php?nro_libranza=EFEC 22363";
			}
		}else{			
			echo $_SESSION["S_TIPO"];
		}
	}

	echo json_encode($response);

?>
