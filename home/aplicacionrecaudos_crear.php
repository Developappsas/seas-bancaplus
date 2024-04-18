<?php
include_once('../functions.php');
include_once('../function_blob_storage.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"]){
	$sufijo = "_ext";
}else{
	$sufijo = '';
}

?>
<?php include("top.php"); ?>
<?php

$queryDB = "SELECT *, FORMAT(fecha_creacion, 'yyyy-MM') as ano_mes from recaudosplanos" . $sufijo . " where procesado = '0'";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
	if ($fila["nombre_grabado"]){
		delete_file("otros", "cartera/" . $fila["ano_mes"] . "/" . $fila["nombre_grabado"]);
	}

	if ($fila["nombre_grabado2"]){
		delete_file("otros", "cartera/" . $fila["ano_mes"] . "/" . $fila["nombre_grabado2"]);
	}

	sqlsrv_query($link, "DELETE from recaudosplanos_detalle" . $sufijo . " where id_recaudoplano = '" . $fila["id_recaudoplano"] . "'");

	sqlsrv_query($link, "SELECT from recaudosplanos" . $sufijo . " where id_recaudoplano = '" . $fila["id_recaudoplano"] . "'");
}

sqlsrv_query($link, "START TRANSACTION");

$uniqueID = date("YmdHis");

if (sqlsrv_query($link, "INSERT into recaudosplanos" . $sufijo . " (descripcion, nombre_original, nombre_grabado, nombre_original2, nombre_grabado2, usuario_creacion, fecha_creacion) VALUES ('" . utf8_encode($_REQUEST["descripcion"]) . "', '" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]) . "', '" . $uniqueID . "_" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]) . "', '" . reemplazar_caracteres_no_utf($_FILES["archivo2"]["name"]) . "', '" . $uniqueID . "_" . reemplazar_caracteres_no_utf($_FILES["archivo2"]["name"]) . "', '" . $_SESSION["S_LOGIN"] . "', GETDATE())")) {
	echo "creado";
} else {
	echo "no creado";
}



$rs = sqlsrv_query($link, "SELECT MAX(id_recaudoplano) as m from recaudosplanos" . $sufijo);

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$id_recaudoplano = $fila["m"];

sqlsrv_query($link, "COMMIT");

if (strcmp($_FILES["archivo"]["name"], "")) {
	$fechaa = new DateTime();
	$fechaFormateada = $fechaa->format("d-m-Y H:i:s");

	$metadata1 = array(
		'id_recaudoplano' => $id_recaudoplano,
		'descripcion' => reemplazar_caracteres_no_utf($_REQUEST["descripcion"]),
		'usuario_creacion' => $_SESSION["S_LOGIN"],
		'fecha_creacion' => $fechaFormateada
	);

	$cargado = false;

	try{
		$cargado = upload_file($_FILES["archivo"], "otros", "cartera/" . date("Y-m") . "/" . $uniqueID . "_" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]), $metadata1);
	} catch (ServiceException $exception) {
        $mensaje = $this->logger->error('failed to upload the file: ' . $exception->getCode() . ':' . $exception->getMessage());
        throw $exception;
    }

    if($cargado){

		$file = fopen($_FILES['archivo']['tmp_name'], "r");
		$primer_registro = 1;
		$i = 0;

		while (!feof($file)) {
			echo $i++;
			$linea = fgets($file, 4096);
			$linea = str_replace(chr(10), "", $linea);
			$linea = str_replace(chr(13), "", $linea);

			if ($i != 1) {
				$datos = explode("\t", $linea);
				$observacion = "";
				$id_simulacion = "NULL";
				if ($datos[0]) {
					$cedula = trim(str_replace(".", "", str_replace(",", "", $datos[0])));
					$nro_libranza = intval(trim(preg_replace("/[^0-9]/", "", $datos[1])));
					$pagaduria = utf8_encode(trim($datos[2]));
					$fecha = trim($datos[3]);
					$valor = trim($datos[4]);
					if (!$_REQUEST["ext"]) {
						$queryDB = "SELECT si.id_simulacion, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota = '1' where si.cedula = '" . $cedula . "' AND dbo.fn_extract_number(si.nro_libranza) = '" . $nro_libranza . "' AND si.pagaduria = '" . $pagaduria . "' AND (si.estado IN ('DES') OR (si.estado = 'EST' AND si.decision = '" . $label_viable . "' AND ((si.id_subestado IN (" . $subestado_compras_desembolso . ") AND si.estado_tesoreria = 'PAR') OR (si.id_subestado IN (" . $subestado_desembolso . ", '" . $subestado_desembolso_cliente . "', '" . $subestado_desembolso_pdte_bloqueo . "'))))) AND cu.id_simulacion IS NOT NULL";

						if ($_SESSION["S_SECTOR"]) {
							$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
						}
						if ($_SESSION["S_TIPO"] == "COMERCIAL") {
							$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
						} else {
							$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
						}

						$rs1 = sqlsrv_query($link, $queryDB);
					} else {
						$queryDB = "SELECT si.id_simulacion, si.opcion_credito, si.opcion_cuota_cso, si.opcion_desembolso_cso from simulaciones" . $sufijo . " si LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.cedula = '" . $cedula . "' AND dbo.fn_extract_number(si.nro_libranza) = '" . $nro_libranza . "' AND si.pagaduria = '" . $pagaduria . "' AND si.estado IN ('DES')";
						if ($_SESSION["S_SECTOR"]) {
							$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
						}

						$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					}
					if (sqlsrv_num_rows($rs1)) {
						if (sqlsrv_num_rows($rs1) > 1) {
							while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
								$opcion_cuota = "";

								switch ($fila1["opcion_credito"]) {
									case "CLI":
										$opcion_cuota = $fila1["opcion_cuota_cli"];
										break;
									case "CCC":
										$opcion_cuota = $fila1["opcion_cuota_ccc"];
										break;
									case "CMP":
										$opcion_cuota = $fila1["opcion_cuota_cmp"];
										break;
									case "CSO":
										$opcion_cuota = $fila1["opcion_cuota_cso"];
										break;
								}
								if ($valor == $opcion_cuota) {
									$observacion = "OK";
									$id_simulacion = $fila1["id_simulacion"];
									break;
								}
							}
							if (!$observacion)
								$observacion = "Cr&eacute;dito no identificado";
						}
						if (!$observacion) {

							$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
							$aplicar_pago = 0;
							if ($_SESSION["FUNC_BOLSAINCORPORACION"] && !$_REQUEST["ext"]) {
								$queryDB = "SELECT dbo.fn_total_recaudado(id_simulacion, 0) as total_recaudado, FORMAT(fecha_primera_cuota, 'yyyy-MM') as mes_primera_cuota, FORMAT('" . $fecha . "', 'yyyy-MM') as mes_recaudo from simulaciones where id_simulacion = '" . $fila1["id_simulacion"] . "'";

								$rs = sqlsrv_query($link, $queryDB);
								$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
								$tiene_recaudo = $fila["total_recaudado"];
								if (!$tiene_recaudo) {
									$opcion_cuota = "";

									switch ($fila1["opcion_credito"]) {
										case "CLI":
											$opcion_cuota = $fila1["opcion_cuota_cli"];
											break;
										case "CCC":
											$opcion_cuota = $fila1["opcion_cuota_ccc"];
											break;
										case "CMP":
											$opcion_cuota = $fila1["opcion_cuota_cmp"];
											break;
										case "CSO":
											$opcion_cuota = $fila1["opcion_cuota_cso"];
											break;
									}
									if ($valor < $opcion_cuota) {
										if ($fila["mes_recaudo"] < $fila["mes_primera_cuota"]) {
											$observacion = "El recaudo no cumple las condiciones para ser aplicado.<br>Ser&aacute; llevado a la bolsa de incorporaci&oacute;n";

											$id_simulacion = $fila1["id_simulacion"];
										} else {
											$aplicar_pago = 1;
										}
									} else {
										$aplicar_pago = 1;
									}
								} else {
									$aplicar_pago = 1;
								}
							} else {
								$aplicar_pago = 1;
							}
							if ($aplicar_pago) {
								$observacion = "OK";
								$id_simulacion = $fila1["id_simulacion"];
							}
						}
					} else {
						$queryDB = "SELECT si.id_simulacion, si.estado from simulaciones" . $sufijo . " si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.cedula = '" . $cedula . "' AND dbo.fn_extract_number(si.nro_libranza) = '" . $nro_libranza . "' AND si.pagaduria = '" . $pagaduria . "'";

						if ($_SESSION["S_SECTOR"]) {
							$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
						}

						if (!$_REQUEST["ext"]) {
							if ($_SESSION["S_TIPO"] == "COMERCIAL") {
								$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
							} else {
								$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
							}
						}

						$rs2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

						if (sqlsrv_num_rows($rs2)) {
							$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
							if ($fila2["estado"] == "NEG" || $fila2["estado"] == "DST" || $fila2["estado"] == "DSS" || $fila2["estado"] == "CAN" || $fila2["estado"] == "ANU") {
								$observacion = "Cr&eacute;dito est&aacute; Negado, Desistido, Cancelado o Anulado";
							} else {
								$observacion = "Cr&eacute;dito no desembolsado";
							}
						} else {
							$observacion = "Cr&eacute;dito no encontrado";
						}
					}
					sqlsrv_query($link, "INSERT into recaudosplanos_detalle" . $sufijo . " (id_recaudoplano, cedula, nro_libranza, pagaduria, fecha, valor, observacion, id_simulacion, usuario_creacion, fecha_creacion) values ('" . $id_recaudoplano . "', '" . $cedula . "', '" . $nro_libranza . "', '" . $pagaduria . "', '" . $fecha . "', '" . $valor . "', '" . $observacion . "', " . $id_simulacion . ", '" . $_SESSION["S_LOGIN"] . "', GETDATE())");
					if (sqlsrv_errors($link)) {
						$mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_errors($link) . "\\n";
						break;
					}
				}
			}
		}

		if (feof($file)) {
			$mensaje = "OK";
		}
		fclose($file);
	}else{
		$mensaje = "Error al cargar el archivo al contenedor";
	}
}

?>
<!-- <script>
	<?php

	if ($mensaje == "OK") {

	?>
		window.location = 'aplicacionrecaudos_detalle.php?ext=<?php echo $_REQUEST["ext"] ?>&id_recaudoplano=<?php echo $id_recaudoplano ?>';
	<?php

	} else {

	?>
		alert("<?php echo $mensaje ?>");

		window.location = 'aplicacionrecaudos.php?ext=<?php echo $_REQUEST["ext"] ?>';
	<?php

	}

	?>
</script> -->