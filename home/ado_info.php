<?php include('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "PROSPECCION" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA")) {
	exit;
}

?>
<style type="text/css">
	table {
		*border-collapse: collapse;
		/* IE7 and lower */
		border-spacing: 0;
	}

	th:first-child {
		border-radius: 6px 0 0 0;
	}

	th:last-child {
		border-radius: 0 6px 0 0;
	}

	th:only-child {
		border-radius: 6px 6px 0 0;
	}

	tr:first-child {
		border-radius: 6px 0 0 0;
	}

	tr:last-child {
		border-radius: 0 6px 0 0;
	}

	tr:only-child {
		border-radius: 6px 6px 0 0;
	}

	td:first-child {
		border-radius: 6px 0 0 0;
	}

	td:last-child {
		border-radius: 0 6px 0 0;
	}

	td:only-child {
		border-radius: 6px 6px 0 0;
	}
</style>
<link href="../style_impresion.css" rel="stylesheet" type="text/css">
<?php

//$get_data = callAPI("GET", "https://adocolumbia.ado-tech.com/Kredit/api/Kredit/ValidationAssociated/", false, $_REQUEST["id_simulacion"]);

$queryDB = "SELECT response_ado, score_ado from simulaciones where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

$rs = sqlsrv_query($link, $queryDB);

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

//if ($get_data != "ERROR")
if ($fila["response_ado"]) {
	//$response = json_decode($get_data, true);
	$response = json_decode($fila["response_ado"], true);

?>
	<table border="0" cellspacing=3 cellpadding=0 align="center">
		<tr>
			<td><img align src="../images/logo.png" height="80"></td>
		</tr>
		<tr>
			<td colspan="4" align="center">
				<h4>INFORMACION BASICA</h4></b>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" cellspacing=1 cellpadding=2 align="center">
					<tr style="background:#E0ECFF;">
						<td class="admintable"><label class="admintable"><b>Cedula:</label></td>
						<td class="admintable"><?php echo utf8_decode($response["Customer"]["IdentificationNumber"]) ?></td>
						<td class="admintable" width="20">&nbsp;</td>
						<td class="admintable"><label class="admintable"><b>Nombre:</label>
						<td class="admintable"><?php echo utf8_decode($response["Customer"]["Surnames"] . " " . $response["Customer"]["Names"]) ?></td>
					</tr>
					<tr>
						<td class="admintable"><label class="admintable"><b>F. Nacimiento:</label></td>
						<td class="admintable"><?php echo utf8_decode(substr($response["Customer"]["BirthDate"], 0, 10)) ?></td>
						<td class="admintable" width="20">&nbsp;</td>
						<td class="admintable"><label class="admintable"><b>Sexo:</label></td>
						<td class="admintable"><?php echo utf8_decode($response["Customer"]["Genre"]) ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4" align="center"><br>
				<h4>RESULTADO VALIDACION</h4></b>
			</td>
		</tr>
		<tr style="background:#E0ECFF;">
			<td colspan="4" class="admintable" align="center"><?php if ($response["Scores"]) {
																	echo utf8_decode(strtoupper($response["Scores"][0]["StateName"]));
																} else {
																	echo "PENDIENTE";
																} ?></td>
		</tr>
	</table>
	<table border="0" cellspacing=3 cellpadding=0 align="center">
		<tr>
			<td colspan="3" align="center"><br>
				<h4>IMAGENES</h4></b>
			</td>
		</tr>
		<?php

		$j = 0;

		for ($i = 0; $i <= 10; $i++) {
			if ($response["Images"][$i]["ImageTypeName"]) {
				$imageData = base64_decode($response["Images"][$i]["Image"]);
				$source = imagecreatefromstring($imageData);
				$imageSave = imagejpeg($source, "../temp/ADOIMG_" . $_REQUEST["id_simulacion"] . "_" . reemplazar_caracteres_no_utf(utf8_decode($response["Images"][$i]["ImageTypeName"])) . ".jpg");
				imagedestroy($source);
			}
		}

		for ($i = 0; $i <= 10; $i++) {
			if ($response["Images"][$i]["ImageTypeName"]) {
				$tr_class = "";

				if ($i % 2 == 1)
					$tr_class = " style='background:#E0ECFF;'";

		?>
				<tr<?php echo $tr_class ?>>
					<td class="admintable"><?php echo utf8_decode($response["Images"][$i]["ImageTypeName"]) ?></td>
					<td class="admintable" width="32" align="center"><a href="#" onClick="window.open('<?php echo "../temp/ADOIMG_" . $_REQUEST["id_simulacion"] . "_" . reemplazar_caracteres_no_utf(utf8_decode($response["Images"][$i]["ImageTypeName"])) . ".jpg" ?>', 'ADOIMG<?php echo $_REQUEST["id_simulacion"] . "_" . $response["Images"][$i]["Id"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><img src="../images/archivo.png" title="Abrir Adjunto"></a></td>
					</tr>
			<?php

			}
		}

			?>
	</table>
<?php

} else {

?>
	<table border="0" cellspacing=3 cellpadding=0 align="center">
		<tr>
			<td><img align src="../images/logo.png" height="80"></td>
		</tr>
		<tr>
			<td colspan="4" align="center"><br>
				<h4>RESULTADO VALIDACION</h4></b>
			</td>
		</tr>
		<tr style="background:#E0ECFF;">
			<td colspan="4" class="admintable" align="center"><?php if ($fila["score_ado"]) {
																	echo $fila["score_ado"];
																} else {
																	echo "PENDIENTE";
																} ?></td>
		</tr>
	</table>
<?

}

?>