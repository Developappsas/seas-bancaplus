<?php
include('../functions.php');
include('../function_blob_storage.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

?>
<?php include("top.php"); ?>
<?php

$queryDB = "select * from planoscuotasfondeador" . $sufijo . " where procesado = '0'";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
	if ($fila["nombre_grabado"])
		delete_file("otros", "ventas/" . $fila["nombre_grabado"]);

	sqlsrv_query($link, "delete from planoscuotasfondeador_detalle" . $sufijo . " where id_planocuotafondeador = '" . $fila["id_planocuotafondeador"] . "'");

	sqlsrv_query($link, "delete from planoscuotasfondeador" . $sufijo . " where id_planocuotafondeador = '" . $fila["id_planocuotafondeador"] . "'");
}

sqlsrv_query($link, "START TRANSACTION");

$uniqueID = date("YmdHis");

sqlsrv_query($link, "insert into planoscuotasfondeador" . $sufijo . " (descripcion, nombre_original, nombre_grabado, usuario_creacion, fecha_creacion) VALUES ('" . utf8_encode($_REQUEST["descripcion"]) . "', '" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]) . "', '" . $uniqueID . "_" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]) . "', '" . $_SESSION["S_LOGIN"] . "', GETDATE())");

$rs = sqlsrv_query($link, "select MAX(id_planocuotafondeador) as m from planoscuotasfondeador" . $sufijo);

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$id_planocuotafondeador = $fila["m"];

sqlsrv_query($link, "COMMIT");

if (strcmp($_FILES["archivo"]["name"], "")) {
	$fechaa = new DateTime();
	$fechaFormateada = $fechaa->format("d-m-Y H:i:s");

	$metadata1 = array(
		'id_planocuotafondeador' => $id_planocuotafondeador,
		'descripcion' => reemplazar_caracteres_no_utf($_REQUEST["descripcion"]),
		'usuario_creacion' => $_SESSION["S_LOGIN"],
		'fecha_creacion' => $fechaFormateada
	);

	upload_file($_FILES["archivo"], "otros", "ventas/" . $uniqueID . "_" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]), $metadata1);

	$file = fopen($_FILES['archivo']['tmp_name'], "r");

	$primer_registro = 1;

	$i = 0;

	while (!feof($file)) {
		$i++;

		$linea = fgets($file, 4096);

		$linea = str_replace(chr(10), "", $linea);

		$linea = str_replace(chr(13), "", $linea);

		if ($i != 1) {
			$datos = explode("\t", $linea);

			$observacion = "";
			$id_simulacion = "NULL";

			if ($datos[0]) {
				$cedula = trim(str_replace(".", "", str_replace(",", "", $datos[0])));
				$pagaduria = utf8_encode(trim($datos[1]));
				$nro_libranza = utf8_encode(trim($datos[2]));
				$cuota = trim($datos[3]);
				$valor = trim($datos[4]);

				if (!$_REQUEST["ext"]) {
					$queryDB = "select id_simulacion, opcion_credito, opcion_cuota_cli, opcion_desembolso_cli, opcion_cuota_ccc, opcion_desembolso_ccc, opcion_cuota_cmp, opcion_desembolso_cmp, opcion_cuota_cso, opcion_desembolso_cso from simulaciones where cedula = '" . $cedula . "' AND pagaduria = '" . $pagaduria . "' AND estado IN ('DES', 'CAN')";

					if ($nro_libranza)
						$queryDB .= " AND nro_libranza = '" . $nro_libranza . "'";

					$rs1 = sqlsrv_query($link, $queryDB);
				} else {
					$queryDB = "select id_simulacion, opcion_credito, opcion_cuota_cso, opcion_desembolso_cso from simulaciones" . $sufijo . " where cedula = '" . $cedula . "' AND pagaduria = '" . $pagaduria . "' AND estado IN ('DES', 'CAN')";

					if ($nro_libranza)
						$queryDB .= " AND nro_libranza = '" . $nro_libranza . "'";

					$rs1 = sqlsrv_query($link, $queryDB);
				}

				if (sqlsrv_num_rows($rs1)) {
					if (sqlsrv_num_rows($rs1) > 1) {
						$observacion = "Cr&eacute;dito no identificado";
					}

					if (!$observacion) {
						$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

						$observacion = "OK";
						$id_simulacion = $fila1["id_simulacion"];
					}
				} else {
					$queryDB = "select id_simulacion, estado from simulaciones" . $sufijo . " where cedula = '" . $cedula . "' AND pagaduria = '" . $pagaduria . "'";

					if ($nro_libranza)
						$queryDB .= " AND nro_libranza = '" . $nro_libranza . "'";

					$rs2 = sqlsrv_query($link, $queryDB);

					if (sqlsrv_num_rows($rs2)) {
						$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);

						if ($fila2["estado"] == "NEG" || $fila2["estado"] == "DST" || $fila2["estado"] == "DSS" || $fila2["estado"] == "ANU") {
							$observacion = "Cr&eacute;dito est&aacute; Negado, Desistido o Anulado";
						} else {
							$observacion = "Cr&eacute;dito no desembolsado";
						}
					} else {
						$observacion = "Cr&eacute;dito no encontrado";
					}
				}

				sqlsrv_query($link, "insert into planoscuotasfondeador_detalle" . $sufijo . " (id_planocuotafondeador, cedula, pagaduria, nro_libranza, cuota, valor, observacion, id_simulacion, usuario_creacion, fecha_creacion) values ('" . $id_planocuotafondeador . "', '" . $cedula . "', '" . $pagaduria . "', '" . $nro_libranza . "', '" . $cuota . "', '" . $valor . "', '" . $observacion . "', " . $id_simulacion . ", '" . $_SESSION["S_LOGIN"] . "', NOW())");

				if (sqlsrv_errors($link)) {
					$mensaje = "Error en la linea [" . $i . "]: " . sqlsrv_errors($link) . "\\n";
					break;
				}
			}
		}
	}

	if (feof($file)) {
		if ($_REQUEST["sin_previsualizar"] == "1") {
			sqlsrv_query($link, "BEGIN");

			$queryDB = "select * from planoscuotasfondeador_detalle" . $sufijo . " where id_planocuotafondeador = '" . $id_planocuotafondeador . "'";

			$queryDB .= " order by id_planocuotafondeadordetalle";

			$rs2 = sqlsrv_query($link, $queryDB);

			while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC)) {
				if ($fila2["id_simulacion"]) {
					$queryDB = "select * from ventas_cuotas_fondeador" . $sufijo . " where id_simulacion = '" . $fila2["id_simulacion"] . "' and cuota = '" . $fila2["cuota"] . "'";

					$rs = sqlsrv_query($link, $queryDB);

					if (sqlsrv_num_rows($rs)) {
						sqlsrv_query($link, "update ventas_cuotas_fondeador" . $sufijo . " set pago_fondeador = '" . $fila2["valor"] . "', usuario_modificacion = '" . $_SESSION["S_LOGIN"] . "', fecha_modificacion = NOW() where id_simulacion = '" . $fila2["id_simulacion"] . "' and cuota = '" . $fila2["cuota"] . "'");
					} else {
						sqlsrv_query($link, "insert into ventas_cuotas_fondeador" . $sufijo . " (id_simulacion, cuota, pago_fondeador, usuario_creacion, fecha_creacion) values ('" . $fila2["id_simulacion"] . "', '" . $fila2["cuota"] . "', '" . $fila2["valor"] . "', '" . $_SESSION["S_LOGIN"] . "', NOW())");
					}

					sqlsrv_query($link, "update planoscuotasfondeador_detalle" . $sufijo . " set aplicado = '1' where id_planocuotafondeadordetalle = '" . $fila2["id_planocuotafondeadordetalle"] . "'");
				}
			}

			sqlsrv_query($link, "update planoscuotasfondeador" . $sufijo . " set procesado = '1' where id_planocuotafondeador = '" . $id_planocuotafondeador . "'");

			sqlsrv_query($link, "COMMIT");
		}

		$mensaje = "OK";
	}

	fclose($file);
}

?>
<script>
	<?php

	if ($mensaje == "OK") {

	?>
		window.location = 'carguepagosfondeador_detalle.php?ext=<?php echo $_REQUEST["ext"] ?>&id_planocuotafondeador=<?php echo $id_planocuotafondeador ?>';
	<?php

	} else {

	?>
		alert("<?php echo $mensaje ?>");

		window.location = 'carguepagosfondeador.php?ext=<?php echo $_REQUEST["ext"] ?>';
	<?php

	}

	?>
</script>